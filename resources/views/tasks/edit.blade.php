@extends('layouts.app')

@section('content')
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">Update</p>
        <h1 class="mt-2 text-3xl font-semibold text-slate-950">Edit Task</h1>
        <p class="mt-2 text-sm text-slate-500">Adjust scope, assignee, due date, or delivery status.</p>

        <form method="POST" action="{{ route('tasks.update', $task->id) }}" class="mt-8">
            @csrf
            @method('PUT')
            @include('tasks._form', ['submitLabel' => 'Save Changes'])
        </form>
    </div>
@endsection
