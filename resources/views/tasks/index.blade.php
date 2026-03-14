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
        <section class="panel reveal flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="eyebrow">Work Queue</p>
                <h1 class="mt-2 hero-title">Task List</h1>
                <p class="mt-2 muted-copy">Browse work items, filter delivery signals, and inspect AI summaries with less friction.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-full border border-slate-200 bg-white/70 px-4 py-2 text-sm font-semibold text-slate-600">{{ $tasks->total() }} total</span>
                @can('manage-tasks')
                    <a href="{{ route('tasks.create') }}" class="btn-accent">Create Task</a>
                @endcan
            </div>
        </section>

        <section class="panel reveal reveal-delay-1">
            <form method="GET" action="{{ route('tasks.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title, description, assignee" class="field-input xl:col-span-2">

                <select name="status" class="field-select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>

                <select name="priority" class="field-select">
                    <option value="">All priorities</option>
                    @foreach ($priorities as $priority)
                        <option value="{{ $priority->value }}" @selected(($filters['priority'] ?? '') === $priority->value)>{{ $priority->label() }}</option>
                    @endforeach
                </select>

                @if (auth()->user()->isAdmin())
                    <select name="assigned_to" class="field-select">
                        <option value="">All assignees</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) ($filters['assigned_to'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                @endif

                <input type="date" name="due_from" value="{{ $filters['due_from'] ?? '' }}" class="field-input">
                <input type="date" name="due_to" value="{{ $filters['due_to'] ?? '' }}" class="field-input">

                <div class="flex gap-3 xl:col-span-6">
                    <button type="submit" class="btn-primary">Apply Filters</button>
                    <a href="{{ route('tasks.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </section>

        <section class="space-y-4 lg:hidden">
            @forelse ($tasks as $task)
                <article class="panel reveal !p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">{{ $task->title }}</h2>
                            <p class="mt-2 text-sm text-slate-500">Assigned to {{ $task->assignedUser->name }}</p>
                        </div>
                        <span class="{{ $priorityClasses[$task->priority->value] }}">{{ $task->priority->label() }}</span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="{{ $statusClasses[$task->status->value] }}">{{ $task->status->label() }}</span>
                        @if ($task->ai_priority)
                            <span class="status-pill bg-slate-100 text-slate-700">AI {{ $task->ai_priority->label() }}</span>
                        @endif
                    </div>
                    <p class="mt-4 text-sm text-slate-500">Due {{ $task->due_date->format('M d, Y') }}</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('tasks.show', $task->id) }}" class="btn-secondary !px-4 !py-2">View</a>
                        @can('manage-tasks')
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn-secondary !px-4 !py-2">Edit</a>
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger !px-4 !py-2">Delete</button>
                            </form>
                        @endcan
                    </div>
                </article>
            @empty
                <div class="panel-soft border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">No tasks matched the current filters.</div>
            @endforelse
        </section>

        <section class="table-shell hidden lg:block reveal reveal-delay-2">
            <table class="min-w-full divide-y divide-slate-200/80 text-left">
                <thead class="bg-slate-50/90">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Task</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Assignee</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Priority</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Due Date</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">AI</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80">
                    @forelse ($tasks as $task)
                        <tr class="align-top transition-colors hover:bg-brand-50/40">
                            <td class="px-6 py-5">
                                <div>
                                    <p class="font-semibold text-slate-950">{{ $task->title }}</p>
                                    <p class="mt-2 max-w-md text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($task->description, 90) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-600">{{ $task->assignedUser->name }}</td>
                            <td class="px-6 py-5"><span class="{{ $priorityClasses[$task->priority->value] }}">{{ $task->priority->label() }}</span></td>
                            <td class="px-6 py-5"><span class="{{ $statusClasses[$task->status->value] }}">{{ $task->status->label() }}</span></td>
                            <td class="px-6 py-5 text-sm text-slate-600">{{ $task->due_date->format('M d, Y') }}</td>
                            <td class="px-6 py-5 text-sm text-slate-600">{{ $task->ai_priority?->label() ?? 'Pending' }}</td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="btn-secondary !px-3 !py-2 !text-xs">View</a>
                                    @can('manage-tasks')
                                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn-secondary !px-3 !py-2 !text-xs">Edit</a>
                                        <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger !px-3 !py-2 !text-xs">Delete</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">No tasks matched the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <div>
            {{ $tasks->links() }}
        </div>
    </div>
@endsection
