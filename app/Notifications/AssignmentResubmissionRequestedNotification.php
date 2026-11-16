<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;

class AssignmentResubmissionRequestedNotification extends LmsNotification
{
    public function __construct(public AssignmentSubmission $submission)
    {
        $assignment = $submission->assignment;
        $course = $assignment?->course;
        $title = $assignment ? $assignment->title : 'Assignment';
        $courseName = $course ? $course->name : 'Course';

        $targetUrl = ($course && $assignment)
            ? route('student.assignments.show', [$course->slug, $assignment->slug])
            : route('student.dashboard');

        $message = "Your instructor requested changes on your submission for '{$title}' in '{$courseName}'.";
        if ($submission->feedback) {
            $message .= " Feedback: {$submission->feedback}";
        }

        parent::__construct([
            'title' => 'Resubmission Requested',
            'message' => $message,
            'url' => $targetUrl,
            'icon' => 'alert',
            'category' => 'assignment',
            'badge_variant' => 'warning',
            'action_label' => 'Review & Resubmit',
            'metadata' => [
                'submission_id' => $submission->id,
                'assignment_id' => $submission->assignment_id,
            ],
        ]);
    }
}
