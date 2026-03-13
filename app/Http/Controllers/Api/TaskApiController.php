<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TaskApiController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->paginateForUser($request->user(), $request->only([
            'search',
            'status',
            'priority',
            'assigned_to',
            'due_from',
            'due_to',
            'page',
            'per_page',
        ]));

        return TaskResource::collection($tasks)->additional([
            'analytics' => $this->taskService->dashboard($request->user())['stats'],
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $task = $this->taskService->createTask($request->validated());

        return TaskResource::make($task)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, int $task): TaskResource
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('view', $taskModel);

        return TaskResource::make($taskModel);
    }

    public function update(UpdateTaskRequest $request, int $task): TaskResource
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('update', $taskModel);

        return TaskResource::make($this->taskService->updateTask($task, $request->validated()));
    }

    public function destroy(Request $request, int $task): Response
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('delete', $taskModel);

        $this->taskService->deleteTask($task);

        return response()->noContent();
    }

    public function updateStatus(UpdateTaskStatusRequest $request, int $task): TaskResource
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('updateStatus', $taskModel);

        return TaskResource::make($this->taskService->updateStatus($task, $request->validated('status')));
    }

    public function aiSummary(Request $request, int $task): JsonResponse
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('view', $taskModel);

        return response()->json([
            'data' => $this->taskService->aiSummary($task),
        ]);
    }
}
