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
        <section class="flex flex-col gap-4 rounded-[2rem] bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">Tasks</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-950">Task List</h1>
                <p class="mt-2 text-sm text-slate-500">Browse work items, filter by delivery signals, and inspect AI insights.</p>
            </div>
            @can('manage-tasks')
                <a href="{{ route('tasks.create') }}" class="rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Create Task</a>
            @endcan
        </section>

        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <form method="GET" action="{{ route('tasks.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title, description, assignee" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100 xl:col-span-2">

                <select name="status" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>

                <select name="priority" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                    <option value="">All priorities</option>
                    @foreach ($priorities as $priority)
                        <option value="{{ $priority->value }}" @selected(($filters['priority'] ?? '') === $priority->value)>{{ $priority->label() }}</option>
                    @endforeach
                </select>

                @if (auth()->user()->isAdmin())
                    <select name="assigned_to" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                        <option value="">All assignees</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) ($filters['assigned_to'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                @endif

                <input type="date" name="due_from" value="{{ $filters['due_from'] ?? '' }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
                <input type="date" name="due_to" value="{{ $filters['due_to'] ?? '' }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">

                <div class="flex gap-3 xl:col-span-6">
                    <button type="submit" class="rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Apply Filters</button>
                    <a href="{{ route('tasks.index') }}" class="rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">Reset</a>
                </div>
            </form>
        </section>

        <section class="space-y-4 lg:hidden">
            @forelse ($tasks as $task)
                <article class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">{{ $task->title }}</h2>
                            <p class="mt-2 text-sm text-slate-500">Assigned to {{ $task->assignedUser->name }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $priorityClasses[$task->priority->value] }}">{{ $task->priority->label() }}</span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$task->status->value] }}">{{ $task->status->label() }}</span>
                        @if ($task->ai_priority)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">AI {{ $task->ai_priority->label() }}</span>
                        @endif
                    </div>
                    <p class="mt-4 text-sm text-slate-500">Due {{ $task->due_date->format('M d, Y') }}</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('tasks.show', $task->id) }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">View</a>
                        @can('manage-tasks')
                            <a href="{{ route('tasks.edit', $task->id) }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">Edit</a>
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:border-rose-600 hover:bg-rose-50">Delete</button>
                            </form>
                        @endcan
                    </div>
                </article>
            @empty
                <div class="rounded-[1.75rem] border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">No tasks matched the current filters.</div>
            @endforelse
        </section>

        <section class="hidden overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm lg:block">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
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
                <tbody class="divide-y divide-slate-200">
                    @forelse ($tasks as $task)
                        <tr class="align-top">
                            <td class="px-6 py-5">
                                <div>
                                    <p class="font-semibold text-slate-950">{{ $task->title }}</p>
                                    <p class="mt-2 max-w-md text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($task->description, 90) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-600">{{ $task->assignedUser->name }}</td>
                            <td class="px-6 py-5"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $priorityClasses[$task->priority->value] }}">{{ $task->priority->label() }}</span></td>
                            <td class="px-6 py-5"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$task->status->value] }}">{{ $task->status->label() }}</span></td>
                            <td class="px-6 py-5 text-sm text-slate-600">{{ $task->due_date->format('M d, Y') }}</td>
                            <td class="px-6 py-5 text-sm text-slate-600">{{ $task->ai_priority?->label() ?? 'Pending' }}</td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">View</a>
                                    @can('manage-tasks')
                                        <a href="{{ route('tasks.edit', $task->id) }}" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">Edit</a>
                                        <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:border-rose-600 hover:bg-rose-50">Delete</button>
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
