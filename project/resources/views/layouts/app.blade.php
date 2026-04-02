<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Catering Fiesta')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Vite / Tailwind -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="antialiased min-h-screen flex flex-col">
        <!-- Navbar Placeholder -->
        <livewire:navbar />

        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer Placeholder -->
        <livewire:footer />

        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Alpine JS is bundled by Vite if configured, or loaded here -->
    </body>
</html>
