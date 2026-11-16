<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;

class AssignmentGradedNotification extends LmsNotification
{
    public function __construct(public AssignmentSubmission $submission)
    {
        $assignment = $submission->assignment;
        $course = $assignment?->course;
        $title = $assignment ? $assignment->title : 'Assignment';
        $courseName = $course ? $course->name : 'Course';
        $maxMarks = $assignment ? $assignment->max_marks : 100;
        $awarded = $submission->marks_awarded ?? 0;

        $targetUrl = ($course && $assignment)
            ? route('student.assignments.submissions.show', [$course->slug, $assignment->slug, $submission->id])
            : route('student.dashboard');

        parent::__construct([
            'title' => 'Assignment Graded',
            'message' => "Your submission for '{$title}' in '{$courseName}' has been evaluated. Score: {$awarded}/{$maxMarks}.",
            'url' => $targetUrl,
            'icon' => 'document-text',
            'category' => 'assignment',
            'badge_variant' => 'info',
            'action_label' => 'View Feedback',
            'metadata' => [
                'submission_id' => $submission->id,
                'assignment_id' => $submission->assignment_id,
                'marks_awarded' => $submission->marks_awarded,
            ],
        ]);
    }
}
