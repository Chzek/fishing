<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Fishing Logbook') }}</title>

    <!-- Vite Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans antialiased flex flex-col justify-between">
    
    <!-- Hero Header Navigation -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center">
                    <i data-lucide="anchor" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="font-bold text-white tracking-wide text-lg block leading-tight">Fishing Logbook</span>
                    <span class="text-xs text-teal-400 font-medium tracking-wider uppercase">Telemetry & Field Logger</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/profile') }}" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold py-2 px-4 rounded-xl shadow transition-colors">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Angler Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-300 hover:text-white px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-1 bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold py-2 px-4 rounded-xl shadow transition-colors">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Main Hero Banner -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col items-center text-center space-y-12">
        <div class="max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 bg-teal-500/10 border border-teal-500/30 text-teal-300 px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider">
                <i data-lucide="zap" class="w-3.5 h-3.5 text-teal-400"></i>
                <span>Designed for Anglers & Boat Field Use</span>
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-tight">
                Log Catches & Explore Waters with <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">Precision Telemetry</span>
            </h1>

            <p class="text-base sm:text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Seamlessly record species, length, weight, lures, and GPS coordinates offline on the water. Sync automatically when back at the cabin.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                @auth
                    <a href="{{ url('/record/quick') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-sm py-3 px-6 rounded-xl shadow-lg shadow-teal-950/50 transition-all transform active:scale-95">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                        <span>Launch Quick Catch</span>
                    </a>
                    <a href="{{ url('/map/explorer') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-sm py-3 px-6 rounded-xl transition-all">
                        <i data-lucide="compass" class="w-4 h-4 text-teal-400"></i>
                        <span>Open Map Explorer</span>
                    </a>
                @else
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-sm py-3 px-6 rounded-xl shadow-lg shadow-teal-950/50 transition-all transform active:scale-95">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            <span>Create Free Account</span>
                        </a>
                    @endif
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-sm py-3 px-6 rounded-xl transition-all">
                        <span>Sign In</span>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Splash Image Feature Preview -->
        <div class="w-full max-w-4xl rounded-2xl overflow-hidden shadow-2xl border border-slate-800 relative bg-slate-900 group">
            <img src="{{ asset('/images/splash.jpg') }}" alt="Fishing Splash Banner" class="w-full h-64 sm:h-96 object-cover opacity-80 group-hover:opacity-90 transition-opacity">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
        </div>

        <!-- Option C Feature Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-5xl text-left">
            <a href="{{ url('/angler') }}" class="bg-slate-900/80 hover:bg-slate-900 border border-slate-800 hover:border-teal-500/40 p-6 rounded-2xl transition-all space-y-3 group">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center border border-teal-500/20 group-hover:scale-110 transition-transform">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-white group-hover:text-teal-300 transition-colors">Angler Roster</h2>
                <p class="text-xs text-slate-400 leading-relaxed">View active crew members, profile bios, and personal best statistics.</p>
            </a>

            <a href="{{ url('/map/explorer') }}" class="bg-slate-900/80 hover:bg-slate-900 border border-slate-800 hover:border-teal-500/40 p-6 rounded-2xl transition-all space-y-3 group">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center border border-teal-500/20 group-hover:scale-110 transition-transform">
                    <i data-lucide="compass" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-white group-hover:text-teal-300 transition-colors">Map Explorer</h2>
                <p class="text-xs text-slate-400 leading-relaxed">Interactive satellite & topographic maps with visible bounding box lake filters.</p>
            </a>

            <a href="{{ url('/record') }}" class="bg-slate-900/80 hover:bg-slate-900 border border-slate-800 hover:border-teal-500/40 p-6 rounded-2xl transition-all space-y-3 group">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center border border-teal-500/20 group-hover:scale-110 transition-transform">
                    <i data-lucide="fish" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-white group-hover:text-teal-300 transition-colors">Logbook Catches</h2>
                <p class="text-xs text-slate-400 leading-relaxed">Comprehensive catch log with length, weight, species, and release status.</p>
            </a>

            <a href="{{ url('/expedition') }}" class="bg-slate-900/80 hover:bg-slate-900 border border-slate-800 hover:border-teal-500/40 p-6 rounded-2xl transition-all space-y-3 group">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center border border-teal-500/20 group-hover:scale-110 transition-transform">
                    <i data-lucide="ship" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-white group-hover:text-teal-300 transition-colors">Expeditions</h2>
                <p class="text-xs text-slate-400 leading-relaxed">Multi-day trip journals, crew rosters, and fishing expedition timelines.</p>
            </a>

            <a href="{{ url('/lake') }}" class="bg-slate-900/80 hover:bg-slate-900 border border-slate-800 hover:border-teal-500/40 p-6 rounded-2xl transition-all space-y-3 group">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center border border-teal-500/20 group-hover:scale-110 transition-transform">
                    <i data-lucide="waves" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-white group-hover:text-teal-300 transition-colors">Lakes & Waters</h2>
                <p class="text-xs text-slate-400 leading-relaxed">Database of fishing lakes with max depth, bottom structure, and visit logs.</p>
            </a>

            <a href="{{ url('/lure') }}" class="bg-slate-900/80 hover:bg-slate-900 border border-slate-800 hover:border-teal-500/40 p-6 rounded-2xl transition-all space-y-3 group">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center border border-teal-500/20 group-hover:scale-110 transition-transform">
                    <i data-lucide="fishing-hook" class="w-5 h-5"></i>
                </div>
                <h2 class="text-base font-bold text-white group-hover:text-teal-300 transition-colors">Lures & Bait</h2>
                <p class="text-xs text-slate-400 leading-relaxed">Tackle box inventory tracking top producing lures and catch success rates.</p>
            </a>
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Fishing Logbook') }}. Built for precision field angling.</p>
    </footer>
</body>
</html>
