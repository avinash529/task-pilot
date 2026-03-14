@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <section class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
            <div class="panel-dark reveal">
                <p class="eyebrow text-brand-300">Control Center</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-semibold leading-tight sm:text-5xl">Run delivery with clear priorities and calm execution.</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">Track scope, assignment, and due dates in one place while AI-generated insights keep your next move obvious.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('tasks.index') }}" class="btn-accent">Open Tasks</a>
                    @can('manage-tasks')
                        <a href="{{ route('tasks.create') }}" class="inline-flex items-center justify-center rounded-full border border-white/25 px-5 py-3 text-sm font-semibold text-white transition duration-200 hover:-translate-y-0.5 hover:border-white hover:bg-white/10">Create Task</a>
                    @endcan
                </div>
            </div>

            <div class="panel reveal reveal-delay-1">
                <p class="eyebrow text-accent-700">Analytics Scope</p>
                <h2 class="mt-4 section-title">{{ auth()->user()->isAdmin() ? 'Administrative overview' : 'Assigned task overview' }}</h2>
                <p class="mt-3 muted-copy">Admins see the full workspace. Standard users see stats scoped only to their assigned tasks.</p>
                <div class="panel-soft mt-6 border-brand-100 bg-brand-50/80">
                    <p class="text-sm font-semibold text-brand-800">Live signal</p>
                    <p class="mt-2 text-sm leading-6 text-brand-900/90">{{ $stats['pending_tasks'] }} pending task(s) currently need attention.</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="metric-card reveal">
                <p class="text-sm font-semibold text-slate-500">Total Tasks</p>
                <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $stats['total_tasks'] }}</p>
            </div>
            <div class="metric-card reveal reveal-delay-1">
                <p class="text-sm font-semibold text-slate-500">Completed</p>
                <p class="mt-4 text-4xl font-semibold text-accent-700">{{ $stats['completed_tasks'] }}</p>
            </div>
            <div class="metric-card reveal reveal-delay-1">
                <p class="text-sm font-semibold text-slate-500">Pending</p>
                <p class="mt-4 text-4xl font-semibold text-brand-600">{{ $stats['pending_tasks'] }}</p>
            </div>
            <div class="metric-card reveal reveal-delay-2">
                <p class="text-sm font-semibold text-slate-500">High Priority</p>
                <p class="mt-4 text-4xl font-semibold text-rose-600">{{ $stats['high_priority_tasks'] }}</p>
            </div>
        </section>

        <section class="panel reveal reveal-delay-2">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <p class="eyebrow">Status Distribution</p>
                    <h2 class="mt-2 section-title">Task Progress Chart</h2>
                </div>
            </div>
            <div class="relative mx-auto h-56 w-full max-w-4xl sm:h-64">
                <canvas id="taskStatusChart"></canvas>
            </div>
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
                        backgroundColor: ['#f26b2e', '#22a596', '#0f172a'],
                        borderRadius: 16,
                        borderSkipped: false,
                        maxBarThickness: 56,
                        categoryPercentage: 0.58,
                        barPercentage: 0.65,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#334155',
                            },
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#64748b',
                                precision: 0,
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.22)',
                            },
                        },
                    },
                },
            });
        }
    </script>
@endpush
