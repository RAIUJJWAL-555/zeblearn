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
        // 1. Assignments
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_module_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedInteger('max_marks')->default(100);
            $table->unsignedInteger('passing_marks')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->boolean('allow_late_submissions')->default(false);
            $table->unsignedInteger('late_penalty_percent')->default(0);
            $table->unsignedInteger('max_attempts')->default(1);
            $table->boolean('allow_text_submission')->default(true);
            $table->boolean('allow_file_submission')->default(true);
            $table->string('allowed_file_types', 255)->default('pdf,doc,docx,zip,txt');
            $table->unsignedInteger('max_file_size_mb')->default(10);
            $table->string('status', 20)->default('draft')->index(); // draft, published
            $table->timestamps();

            $table->unique(['course_id', 'slug']);
            $table->index(['course_id', 'status']);
            $table->index(['course_module_id', 'status']);
            $table->index(['lesson_id', 'status']);
        });

        // 2. Assignment Submissions
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->string('status', 30)->default('submitted')->index('asgn_sub_status_idx'); // submitted, under_review, graded, resubmission_required
            $table->longText('submission_text')->nullable();
            $table->timestamp('submitted_at');
            $table->boolean('is_late')->default(false);
            $table->unsignedInteger('marks_awarded')->nullable();
            $table->foreignId('grader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->text('feedback')->nullable();
            $table->text('private_notes')->nullable();
            $table->timestamps();

            $table->unique(['assignment_id', 'user_id', 'attempt_number'], 'asgn_sub_user_att_unique');
            $table->index(['user_id', 'assignment_id', 'status'], 'asgn_sub_user_asgn_status_idx');
            $table->index(['assignment_id', 'status'], 'asgn_sub_asgn_status_idx');
        });

        // 3. Assignment Submission Files
        Schema::create('assignment_submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_submission_id')->constrained()->cascadeOnDelete();
            $table->string('original_filename');
            $table->string('stored_path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size'); // bytes
            $table->string('disk', 30)->default('local');
            $table->timestamps();

            $table->index(['assignment_submission_id'], 'asgn_sub_file_sub_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submission_files');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
