@extends('layouts.app')

@section('content')
    <div class="mx-auto grid min-h-[72vh] max-w-5xl items-center gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <section class="panel-dark reveal hidden lg:block">
            <p class="eyebrow text-brand-300">Welcome Back</p>
            <h1 class="mt-4 max-w-xl text-4xl font-semibold leading-tight">Coordinate your team without the dashboard noise.</h1>
            <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300">Sign in to manage assignments, monitor AI summaries, and keep deadlines visible for the whole team.</p>
            <div class="mt-8 space-y-3 text-sm text-slate-300">
                <p>Fast filtering for status, priority, and assignee</p>
                <p>Live analytics for pending and high-priority work</p>
                <p>AI-generated summaries available on each task</p>
            </div>
        </section>

        <section class="panel reveal reveal-delay-1 w-full max-w-xl justify-self-center">
            <div class="mb-8">
                <p class="eyebrow">Welcome Back</p>
                <h1 class="mt-3 text-3xl font-semibold text-slate-950">Sign in to TaskPilot</h1>
                <p class="mt-2 text-sm text-slate-500">Use the seeded admin or create a teammate account.</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="field-label">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="field-input">
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="field-label">Password</label>
                    <input id="password" name="password" type="password" required class="field-input">
                    @error('password')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    Keep me signed in
                </label>

                <button type="submit" class="btn-accent w-full rounded-2xl">Sign In</button>
            </form>

            <div class="panel-soft mt-6 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-900">Demo admin:</span> admin@example.com / password</p>
            </div>

            <p class="mt-6 text-sm text-slate-500">
                Need an account?
                <a href="{{ route('register') }}" class="font-semibold text-brand-700 transition hover:text-brand-900">Create one here</a>
            </p>
        </section>
    </div>
@endsection
