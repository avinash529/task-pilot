@php
    $priorityClasses = [
        'low' => 'bg-slate-100 text-slate-700',
        'medium' => 'bg-amber-100 text-amber-800',
        'high' => 'bg-rose-100 text-rose-700',
    ];

    $statusClasses = [
        'pending' => 'bg-amber-100 text-amber-800',
        'in_progress' => 'bg-sky-100 text-sky-700',
        'completed' => 'bg-brand-100 text-brand-800',
    ];
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="flex flex-col gap-4 rounded-[2rem] bg-white p-6 shadow-sm sm:flex-row sm:items-start sm:justify-between sm:p-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">Task Detail</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-950">{{ $task->title }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-500">{{ $task->description }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <span class="rounded-full px-4 py-2 text-sm font-semibold {{ $priorityClasses[$task->priority->value] }}">{{ $task->priority->label() }}</span>
                <span class="rounded-full px-4 py-2 text-sm font-semibold {{ $statusClasses[$task->status->value] }}">{{ $task->status->label() }}</span>
                @if ($task->ai_priority)
                    <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">AI {{ $task->ai_priority->label() }}</span>
                @endif
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">Execution</p>
                    <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Assigned User</dt>
                            <dd class="mt-2 text-lg font-semibold text-slate-950">{{ $task->assignedUser->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Due Date</dt>
                            <dd class="mt-2 text-lg font-semibold text-slate-950">{{ $task->due_date->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Created</dt>
                            <dd class="mt-2 text-lg font-semibold text-slate-950">{{ $task->created_at->format('M d, Y h:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Last Updated</dt>
                            <dd class="mt-2 text-lg font-semibold text-slate-950">{{ $task->updated_at->format('M d, Y h:i A') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">AI Summary</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-950">Generated Work Brief</h2>
                        </div>
                        @can('manage-tasks')
                            <form method="POST" action="{{ route('tasks.ai.refresh', $task->id) }}">
                                @csrf
                                <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">Refresh AI</button>
                            </form>
                        @endcan
                    </div>

                    <div class="mt-6 rounded-[1.75rem] bg-slate-50 p-5 text-sm leading-7 text-slate-600">
                        {{ $task->ai_summary ?: 'AI summary will appear after the task is processed.' }}
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @can('manage-tasks')
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">Actions</p>
                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ route('tasks.edit', $task->id) }}" class="rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Edit Task</a>
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-rose-200 px-5 py-3 text-sm font-semibold text-rose-600 transition hover:border-rose-600 hover:bg-rose-50">Delete</button>
                            </form>
                        </div>
                    </div>
                @endcan

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">API Endpoints</p>
                    <div class="mt-5 space-y-3 rounded-[1.75rem] bg-slate-950 p-5 font-mono text-sm text-slate-200">
                        <p>GET /api/tasks/{{ $task->id }}</p>
                        <p>GET /api/tasks/{{ $task->id }}/ai-summary</p>
                        <p>PATCH /api/tasks/{{ $task->id }}/status</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
