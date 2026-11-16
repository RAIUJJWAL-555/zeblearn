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
        // 1. Centralized Trust & Proof of Work Items
        Schema::create('trust_items', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40)->index(); // placement, testimonial, certification, trainer, partner, project, video_story, award
            $table->string('title');
            $table->string('subtitle')->nullable(); // Candidate role, company, designation, credential, award authority
            $table->text('description')->nullable();
            $table->string('image_path')->nullable(); // Avatar, logo, certificate badge, project screenshot, award medal
            $table->string('media_url')->nullable(); // Video URL, verification URL, external proof link
            $table->string('content_category', 100)->nullable()->index(); // e.g. sap-erp, workday, salesforce, data-science, etc.
            $table->string('scope_type', 40)->default('global')->index(); // global, all, category, selected_courses, selected_modules
            $table->string('scope_category', 100)->nullable()->index();
            $table->json('metadata')->nullable(); // Type-specific attributes
            $table->boolean('is_verified')->default(true)->index();
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('display_priority')->default(0)->index();
            $table->timestamps();

            $table->index(['type', 'is_published', 'is_verified']);
            $table->index(['scope_type', 'is_published']);
        });

        // 2. Dynamic Course Mapping Pivot Table
        Schema::create('trust_item_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trust_item_id')->constrained('trust_items')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['trust_item_id', 'course_id']);
            $table->index(['course_id', 'sort_order']);
        });

        // 3. Add course-level trust configuration column
        if (Schema::hasTable('courses') && ! Schema::hasColumn('courses', 'trust_config')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->json('trust_config')->nullable()->after('section_config');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'trust_config')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('trust_config');
            });
        }

        Schema::dropIfExists('trust_item_courses');
        Schema::dropIfExists('trust_items');
    }
};
