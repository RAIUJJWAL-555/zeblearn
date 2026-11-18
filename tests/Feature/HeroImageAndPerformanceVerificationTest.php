<?php

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('homepage renders hero typography and lead form with crisp light UI', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('LEARN SAP ONLINE');
    $response->assertSee('GET HIRED');
    $response->assertSee('BOOK YOUR FREE LIVE DEMO');
    $response->assertSee('TRANSFORM YOUR CAREER WITH THE BEST');
    $response->assertSee('Live Online Batches');
    $response->assertSee('Real SAP System Practice');
    $response->assertSee('Mock Interviews');
    $response->assertSee('Placement Partner');
});

test('cache invalidation works when blog is created, updated, or deleted', function () {
    Cache::put('blog_active_categories_with_count', 'cached_dummy_data', 600);
    Cache::put('search_default_top_blogs', 'cached_dummy_blogs', 600);

    expect(Cache::has('blog_active_categories_with_count'))->toBeTrue();
    expect(Cache::has('search_default_top_blogs'))->toBeTrue();

    // Create a new blog
    $admin = User::factory()->create();
    $blog = Blog::create([
        'title' => 'Cache Test Blog Post',
        'slug' => 'cache-test-blog-post',
        'content' => 'Content for testing cache invalidation.',
        'status' => Blog::STATUS_PUBLISHED,
        'published_at' => now(),
        'author_id' => $admin->id,
    ]);

    // Cache should be automatically purged
    expect(Cache::has('blog_active_categories_with_count'))->toBeFalse();
    expect(Cache::has('search_default_top_blogs'))->toBeFalse();

    // Repopulate cache
    Cache::put('blog_active_categories_with_count', 'cached_dummy_data', 600);

    // Update blog
    $blog->update(['title' => 'Updated Cache Test Blog Title']);
    expect(Cache::has('blog_active_categories_with_count'))->toBeFalse();

    // Repopulate and delete
    Cache::put('blog_active_categories_with_count', 'cached_dummy_data', 600);
    $blog->delete();
    expect(Cache::has('blog_active_categories_with_count'))->toBeFalse();
});

test('cache invalidation works when blog category is created, updated, or deleted', function () {
    Cache::put('blog_active_categories_with_count', 'cached_dummy_data', 600);
    expect(Cache::has('blog_active_categories_with_count'))->toBeTrue();

    $category = BlogCategory::create([
        'name' => 'Cache Test Category',
        'slug' => 'cache-test-category',
        'is_active' => true,
    ]);

    expect(Cache::has('blog_active_categories_with_count'))->toBeFalse();

    Cache::put('blog_active_categories_with_count', 'cached_dummy_data', 600);
    $category->update(['name' => 'Renamed Cache Test Category']);
    expect(Cache::has('blog_active_categories_with_count'))->toBeFalse();

    Cache::put('blog_active_categories_with_count', 'cached_dummy_data', 600);
    $category->delete();
    expect(Cache::has('blog_active_categories_with_count'))->toBeFalse();
});

test('cache invalidation works when course is created, updated, or deleted', function () {
    Cache::put('search_default_top_courses', 'cached_dummy_courses', 300);
    expect(Cache::has('search_default_top_courses'))->toBeTrue();

    $course = Course::create([
        'name' => 'Cache Invalidation Verification Course',
        'slug' => 'cache-invalidation-verification-course',
        'short_description' => 'A comprehensive course on SAP ERP.',
        'description' => 'Detailed syllabus and enterprise training overview.',
        'category' => 'sap-erp',
        'status' => 'available',
        'price' => 25000,
        'published_at' => now(),
    ]);

    expect(Cache::has('search_default_top_courses'))->toBeFalse();

    Cache::put('search_default_top_courses', 'cached_dummy_courses', 300);
    $course->update(['name' => 'Updated Course Title']);
    expect(Cache::has('search_default_top_courses'))->toBeFalse();

    Cache::put('search_default_top_courses', 'cached_dummy_courses', 300);
    $course->delete();
    expect(Cache::has('search_default_top_courses'))->toBeFalse();
});
