<?php

namespace App\Notifications;

use App\Models\Certificate;

class CertificateIssuedNotification extends LmsNotification
{
    public function __construct(public Certificate $certificate)
    {
        $courseName = $certificate->course ? $certificate->course->name : 'Course';

        parent::__construct([
            'title' => 'Certificate Earned!',
            'message' => "Congratulations! You have completed all required lessons in '{$courseName}'. Your certificate ({$certificate->certificate_number}) is now ready.",
            'url' => route('student.certificates.show', $certificate->id),
            'icon' => 'academic-cap',
            'category' => 'certificate',
            'badge_variant' => 'success',
            'action_label' => 'View Certificate',
            'metadata' => [
                'certificate_id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'course_id' => $certificate->course_id,
            ],
        ]);
    }
}
