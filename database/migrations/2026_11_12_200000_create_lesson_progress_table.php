<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('lesson_id')->constrained('lessons')->restrictOnDelete();
            $table->string('status', 20)->default('not_started')->index(); // not_started, in_progress, completed
            $table->timestamp('first_started_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamps();

            // Enforce strictly one progress record per user and lesson
            $table->unique(['user_id', 'lesson_id']);

            // Composite indexes for fast progress calculations and resume-learning lookups
            $table->index(['user_id', 'status']);
            $table->index(['lesson_id', 'status']);
            $table->index(['user_id', 'last_accessed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
