<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(2),
            'content' => '<p>'.implode('</p><p>', fake()->paragraphs(4)).'</p>',
            'featured_image' => null,
            'featured_image_alt' => fake()->sentence(3),
            'featured_image_original_url' => null,
            'author_id' => null,
            'author_name' => fake()->name(),
            'status' => Blog::STATUS_PUBLISHED,
            'published_at' => now(),
            'scheduled_at' => null,
            'legacy_source_url' => null,
            'legacy_id' => null,
            'recovery_status' => Blog::RECOVERY_NONE,
            'recovery_notes' => null,
            'seo_title' => $title.' | ZebLearn',
            'meta_description' => fake()->sentence(16),
            'focus_keyword' => fake()->word(),
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'robots_index' => true,
            'robots_follow' => true,
            'schema_type' => 'BlogPosting',
            'view_count' => fake()->numberBetween(10, 500),
            'reading_time' => 4,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => Blog::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => Blog::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => Blog::STATUS_SCHEDULED,
            'scheduled_at' => now()->addDays(3),
            'published_at' => null,
        ]);
    }

    public function incomplete(): static
    {
        return $this->state(fn () => [
            'status' => Blog::STATUS_INCOMPLETE,
            'recovery_status' => Blog::RECOVERY_INCOMPLETE,
            'content' => null,
            'published_at' => null,
        ]);
    }
}
