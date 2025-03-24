<x-mail::message>
{!! $message !!}
 
<x-mail::button :url="$url">
{{ __('emails.badge', ['name' => $user->first_name]) }}
</x-mail::button>

<div class="problems">
  {{ __('emails.open_url') }}

  <div class="break-all">
  {{ $url }}
  </div>
</div>

</x-mail::message>