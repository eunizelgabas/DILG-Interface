<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DILG Issuances') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased flex h-screen bg-gray-100">

    <!-- Sidebar -->
    @include('layouts.navigation')

    <div class="flex-1 flex flex-col">

        @include('layouts.nav')
        <!-- Header -->
        @if (isset($header))
        <header class="bg-white shadow" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Main content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        {{-- <footer class="bg-white footer footer-center text-center p-4 bg-base-300 text-base-content">
            <aside>
                <p class="text-center"> © 2024 - All rights reserved | MDC Developers</p>
            </aside>
        </footer> --}}
    </div>


</body>
</html>
