<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Fishing Logbook') }}</title>

    <!-- Vite Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans antialiased flex flex-col justify-between selection:bg-teal-500 selection:text-white">
    
    <!-- Header Navigation -->
    <header class="border-b border-slate-800/80 bg-slate-900/40 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shadow-sm">
                    <x-lucide-anchor class="w-4 h-4" />
                </div>
                <span class="font-extrabold text-white tracking-tight text-lg">Fishing Logbook</span>
            </div>

            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/profile') }}" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold py-2 px-4 rounded-xl shadow transition-colors">
                            <x-lucide-layout-dashboard class="w-4 h-4" />
                            <span>Angler Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-300 hover:text-white px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors">Sign In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-1 bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold py-2 px-4 rounded-xl shadow transition-colors">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Minimalist Main Content -->
    <main class="flex-1 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col items-center justify-center text-center space-y-8 my-auto">
        
        <div class="max-w-2xl space-y-3">
            <h1 class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                Fishing Logbook
            </h1>
            <p class="text-sm sm:text-base text-slate-400 max-w-lg mx-auto leading-relaxed">
                Precision telemetry & catch logging for boat and shore field use.
            </p>
        </div>


        <!-- Featured Lake Image Card -->
        <div class="w-full max-w-3xl rounded-2xl overflow-hidden shadow-2xl border border-slate-800 relative bg-slate-900">
            <img src="{{ asset('/images/splash.jpg') }}" alt="Lake Landscape" class="w-full h-64 sm:h-80 object-cover opacity-85">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
        </div>
    </main>

    <!-- Simple Minimalist Footer -->
    <footer class="border-t border-slate-800/80 py-5 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Fishing Logbook') }}</p>
    </footer>
</body>
</html>
