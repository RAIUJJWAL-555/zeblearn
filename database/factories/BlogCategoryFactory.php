<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BlogCategory>
 */
class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(10),
            'seo_title' => ucfirst($name).' Guides & Tutorials | ZebLearn',
            'meta_description' => fake()->sentence(15),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
