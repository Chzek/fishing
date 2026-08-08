<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Fishing Logbook') }}</title>

    <!-- Vite Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/offline-sync.js') }}" defer></script>

    <!-- Leaflet CSS & JS for Offline Maps -->
    <link rel="stylesheet" href="{{ asset('css/leaflet.css') }}">
    <script src="{{ asset('js/leaflet.js') }}"></script>
    <script>
        if (typeof L !== 'undefined') {
            L.Icon.Default.imagePath = '/css/images/';
        }
    </script>
</head>
<body class="h-full bg-slate-100 font-sans antialiased text-slate-900" x-data="{ mobileMenuOpen: false }">
    <div id="app" class="flex min-h-screen flex-col lg:flex-row bg-slate-100">
        
        <!-- Desktop Sidebar (Option C Design) -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-slate-900 text-slate-300 min-h-screen border-r border-slate-800 shrink-0">
            <!-- Brand Header -->
            <div class="p-5 flex items-center justify-between border-b border-slate-800">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shadow-inner group-hover:bg-teal-500/20 transition-all duration-200">
                        <i data-lucide="anchor" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="font-bold text-white tracking-wide text-base block leading-tight">Fishing Log</span>
                        <span class="text-xs text-teal-400 font-medium tracking-wider uppercase">Telemetry v2</span>
                    </div>
                </a>
            </div>

            <!-- Global Omnibox Search Input -->
            @auth
            <div class="px-4 pt-4 pb-1">
                <form action="{{ route('search') }}" method="GET" class="relative flex items-center">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none shrink-0"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search & commands..."
                        class="w-full h-9 pl-10 pr-14 text-xs rounded-xl border border-slate-800 bg-slate-950/80 text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                    <kbd class="absolute right-2.5 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-slate-500 bg-slate-900 border border-slate-800 rounded pointer-events-none">Ctrl K</kbd>
                </form>
            </div>

            <!-- Quick Catch Primary Action Button -->
            <div class="px-4 pt-2 pb-2">
                <a href="{{ url('/record/quick') }}" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-teal-900/30 hover:shadow-teal-800/40 transition-all duration-200 group">
                    <i data-lucide="zap" class="w-4 h-4 text-teal-200 group-hover:scale-110 transition-transform"></i>
                    <span>Quick Catch</span>
                </a>
            </div>
            @endauth

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @auth
                    <div class="px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Field Tools</div>
                    <a href="{{ url('/profile') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors {{ Request::is('profile*') ? 'bg-teal-500/15 text-teal-300 font-semibold border-l-2 border-teal-400' : 'hover:bg-slate-800/60 text-slate-300 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard & Stats</span>
                    </a>
                    <a href="{{ url('/map/explorer') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors {{ Request::is('map/explorer*') ? 'bg-teal-500/15 text-teal-300 font-semibold border-l-2 border-teal-400' : 'hover:bg-slate-800/60 text-slate-300 hover:text-white' }}">
                        <i data-lucide="compass" class="w-4 h-4"></i>
                        <span>Map Explorer</span>
                    </a>
                    <a href="{{ url('/map/offline') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors {{ Request::is('map/offline*') ? 'bg-teal-500/15 text-teal-300 font-semibold border-l-2 border-teal-400' : 'hover:bg-slate-800/60 text-slate-300 hover:text-white' }}">
                        <i data-lucide="map" class="w-4 h-4"></i>
                        <span>Offline Maps</span>
                    </a>

                    <div class="px-3 pt-4 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Logbook Records</div>
                    <a href="{{ url('/expedition') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors {{ Request::is('expedition*') ? 'bg-teal-500/15 text-teal-300 font-semibold border-l-2 border-teal-400' : 'hover:bg-slate-800/60 text-slate-300 hover:text-white' }}">
                        <i data-lucide="ship" class="w-4 h-4"></i>
                        <span>Expeditions</span>
                    </a>
                    <a href="{{ url('/record') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors {{ Request::is('record') ? 'bg-teal-500/15 text-teal-300 font-semibold border-l-2 border-teal-400' : 'hover:bg-slate-800/60 text-slate-300 hover:text-white' }}">
                        <i data-lucide="fish" class="w-4 h-4"></i>
                        <span>Catches Log</span>
                    </a>
                    <a href="{{ url('/lake') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors {{ Request::is('lake*') ? 'bg-teal-500/15 text-teal-300 font-semibold border-l-2 border-teal-400' : 'hover:bg-slate-800/60 text-slate-300 hover:text-white' }}">
                        <i data-lucide="waves" class="w-4 h-4"></i>
                        <span>Lakes & Waters</span>
                    </a>
                    <a href="{{ url('/lure') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors {{ Request::is('lure*') ? 'bg-teal-500/15 text-teal-300 font-semibold border-l-2 border-teal-400' : 'hover:bg-slate-800/60 text-slate-300 hover:text-white' }}">
                        <i data-lucide="fishing-hook" class="w-4 h-4"></i>
                        <span>Lures & Gear</span>
                    </a>
                    <a href="{{ url('/angler') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors {{ Request::is('angler*') ? 'bg-teal-500/15 text-teal-300 font-semibold border-l-2 border-teal-400' : 'hover:bg-slate-800/60 text-slate-300 hover:text-white' }}">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Anglers</span>
                    </a>
                @endauth
            </nav>

            <!-- Sidebar User Profile Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                <!-- Offline Sync Button -->
                <button id="offline-sync-badge" onclick="window.offlineSyncManager.syncNow()" class="w-full mb-3 hidden items-center justify-center gap-2 bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-semibold py-2 px-3 rounded-lg hover:bg-amber-500/30 transition-all">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 animate-spin"></i>
                    <span><span id="offline-sync-count">0</span> Catches Queued (Sync Now)</span>
                </button>

                @auth
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 overflow-hidden flex items-center justify-center text-slate-300">
                                <i data-lucide="user" class="w-5 h-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <span class="font-medium text-white text-sm block truncate">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-slate-400 capitalize block">{{ Auth::user()->type ?? 'Angler' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1">
                            @if(Auth()->user()->type === "admin")
                                <a href="/admin" title="Admin Portal" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                                    <i data-lucide="shield-alert" class="w-4 h-4 text-amber-400"></i>
                                </a>
                            @endif
                            <a href="{{ route('logout') }}" title="Logout"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-800 transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('login') }}" class="w-full text-center py-2 px-3 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-medium text-xs transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="w-full text-center py-2 px-3 rounded-lg border border-slate-700 text-slate-300 hover:text-white text-xs transition-colors">Register</a>
                    </div>
                @endauth
            </div>
        </aside>

        <!-- Mobile Top Navigation Header -->
        <header class="lg:hidden bg-slate-900 text-white border-b border-slate-800 px-4 py-3 flex items-center justify-between sticky top-0 z-40">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center">
                    <i data-lucide="anchor" class="w-4 h-4"></i>
                </div>
                <span class="font-bold text-white tracking-wide text-sm">Fishing Log</span>
            </a>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('search') }}" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition-colors" title="Search">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </a>
                    <a href="{{ url('/record/quick') }}" class="flex items-center gap-1 bg-teal-600 hover:bg-teal-500 text-white text-xs font-semibold py-1.5 px-3 rounded-lg shadow-sm">
                        <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                        <span>Quick Catch</span>
                    </a>
                @endauth

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none">
                    <i data-lucide="menu" class="w-5 h-5" x-show="!mobileMenuOpen"></i>
                    <i data-lucide="x" class="w-5 h-5" x-show="mobileMenuOpen" style="display: none;"></i>
                </button>
            </div>
        </header>

        <!-- Mobile Slide-over Drawer -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden bg-slate-900 border-b border-slate-800 text-slate-300 p-4 space-y-3 z-30" style="display: none;">
            @auth
                <a href="{{ url('/profile') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-teal-400"></i> Dashboard
                </a>
                <a href="{{ url('/map/explorer') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">
                    <i data-lucide="compass" class="w-4 h-4 text-teal-400"></i> Map Explorer
                </a>
                <a href="{{ url('/map/offline') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">
                    <i data-lucide="map" class="w-4 h-4 text-teal-400"></i> Offline Maps
                </a>
                <a href="{{ url('/record') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">
                    <i data-lucide="fish" class="w-4 h-4 text-teal-400"></i> Catches Log
                </a>
                <a href="{{ url('/expedition') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">
                    <i data-lucide="ship" class="w-4 h-4 text-teal-400"></i> Expeditions
                </a>
                <a href="{{ url('/lake') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">
                    <i data-lucide="waves" class="w-4 h-4 text-teal-400"></i> Lakes
                </a>
                <a href="{{ url('/lure') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">
                    <i data-lucide="fishing-hook" class="w-4 h-4 text-teal-400"></i> Lures
                </a>
                <div class="pt-2 border-t border-slate-800 flex justify-between items-center text-xs text-slate-400">
                    <span>Logged in as <strong>{{ Auth::user()->name }}</strong></span>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-red-400 hover:underline">Logout</a>
                </div>
            @else
                <a href="{{ route('login') }}" class="block text-center py-2 bg-teal-600 text-white rounded-lg font-medium text-xs">Login</a>
                <a href="{{ route('register') }}" class="block text-center py-2 border border-slate-700 text-slate-300 rounded-lg font-medium text-xs">Register</a>
            @endauth
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0 pb-20 lg:pb-8">
            <!-- Offline Sync Global Alert Banner -->
            <div id="offline-sync-alert" class="hidden bg-emerald-600 text-white text-xs font-semibold px-4 py-2 text-center transition-all shadow-sm" role="alert"></div>

            <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

        <!-- Mobile Bottom Floating Navigation Bar -->
        @auth
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-slate-900/95 backdrop-blur-md border-t border-slate-800 text-slate-400 flex items-center justify-around py-2 px-3 z-40 shadow-2xl">
            <a href="{{ url('/profile') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ Request::is('profile*') ? 'text-teal-400 font-semibold' : 'hover:text-slate-200' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Stats</span>
            </a>
            <a href="{{ url('/map/explorer') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ Request::is('map/explorer*') ? 'text-teal-400 font-semibold' : 'hover:text-slate-200' }}">
                <i data-lucide="compass" class="w-5 h-5"></i>
                <span>Map</span>
            </a>
            <a href="{{ url('/record/quick') }}" class="flex flex-col items-center justify-center w-11 h-11 bg-gradient-to-tr from-teal-600 to-teal-400 text-white rounded-full -mt-5 shadow-lg shadow-teal-950/60 border-2 border-slate-900 active:scale-95 transition-transform">
                <i data-lucide="plus" class="w-6 h-6"></i>
            </a>
            <a href="{{ url('/record') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ Request::is('record') ? 'text-teal-400 font-semibold' : 'hover:text-slate-200' }}">
                <i data-lucide="fish" class="w-5 h-5"></i>
                <span>Catches</span>
            </a>
            <a href="{{ url('/expedition') }}" class="flex flex-col items-center gap-1 text-[10px] font-medium {{ Request::is('expedition*') ? 'text-teal-400 font-semibold' : 'hover:text-slate-200' }}">
                <i data-lucide="ship" class="w-5 h-5"></i>
                <span>Expeditions</span>
            </a>
        </nav>
        @endauth
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('Service Worker registered:', reg.scope))
                    .catch((err) => console.log('Service Worker registration failed:', err));
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
