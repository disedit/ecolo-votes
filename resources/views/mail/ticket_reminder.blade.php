<x-mail::message>
Hi {{ $user->first_name }},
 
For your convenience, we are sending you again your ticket for the <strong>39th European Greens Congress</strong>, which begins today at <strong>The Convention Centre Dublin</strong>.
Please, show it at the registration desk when you arrive to check in.
 
<x-mail::button :url="$url">
  View ticket ({{ $user->first_name }})
</x-mail::button>

<div class="problems">
  If you are having trouble clicking the button above, copy and paste the following URL into your web browser:
  <div class="break-all">
  {{ $url }}
  </div>
</div>

<div class="highlight">
⚠️ <strong>The button above links to your personal ticket. Please do not forward this email or share the link with anyone else.</strong>
</div>

@if ($user->attendee()->votes > 0)
As a delegate, this ticket will also grant you access to the <strong><a href="{{ $url }}">Voting Platform</a></strong> once
you have checked in at the registration desk.
@endif

<p>See you in Dublin! 🇮🇪</p>
</x-mail::message>
