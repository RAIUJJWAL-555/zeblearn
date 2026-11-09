<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'SAP '.fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-training',
            'category' => fake()->randomElement(['functional', 'technical', 'cloud_specialized']),
            'status' => fake()->randomElement(['available', 'upcoming', 'planned']),
            'short_description' => fake()->sentence(14),
            'description' => fake()->paragraph(3),
            'curriculum' => [
                'Module 1: Foundations',
                'Module 2: Core Configuration',
                'Module 3: Business Scenarios',
                'Module 4: Integration & Testing',
            ],
            'duration' => '3 Months',
            'delivery_mode' => 'Live Online + Real System Practice',
            'fee' => 25000,
            'featured' => false,
            'learner_count' => fake()->numberBetween(500, 2000),
            'badge_label' => null,
            'highlights' => ['Live Sandbox', 'Mock Interviews', 'Placement Assistance'],
            'target_audience' => ['Freshers', 'Domain Professionals'],
            'seo_title' => $name.' Course Online | ZebLearn',
            'seo_description' => fake()->sentence(20),
            'sort_order' => fake()->numberBetween(1, 20),
            'published_at' => now(),
        ];
    }

    /**
     * Indicate that the course is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
            'fee' => 25000,
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the course is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'upcoming',
            'fee' => null,
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the course is planned.
     */
    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planned',
            'fee' => null,
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the course is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }
}
