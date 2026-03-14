@extends('layouts.app')

@section('content')
    <div class="panel reveal">
        <p class="eyebrow">Create</p>
        <h1 class="mt-2 hero-title">Create Task</h1>
        <p class="mt-2 muted-copy">Add scope, assignee, and timing details. AI insight generation starts after the task is saved.</p>

        <form method="POST" action="{{ route('tasks.store') }}" class="mt-8">
            @csrf
            @include('tasks._form', ['submitLabel' => 'Create Task'])
        </form>
    </div>
@endsection
