@extends('layouts.app')

@section('content')
    <div class="mx-auto flex min-h-[70vh] max-w-md items-center justify-center">
        <div class="w-full rounded-[2rem] border border-white/60 bg-white/90 p-8 shadow-[0_24px_80px_rgba(15,23,42,0.08)] backdrop-blur">
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">Welcome back</p>
                <h1 class="mt-3 text-3xl font-semibold text-slate-950">Sign in to TaskPilot AI</h1>
                <p class="mt-2 text-sm text-slate-500">Use the seeded admin or register a new teammate account.</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                    @error('password')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    Keep me signed in
                </label>

                <button type="submit" class="w-full rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Sign In</button>
            </form>

            <div class="mt-6 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-900">Demo admin:</span> admin@example.com / password</p>
            </div>

            <p class="mt-6 text-sm text-slate-500">
                Need an account?
                <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:text-brand-900">Create one here</a>
            </p>
        </div>
    </div>
@endsection
