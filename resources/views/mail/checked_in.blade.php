<x-mail::message>
Hi {{ $user->first_name }},
 
You have checked in at the <strong>39th European Greens Congress</strong>.
Please, click the button below to access the voting platform and cast your votes as delegate.
This is your personal link to log in. **Do not forward it to anybody.**

<x-mail::button :url="$url">
  Voting area
</x-mail::button>

<div class="loggedinas">
You will be logged in as <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
</div>

<div class="problems">
If you are having trouble clicking the button above, copy and paste the following URL into your web browser:
<div class="break-all">
{{ $url }}
</div>
</div>

</x-mail::message>
