<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Course;
use App\Models\InternalLinkingLog;
use App\Models\InternalLinkOverride;
use App\Models\Lead;
use App\Services\InternalLinkingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSeoSpiderController extends Controller
{
    public function __construct(
        protected InternalLinkingService $spiderService
    ) {}

    /**
     * Display the SEO Spider Web dashboard.
     */
    public function index(Request $request): View
    {
        $stats = $this->spiderService->getSpiderWebStats();
        $orphans = $this->spiderService->getOrphanPages();

        $recentLogs = InternalLinkingLog::latest()
            ->limit(15)
            ->get();

        $overrides = InternalLinkOverride::latest()
            ->paginate(15);

        $courses = Course::published()->orderBy('name')->get();
        $sampleBlogs = Blog::published()->latest('id')->limit(30)->get();

        $leadsCount = Lead::count();
        $recentLeads = Lead::latest()->limit(5)->get();

        return view('admin.seo.internal-linking', compact(
            'stats',
            'orphans',
            'recentLogs',
            'overrides',
            'courses',
            'sampleBlogs',
            'leadsCount',
            'recentLeads'
        ));
    }

    /**
     * Trigger dynamic recalculation of internal link cache.
     */
    public function recalculate(Request $request): RedirectResponse
    {
        $this->spiderService->invalidateCache();

        $courses = Course::published()->get();
        foreach ($courses as $c) {
            $this->spiderService->getRecommendationsForCourse($c);
        }

        $sampleBlogs = Blog::published()->limit(50)->get();
        foreach ($sampleBlogs as $b) {
            $this->spiderService->getRecommendationsForBlog($b);
        }

        $this->spiderService->recordLog('system', null, 'manual_recalculated', $courses->count() + $sampleBlogs->count(), 100, [
            'initiated_by' => $request->user()->name,
        ]);

        return redirect()->route('admin.seo.spider')
            ->with('success', 'SEO Spider Web relationship graph successfully refreshed and reconciled.');
    }

    /**
     * Store a manual linking override or exclusion.
     */
    public function storeOverride(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_type' => ['required', 'string', 'in:blog,course,category'],
            'source_id' => ['nullable', 'integer'],
            'target_type' => ['required', 'string', 'in:blog,course,category'],
            'target_id' => ['required', 'integer'],
            'action' => ['required', 'string', 'in:pin,exclude'],
            'custom_anchor_text' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        InternalLinkOverride::updateOrCreate(
            [
                'source_type' => $validated['source_type'],
                'source_id' => $validated['source_id'],
                'target_type' => $validated['target_type'],
                'target_id' => $validated['target_id'],
            ],
            [
                'action' => $validated['action'],
                'custom_anchor_text' => $validated['custom_anchor_text'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $this->spiderService->invalidateCache();

        return redirect()->route('admin.seo.spider')
            ->with('success', 'Internal link rule override successfully saved.');
    }

    /**
     * Remove a link override.
     */
    public function destroyOverride(InternalLinkOverride $override): RedirectResponse
    {
        $override->delete();
        $this->spiderService->invalidateCache();

        return redirect()->route('admin.seo.spider')
            ->with('success', 'Internal link override deleted.');
    }
}
