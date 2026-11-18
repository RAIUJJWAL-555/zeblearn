<?php

namespace App\Observers;

use App\Models\Blog;
use App\Services\InternalLinkingService;

class BlogSpiderObserver
{
    /**
     * Handle the Blog "saved" event.
     */
    public function saved(Blog $blog): void
    {
        $service = app(InternalLinkingService::class);
        $service->invalidateCache('blog', $blog->id);

        if ($blog->isPublished()) {
            $recommendations = $service->getRecommendationsForBlog($blog);
            $linksCount = $recommendations['relatedBlogs']->count() + ($recommendations['primaryCourse'] ? 1 : 0);

            // Bi-directional reverse discovery: invalidate primary course cache so course reflects new blog
            if ($recommendations['primaryCourse']) {
                $service->invalidateCache('course', $recommendations['primaryCourse']->id);
            }

            $service->recordLog(
                'blog',
                $blog->id,
                'auto_linked',
                $linksCount,
                85,
                [
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'primary_course' => $recommendations['primaryCourse']?->name,
                    'related_count' => $recommendations['relatedBlogs']->count(),
                ]
            );
        } else {
            $service->recordLog(
                'blog',
                $blog->id,
                'purged',
                0,
                0,
                ['status' => $blog->status]
            );
        }
    }

    /**
     * Handle the Blog "deleted" event.
     */
    public function deleted(Blog $blog): void
    {
        $service = app(InternalLinkingService::class);
        $service->invalidateCache('blog', $blog->id);
        $service->recordLog('blog', $blog->id, 'deleted_purged', 0, 0);
    }
}
