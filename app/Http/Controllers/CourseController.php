<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Course;
use App\Services\CourseCmsService;
use App\Services\InternalLinkingService;
use App\Services\TrustProofService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $sort = $request->query('sort', 'newest');
        $currentPage = (int) $request->query('page', 1);

        // Determine SEO Robots Indexation Directive
        $hasSearch = ! empty($search);
        $hasFilters = ($category !== 'all') || ($status !== 'all') || ($deliveryMode !== 'all') || ($sort !== 'newest');
        $isPaginated = $currentPage > 1;

        if ($hasSearch || $hasFilters || $isPaginated) {
            $robotsDirective = 'noindex, follow';
        } else {
            $robotsDirective = 'index, follow, max-image-preview:large';
        }

        // Base Query
        $query = Course::query()->published();

        // 1. Comprehensive Keyword Search (Title, Slug, Category, Description, Skills, Highlights, Modules, Lessons)
        if ($hasSearch) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('skills_covered', 'like', "%{$search}%")
                    ->orWhere('highlights', 'like', "%{$search}%")
                    ->orWhere('tag_badge', 'like', "%{$search}%")
                    ->orWhere('badge_label', 'like', "%{$search}%")
                    ->orWhereHas('modules', function ($mq) use ($search) {
                        $mq->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    })
                    ->orWhereHas('lessons', function ($lq) use ($search) {
                        $lq->where('lessons.title', 'like', "%{$search}%");
                    });
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

        // 5. Sorting (Default: Newest / Recently Added First)
        match ($sort) {
            'popular' => $query->orderBy('learner_count', 'desc')->orderBy('id', 'desc'),
            'price_asc' => $query->orderBy('fee', 'asc'),
            'price_desc' => $query->orderBy('fee', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'oldest' => $query->orderBy('created_at', 'asc')->orderBy('id', 'asc'),
            // 'newest' or default: Recently added courses shown first
            default => $query->orderBy('created_at', 'desc')->orderBy('id', 'desc'),
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
        $course = Course::where('slug', 'sap-fico-on-s-4-hana')
            ->orWhere('slug', 'sap-fico-training')
            ->orWhere('slug', 'sap-fico')
            ->first();

        if (! $course) {
            $course = Course::firstOrFail();
        }

        return $this->show($request, $course);
    }

    /**
     * Display individual course landing page using category-based URL.
     * Example: /{category}/{course} (e.g. /sap-functional/sap-fico-on-s-4-hana)
     */
    public function showByCategory(Request $request, string $category, string $course): View|RedirectResponse
    {
        $cleanCourse = trim($course, "/ \t\n\r\0\x0B");
        $cleanCategory = trim($category, "/ \t\n\r\0\x0B");

        $courseModel = Course::where('slug', $cleanCourse)
            ->orWhere('slug', $cleanCourse.'/')
            ->first();

        if (! $courseModel) {
            $newSlug = app(CourseCmsService::class)->resolveRedirect($cleanCourse);
            if ($newSlug) {
                $cleanNewSlug = trim($newSlug, "/ \t\n\r\0\x0B");
                if ($cleanNewSlug !== $cleanCourse) {
                    return redirect()->to(url('/'.$cleanCategory.'/'.$cleanNewSlug), 301);
                }
            }

            abort(404);
        }

        return $this->show($request, $courseModel);
    }

    /**
     * Display a published course LMS landing page.
     */
    public function show(Request $request, Course $course): View
    {
        // Only published courses are accessible to public visitors
        if (! $course->isPublished()) {
            if (! $request->user()?->isAdmin() && ! $request->user()?->isCourseEditor()) {
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

        // SEO Spider Web Dynamic Relationships
        $linkingService = app(InternalLinkingService::class);
        $spiderRecommendations = $linkingService->getRecommendationsForCourse($course);

        return view('pages.courses.landing', [
            'course' => $course,
            'enrollment' => $enrollment,
            'relatedCourses' => $spiderRecommendations['siblingCourses'],
            'relatedBlogs' => $spiderRecommendations['relatedBlogs'],
            'interviewQuestions' => $spiderRecommendations['interviewQuestions'],
            'isPreview' => false,
            'trustRecords' => $trustRecords,
        ]);
    }

    /**
     * Live search API for interactive course search in header, search modal, and catalog.
     */
    public function searchApi(Request $request): JsonResponse
    {
        $search = trim($request->input('q', $request->input('search', '')));

        if (empty($search)) {
            // Return top recently added courses (cached 5 minutes)
            $courses = Cache::remember('search_default_top_courses', 300, function () {
                return Course::query()
                    ->published()
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->limit(6)
                    ->get();
            });

            // Return latest published blogs (cached 5 minutes)
            $blogs = Cache::remember('search_default_top_blogs', 300, function () {
                return Blog::query()
                    ->newestPublished()
                    ->with('categories')
                    ->limit(4)
                    ->get();
            });
        } else {
            $courses = Course::query()
                ->published()
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('skills_covered', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('tag_badge', 'like', "%{$search}%")
                        ->orWhere('badge_label', 'like', "%{$search}%")
                        ->orWhereHas('modules', function ($mq) use ($search) {
                            $mq->where('title', 'like', "%{$search}%");
                        });
                })
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->limit(8)
                ->get();

            $blogs = Blog::query()
                ->newestPublished()
                ->with('categories')
                ->search($search)
                ->limit(6)
                ->get();
        }

        $courseResults = $courses->map(function (Course $course) {
            return [
                'id' => $course->id,
                'name' => $course->name,
                'slug' => $course->slug,
                'url' => $course->url,
                'category_label' => $course->category_label,
                'display_price' => $course->display_price,
                'duration' => $course->duration ?? '3 Months',
                'delivery_mode' => $course->delivery_mode ?? 'Live Online',
                'is_recently_added' => $course->isRecentlyAdded(),
                'tag_badge' => $course->tag_badge ?? $course->badge_label,
            ];
        });

        $blogResults = $blogs->map(function (Blog $blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'url' => route('blogs.show', ['slug' => $blog->slug]),
                'reading_time' => ($blog->reading_time ?? 5).' min read',
                'category_label' => $blog->categories->first()?->name ?? 'Article',
                'published_date' => $blog->published_at ? $blog->published_at->format('M d, Y') : null,
                'author_name' => $blog->effective_author_name,
            ];
        });

        return response()->json([
            'query' => $search,
            'count' => $courseResults->count() + $blogResults->count(),
            'courses' => $courseResults,
            'blogs' => $blogResults,
        ]);
    }

    /**
     * Store a verified student review for a course.
     */
    public function storeReview(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:150'],
            'student_title_or_company' => ['nullable', 'string', 'max:150'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'review_text' => ['required', 'string', 'min:15', 'max:2000'],
        ]);

        $course->testimonials()->create([
            'student_name' => trim($validated['student_name']),
            'student_title_or_company' => ! empty($validated['student_title_or_company']) ? trim($validated['student_title_or_company']) : 'Verified Student',
            'rating' => (float) $validated['rating'],
            'review_text' => trim($validated['review_text']),
            'is_verified' => true,
            'is_published' => true,
            'sort_order' => 1,
        ]);

        // Increment dynamic reviews_count on the course
        if ($course->reviews_count !== null) {
            $course->increment('reviews_count');
        } else {
            $course->update(['reviews_count' => $course->dynamic_reviews_count + 1]);
        }

        $this->cmsService->invalidateCourseCache($course);

        return redirect()->to(route('courses.show', $course).'#reviews')
            ->with('success', 'Thank you! Your verified review has been submitted and added successfully.');
    }
}
