<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;

class NewAssignmentSubmissionNotification extends LmsNotification
{
    public function __construct(public AssignmentSubmission $submission)
    {
        $assignment = $submission->assignment;
        $student = $submission->user;
        $course = $assignment?->course;
        $title = $assignment ? $assignment->title : 'Assignment';
        $courseName = $course ? $course->name : 'Course';
        $studentName = $student ? $student->name : 'A student';

        $url = $assignment
            ? route('instructor.assignments.submissions.show', [$assignment->id, $submission->id])
            : route('instructor.dashboard');

        parent::__construct([
            'title' => 'New Submission to Review',
            'message' => "{$studentName} submitted an attempt for '{$title}' in '{$courseName}'.",
            'url' => $url,
            'icon' => 'document-text',
            'category' => 'assignment',
            'badge_variant' => 'info',
            'action_label' => 'Review Submission',
            'metadata' => [
                'submission_id' => $submission->id,
                'assignment_id' => $submission->assignment_id,
                'user_id' => $submission->user_id,
            ],
        ]);
    }
}
