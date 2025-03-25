<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="Ecolo" />
        <link rel="manifest" href="/site.webmanifest" />
        <meta name="theme-color" content="#ffffff">

        <title inertia>{{ config('app.name', 'EGP Congress') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://use.typekit.net/nus5lqe.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
        <script src="/qr-scanner/qr-scanner.umd.min.js"></script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        <div id="teleports"></div>
    </body>
</html>
