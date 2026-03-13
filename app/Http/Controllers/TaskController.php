<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        return view('tasks.index', [
            'tasks' => $this->taskService->paginateForUser($request->user(), $request->only([
                'search',
                'status',
                'priority',
                'assigned_to',
                'due_from',
                'due_to',
                'page',
            ])),
            'filters' => $request->only(['search', 'status', 'priority', 'assigned_to', 'due_from', 'due_to']),
            'priorities' => TaskPriority::cases(),
            'statuses' => TaskStatus::cases(),
            'users' => $request->user()->isAdmin() ? $this->taskService->assignableUsers() : collect(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Task::class);

        return view('tasks.create', [
            'task' => null,
            'priorities' => TaskPriority::cases(),
            'statuses' => TaskStatus::cases(),
            'users' => $this->taskService->assignableUsers(),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $this->authorize('create', Task::class);

        $task = $this->taskService->createTask($request->validated());

        return redirect()
            ->route('tasks.show', $task->id)
            ->with('status', 'Task created successfully.');
    }

    public function show(Request $request, int $task): View
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('view', $taskModel);

        return view('tasks.show', [
            'task' => $taskModel,
        ]);
    }

    public function edit(Request $request, int $task): View
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('update', $taskModel);

        return view('tasks.edit', [
            'task' => $taskModel,
            'priorities' => TaskPriority::cases(),
            'statuses' => TaskStatus::cases(),
            'users' => $this->taskService->assignableUsers(),
        ]);
    }

    public function update(UpdateTaskRequest $request, int $task): RedirectResponse
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('update', $taskModel);

        $updatedTask = $this->taskService->updateTask($task, $request->validated());

        return redirect()
            ->route('tasks.show', $updatedTask->id)
            ->with('status', 'Task updated successfully.');
    }

    public function destroy(Request $request, int $task): RedirectResponse
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('delete', $taskModel);

        $this->taskService->deleteTask($task);

        return redirect()
            ->route('tasks.index')
            ->with('status', 'Task deleted successfully.');
    }

    public function refreshAiSummary(Request $request, int $task): RedirectResponse
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('refreshAiSummary', $taskModel);

        $this->taskService->generateAndPersistInsights($task);

        return redirect()
            ->route('tasks.show', $task)
            ->with('status', 'AI summary refreshed successfully.');
    }
}
