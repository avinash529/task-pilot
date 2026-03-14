@php
    $priorityClasses = [
        'low' => 'status-pill bg-slate-100 text-slate-700',
        'medium' => 'status-pill bg-brand-100 text-brand-800',
        'high' => 'status-pill bg-rose-100 text-rose-700',
    ];

    $statusClasses = [
        'pending' => 'status-pill bg-brand-100 text-brand-800',
        'in_progress' => 'status-pill bg-sky-100 text-sky-700',
        'completed' => 'status-pill bg-accent-100 text-accent-800',
    ];
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="panel reveal flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="eyebrow">Task Detail</p>
                <h1 class="mt-2 hero-title">{{ $task->title }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">{{ $task->description }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <span class="{{ $priorityClasses[$task->priority->value] }} px-4 py-2 text-sm">{{ $task->priority->label() }}</span>
                <span class="{{ $statusClasses[$task->status->value] }} px-4 py-2 text-sm">{{ $task->status->label() }}</span>
                @if ($task->ai_priority)
                    <span class="status-pill bg-slate-100 px-4 py-2 text-sm text-slate-700">AI {{ $task->ai_priority->label() }}</span>
                @endif
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="space-y-6">
                <div class="panel reveal reveal-delay-1">
                    <p class="eyebrow">Execution</p>
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

                <div class="panel reveal reveal-delay-2">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="eyebrow">AI Summary</p>
                            <h2 class="mt-2 section-title">Generated Work Brief</h2>
                        </div>
                        @can('manage-tasks')
                            <form method="POST" action="{{ route('tasks.ai.refresh', $task->id) }}">
                                @csrf
                                <button type="submit" class="btn-secondary !px-4 !py-2">Refresh AI</button>
                            </form>
                        @endcan
                    </div>

                    <div class="panel-soft mt-6 text-sm leading-7 text-slate-600">
                        {{ $task->ai_summary ?: 'AI summary will appear after the task is processed.' }}
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @can('manage-tasks')
                    <div class="panel reveal reveal-delay-1">
                        <p class="eyebrow">Actions</p>
                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn-primary">Edit Task</a>
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                @endcan

                <div class="panel reveal reveal-delay-2">
                    <p class="eyebrow">API Endpoints</p>
                    <div class="mt-5 space-y-3 rounded-[1.5rem] bg-slate-950 p-5 font-mono text-sm text-slate-200">
                        <p>GET /api/tasks/{{ $task->id }}</p>
                        <p>GET /api/tasks/{{ $task->id }}/ai-summary</p>
                        <p>PATCH /api/tasks/{{ $task->id }}/status</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
