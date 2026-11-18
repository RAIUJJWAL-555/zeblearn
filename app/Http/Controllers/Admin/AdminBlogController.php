<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use App\Services\BlogContentProcessor;
use App\Services\BlogSanitizerService;
use App\Services\SitemapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminBlogController extends Controller
{
    public function __construct(
        protected BlogSanitizerService $sanitizer,
        protected SitemapService $sitemap
    ) {}

    /**
     * Display a paginated listing of blogs with filters.
     */
    public function index(Request $request): View
    {
        $query = Blog::with(['categories', 'author']);

        // Search by title, slug, content
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Filter by status
        if ($request->filled('status') && in_array($request->input('status'), array_keys(Blog::STATUSES))) {
            $query->where('status', $request->input('status'));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('blog_categories.id', $request->input('category_id'));
            });
        }

        // Filter by author
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->input('author_id'));
        }

        // Unified chronological sorting: default newest published date first, id DESC
        $sortBy = $request->input('sort');
        $sortOrder = $request->input('order', 'desc');

        if ($sortBy && in_array($sortBy, ['title', 'published_at', 'created_at', 'view_count'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc')->orderBy('id', 'desc');
        } else {
            $query->orderByRaw('COALESCE(published_at, created_at) DESC')->orderBy('id', 'desc');
        }

        $blogs = $query->paginate(20)->withQueryString();
        $categories = BlogCategory::orderBy('name')->get();
        $authors = User::whereIn('role', ['admin', 'instructor'])->orderBy('name')->get();

        return view('admin.blogs.index', compact('blogs', 'categories', 'authors'));
    }

    /**
     * Show form to create a new blog.
     */
    public function create(): View
    {
        $blog = new Blog([
            'status' => Blog::STATUS_DRAFT,
            'robots_index' => true,
            'robots_follow' => true,
            'schema_type' => 'BlogPosting',
        ]);

        $categories = BlogCategory::orderBy('name')->get();
        $tags = BlogTag::orderBy('name')->get();
        $authors = User::whereIn('role', ['admin', 'instructor'])->orderBy('name')->get();

        return view('admin.blogs.create', compact('blog', 'categories', 'tags', 'authors'));
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBlog($request);

        // Sanitize content
        $validated['content'] = $this->sanitizer->sanitize($validated['content'] ?? '');

        // Auto-generate sensible defaults if missing
        $validated = $this->fillDefaults($validated);

        // Handle image upload
        if ($request->hasFile('featured_image_file')) {
            $validated['featured_image'] = $this->uploadImage($request->file('featured_image_file'), $validated['slug']);
        }

        $blog = Blog::create($validated);

        if (! empty($request->input('categories'))) {
            $blog->categories()->sync($request->input('categories'));
        }

        if (! empty($request->input('tags'))) {
            $this->syncTags($blog, $request->input('tags'));
        }

        $this->sitemap->clearCache();

        return redirect()->route('admin.blogs.edit', $blog)
            ->with('success', 'Blog article created successfully.');
    }

    /**
     * Show form to edit an existing blog.
     */
    public function edit(Blog $blog): View
    {
        $categories = BlogCategory::orderBy('name')->get();
        $tags = BlogTag::orderBy('name')->get();
        $authors = User::whereIn('role', ['admin', 'instructor'])->orderBy('name')->get();

        return view('admin.blogs.edit', compact('blog', 'categories', 'tags', 'authors'));
    }

    /**
     * Update an existing blog in storage.
     */
    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $validated = $this->validateBlog($request, $blog->id);

        $validated['content'] = $this->sanitizer->sanitize($validated['content'] ?? '');
        $validated = $this->fillDefaults($validated);

        if ($request->hasFile('featured_image_file')) {
            $validated['featured_image'] = $this->uploadImage($request->file('featured_image_file'), $validated['slug']);
        }

        $blog->update($validated);

        $blog->categories()->sync($request->input('categories', []));
        $this->syncTags($blog, $request->input('tags', []));

        $this->sitemap->clearCache();

        return redirect()->route('admin.blogs.edit', $blog)
            ->with('success', 'Blog article updated successfully.');
    }

    /**
     * Direct action to publish a blog.
     */
    public function publish(Blog $blog): RedirectResponse
    {
        $blog->update([
            'status' => Blog::STATUS_PUBLISHED,
            'published_at' => $blog->published_at ?? now(),
        ]);

        $this->sitemap->clearCache();

        return back()->with('success', "Blog '{$blog->title}' is now published publicly.");
    }

    /**
     * Direct action to unpublish a blog.
     */
    public function unpublish(Blog $blog): RedirectResponse
    {
        $blog->update([
            'status' => Blog::STATUS_DRAFT,
        ]);

        $this->sitemap->clearCache();

        return back()->with('success', "Blog '{$blog->title}' moved back to Draft.");
    }

    /**
     * Preview blog in admin view without altering state.
     */
    public function preview(Blog $blog): View
    {
        $isPreview = true;

        $contentProcessor = app(BlogContentProcessor::class);
        $processed = $contentProcessor->process($blog->content);
        $processedContent = $processed['content'];
        $tableOfContents = $processed['toc'];
        $readingTime = $processed['reading_time'] ?: ($blog->reading_time ?: 4);

        $relatedBlogs = Blog::published()
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->limit(4)
            ->get();

        if ($relatedBlogs->isEmpty()) {
            $relatedBlogs = Blog::where('id', '!=', $blog->id)
                ->latest('id')
                ->limit(4)
                ->get();
        }

        return view('pages.blog.show', compact(
            'blog',
            'relatedBlogs',
            'isPreview',
            'processedContent',
            'tableOfContents',
            'readingTime'
        ));
    }

    /**
     * Remove blog from storage.
     */
    public function destroy(Blog $blog): RedirectResponse
    {
        $title = $blog->title;
        $blog->delete();
        $this->sitemap->clearCache();

        return redirect()->route('admin.blogs.index')
            ->with('success', "Blog '{$title}' has been deleted.");
    }

    /**
     * Validate incoming blog payload.
     */
    protected function validateBlog(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blogs', 'slug')->ignore($ignoreId)],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'featured_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'author_id' => ['nullable', 'exists:users,id'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Blog::STATUSES))],
            'published_at' => ['nullable', 'date'],
            'scheduled_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'focus_keyword' => ['nullable', 'string', 'max:150'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:255'],
            'robots_index' => ['nullable', 'boolean'],
            'robots_follow' => ['nullable', 'boolean'],
            'schema_type' => ['nullable', 'string', 'max:50'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:blog_categories,id'],
            'tags' => ['nullable', 'array'],
        ]);
    }

    /**
     * Auto-generate sensible defaults for slug and SEO metadata.
     */
    protected function fillDefaults(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if (empty($data['seo_title'])) {
            $data['seo_title'] = $data['title'].' | ZebLearn';
        }

        if (empty($data['meta_description'])) {
            $source = ! empty($data['excerpt']) ? $data['excerpt'] : strip_tags($data['content'] ?? '');
            $data['meta_description'] = Str::limit(trim($source), 160);
        }

        if (empty($data['canonical_url'])) {
            $data['canonical_url'] = url('/'.ltrim($data['slug'], '/'));
        }

        if (empty($data['og_title'])) {
            $data['og_title'] = $data['seo_title'];
        }

        if (empty($data['og_description'])) {
            $data['og_description'] = $data['meta_description'];
        }

        $data['robots_index'] = (bool) ($data['robots_index'] ?? true);
        $data['robots_follow'] = (bool) ($data['robots_follow'] ?? true);
        $data['schema_type'] = $data['schema_type'] ?? 'BlogPosting';

        // When actively publishing without an explicit published_at date, default to now
        if (($data['status'] ?? null) === Blog::STATUS_PUBLISHED && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }

    /**
     * Upload an image to storage/app/public/blogs/featured.
     */
    protected function uploadImage($file, string $slug): string
    {
        $dir = storage_path('app/public/blogs/featured');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $extension = $file->getClientOriginalExtension() ?: 'webp';
        $filename = Str::slug($slug).'-'.time().'.'.$extension;
        $file->move($dir, $filename);

        return 'storage/blogs/featured/'.$filename;
    }

    /**
     * Sync string or ID tags to blog.
     */
    protected function syncTags(Blog $blog, array $tags): void
    {
        $tagIds = [];
        foreach ($tags as $tagItem) {
            if (is_numeric($tagItem)) {
                $tagIds[] = (int) $tagItem;
            } elseif (is_string($tagItem) && ! empty(trim($tagItem))) {
                $name = trim($tagItem);
                $tag = BlogTag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                );
                $tagIds[] = $tag->id;
            }
        }

        $blog->tags()->sync(array_unique($tagIds));
    }
}
