<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Safely sync published status before removing workflow columns
        if (Schema::hasColumn('courses', 'publication_status')) {
            DB::table('courses')
                ->where('publication_status', 'published')
                ->whereNull('published_at')
                ->update(['published_at' => now()]);

            DB::table('courses')
                ->where('publication_status', '!=', 'published')
                ->update(['published_at' => null]);
        }

        // 2. Drop dependent review tables in reverse dependency order
        Schema::dropIfExists('course_publication_histories');
        Schema::dropIfExists('course_review_histories');
        Schema::dropIfExists('course_review_comments');
        Schema::dropIfExists('course_review_sections');
        Schema::dropIfExists('course_reviews');

        // 3. Drop review workflow columns and foreign keys from courses (MySQL / PostgreSQL)
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('courses', function (Blueprint $table) {
                foreach ([
                    'courses_review_status_index',
                    'courses_publication_status_index',
                    'courses_scheduled_publish_at_index',
                ] as $idx) {
                    try {
                        $table->dropIndex($idx);
                    } catch (Throwable $e) {
                        // Ignore
                    }
                }

                $foreignKeys = [
                    'courses_submitted_by_id_foreign',
                    'courses_reviewed_by_id_foreign',
                    'courses_approved_by_id_foreign',
                    'courses_published_by_id_foreign',
                ];

                foreach ($foreignKeys as $fk) {
                    try {
                        $table->dropForeign($fk);
                    } catch (Throwable $e) {
                        // Ignore if foreign key was already dropped or absent
                    }
                }

                $columnsToDrop = [
                    'review_status',
                    'publication_status',
                    'scheduled_publish_at',
                    'submitted_at',
                    'submitted_by_id',
                    'reviewed_at',
                    'reviewed_by_id',
                    'approved_at',
                    'approved_by_id',
                    'approved_content_hash',
                    'current_content_hash',
                    'published_by_id',
                    'rejection_reason',
                ];

                foreach ($columnsToDrop as $col) {
                    if (Schema::hasColumn('courses', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });

            // 4. Drop workflow permissions from users table if present
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'workflow_permissions')) {
                    $table->dropColumn('workflow_permissions');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op or recreate schema if needed
    }
};
