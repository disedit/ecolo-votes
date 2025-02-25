<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Response as InertiaResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * List invoices
     */
    public function index(Request $request): InertiaResponse
    {
        $invoices = $request->user()->payments()->where('completed_checkout', 1)->orderBy('created_at', 'desc')->get();
        return Inertia::render('Invoices', [
            'invoices' => $invoices
        ]);
    }

    /**
     * Download an invoice
     */
    public function view(Payment $payment, Request $request): Response
    {
        if ($request->user()->cannot('view', $payment)) {
            abort(403, 'You don\'t have access to this invoice');
        }

        $edition = Edition::current()->first();

        $data = [
            'user' => $request->user(),
            'attendee' => $request->user()->attendee(),
            'payment' => $payment,
            'edition' => $edition
        ];
        $pdf = Pdf::loadView('pdf.invoice', $data);
        return $pdf->stream('invoice.pdf');
    }
}
