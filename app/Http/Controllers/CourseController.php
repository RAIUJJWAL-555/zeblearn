<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CourseCmsService;
use App\Services\TrustProofService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        protected CourseCmsService $cmsService
    ) {}

    /**
     * Display the scalable course academy catalog and directory.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', $request->input('q', '')));
        $category = $request->query('category', 'all');
        $status = $request->query('status', 'all');
        $deliveryMode = $request->query('delivery_mode', 'all');
        $sort = $request->query('sort', 'default');
        $currentPage = (int) $request->query('page', 1);

        // Determine SEO Robots Indexation Directive
        $hasSearch = ! empty($search);
        $hasFilters = ($category !== 'all') || ($status !== 'all') || ($deliveryMode !== 'all') || ($sort !== 'default');
        $isPaginated = $currentPage > 1;

        if ($hasSearch || $hasFilters || $isPaginated) {
            $robotsDirective = 'noindex, follow';
        } else {
            $robotsDirective = 'index, follow, max-image-preview:large';
        }

        // Base Query
        $query = Course::query()->published();

        // 1. Keyword Search
        if ($hasSearch) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('skills_covered', 'like', "%{$search}%");
            });
        }

        // 2. Category Filter
        if ($category && $category !== 'all') {
            $query->byCategory($category);
        }

        // 3. Status Filter
        if ($status && $status !== 'all') {
            $query->byStatus($status);
        }

        // 4. Delivery Mode Filter
        if ($deliveryMode && $deliveryMode !== 'all') {
            $query->where('delivery_mode', 'like', "%{$deliveryMode}%");
        }

        // 5. Sorting
        match ($sort) {
            'newest' => $query->orderBy('published_at', 'desc')->orderBy('id', 'desc'),
            'price_asc' => $query->orderBy('fee', 'asc'),
            'price_desc' => $query->orderBy('fee', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'popular' => $query->orderBy('learner_count', 'desc'),
            default => $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc'),
        };

        // Paginate results (12 per page)
        $courses = $query->paginate(12)->withQueryString();

        // All published courses for directory statistics, client filter chips & schemas
        $allCourses = Course::query()->published()->orderBy('sort_order', 'asc')->get();

        $categoryMeta = Course::CATEGORIES[$category] ?? Course::CATEGORIES['sap-erp'];

        $userEnrollments = $request->user()
            ? $request->user()->enrollments()->get()->keyBy('course_id')
            : collect();

        return view('pages.courses.index', [
            'courses' => $courses,
            'allCourses' => $allCourses,
            'categories' => Course::CATEGORIES,
            'categoryMeta' => $categoryMeta,
            'availableCount' => $allCourses->where('status', 'available')->count(),
            'upcomingCount' => $allCourses->where('status', 'upcoming')->count(),
            'plannedCount' => $allCourses->where('status', 'planned')->count(),
            'activeCategory' => $category,
            'activeStatus' => $status,
            'activeDeliveryMode' => $deliveryMode,
            'activeSort' => $sort,
            'searchQuery' => $search,
            'robotsDirective' => $robotsDirective,
            'userEnrollments' => $userEnrollments,
        ]);
    }

    /**
     * Display the flagship SAP FICO course landing & LMS overview.
     */
    public function fico(Request $request): View
    {
        $course = Course::where('slug', 'sap-fico-training')->first();

        if (! $course) {
            return view('pages.courses.sap-fico', [
                'course' => null,
                'enrollment' => null,
            ]);
        }

        return $this->show($request, $course);
    }

    /**
     * Display a published course LMS landing page.
     */
    public function show(Request $request, Course $course): View
    {
        // If course is draft / unpublished and user is not an administrator, return 404
        if ($course->published_at === null || $course->published_at > now()) {
            if (! $request->user()?->isAdmin()) {
                abort(404);
            }
        }

        $user = $request->user();
        $enrollment = $user
            ? $user->enrollments()->where('course_id', $course->id)->first()
            : null;

        $course->load([
            'modules' => function ($query) {
                $query->published()->ordered()->withCount('lessons')->with(['lessons' => function ($lq) {
                    $lq->published()->ordered();
                }]);
            },
            'instructors' => function ($query) {
                $query->orderByPivot('sort_order', 'asc');
            },
            'activeFaqs',
            'activeBatches',
            'publishedTestimonials',
            'activePartners',
        ]);

        $trustRecords = app(TrustProofService::class)->getRecordsForCourse($course);

        return view('pages.courses.landing', [
            'course' => $course,
            'enrollment' => $enrollment,
            'relatedCourses' => $course->relatedCourses(3),
            'isPreview' => false,
            'trustRecords' => $trustRecords,
        ]);
    }
}
