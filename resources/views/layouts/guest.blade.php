<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#111412">
        <title>{{ config('app.name', 'CANCHA') }} · Baloncesto</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <main class="auth-screen">
            <section class="auth-photo" style="--auth-image: url('{{ asset('images/basketball-arena.jpg') }}')">
                <a class="auth-brand" href="{{ url('/') }}">CANCHA<span>.</span></a>
                <div class="auth-photo__caption">
                    <p class="eyebrow"><span class="eyebrow__dot"></span> Más que un marcador</p>
                    <h1>La próxima<br>jugada es tuya.</h1>
                    <p>Franquicias, partidos y toda una liga por escribir.</p>
                </div>
                <span class="auth-photo__credit">Fotografía: Unsplash</span>
            </section>
            <section class="auth-panel">
                <div class="auth-panel__inner">
                    <a class="auth-panel__brand" href="{{ url('/') }}">CANCHA<span>.</span></a>
                    {{ $slot }}
                </div>
                <p class="auth-panel__foot">Hecho para quienes viven el juego.</p>
            </section>
        </main>
    </body>
</html>
