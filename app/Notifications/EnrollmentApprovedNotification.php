<?php

namespace App\Notifications;

use App\Models\Enrollment;

class EnrollmentApprovedNotification extends LmsNotification
{
    public function __construct(public Enrollment $enrollment)
    {
        $course = $enrollment->course;
        $courseName = $course ? $course->name : 'your enrolled course';
        $courseSlug = $course ? $course->slug : '';

        parent::__construct([
            'title' => 'Enrollment Approved!',
            'message' => "Your enrollment in '{$courseName}' has been approved. You now have full access to all course modules and lessons.",
            'url' => $courseSlug ? route('student.courses.show', $courseSlug) : route('student.dashboard'),
            'icon' => 'check-circle',
            'category' => 'enrollment',
            'badge_variant' => 'success',
            'action_label' => 'Start Learning',
            'metadata' => [
                'enrollment_id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
            ],
        ]);
    }
}
