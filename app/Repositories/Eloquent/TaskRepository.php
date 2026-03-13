<?php

namespace App\Repositories\Eloquent;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use BackedEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    private string $versionKey = 'tasks:cache:version';

    public function all(array $filters = []): LengthAwarePaginator
    {
        $payload = $this->serializableFilters($filters);
        $perPage = (int) ($payload['per_page'] ?? 10);
        $page = (int) ($payload['page'] ?? 1);
        $cacheKey = $this->cacheKey('list', $payload);

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($filters, $perPage, $page) {
            return $this->query($filters)
                ->paginate($perPage, ['*'], 'page', $page);
        });
    }

    public function find(int $id): ?Task
    {
        return Cache::remember($this->cacheKey('task', ['id' => $id]), now()->addMinutes(5), function () use ($id) {
            return Task::query()
                ->with('assignedUser')
                ->find($id);
        });
    }

    public function create(array $data): Task
    {
        $task = Task::query()->create($data);

        $this->invalidateCache();

        return $task->load('assignedUser');
    }

    public function update(int $id, array $data): Task
    {
        $task = Task::query()->findOrFail($id);
        $task->fill($data);
        $task->save();

        $this->invalidateCache();

        return $task->refresh()->load('assignedUser');
    }

    public function delete(int $id): bool
    {
        $deleted = (bool) Task::query()->findOrFail($id)->delete();

        $this->invalidateCache();

        return $deleted;
    }

    public function stats(array $filters = []): array
    {
        return Cache::remember($this->cacheKey('stats', $this->serializableFilters($filters)), now()->addMinutes(5), function () use ($filters) {
            $query = $this->query($filters);

            return [
                'total_tasks' => (clone $query)->count(),
                'completed_tasks' => (clone $query)->where('status', TaskStatus::Completed->value)->count(),
                'pending_tasks' => (clone $query)->where('status', TaskStatus::Pending->value)->count(),
                'high_priority_tasks' => (clone $query)->where('priority', TaskPriority::High->value)->count(),
            ];
        });
    }

    public function statusBreakdown(array $filters = []): array
    {
        return Cache::remember($this->cacheKey('status-breakdown', $this->serializableFilters($filters)), now()->addMinutes(5), function () use ($filters) {
            $query = $this->query($filters);

            return collect(TaskStatus::cases())
                ->map(fn (TaskStatus $status) => [
                    'status' => $status->value,
                    'label' => $status->label(),
                    'count' => (clone $query)->where('status', $status->value)->count(),
                ])
                ->all();
        });
    }

    protected function query(array $filters = []): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $status = $this->enumValue($filters['status'] ?? null);
        $priority = $this->enumValue($filters['priority'] ?? null);
        $assignedTo = $filters['assigned_to'] ?? null;
        $dueFrom = $filters['due_from'] ?? null;
        $dueTo = $filters['due_to'] ?? null;

        return Task::query()
            ->with('assignedUser')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $taskQuery) use ($search) {
                    $taskQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('assignedUser', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== null, fn (Builder $query) => $query->where('status', $status))
            ->when($priority !== null, fn (Builder $query) => $query->where('priority', $priority))
            ->when($assignedTo !== null && $assignedTo !== '', fn (Builder $query) => $query->where('assigned_to', $assignedTo))
            ->when($dueFrom !== null && $dueFrom !== '', fn (Builder $query) => $query->whereDate('due_date', '>=', $dueFrom))
            ->when($dueTo !== null && $dueTo !== '', fn (Builder $query) => $query->whereDate('due_date', '<=', $dueTo))
            ->orderBy('due_date')
            ->latest('created_at');
    }

    protected function cacheKey(string $prefix, array $filters): string
    {
        return sprintf('tasks:%s:v%s:%s', $prefix, $this->cacheVersion(), md5(json_encode($filters)));
    }

    protected function cacheVersion(): int
    {
        if (! Cache::has($this->versionKey)) {
            Cache::forever($this->versionKey, 1);
        }

        return (int) Cache::get($this->versionKey, 1);
    }

    protected function invalidateCache(): void
    {
        Cache::forever($this->versionKey, $this->cacheVersion() + 1);
    }

    protected function serializableFilters(array $filters): array
    {
        ksort($filters);

        return collect($filters)
            ->map(fn (mixed $value) => $this->enumValue($value) ?? $value)
            ->toArray();
    }

    protected function enumValue(mixed $value): mixed
    {
        return $value instanceof BackedEnum ? $value->value : $value;
    }
}
