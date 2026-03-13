<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('priority', TaskPriority::values());
            $table->enum('status', TaskStatus::values())->default(TaskStatus::Pending->value);
            $table->date('due_date');
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('ai_summary')->nullable();
            $table->enum('ai_priority', TaskPriority::values())->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
