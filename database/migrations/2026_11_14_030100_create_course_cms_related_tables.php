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
        // 1. Course FAQs Table
        Schema::create('course_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['course_id', 'is_active', 'sort_order']);
        });

        // 2. Upcoming Live Cohort Batches Table
        Schema::create('course_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('timings'); // e.g. "08:00 AM – 10:00 AM IST"
            $table->string('days'); // e.g. "Sat & Sun (Weekend)"
            $table->string('delivery_mode')->default('Live Online');
            $table->string('timezone')->default('Asia/Kolkata');
            $table->unsignedInteger('total_seats')->default(25);
            $table->unsignedInteger('available_seats')->default(8);
            $table->string('status')->default('filling_fast')->index(); // open, filling_fast, almost_full, closed
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['course_id', 'is_active', 'start_date']);
        });

        // 3. Verified Student Testimonials Table
        Schema::create('course_testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('student_name');
            $table->string('student_title_or_company')->nullable(); // e.g. "SAP MM Consultant @ Deloitte"
            $table->string('avatar_path')->nullable();
            $table->text('review_text');
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->string('video_url')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();

            $table->index(['course_id', 'is_published', 'sort_order']);
        });

        // 4. Industry & Accreditation Partners Table
        Schema::create('course_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('description')->nullable();
            $table->string('website_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['course_id', 'is_active', 'sort_order']);
        });

        // 5. Permanent 301 URL Slug Redirect History Table
        Schema::create('course_slug_redirects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('old_slug')->unique()->index();
            $table->string('new_slug')->index();
            $table->unsignedSmallInteger('http_code')->default(301);
            $table->timestamps();
        });

        // 6. Add sort_order to course_instructors pivot table if not already present
        if (Schema::hasTable('course_instructors') && ! Schema::hasColumn('course_instructors', 'sort_order')) {
            Schema::table('course_instructors', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('role_or_title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('course_instructors') && Schema::hasColumn('course_instructors', 'sort_order')) {
            Schema::table('course_instructors', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }

        Schema::dropIfExists('course_slug_redirects');
        Schema::dropIfExists('course_partners');
        Schema::dropIfExists('course_testimonials');
        Schema::dropIfExists('course_batches');
        Schema::dropIfExists('course_faqs');
    }
};
