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
        // 1. Add instructor profile fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('designation')->nullable()->after('bio');
            $table->string('specialization')->nullable()->after('designation');
            $table->unsignedTinyInteger('experience_years')->nullable()->after('specialization');
            $table->string('qualification')->nullable()->after('experience_years');
        });

        // 2. Create course_instructors pivot table
        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_or_title')->default('Lead Instructor')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['course_id', 'user_id'], 'course_user_unique');
            $table->index('course_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_instructors');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'designation',
                'specialization',
                'experience_years',
                'qualification',
            ]);
        });
    }
};
