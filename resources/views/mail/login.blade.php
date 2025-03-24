<x-mail::message>
Hi {{ $user->first_name }},
 
Please, use the button below to log into the **Ecolo Voting Platform**.
 
<x-mail::button :url="$url">
Log in as {{ $user->first_name }}
</x-mail::button>

<div class="problems">
  If you are having trouble clicking the button above, copy and paste the following URL into your web browser::
  <div class="break-all">
  {{ $url }}
  </div>
</div>

</x-mail::message>
