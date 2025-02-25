<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Stripe\Webhook;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Payment;
use App\Models\Attendee;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use App\Notifications\PaymentFailed;
use Illuminate\Http\RedirectResponse;
use App\Notifications\PaidTicketIssued;

class CartController extends Controller
{
    /**
     * Cart
     */
    public function cart(Request $request): Response | RedirectResponse {
        $user = $request->user();
        $attendee = $user->attendee();
        $fees = $attendee->fees();
        $attendee->load('type');
        $user->load('group');
        $subdelegates = null;
        $unassignedDelegates = null;

        if($attendee->hasSubdelegates()) {
            $subdelegates = collect($attendee->subdelegates)->map(function ($delegate, $key) use ($user, $attendee) {
                $relatedUser = User::where('email', $delegate->email)->where('group_id', $user->group_id)->first();
                $delegate->attendee_id = ($relatedUser) ? $relatedUser->attendee()->id : false;
                $delegate->id = $key;
                $delegate->fees = ($relatedUser) ? $relatedUser->fees($attendee->custom_fee) : null;;
                $delegate->fee = ($delegate->fees) ? $delegate->fees[0] : null;
                return $delegate;
            });

            $unassignedDelegates = User::select('first_name', 'last_name', 'users.id', 'attendees.id as attendee_id')
              ->join('attendees', 'attendees.user_id', '=', 'users.id')
              ->where('users.group_id', $user->group_id)
              ->where('attendees.type_id', $attendee->type_id)
              ->whereNull('attendees.registered_by_user_id')
              ->whereNot('users.id', $user->id)
              ->get();
        }

        return Inertia::render('Cart/Cart', [
            'user' => $request->user(),
            'attendee' => $attendee,
            'fees' => $fees,
            'subdelegates' => $subdelegates,
            'unassignedDelegates' => $unassignedDelegates
        ]);
    }

    /**
     * Create a payment
     */
    public function createPayment(Request $request): RedirectResponse {
      $stripe = new StripeClient(config('services.stripe.secret'));

      // Validate cart
      $request->validate([
        'cart.*.id' => 'required',
        'invoice.name' => 'required',
        'invoice.address' => 'required_if:invoice.type,organisation'
      ]);
      
      $cart = [
        'ui_mode' => 'embedded',
        'customer_email' => $request->user()->email,
        'line_items' => $this->buildLineItems($request),
        'mode' => 'payment',
        'return_url' => config('app.url') . '/cart/return?session_id={CHECKOUT_SESSION_ID}',
      ];
      $checkoutSession = $stripe->checkout->sessions->create($cart);

      // Create a new paymet
      $payment = new Payment;
      $payment->attendee_id = $request->user()->attendee()->id;
      $payment->receipt = ['cart' => $request->cart, 'invoice' => $request->invoice];
      $payment->amount = $checkoutSession->amount_total / 100;
      $payment->status = 'pending';
      $payment->checkout_session_id = $checkoutSession->id;
      $payment->checkout_client_secret = $checkoutSession->client_secret;
      $payment->save();

      return to_route('payment', $payment);
    }

    /**
     * Payment screen
     */
    public function payment(Payment $payment, Request $request): Response | RedirectResponse
    {
      if ($request->user()->cannot('view', $payment)) {
        abort(403);
      }
      
      return Inertia::render('Cart/Payment', [
          'payment' => $payment,
          'stripeKey' => config('services.stripe.publishable')
      ]);
  }

    /**
     * Return page
     */
    public function return(Request $request): Response | RedirectResponse {
      $stripe = new StripeClient(config('services.stripe.secret'));
      $payment = Payment::where('checkout_session_id', $request->session_id)->first();

      if (!$payment) {
        abort(404, 'Payment not found');
      }

      try {
        $session = $stripe->checkout->sessions->retrieve($request->session_id);

        if($session->status === 'complete') {
          $payment->completed_checkout = 1;
          $payment->save();

          if ($session->payment_status === 'paid' && $payment->status !== 'refunded') {
            $payment->status = 'paid';
            $payment->save();

            return redirect()->route('badge')->with('status', 'success');
          }
        }
      } catch (\Exception $e) {
        return abort(500, 'Error processing payment');
      }

      return Inertia::render('Cart/Return', [
        'user' => $request->user(),
        'status' => $session->status,
        'payment_status' => $session->payment_status,
        'payment_declined' => $payment->status === 'declined'
      ]);
    }

    /**
     * Fulfill a Stripe payment
     */
    public function fulfill()
    {
      $payload = @file_get_contents('php://input');
      $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
      $endpoint_secret = config('services.stripe.webhook');
      $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

      if ($event->type === 'checkout.session.completed' || $event->type === 'checkout.session.async_payment_succeeded') {
        $this->fulfillCheckout($event->data->object->id);
      } elseif ($event->type === 'checkout.session.async_payment_failed') {
        $this->failedCheckout($event->data->object->id);
      } elseif ($event->type === 'checkout.session.expired') {
        $this->deletePayment($event->data->object->id);
      }

      return response()->json([
        'success' => true
      ]);
    }

     /**
     * Link a delegate
     */
    public function linkDelegate(Request $request): RedirectResponse {
      $delegate = Attendee::find($request->delegate);
      
      if (!$delegate) {
        abort(404, 'Delegate not found');
      }

      $delegate->registered_by_user_id = $request->user()->id;
      $delegate->save();

      // Update correct email on main delegate json
      $attendee = $request->user()->attendee();
      $subdelegates = collect($attendee->subdelegates)->map(function ($subdelegate, $key) use ($request, $delegate) {
        if ($key === $request->row_id) {
          $subdelegate->first_name = $delegate->user->first_name;
          $subdelegate->last_name = $delegate->user->last_name;
          $subdelegate->email = $delegate->user->email;
        }
        return $subdelegate;
      })->toArray();
      $attendee->subdelegates = $subdelegates;
      $attendee->save();

      return to_route('cart');
    }

    /** 
     * Remove a subdelegate
     */
    public function removeDelegate(Request $request): RedirectResponse {
      // Remove subdelegate from json
      $attendee = $request->user()->attendee();
      $subdelegates = collect($attendee->subdelegates)->filter(function ($subdelegate, $key) use ($request) {
        return $key !== $request->row_id;
      })->values()->toArray();

      $attendee->subdelegates = $subdelegates;
      $attendee->save();
      
      return to_route('cart');
    }

    /**
     * Fulfill an order
     */
    private function fulfillCheckout($sessionId)
    {
      $stripe = new StripeClient(config('services.stripe.secret'));
      $session = $stripe->checkout->sessions->retrieve($sessionId, [
        'expand' => ['line_items'],
      ]);

      if ($session->payment_status != 'unpaid') {
        $payment = Payment::where('checkout_session_id', $sessionId)->first();

        if ($payment) {
          $payment->status = 'paid';
          $payment->save();

          // Fullfil each item in ticket
          foreach($payment->receipt->cart as $ticket) {
            $attendee = Attendee::find($ticket->attendee_id);

            if ($attendee) {
              if (!$attendee->ticket_notified) {
                $attendee->user->notify(new PaidTicketIssued($payment));
              }

              $attendee->paid = 1;
              $attendee->ticket_notified = 1;
              $attendee->save();
            }
          }
        }
      }
    }

    /**
     * Notify user of failed order
     */
    private function failedCheckout($sessionId)
    {
      $payment = Payment::where('checkout_session_id', $sessionId)->first();
      $payment->status = 'declined';

      if (!$payment->error_notified) {
        $payment->error_notified = 1;
        $payment->user->notify(new PaymentFailed($payment));
      }

      return $payment->save();
    }

    /**
     * Notify user of failed order
     */
    private function deletePayment($sessionId)
    {
      $payment = Payment::where('checkout_session_id', $sessionId)->whereNot('status', 'paid')->first();
      return ($payment) ? $payment->delete() : false;
    }

    /**
     * Create line items
     */
    private function buildLineItems($request): array
    {
      $items = collect($request->cart)->map(function ($item) use ($request) {
        if ($item['id'] === 'custom') {
          $customFee = $request->user()->attendee()->custom_fee;
          if (!$customFee) {
            abort(401, 'Not authorized');
          }
          $fee = ['name' => 'Participant fee', 'amount' => $customFee];
        } else {
          $fee = Fee::find($item['id'])->toArray();
        }
  
        return [
          'price_data' => [
            'currency' => 'EUR',
            'product_data' => [
                'name' => $fee['name'],
                'description' => $item['description']
            ],
            'unit_amount' => $fee['amount'] * 100
          ],
          'quantity' => 1,
        ];
      })->toArray();

      return $items;
    }
}
