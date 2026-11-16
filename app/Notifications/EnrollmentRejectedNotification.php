<?php

namespace App\Notifications;

use App\Models\Enrollment;

class EnrollmentRejectedNotification extends LmsNotification
{
    public function __construct(public Enrollment $enrollment, ?string $reason = null)
    {
        $course = $enrollment->course;
        $courseName = $course ? $course->name : 'the requested course';
        $note = $reason ?: $enrollment->admin_note;

        $message = "Your enrollment request for '{$courseName}' was declined by the administration.";
        if ($note) {
            $message .= " Reason: {$note}";
        }

        parent::__construct([
            'title' => 'Enrollment Request Update',
            'message' => $message,
            'url' => route('student.dashboard'),
            'icon' => 'x-circle',
            'category' => 'enrollment',
            'badge_variant' => 'danger',
            'action_label' => 'View Dashboard',
            'metadata' => [
                'enrollment_id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
            ],
        ]);
    }
}
