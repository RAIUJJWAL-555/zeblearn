<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentAnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * Display personal learning analytics for the authenticated student.
     */
    public function index(Request $request): View
    {
        /** @var User $student */
        $student = $request->user();

        $analytics = $this->analyticsService->getStudentPersonalAnalytics($student);

        return view('student.analytics.index', [
            'student' => $student,
            'analytics' => $analytics,
        ]);
    }
}
