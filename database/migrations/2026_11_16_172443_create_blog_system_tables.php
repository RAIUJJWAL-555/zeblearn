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
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->string('featured_image_original_url')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name')->nullable();
            $table->string('status', 32)->default('draft')->index(); // draft, published, scheduled, incomplete
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('scheduled_at')->nullable();

            // Legacy Migration Tracking
            $table->string('legacy_source_url')->nullable()->index();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->string('recovery_status', 32)->default('none')->index(); // none, fully_recovered, incomplete, failed
            $table->text('recovery_notes')->nullable();

            // SEO & OpenGraph Metadata
            $table->string('seo_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('focus_keyword')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->string('schema_type', 32)->default('BlogPosting');

            // Metrics
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedInteger('reading_time')->default(3);

            $table->timestamps();

            $table->index(['status', 'published_at']);
        });

        Schema::create('blog_blog_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->foreignId('blog_category_id')->constrained('blog_categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['blog_id', 'blog_category_id'], 'blog_cat_unique');
        });

        Schema::create('blog_blog_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->foreignId('blog_tag_id')->constrained('blog_tags')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['blog_id', 'blog_tag_id'], 'blog_tag_unique');
        });

        Schema::create('blog_migration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->nullable()->index();
            $table->string('source_url')->index();
            $table->unsignedBigInteger('post_id')->nullable()->index();
            $table->string('slug')->nullable()->index();
            $table->string('title')->nullable();
            $table->string('status', 32)->default('discovered')->index(); // discovered, imported, incomplete, duplicate, failed, skipped
            $table->text('reason')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_migration_logs');
        Schema::dropIfExists('blog_blog_tag');
        Schema::dropIfExists('blog_blog_category');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};
