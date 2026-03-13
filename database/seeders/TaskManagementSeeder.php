<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskManagementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        $productOwner = User::query()->updateOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Product Owner',
                'password' => Hash::make('password'),
                'role' => UserRole::User,
                'email_verified_at' => now(),
            ]
        );

        $developer = User::query()->updateOrCreate(
            ['email' => 'developer@example.com'],
            [
                'name' => 'Delivery Engineer',
                'password' => Hash::make('password'),
                'role' => UserRole::User,
                'email_verified_at' => now(),
            ]
        );

        Task::query()->updateOrCreate(
            ['title' => 'Launch customer onboarding dashboard'],
            [
                'description' => 'Build the analytics dashboard for onboarding completion metrics and delivery visibility.',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::InProgress,
                'due_date' => now()->addDays(3)->toDateString(),
                'assigned_to' => $productOwner->id,
                'ai_summary' => 'The onboarding dashboard is time-sensitive because it aggregates critical launch metrics for stakeholders.',
                'ai_priority' => TaskPriority::High,
            ]
        );

        Task::query()->updateOrCreate(
            ['title' => 'Refine API response contracts'],
            [
                'description' => 'Align task endpoints with frontend consumption patterns and document stable response fields.',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::Pending,
                'due_date' => now()->addDays(6)->toDateString(),
                'assigned_to' => $developer->id,
                'ai_summary' => 'The API contract refinement task reduces delivery risk by tightening payload expectations before frontend integration.',
                'ai_priority' => TaskPriority::Medium,
            ]
        );

        Task::query()->updateOrCreate(
            ['title' => 'Prepare regression test pack'],
            [
                'description' => 'Create a focused regression checklist for task lifecycle and AI summary flows before release.',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::Completed,
                'due_date' => now()->addDays(1)->toDateString(),
                'assigned_to' => $developer->id,
                'ai_summary' => 'This task is nearly complete and safeguards release quality by covering critical task and AI workflows.',
                'ai_priority' => TaskPriority::Medium,
            ]
        );

        Task::query()->updateOrCreate(
            ['title' => 'Consolidate stakeholder launch notes'],
            [
                'description' => 'Summarize launch dependencies, open blockers, and assign follow-up owners across the team.',
                'priority' => TaskPriority::Low,
                'status' => TaskStatus::Pending,
                'due_date' => now()->addDays(10)->toDateString(),
                'assigned_to' => $productOwner->id,
                'ai_summary' => 'The stakeholder notes task supports launch coordination and should stay visible but does not currently outrank delivery blockers.',
                'ai_priority' => TaskPriority::Low,
            ]
        );

        Task::factory()->count(3)->withoutAi()->create([
            'assigned_to' => $admin->id,
        ]);
    }
}
