<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_status_and_fetch_ai_summary_via_api(): void
    {
        $admin = User::factory()->admin()->create();
        $assignee = User::factory()->create();

        $createResponse = $this->actingAs($admin)->postJson('/api/tasks', [
            'title' => 'API Created Task',
            'description' => 'Ship the API-driven task flow for the frontend integration.',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->addDays(4)->toDateString(),
            'assigned_to' => $assignee->id,
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.title', 'API Created Task')
            ->assertJsonPath('data.priority', 'high');

        $taskId = $createResponse->json('data.id');

        $this->actingAs($admin)
            ->patchJson('/api/tasks/'.$taskId.'/status', [
                'status' => TaskStatus::Completed->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', TaskStatus::Completed->value);

        $this->actingAs($admin)
            ->getJson('/api/tasks/'.$taskId.'/ai-summary')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['ai_summary', 'ai_priority'],
            ]);
    }

    public function test_standard_user_cannot_create_tasks_via_api(): void
    {
        $user = User::factory()->create();
        $assignee = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/tasks', [
                'title' => 'Blocked Task',
                'description' => 'This should not be allowed for a standard user.',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addDays(3)->toDateString(),
                'assigned_to' => $assignee->id,
            ])
            ->assertForbidden();
    }

    public function test_standard_user_only_receives_assigned_tasks_from_api(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $visibleTask = Task::factory()->create([
            'assigned_to' => $user->id,
            'title' => 'Visible API Task',
        ]);

        Task::factory()->create([
            'assigned_to' => $otherUser->id,
            'title' => 'Hidden API Task',
        ]);

        $this->actingAs($user)
            ->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonFragment(['title' => $visibleTask->title])
            ->assertJsonMissing(['title' => 'Hidden API Task']);
    }
}
