<?php

namespace App\Notifications;

use App\Models\Enrollment;

class NewEnrollmentRequestNotification extends LmsNotification
{
    public function __construct(public Enrollment $enrollment)
    {
        $studentName = $enrollment->user ? $enrollment->user->name : 'A student';
        $courseName = $enrollment->course ? $enrollment->course->name : 'a course';

        parent::__construct([
            'title' => 'New Enrollment Request',
            'message' => "{$studentName} has requested enrollment in '{$courseName}'. Review and grant access.",
            'url' => route('admin.enrollments.show', $enrollment->id),
            'icon' => 'academic-cap',
            'category' => 'enrollment',
            'badge_variant' => 'info',
            'action_label' => 'Review Request',
            'metadata' => [
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id,
            ],
        ]);
    }
}
