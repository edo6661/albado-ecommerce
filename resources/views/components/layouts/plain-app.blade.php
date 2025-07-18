<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-layouts.head />

    <body>
        <x-shared.session-message/>
        <x-shared.header/>
        <main class="min-h-screen">
            {{ $slot }}
        </main>
        <x-shared.footer/>
    </body>
</html>
