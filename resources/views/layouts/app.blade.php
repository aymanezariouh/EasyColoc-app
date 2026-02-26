<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>body { font-family: 'Inter', sans-serif; }</style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen relative">
            {{-- Ambient background --}}
            <div class="fixed inset-0 -z-10">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-gray-50 to-indigo-50/20"></div>
                <div class="absolute inset-0 bg-grid-pattern opacity-30"></div>
                {{-- Soft decorative blobs --}}
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-indigo-100/20 to-violet-100/10 rounded-full blur-3xl translate-x-1/3 -translate-y-1/4 animate-pulse-soft"></div>
                <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-gradient-to-tr from-sky-100/15 to-indigo-100/10 rounded-full blur-3xl -translate-x-1/4 translate-y-1/3 animate-pulse-soft" style="animation-delay: 2s;"></div>
            </div>

            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="relative">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 animate-slide-up">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Session Messages -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200/60 bg-emerald-50/80 backdrop-blur-sm px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm animate-slide-up">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500 flex-shrink-0">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </div>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-5 flex items-center gap-3 rounded-2xl border border-red-200/60 bg-red-50/80 backdrop-blur-sm px-5 py-4 text-sm font-medium text-red-800 shadow-sm animate-slide-up">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-red-500 flex-shrink-0">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="max-w-7xl mx-auto pb-12 px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
