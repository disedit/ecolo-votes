<x-mail::message>
Hi {{ $user->first_name }},
 
We have reviewed your registration for the upcoming <strong>39th European Greens Congress</strong> and have issued your ticket.
Please click the button below to access it and show it at the entrance to check in.
 
<x-mail::button :url="$url">
View ticket
</x-mail::button>

<div class="problems">
If you are having trouble clicking the button above, copy and paste the following URL into your web browser:
<div class="break-all">
{{ $url }}
</div>
</div>

<p>See you in Dublin! 🇮🇪</p>
</x-mail::message>
