<x-mail::message>
Hi {{ $user->first_name }},
 
This is your ticket for the upcoming <strong>39th European Greens Congress</strong>. Please, show it at the entrance to check in.
 
<x-mail::button :url="$url">
View ticket
</x-mail::button>

<div class="problems">
If you are having trouble clicking the button above, copy and paste the following URL into your web browser:
<div class="break-all">
{{ $url }}
</div>
</div>

<p>We have also attached the invoice for your convenience.</p>

<p>See you in Dublin! 🇮🇪</p>
</x-mail::message>
