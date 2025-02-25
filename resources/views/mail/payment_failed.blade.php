<x-mail::message>
Hi {{ $user->first_name }},
 
We have had a problem processing your payment for the **39th European Greens Congress**.
Please try to purchase the ticket again or get in touch with us at
<a href="mailto:congress@europeangreens.eu">congress@europeangreens.eu</a>
 
<x-mail::button :url="$url">
Retry payment
</x-mail::button>

<div class="problems">
If you are having trouble clicking the button above, copy and paste the following URL into your web browser:
<div class="break-all">
{{ $url }}
</div>
</div>
</x-mail::message>
