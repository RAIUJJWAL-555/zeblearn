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
        // 1. Quizzes
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_module_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedInteger('time_limit_minutes')->nullable();
            $table->unsignedInteger('total_marks')->default(0);
            $table->unsignedInteger('passing_percentage')->default(70);
            $table->unsignedInteger('attempts_allowed')->default(1);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->boolean('show_correct_answers')->default(true);
            $table->string('status', 20)->default('draft')->index(); // draft, published
            $table->timestamps();

            $table->unique(['course_id', 'slug']);
            $table->index(['course_id', 'status']);
            $table->index(['course_module_id', 'status']);
            $table->index(['lesson_id', 'status']);
        });

        // 2. Quiz Questions
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->restrictOnDelete();
            $table->text('question_text');
            $table->string('question_type', 30)->default('single_choice');
            $table->unsignedInteger('marks')->default(1);
            $table->text('explanation')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['quiz_id', 'sort_order']);
        });

        // 3. Quiz Options
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained()->restrictOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['quiz_question_id', 'sort_order']);
        });

        // 4. Quiz Attempts
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->string('status', 20)->default('in_progress')->index(); // in_progress, submitted, expired
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->unsignedInteger('total_marks')->default(0);
            $table->unsignedInteger('percentage')->nullable();
            $table->boolean('is_passed')->nullable();
            $table->timestamps();

            $table->unique(['quiz_id', 'user_id', 'attempt_number']);
            $table->index(['user_id', 'quiz_id', 'status']);
            $table->index(['quiz_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // 5. Quiz Attempt Answers
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->restrictOnDelete();
            $table->foreignId('quiz_question_id')->constrained()->restrictOnDelete();
            $table->foreignId('quiz_option_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('awarded_marks')->default(0);
            $table->timestamps();

            $table->unique(['quiz_attempt_id', 'quiz_question_id']);
            $table->index(['quiz_attempt_id']);
            $table->index(['quiz_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
