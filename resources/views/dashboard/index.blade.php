@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <section class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
            <div class="rounded-[2rem] bg-slate-950 px-8 py-10 text-white shadow-[0_24px_80px_rgba(15,23,42,0.12)]">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-300">Overview</p>
                <h1 class="mt-4 max-w-2xl text-4xl font-semibold tracking-tight">AI-assisted task operations for delivery teams.</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">Track execution, review AI-generated summaries, and keep priorities visible with a service-driven Laravel architecture.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('tasks.index') }}" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100">View Tasks</a>
                    @can('manage-tasks')
                        <a href="{{ route('tasks.create') }}" class="rounded-full border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10">Create Task</a>
                    @endcan
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_24px_80px_rgba(15,23,42,0.06)]">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-600">Analytics scope</p>
                <h2 class="mt-4 text-2xl font-semibold text-slate-950">{{ auth()->user()->isAdmin() ? 'Administrative overview' : 'Assigned task overview' }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-500">Admins see the full system. Standard users see analytics scoped to tasks assigned to them.</p>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Total Tasks</p>
                <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $stats['total_tasks'] }}</p>
            </div>
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Completed</p>
                <p class="mt-4 text-4xl font-semibold text-brand-700">{{ $stats['completed_tasks'] }}</p>
            </div>
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Pending</p>
                <p class="mt-4 text-4xl font-semibold text-amber-600">{{ $stats['pending_tasks'] }}</p>
            </div>
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">High Priority</p>
                <p class="mt-4 text-4xl font-semibold text-rose-600">{{ $stats['high_priority_tasks'] }}</p>
            </div>
        </section>

        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-700">Status distribution</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-950">Task Progress Chart</h2>
                </div>
            </div>
            <canvas id="taskStatusChart" height="120"></canvas>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartElement = document.getElementById('taskStatusChart');

        if (chartElement && window.Chart) {
            new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: @json($chart['labels']),
                    datasets: [{
                        label: 'Tasks',
                        data: @json($chart['data']),
                        backgroundColor: ['#f59e0b', '#2fb27a', '#0f172a'],
                        borderRadius: 16,
                        borderSkipped: false,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                },
            });
        }
    </script>
@endpush
