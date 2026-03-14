@extends('layouts.app')

@section('content')
    <div class="panel reveal">
        <p class="eyebrow">Update</p>
        <h1 class="mt-2 hero-title">Edit Task</h1>
        <p class="mt-2 muted-copy">Update ownership, status, due date, and detail quality before pushing the next delivery step.</p>

        <form method="POST" action="{{ route('tasks.update', $task->id) }}" class="mt-8">
            @csrf
            @method('PUT')
            @include('tasks._form', ['submitLabel' => 'Save Changes'])
        </form>
    </div>
@endsection
