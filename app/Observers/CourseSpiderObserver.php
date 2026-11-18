<?php

namespace App\Observers;

use App\Models\Course;
use App\Services\InternalLinkingService;

class CourseSpiderObserver
{
    /**
     * Handle the Course "saved" event.
     */
    public function saved(Course $course): void
    {
        $service = app(InternalLinkingService::class);
        $service->invalidateCache('course', $course->id);

        if ($course->isPublished()) {
            $recommendations = $service->getRecommendationsForCourse($course);
            $linksCount = $recommendations['siblingCourses']->count() + $recommendations['relatedBlogs']->count();

            $service->recordLog(
                'course',
                $course->id,
                'auto_linked',
                $linksCount,
                90,
                [
                    'name' => $course->name,
                    'slug' => $course->slug,
                    'sibling_count' => $recommendations['siblingCourses']->count(),
                    'related_blogs_count' => $recommendations['relatedBlogs']->count(),
                ]
            );
        } else {
            $service->recordLog(
                'course',
                $course->id,
                'purged',
                0,
                0,
                ['status' => $course->status]
            );
        }
    }

    /**
     * Handle the Course "deleted" event.
     */
    public function deleted(Course $course): void
    {
        $service = app(InternalLinkingService::class);
        $service->invalidateCache('course', $course->id);
        $service->recordLog('course', $course->id, 'deleted_purged', 0, 0);
    }
}
