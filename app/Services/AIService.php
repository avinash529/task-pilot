<?php

namespace App\Services;

use App\Contracts\AI\AIClientInterface;
use App\Enums\TaskPriority;
use App\Models\Task;
use Illuminate\Support\Str;
use Throwable;

class AIService
{
    public function __construct(
        private readonly AIClientInterface $client,
    ) {
    }

    public function generateSummary(Task $task): array
    {
        try {
            if ((bool) config('ai.force_mock', true)) {
                return $this->mockInsights($task);
            }

            $response = $this->client->generateText($this->buildPrompt($task), [
                'model' => config('ai.model'),
            ]);

            return $this->parseResponse($response, $task);
        } catch (Throwable $throwable) {
            report($throwable);

            return $this->mockInsights($task);
        }
    }

    public function buildPrompt(Task $task): string
    {
        return <<<PROMPT
You are an AI assistant for a professional task management system.

Return strict JSON with two keys only:
- ai_summary: concise summary in 1-2 sentences
- ai_priority: one of low, medium, high

Task title: {$task->title}
Task description: {$task->description}
Current priority: {$task->priority->value}
Current status: {$task->status->value}
Due date: {$task->due_date?->toDateString()}
Assigned user: {$task->assignedUser?->name}

Prioritize urgency, delivery risk, and workload.
PROMPT;
    }

    protected function parseResponse(string $response, Task $task): array
    {
        $payload = trim($response);
        $payload = preg_replace('/^```json|^```|```$/m', '', $payload) ?? $payload;

        /** @var array<string, mixed>|null $decoded */
        $decoded = json_decode(trim($payload), true);

        if (! is_array($decoded)) {
            return $this->mockInsights($task);
        }

        $summary = trim((string) ($decoded['ai_summary'] ?? ''));

        if ($summary === '') {
            return $this->mockInsights($task);
        }

        return [
            'ai_summary' => Str::limit($summary, 500),
            'ai_priority' => $this->normalizePriority($decoded['ai_priority'] ?? null, $task)->value,
        ];
    }

    protected function normalizePriority(mixed $value, Task $task): TaskPriority
    {
        if (is_string($value)) {
            $priority = TaskPriority::tryFrom(Str::lower(trim($value)));

            if ($priority !== null) {
                return $priority;
            }
        }

        return $this->predictPriority($task);
    }

    protected function mockInsights(Task $task): array
    {
        $dueDate = $task->due_date?->format('M d, Y') ?? 'an upcoming date';
        $summary = sprintf(
            'Task "%s" is assigned to %s and is currently %s with a due date of %s. Focus on the %s work first and keep follow-up visibility high.',
            $task->title,
            $task->assignedUser?->name ?? 'an assigned teammate',
            Str::lower($task->status->label()),
            $dueDate,
            Str::lower($task->priority->label())
        );

        return [
            'ai_summary' => Str::limit($summary, 500),
            'ai_priority' => $this->predictPriority($task)->value,
        ];
    }

    protected function predictPriority(Task $task): TaskPriority
    {
        $daysUntilDue = now()->startOfDay()->diffInDays($task->due_date, false);

        if ($task->priority === TaskPriority::High || $daysUntilDue <= 2) {
            return TaskPriority::High;
        }

        if ($task->priority === TaskPriority::Medium || $daysUntilDue <= 5) {
            return TaskPriority::Medium;
        }

        return TaskPriority::Low;
    }
}
