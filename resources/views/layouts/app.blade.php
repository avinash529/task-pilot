<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($title) ? $title.' | TaskPilot AI' : 'TaskPilot AI' }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body>
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute left-0 top-0 h-72 w-72 rounded-full bg-brand-200/60 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-amber-200/70 blur-3xl"></div>
            <div class="absolute inset-x-0 top-40 h-40 bg-[linear-gradient(135deg,rgba(47,178,122,0.08),rgba(251,191,36,0.08))]"></div>
        </div>

        <div class="min-h-screen">
            <header class="border-b border-slate-200/80 bg-white/80 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-8">
                        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-950 text-sm font-semibold tracking-[0.2em] text-white">AI</span>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-700">TaskPilot</p>
                                <p class="text-lg font-semibold text-slate-950">Task Management System</p>
                            </div>
                        </a>

                        @auth
                            <nav class="hidden items-center gap-3 md:flex">
                                <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">Dashboard</a>
                                <a href="{{ route('tasks.index') }}" class="rounded-full px-4 py-2 text-sm font-medium transition {{ request()->routeIs('tasks.*') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">Tasks</a>
                            </nav>
                        @endauth
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            <div class="hidden rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 sm:block">
                                <span class="font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                                <span class="mx-2 text-slate-300">/</span>
                                <span class="text-xs uppercase tracking-[0.2em]">{{ auth()->user()->role->value }}</span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-950 hover:text-slate-950">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-950 hover:text-slate-950">Login</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Register</a>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-3xl border border-brand-200 bg-brand-50 px-5 py-4 text-sm font-medium text-brand-900">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
