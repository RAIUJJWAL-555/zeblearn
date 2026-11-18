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
        // 1. Course Reviews Table
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->unsignedInteger('version_number')->default(1);
            $table->string('review_status', 30)->default('draft')->index();
            $table->string('content_hash', 64)->nullable();
            $table->foreignId('submitted_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('decision', 30)->nullable();
            $table->text('decision_notes')->nullable();
            $table->timestamps();
        });

        // 2. Course Review Sections Table (14 sections)
        Schema::create('course_review_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_review_id')->constrained('course_reviews')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('section_key', 50)->index();
            $table->string('section_title', 150);
            $table->string('status', 30)->default('not_reviewed'); // not_reviewed, passed, needs_correction, not_applicable
            $table->text('na_reason')->nullable();
            $table->foreignId('reviewed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['course_review_id', 'section_key'], 'cr_section_unique');
        });

        // 3. Course Review Comments Table
        Schema::create('course_review_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_review_id')->constrained('course_reviews')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('section_key', 50)->nullable()->index();
            $table->text('comment');
            $table->boolean('is_correction_required')->default(false)->index();
            $table->boolean('is_resolved')->default(false)->index();
            $table->foreignId('resolved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Course Review Histories (Audit Log)
        Schema::create('course_review_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_review_id')->nullable()->constrained('course_reviews')->nullOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->string('action', 50);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 5. Course Publication Histories
        Schema::create('course_publication_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('from_status', 30);
            $table->string('to_status', 30);
            $table->string('action', 50);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('content_hash_at_action', 64)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_publication_histories');
        Schema::dropIfExists('course_review_histories');
        Schema::dropIfExists('course_review_comments');
        Schema::dropIfExists('course_review_sections');
        Schema::dropIfExists('course_reviews');
    }
};
