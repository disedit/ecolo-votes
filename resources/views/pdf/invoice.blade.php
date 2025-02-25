@php
  $fmt = new NumberFormatter('en_IE', NumberFormatter::CURRENCY);
@endphp
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<div class="page">
  <table class="invoice-header">
    <tr>
      <td class="invoice-logo" valign="top">
        <img src="{{ public_path('images/logos/egp-logo.svg') }}" />
      </td>
      <td class="invoice-issuer">
        <div>
          <h2>European Green Party (PPEU)</h2>
          <p>Rue du Taciturne 34<br>1000 Brussels - Belgium</p>
          <p>VAT: BE 0872 183 517</p>
        </div>
      </td>
    </tr>
  </table>
  <table class="invoice-heading">
    <tr>
      <td class="invoice-number">
        <h1>INVOICE<span>{{ ($payment->status === 'refunded') ? '#REGPE-' . $payment->id : '#EGPE-' . $payment->id  }}</span></h1>
      </td>
      <td class="invoice-status {{ $payment->status }}">
        {{ $payment->status }}
      </td>
    </tr>
  </table>
  <table class="invoice-client" cellpadding="0" cellspacing="0">
    <tr>
      <th width="100">DATE</th>
      <td>{{ date('j F Y', strtotime($payment->created_at)) }}</td>
    </tr>
    <tr>
      <th>INVOICED TO</th>
      <td>
        <h2>{{ $payment->receipt->invoice->name }}</h2>
        @if($payment->receipt->invoice->address)
        <p>{{ $payment->receipt->invoice->address }}</p>
        @endif
        @if($payment->receipt->invoice->vat)
        <p>VAT: {{ $payment->receipt->invoice->vat }}</p>
        @endif
      </td>
    </tr>
  </table>
  <table class="invoice-items items-{{ count($payment->receipt->cart) }}" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th>ITEM</th>
        <th width="80" class="price-col">PRICE</th>
      </tr>
    </thead>
    <tbody>
      @foreach($payment->receipt->cart as $item)
      <tr>
        <td>
          <h3>{{ $edition->title }}, {{ $edition->location }}, {{ $edition->dates }}</h3>
          <p>{{ $item->name }}</p>
          <p>{{ $item->description }}</p>
        </td>
        <td class="price-col">
          {{ ($payment->status === 'refunded') ? $fmt->formatCurrency($item->amount * -1, "EUR") : $fmt->formatCurrency($item->amount, "EUR") }}
        </td>
      </tr>
      @endforeach
      <tr>
          <td colspan="2" class="spacer"></td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td>Subtotal</td>
        <td class="price-col">
          {{ ($payment->status === 'refunded') ? $fmt->formatCurrency($payment->amount * -1, "EUR") : $fmt->formatCurrency($payment->amount, "EUR") }}
        </td>
      </tr>
      <tr>
        <td>
          VAT - 0%
          <p class="disclaimer">Exemption art. 44 alinea 2.11°C.T.V.A/ art . 132 alinea 1ier 1),133 et 134 Directive 2006/113/CE</p>
        </td>
        <td class="price-col">€0.00</td>
      </tr>
      <tr class="invoice-total">
        <td>TOTAL</td>
        <td class="price-col">
          {{ ($payment->status === 'refunded') ? $fmt->formatCurrency($payment->amount * -1, "EUR") : $fmt->formatCurrency($payment->amount, "EUR") }}
        </td>
      <tr>
    <tfoot>
  </table>
  <div class="footer">
    <p><strong>European Green Party</strong> (PPEU) - Rue du Taciturne 34 - 1000 Brussels - Belgium</p>
    <p>+32 2 626 07 20 | www.europeangreens.eu | info@europeangreens.eu | VAT : BE 0872 183 517</p>
  </div>
</div>

<style>
  @font-face {
    font-family: 'Space Mono';
    src: url('{{ public_path('fonts/SpaceMono-Regular.ttf') }}') format('truetype');
    font-weight: normal;
    font-style: normal;
  }
  
  @font-face {
    font-family: 'Space Mono';
    src: url('{{ public_path('fonts/SpaceMono-Bold.ttf') }}') format('truetype');
    font-weight: bold;
    font-style: normal;
  }
  
  @font-face {
    font-family: 'Inter';
    src: url('{{ public_path('fonts/Inter_18pt-Regular.ttf') }}') format('truetype');
    font-weight: normal;
    font-style: normal;
  }
  
  @font-face {
    font-family: 'Inter';
    src: url('{{ public_path('fonts/Inter_18pt-Bold.ttf') }}') format('truetype');
    font-weight: bold;
    font-style: normal;
  }

  @font-face {
    font-family: 'Doumbar';
    src: url('{{ public_path('fonts/Doumbar-Regular.ttf') }}') format('truetype');
    font-weight: normal;
    font-style: normal;
  }

  @font-face {
    font-family: 'Doumbar';
    src: url('{{ public_path('fonts/Doumbar-SemiBold.ttf') }}') format('truetype');
    font-weight: bold;
    font-style: normal;
  }
  
  @page { margin: 10mm; }

  :root {
    --egp-white: #FFFFFF;
    --egp-gray-50: #fafafa;
    --egp-gray-100: #f4f2f5;
    --egp-gray-200: #e9e6ea;
    --egp-gray-300: #dbd6dc;
    --egp-gray-400: #c3bbc5;
    --egp-gray-500: #aa9fad;
    --egp-gray-600: #938796;
    --egp-gray-700: #7d717f;
    --egp-gray-800: #685f6a;
    --egp-gray-900: #544d56;
    --egp-gray-950: #38313a;
    --egp-green: #47B972;
    --egp-green-dark: #0F8A54;
    --egp-green-pine: #0B3323;
    --egp-yellow: #FFDC2E;
    --egp-pink: #F6ADCD;
    --egp-purple: #6950A1;
    --egp-black: #000;
    --egp-red: #fc574a;
    --egp-blue: #00a5ec;
    --font-base: 'Inter', sans-serif;
    --font-mono: 'Space Mono', sans-serif;
    --font-brand: 'Doumbar', sans-serif;
  }
  
  .page {
    position: relative;
    font-family: var(--font-base);
    font-size: 15px;
  }

  .page table {
    width: 100%;
  }

  h1, h2, h3, h4, h5, p {
    margin: 0;
    font-size: 1em;
  }

  .invoice-header {
    width: 100%;
  }

  .invoice-logo img {
    margin-top: -11mm;
    height: 100px;
  }

  .invoice-issuer div {
    margin-top: -4mm;
    text-align: right;
  }

  .invoice-issuer h2 {
    margin: 0;
    font-size: 15px;
    color: var(--egp-green-pine);
  }

  .invoice-issuer p {
    margin: 0;
    line-height: 1;
    color: var(--egp-gray-800);
  }

  .invoice-status {
    text-align: right;
    text-transform: uppercase;
  }

  .invoice-status.paid {
    color: var(--egp-green-dark);
  }

  .invoice-status.pending {
    color: var(--egp-purple);
  }

  .invoice-status.declined {
    color: var(--egp-red);
  }
  
  .invoice-status.refunded {
    color: var(--egp-blue);
  }

  .invoice-heading {
    font-family: var(--font-brand);
    font-size: 50px;
  }

  .invoice-heading h1 {
    margin: 0;
  }

  .invoice-heading h1 span {
    font-size: .6em;
    position: relative;
    top: -.6em;
    left: .2em;
  }

  .invoice-client {
    margin-bottom: 24px;
  }

  .invoice-client th {
    font-family: var(--font-mono);
    font-weight: normal;
    color: var(--egp-gray-700);
    text-align: left;
    vertical-align: top;
  }

  .invoice-client td,
  .invoice-client th {
    padding: 5px 0;
  }

  .invoice-client p {
    line-height: 1;
    color: var(--egp-gray-800);
  }

  .invoice-client tr:first-child th,
  .invoice-client tr:first-child td {
    border-bottom: 1px var(--egp-gray-300) solid;
  }

  .invoice-items .price-col {
    text-align: right;
    vertical-align: top;
  }

  .invoice-items th,
  .invoice-items td {
    padding: 8px 10px 10px 10px;
  }

  .invoice-items thead th {
    background-color: var(--egp-gray-200);
    font-family: var(--font-mono);
    text-align: left;
    font-weight: normal;
    color: var(--epg-gray-pine);
    line-height: 1;
  }

  .invoice-items tbody td,
  .invoice-items tfoot td {
    border-bottom: 1px var(--egp-gray-200) solid;
    vertical-align: top;
  }

  .invoice-items tbody p {
    color: var(--egp-gray-800);
  }

  .invoice-items .disclaimer {
    font-size: 13px;
    color: var(--egp-gray-800);
  }

  .invoice-items.items-1 .spacer {
    height: 310px;
  }

  .invoice-items.items-2 .spacer {
    height: 220px;
  }

  .invoice-items.items-3 .spacer {
    height: 120px;
  }

  .invoice-items.items-4 .spacer {
    height: 30px;
  }

  .invoice-total td {
    background-color: var(--egp-pink);
    color: var(--egp-green-pine);
    font-weight: bold;
    font-size: 24px;
    font-family: var(--font-mono);
    padding-top: 0;
  }
  
  .footer {
    font-size: 13px;
    text-align: center;
    color: var(--egp-gray-800);
    margin-top: 50px;
  }
  </style>