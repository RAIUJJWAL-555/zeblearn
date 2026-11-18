<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Category;
use App\Services\BlogContentProcessor;
use App\Services\InternalLinkingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display the public blog catalog / knowledge base.
     */
    public function index(Request $request): View
    {
        $query = Blog::query()->with(['categories', 'author']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Category filter
        $activeCategory = null;
        if ($request->filled('category')) {
            $activeCategory = BlogCategory::where('slug', $request->input('category'))->first();
            if ($activeCategory) {
                $query->whereHas('categories', function ($q) use ($activeCategory) {
                    $q->where('blog_categories.id', $activeCategory->id);
                });
            }
        }

        // Tag filter
        $activeTag = null;
        if ($request->filled('tag')) {
            $activeTag = BlogTag::where('slug', $request->input('tag'))->first();
            if ($activeTag) {
                $query->whereHas('tags', function ($q) use ($activeTag) {
                    $q->where('blog_tags.id', $activeTag->id);
                });
            }
        }

        // Dynamically select the newest published article as the featured hero on the main listing
        $featuredBlog = null;
        $isMainIndex = ! $request->filled('search') && ! $request->filled('category') && ! $request->filled('tag');
        if ($isMainIndex) {
            $featuredBlog = Blog::newestPublished()->first();
            if ($featuredBlog) {
                $query->where($query->getModel()->qualifyColumn('id'), '!=', $featuredBlog->id);
            }
        }

        // Consistent database-level ordering: newest published first
        $blogs = $query->newestPublished()->paginate(12)->withQueryString();

        $categories = BlogCategory::active()
            ->withCount(['blogs' => function ($q) {
                $q->published();
            }])
            ->get();

        return view('pages.blog.index', compact('blogs', 'categories', 'activeCategory', 'activeTag', 'featuredBlog'));
    }

    /**
     * Display the category archive page (e.g. /category/sap-blogs/).
     */
    public function category(string $slug, ?int $page = null): View
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $blogs = $category->blogs()
            ->newestPublished()
            ->with(['categories', 'author'])
            ->paginate(12);

        $categories = BlogCategory::active()
            ->withCount(['blogs' => function ($q) {
                $q->published();
            }])
            ->get();

        return view('pages.blog.category', compact('category', 'blogs', 'categories'));
    }

    /**
     * Display the tag archive page (e.g. /tag/sap-fico/).
     */
    public function tag(string $slug, ?int $page = null): View
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();

        $blogs = $tag->blogs()
            ->newestPublished()
            ->with(['categories', 'author'])
            ->paginate(12);

        $categories = BlogCategory::active()
            ->withCount(['blogs' => function ($q) {
                $q->published();
            }])
            ->get();

        return view('pages.blog.tag', compact('tag', 'blogs', 'categories'));
    }

    /**
     * Display individual blog detail page.
     * Supports both /blogs/{slug} and legacy root /{slug}.
     */
    public function show(string $slug): View|RedirectResponse
    {
        // Check if slug matches a course category
        $courseCat = Category::where('slug', $slug)->first();
        if ($courseCat) {
            return redirect()->route('courses.index', ['category' => $courseCat->slug]);
        }

        // Special case for legacy static page if exists and not yet in database
        if ($slug === 'top-50-sap-ewm-interview-question-answer' && ! Blog::where('slug', $slug)->exists()) {
            return view('pages.blog.ewm-interview-questions');
        }

        // Query blog
        $blogQuery = Blog::with(['categories', 'tags', 'author'])->where('slug', $slug);

        // If not logged in as admin, require published status
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') {
            $blogQuery->published();
        }

        $blog = $blogQuery->first();

        if (! $blog) {
            abort(404);
        }

        // Increment view count quietly
        $blog->increment('view_count');

        // Centralized SEO Spider Web Recommendations
        $linkingService = app(InternalLinkingService::class);
        $spiderRecommendations = $linkingService->getRecommendationsForBlog($blog);

        $primaryCourse = $spiderRecommendations['primaryCourse'];
        $relatedBlogs = $spiderRecommendations['relatedBlogs'];
        $relatedCourses = $spiderRecommendations['relatedCourses'];
        $interviewQuestions = $spiderRecommendations['interviewQuestions'];
        $anchorMap = $spiderRecommendations['qualifyingInlineAnchors'];

        // Process content for TOC, responsive elements, and contextual internal links
        $contentProcessor = app(BlogContentProcessor::class);
        $processed = $contentProcessor->process($blog->content, $anchorMap, $blog->title);
        $processedContent = $processed['content'];
        $tableOfContents = $processed['toc'];
        $readingTime = $processed['reading_time'] ?: ($blog->reading_time ?: 4);

        $isPreview = ($blog->status !== Blog::STATUS_PUBLISHED);

        return view('pages.blog.show', compact(
            'blog',
            'relatedBlogs',
            'relatedCourses',
            'primaryCourse',
            'interviewQuestions',
            'isPreview',
            'processedContent',
            'tableOfContents',
            'readingTime'
        ));
    }

    /**
     * Show blog by /blogs/{slug} prefix: 301 Permanent Redirect to canonical legacy path to avoid duplicate indexable pages.
     */
    public function showByBlogPrefix(string $slug): RedirectResponse
    {
        return redirect()->to(url('/'.ltrim($slug, '/')), 301);
    }
}
