<x-mail::message>
Hi {{ $user->first_name }},
 
We have reviewed your registration for the upcoming **39th European Greens Congress** and have issued your ticket. Please, click the button below to complete the payment and confirm the ticket. 
 
<x-mail::button :url="$url">
Complete payment
</x-mail::button>

<div class="problems">
  If you are having trouble clicking the button above, copy and paste the following URL into your web browser:
<div class="break-all">
{{ $url }}
</div>
</div>

<p>If you have any questions, you can reply to this email or contact us at <a href="mailto:congress@europeangreens.eu">congress@europeangreens.eu</a></p>

<p>See you in Dublin! 🇮🇪</p>
</x-mail::message>
