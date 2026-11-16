<?php

namespace App\Notifications;

use App\Models\Course;

class CourseAssignedNotification extends LmsNotification
{
    public function __construct(public Course $course, ?string $roleOrTitle = null)
    {
        $roleText = $roleOrTitle ? " as {$roleOrTitle}" : '';

        parent::__construct([
            'title' => 'Course Assigned',
            'message' => "You have been assigned to instruct '{$course->name}'{$roleText}.",
            'url' => route('instructor.courses.show', $course->id),
            'icon' => 'academic-cap',
            'category' => 'course',
            'badge_variant' => 'info',
            'action_label' => 'View Course',
            'metadata' => [
                'course_id' => $course->id,
            ],
        ]);
    }
}
