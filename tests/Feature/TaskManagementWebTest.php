<?php

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard_and_task_list(): void
    {
        $admin = User::factory()->admin()->create();
        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'title' => 'Admin Visible Task',
            'assigned_to' => $assignee->id,
            'priority' => TaskPriority::High,
            'status' => TaskStatus::Pending,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Total Tasks');

        $this->actingAs($admin)
            ->get('/tasks')
            ->assertOk()
            ->assertSee($task->title)
            ->assertSee('Create Task');
    }

    public function test_standard_user_only_sees_assigned_tasks(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownTask = Task::factory()->create([
            'title' => 'Own Assigned Task',
            'assigned_to' => $user->id,
        ]);

        $otherTask = Task::factory()->create([
            'title' => 'Someone Else Task',
            'assigned_to' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->get('/tasks')
            ->assertOk()
            ->assertSee($ownTask->title)
            ->assertDontSee($otherTask->title);

        $this->actingAs($user)
            ->get('/tasks/'.$otherTask->id)
            ->assertForbidden();
    }
}
