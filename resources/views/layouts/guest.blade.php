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
    <body class="font-sans text-gray-900 antialiased">
        {{-- Immersive background with animated blobs --}}
        <div class="min-h-screen relative overflow-hidden">
            {{-- Animated background layer --}}
            <div class="fixed inset-0 -z-10">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-indigo-50/40 to-violet-50/30"></div>
                <div class="absolute inset-0 bg-dot-pattern opacity-40"></div>
                {{-- Floating blobs for organic feel --}}
                <div class="absolute top-[-10%] left-[-5%] w-[500px] h-[500px] bg-gradient-to-br from-indigo-200/30 to-violet-200/20 rounded-full blur-3xl animate-blob"></div>
                <div class="absolute bottom-[-15%] right-[-10%] w-[600px] h-[600px] bg-gradient-to-br from-violet-200/25 to-pink-200/15 rounded-full blur-3xl animate-blob-delayed"></div>
                <div class="absolute top-[40%] right-[20%] w-[300px] h-[300px] bg-gradient-to-br from-sky-200/20 to-indigo-200/15 rounded-full blur-3xl animate-pulse-soft"></div>
            </div>

            {{-- Content --}}
            <div class="relative flex flex-col sm:justify-center items-center min-h-screen pt-8 sm:pt-0 px-4">
                {{-- Logo --}}
                <div class="mb-6 animate-slide-up">
                    <a href="/" class="flex flex-col items-center gap-3 group">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-2xl blur-xl opacity-40 group-hover:opacity-60 transition-opacity duration-500"></div>
                            <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-600 shadow-elevated-lg animate-gradient">
                                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-gradient">{{ config('app.name', 'EasyColoc') }}</span>
                    </a>
                </div>

                {{-- Auth Card --}}
                <div class="w-full sm:max-w-md animate-slide-up" style="animation-delay: 0.1s">
                    <div class="glass-strong rounded-3xl shadow-elevated-lg px-8 py-8 sm:px-10 sm:py-10">
                        {{ $slot }}
                    </div>
                </div>

                {{-- Footer --}}
                <p class="mt-10 text-xs text-gray-400 animate-fade-in" style="animation-delay: 0.3s">
                    &copy; {{ date('Y') }} {{ config('app.name', 'EasyColoc') }} &middot; Built with care
                </p>
            </div>
        </div>
    </body>
</html>
