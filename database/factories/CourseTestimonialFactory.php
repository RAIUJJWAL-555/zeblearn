<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseTestimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseTestimonial>
 */
class CourseTestimonialFactory extends Factory
{
    protected $model = CourseTestimonial::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'student_name' => fake()->name(),
            'student_title_or_company' => fake()->jobTitle().' @ '.fake()->company(),
            'avatar_path' => null,
            'review_text' => fake()->paragraph(),
            'rating' => fake()->randomElement([4.8, 4.9, 5.0]),
            'video_url' => null,
            'is_verified' => true,
            'is_published' => true,
            'sort_order' => 10,
        ];
    }
}
