<x-mail::message>
Hi {{ $user->first_name }},

For your convenience, we are sending you again your link to access the <strong>Voting Tool</strong>.
Please, open the link below to cast your votes as a delegate.

When a vote opens, the voting tool will display the available options.
To confirm your vote, you will need to enter a code displayed on the big screen.

<x-mail::button :url="$url">
  Cast your votes ({{ $user->first_name }})
</x-mail::button>

<div class="problems">
  If you are having trouble clicking the button above, copy and paste the following URL into your web browser:
  <div class="break-all">
  {{ $url }}
  </div>
</div>

<div class="highlight">
⚠️ <strong>The link above will log you in as <em>{{ $user->first_name }} {{ $user->last_name }}</em> on the Voting Tool. Please do not forward this email or share the link with anyone else.</strong>
</div>

</x-mail::message>
