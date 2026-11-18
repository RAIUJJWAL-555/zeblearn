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
        // 1. Add workflow_permissions to users table
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'workflow_permissions')) {
                $table->json('workflow_permissions')->nullable()->after('preferences');
            }
        });

        // 2. Add review and publication workflow fields to courses table
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'review_status')) {
                $table->string('review_status', 30)->default('draft')->after('status')->index();
            }
            if (! Schema::hasColumn('courses', 'publication_status')) {
                $table->string('publication_status', 30)->default('unpublished')->after('review_status')->index();
            }
            if (! Schema::hasColumn('courses', 'scheduled_publish_at')) {
                $table->timestamp('scheduled_publish_at')->nullable()->after('published_at')->index();
            }
            if (! Schema::hasColumn('courses', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('scheduled_publish_at');
            }
            if (! Schema::hasColumn('courses', 'submitted_by_id')) {
                $table->foreignId('submitted_by_id')->nullable()->after('submitted_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('courses', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('submitted_by_id');
            }
            if (! Schema::hasColumn('courses', 'reviewed_by_id')) {
                $table->foreignId('reviewed_by_id')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('courses', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('reviewed_by_id');
            }
            if (! Schema::hasColumn('courses', 'approved_by_id')) {
                $table->foreignId('approved_by_id')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('courses', 'approved_content_hash')) {
                $table->string('approved_content_hash', 64)->nullable()->after('approved_by_id');
            }
            if (! Schema::hasColumn('courses', 'current_content_hash')) {
                $table->string('current_content_hash', 64)->nullable()->after('approved_content_hash');
            }
            if (! Schema::hasColumn('courses', 'published_by_id')) {
                $table->foreignId('published_by_id')->nullable()->after('current_content_hash')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('courses', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('published_by_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $cols = [
                'published_by_id',
                'approved_by_id',
                'reviewed_by_id',
                'submitted_by_id',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('courses', $col)) {
                    $table->dropForeign([$col]);
                }
            }

            $table->dropColumn(array_filter([
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
            ], fn ($c) => Schema::hasColumn('courses', $c)));
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'workflow_permissions')) {
                $table->dropColumn('workflow_permissions');
            }
        });
    }
};
