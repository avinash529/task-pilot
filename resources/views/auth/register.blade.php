@extends('layouts.app')

@section('content')
    <div class="mx-auto grid min-h-[72vh] max-w-5xl items-center gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <section class="panel-dark reveal hidden lg:block">
            <p class="eyebrow text-brand-300">Team Onboarding</p>
            <h1 class="mt-4 max-w-xl text-4xl font-semibold leading-tight">Set up your profile and join the delivery workspace.</h1>
            <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300">New registrations are created as standard users and can immediately track assigned tasks and AI summaries.</p>
            <div class="mt-8 space-y-3 text-sm text-slate-300">
                <p>Assigned-task visibility tailored by user role</p>
                <p>Consistent handoff between managers and contributors</p>
                <p>Shared progress metrics for team coordination</p>
            </div>
        </section>

        <section class="panel reveal reveal-delay-1 w-full max-w-xl justify-self-center">
            <div class="mb-8">
                <p class="eyebrow">New Teammate</p>
                <h1 class="mt-3 text-3xl font-semibold text-slate-950">Create your account</h1>
                <p class="mt-2 text-sm text-slate-500">Get started in a minute and join the task workspace.</p>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="field-label">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="field-input">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="field-label">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required class="field-input">
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

                <div>
                    <label for="password_confirmation" class="field-label">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="field-input">
                </div>

                <button type="submit" class="btn-accent w-full rounded-2xl">Create Account</button>
            </form>

            <p class="mt-6 text-sm text-slate-500">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-brand-700 transition hover:text-brand-900">Sign in</a>
            </p>
        </section>
    </div>
@endsection
