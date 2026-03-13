<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Enums\TaskStatus;
use App\Jobs\GenerateTaskAiInsightsJob;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
        private readonly UserRepositoryInterface $users,
        private readonly AIService $aiService,
    ) {
    }

    public function paginateForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->tasks->all($this->visibilityFilters($user, $filters));
    }

    public function findOrFail(int $taskId): Task
    {
        $task = $this->tasks->find($taskId);

        if ($task === null) {
            throw (new ModelNotFoundException())->setModel(Task::class, [$taskId]);
        }

        return $task;
    }

    public function createTask(array $data): Task
    {
        $task = DB::transaction(fn () => $this->tasks->create($data));

        return $this->handleAiWorkflow($task);
    }

    public function updateTask(int $taskId, array $data): Task
    {
        $task = DB::transaction(fn () => $this->tasks->update($taskId, $data));

        return $this->handleAiWorkflow($task);
    }

    public function deleteTask(int $taskId): bool
    {
        return DB::transaction(fn () => $this->tasks->delete($taskId));
    }

    public function updateStatus(int $taskId, TaskStatus|string $status): Task
    {
        $value = $status instanceof TaskStatus ? $status->value : TaskStatus::from($status)->value;

        return DB::transaction(fn () => $this->tasks->update($taskId, [
            'status' => $value,
        ]));
    }

    public function generateAndPersistInsights(int $taskId): Task
    {
        $task = $this->findOrFail($taskId);
        $insights = $this->aiService->generateSummary($task);

        return DB::transaction(fn () => $this->tasks->update($taskId, $insights));
    }

    public function aiSummary(int $taskId): array
    {
        $task = $this->findOrFail($taskId);

        if (blank($task->ai_summary) || $task->ai_priority === null) {
            $task = $this->generateAndPersistInsights($taskId);
        }

        return [
            'ai_summary' => $task->ai_summary,
            'ai_priority' => $task->ai_priority?->value,
        ];
    }

    public function dashboard(User $user): array
    {
        $filters = $this->visibilityFilters($user);
        $breakdown = $this->tasks->statusBreakdown($filters);

        return [
            'stats' => $this->tasks->stats($filters),
            'chart' => [
                'labels' => array_column($breakdown, 'label'),
                'data' => array_column($breakdown, 'count'),
            ],
        ];
    }

    public function assignableUsers(): Collection
    {
        return $this->users->allAssignable();
    }

    protected function handleAiWorkflow(Task $task): Task
    {
        if ((bool) config('ai.queue', false)) {
            GenerateTaskAiInsightsJob::dispatch($task->id)->afterCommit();

            return $task->refresh();
        }

        return $this->generateAndPersistInsights($task->id);
    }

    protected function visibilityFilters(User $user, array $filters = []): array
    {
        $scoped = [
            'search' => $filters['search'] ?? null,
            'status' => $filters['status'] ?? null,
            'priority' => $filters['priority'] ?? null,
            'assigned_to' => $filters['assigned_to'] ?? null,
            'due_from' => $filters['due_from'] ?? null,
            'due_to' => $filters['due_to'] ?? null,
            'page' => $filters['page'] ?? 1,
            'per_page' => $filters['per_page'] ?? 10,
        ];

        if (! $user->isAdmin()) {
            $scoped['assigned_to'] = $user->id;
        }

        return array_filter($scoped, fn (mixed $value) => ! ($value === null || $value === ''));
    }
}
