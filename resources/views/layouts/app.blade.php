<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
            <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo_sympony_icon.png') }}">
        {{-- BARU: TRIX EDITOR ASSETS --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{-- OPTIONAL: Style kustom untuk Trix agar lebih sesuai Tailwind --}}
    <style>
        .trix-content {
            min-height: 150px;
            padding: 0.5rem;
            border: 1px solid var(--tw-border-gray-300, #d1d5db); /* Border standard Tailwind */
            border-radius: 0.375rem; /* rounded-md standard Tailwind */
            background-color: var(--tw-bg-white, #ffffff);
            color: var(--tw-text-gray-900, #1f2937);
        }
        /* Mengganti font untuk Trix */
        .trix-button-group {
            font-family: inherit !important;
        }
    </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
