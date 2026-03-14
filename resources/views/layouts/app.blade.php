<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
        <title>{{ isset($title) ? $title.' | TaskPilot AI' : 'TaskPilot AI' }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body>
        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="floating absolute -left-16 top-8 h-64 w-64 rounded-full bg-brand-200/80 blur-3xl"></div>
            <div class="absolute right-[-5rem] top-1/4 h-80 w-80 rounded-full bg-accent-200/70 blur-3xl"></div>
            <div class="floating absolute bottom-[-8rem] left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-brand-100/80 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.45),transparent_52%)]"></div>
        </div>

        <div class="min-h-screen pb-10">
            <header class="sticky top-0 z-40 px-4 pt-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl rounded-[1.75rem] border border-white/80 bg-white/75 px-4 py-4 shadow-[0_18px_45px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-8">
                            <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-accent-500 text-sm font-bold tracking-[0.18em] text-white">TP</span>
                                <div>
                                    <p class="eyebrow">Operations Console</p>
                                    <p class="text-lg font-semibold text-slate-950">TaskPilot</p>
                                </div>
                            </a>

                            @auth
                                <nav class="hidden items-center gap-2 md:flex">
                                    <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">Dashboard</a>
                                    <a href="{{ route('tasks.index') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('tasks.*') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">Tasks</a>
                                </nav>
                            @endauth
                        </div>

                        <div class="flex items-center gap-2 sm:gap-3">
                            @auth
                                <div class="hidden rounded-full border border-slate-200 bg-white/70 px-4 py-2 text-sm text-slate-600 sm:block">
                                    <span class="font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                                    <span class="mx-2 text-slate-300">/</span>
                                    <span class="text-xs uppercase tracking-[0.2em]">{{ auth()->user()->role->value }}</span>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary !px-4 !py-2">Logout</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn-secondary !px-4 !py-2">Login</a>
                                <a href="{{ route('register') }}" class="btn-accent !px-4 !py-2">Register</a>
                            @endauth
                        </div>
                    </div>

                    @auth
                        <nav class="mt-4 flex items-center gap-2 md:hidden">
                            <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">Dashboard</a>
                            <a href="{{ route('tasks.index') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('tasks.*') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">Tasks</a>
                        </nav>
                    @endauth
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="reveal mb-6 rounded-3xl border border-brand-200 bg-brand-50/95 px-5 py-4 text-sm font-semibold text-brand-900">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
