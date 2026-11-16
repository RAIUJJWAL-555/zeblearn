<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\InstructorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorDashboardController extends Controller
{
    public function __construct(
        protected InstructorService $instructorService
    ) {}

    /**
     * Display the instructor dashboard with authentic assigned academic metrics.
     */
    public function index(Request $request): View
    {
        /** @var User $instructor */
        $instructor = $request->user();

        $metrics = $this->instructorService->getInstructorMetrics($instructor);

        return view('instructor.dashboard', array_merge([
            'instructor' => $instructor,
        ], $metrics));
    }
}
