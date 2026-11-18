<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogMigrationLog;
use App\Services\BlogRecoveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBlogMigrationController extends Controller
{
    public function __construct(
        protected BlogRecoveryService $recoveryService
    ) {}

    /**
     * Display Migration Console Dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_discovered' => BlogMigrationLog::distinct('source_url')->count('source_url'),
            'total_imported' => Blog::whereNotNull('legacy_source_url')->count(),
            'fully_recovered' => Blog::where('recovery_status', Blog::RECOVERY_FULLY_RECOVERED)->count(),
            'incomplete' => Blog::where('recovery_status', Blog::RECOVERY_INCOMPLETE)->count(),
            'failed' => BlogMigrationLog::where('status', 'failed')->count(),
            'duplicates' => BlogMigrationLog::where('status', 'duplicate')->count(),
        ];

        $recentLogs = BlogMigrationLog::orderBy('created_at', 'desc')->limit(10)->get();
        $recentRecoveredBlogs = Blog::whereNotNull('legacy_source_url')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('admin.blogs.migration.index', compact('stats', 'recentLogs', 'recentRecoveredBlogs'));
    }

    /**
     * Trigger Migration Run.
     */
    public function start(Request $request): RedirectResponse
    {
        $limit = $request->filled('limit') ? (int) $request->input('limit') : null;
        $force = (bool) $request->input('force', false);

        $result = $this->recoveryService->runMigration($limit, $force);

        return redirect()->route('admin.blogs.migration.index')
            ->with('success', $result['message']);
    }

    /**
     * View Migration Audit Logs with filtering.
     */
    public function logs(Request $request): View
    {
        $query = BlogMigrationLog::orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('slug', 'like', "%{$term}%")
                    ->orWhere('title', 'like', "%{$term}%")
                    ->orWhere('source_url', 'like', "%{$term}%")
                    ->orWhere('reason', 'like', "%{$term}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('admin.blogs.migration.logs', compact('logs'));
    }
}
