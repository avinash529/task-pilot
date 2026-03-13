@extends('layouts.app')

@section('content')
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">Create</p>
        <h1 class="mt-2 text-3xl font-semibold text-slate-950">Create Task</h1>
        <p class="mt-2 text-sm text-slate-500">New tasks are persisted through the service layer and enriched with AI insights.</p>

        <form method="POST" action="{{ route('tasks.store') }}" class="mt-8">
            @csrf
            @include('tasks._form', ['submitLabel' => 'Create Task'])
        </form>
    </div>
@endsection
