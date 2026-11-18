<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogMigrationLog;
use App\Models\BlogTag;
use Illuminate\View\View;

class AdminBlogDashboardController extends Controller
{
    /**
     * Display the Blog Management Dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total' => Blog::count(),
            'published' => Blog::where('status', Blog::STATUS_PUBLISHED)->count(),
            'draft' => Blog::where('status', Blog::STATUS_DRAFT)->count(),
            'scheduled' => Blog::where('status', Blog::STATUS_SCHEDULED)->count(),
            'incomplete' => Blog::where('status', Blog::STATUS_INCOMPLETE)->count(),
            'categories' => BlogCategory::count(),
            'tags' => BlogTag::count(),
            'migration_logs' => BlogMigrationLog::count(),
            'fully_recovered' => Blog::where('recovery_status', Blog::RECOVERY_FULLY_RECOVERED)->count(),
        ];

        $recentlyUpdated = Blog::with(['categories', 'author'])
            ->orderBy('updated_at', 'desc')
            ->limit(8)
            ->get();

        $recentLogs = BlogMigrationLog::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.blogs.dashboard', compact('stats', 'recentlyUpdated', 'recentLogs'));
    }
}
