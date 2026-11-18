<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\Course;
use App\Models\User;
use App\Observers\BlogSpiderObserver;
use App\Observers\CourseSpiderObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicit route model binding for instructor parameter
        Route::model('instructor', User::class);

        // Strict role-specific gates
        Gate::define('admin-only', function (User $user): bool {
            return $user->isAdmin() && $user->isActive();
        });

        Gate::define('instructor-only', function (User $user): bool {
            return $user->isInstructor() && $user->isActive();
        });

        Gate::define('student-only', function (User $user): bool {
            return $user->isStudent() && $user->isActive();
        });

        // Hierarchical LMS access gates
        Gate::define('access-admin', function (User $user): bool {
            return $user->isAdmin() && $user->isActive();
        });

        Gate::define('access-instructor', function (User $user): bool {
            return ($user->isInstructor() || $user->isAdmin()) && $user->isActive();
        });

        Gate::define('access-student', function (User $user): bool {
            return $user->isStudent() && $user->isActive();
        });

        Gate::define('access-lms', function (User $user): bool {
            return $user->isActive();
        });

        // SEO Spider Web Observers
        Blog::observe(BlogSpiderObserver::class);
        Course::observe(CourseSpiderObserver::class);

        // SEO Spider Web Observers
        Blog::observe(BlogSpiderObserver::class);
        Course::observe(CourseSpiderObserver::class);
    }
}
