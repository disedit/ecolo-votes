<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ config('app.name') }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
  <p><strong>European Green Party</strong></p>
  <p>Rue du Taciturne 34 B-1000 Brussels, Belgium</p>
  <p><a href="tel:003226260720">+32 (0) 2 626 07 20</a> <a href="mailto:info@europeangreens.eu">info@europeangreens.eu</a></p>
  <p style="margin-top: 6px;">You are receiving this email because you signed up to attend one of our events.</p>

  <p style="margin-top: 18px; text-align: left"><img src="{{ asset('images/logos/ep-gray.svg') }}" alt="European Parliament"></p>
  <p style="margin-top: 6px;"">
    With the financial support of the European Parliament.
    Sole liability rests with the author. The European Parliament is not responsible
    for any use that may be made of the information contained therein.
  </p>
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
