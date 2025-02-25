<x-mail::message>
Dear {{ $user->first_name }},
 
This is an <strong>urgent reminder</strong>: We have reviewed and confirmed your registration for the upcoming **39th EGP Congress**.
However, we haven't received your payment yet and <strong><u>this must be completed before we can issue your ticket</u></strong>.

Click the button below to complete your registration now:
 
<x-mail::button :url="$url">
Complete payment
</x-mail::button>

<div class="problems">
  If the button above doesn't work, copy and paste the following URL into your web browser:
  <div class="break-all">
  {{ $url }}
  </div>
</div>
 
If you have any questions, you can reply to this email or contact us at <a href="mailto:congress@europeangreens.eu">congress@europeangreens.eu</a>
 
Don’t miss out — Complete your payment today and join us in Dublin! 🇮🇪
 
Kind regards,
The European Greens Team
</x-mail::message>
