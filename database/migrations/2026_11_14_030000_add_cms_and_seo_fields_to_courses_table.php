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
        Schema::table('courses', function (Blueprint $table) {
            // Media & Visual Assets
            $table->string('thumbnail_path')->nullable()->after('tag_badge');
            $table->string('banner_path')->nullable()->after('thumbnail_path');

            // Hero Section Overrides
            $table->string('hero_heading')->nullable()->after('banner_path');
            $table->string('hero_subtitle')->nullable()->after('hero_heading');
            $table->text('hero_description')->nullable()->after('hero_subtitle');
            $table->string('hero_cta_text')->nullable()->after('hero_description');
            $table->string('hero_cta_url')->nullable()->after('hero_cta_text');
            $table->string('secondary_cta_text')->nullable()->after('hero_cta_url');
            $table->string('secondary_cta_url')->nullable()->after('secondary_cta_text');

            // Academic & Learning Outcomes
            $table->json('learning_outcomes')->nullable()->after('highlights');
            $table->json('skills_covered')->nullable()->after('learning_outcomes');
            $table->text('course_objectives')->nullable()->after('skills_covered');
            $table->text('prerequisites')->nullable()->after('course_objectives');
            $table->text('target_audience_text')->nullable()->after('prerequisites');
            $table->text('career_opportunities')->nullable()->after('target_audience_text');
            $table->json('why_choose_us')->nullable()->after('career_opportunities');

            // Certification Authority Metadata
            $table->string('certification_name')->nullable()->after('why_choose_us');
            $table->string('certification_authority')->nullable()->after('certification_name');
            $table->text('certification_description')->nullable()->after('certification_authority');
            $table->string('certification_image_path')->nullable()->after('certification_description');
            $table->json('certification_skills')->nullable()->after('certification_image_path');
            $table->json('certification_benefits')->nullable()->after('certification_skills');

            // Dynamic Page Section Order & Visibility
            $table->json('section_config')->nullable()->after('certification_benefits');

            // Advanced SEO & Indexing Directives
            $table->string('meta_keywords')->nullable()->after('seo_description');
            $table->string('og_image_path')->nullable()->after('meta_keywords');
            $table->string('robots_directive')->default('index, follow')->after('og_image_path');
            $table->string('canonical_url')->nullable()->after('robots_directive');

            // Optimized indexes for scalable directory queries
            $table->index(['category', 'status', 'published_at'], 'courses_cat_status_pub_idx');
            $table->index(['status', 'published_at', 'sort_order'], 'courses_status_pub_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_cat_status_pub_idx');
            $table->dropIndex('courses_status_pub_sort_idx');

            $table->dropColumn([
                'thumbnail_path',
                'banner_path',
                'hero_heading',
                'hero_subtitle',
                'hero_description',
                'hero_cta_text',
                'hero_cta_url',
                'secondary_cta_text',
                'secondary_cta_url',
                'learning_outcomes',
                'skills_covered',
                'course_objectives',
                'prerequisites',
                'target_audience_text',
                'career_opportunities',
                'why_choose_us',
                'certification_name',
                'certification_authority',
                'certification_description',
                'certification_image_path',
                'certification_skills',
                'certification_benefits',
                'section_config',
                'meta_keywords',
                'og_image_path',
                'robots_directive',
                'canonical_url',
            ]);
        });
    }
};
