<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LessonProgressService
{
    /**
     * Record student access to a lesson upon opening.
     *
     * Sets in_progress status on first access, sets first_started_at once,
     * and updates last_accessed_at without resetting completed status.
     */
    public function recordLessonAccess(User $user, Lesson|Course $lessonOrCourse, ?Lesson $lesson = null): LessonProgress
    {
        $targetLesson = $lesson ?? ($lessonOrCourse instanceof Lesson ? $lessonOrCourse : null);

        if (! $targetLesson) {
            throw new InvalidArgumentException('A valid Lesson instance must be provided.');
        }

        $now = now();

        try {
            return DB::transaction(function () use ($user, $targetLesson, $now) {
                $progress = LessonProgress::where('user_id', $user->id)
                    ->where('lesson_id', $targetLesson->id)
                    ->lockForUpdate()
                    ->first();

                if (! $progress) {
                    return LessonProgress::create([
                        'user_id' => $user->id,
                        'lesson_id' => $targetLesson->id,
                        'status' => LessonProgress::STATUS_IN_PROGRESS,
                        'first_started_at' => $now,
                        'last_accessed_at' => $now,
                    ]);
                }

                $updates = [
                    'last_accessed_at' => $now,
                ];

                if ($progress->first_started_at === null) {
                    $updates['first_started_at'] = $now;
                }

                // Never downgrade completed lesson to in_progress on reopen or race
                if ($progress->status === LessonProgress::STATUS_NOT_STARTED) {
                    $updates['status'] = LessonProgress::STATUS_IN_PROGRESS;
                }

                $progress->update($updates);

                return $progress;
            });
        } catch (QueryException $e) {
            if ($this->isDuplicateKeyException($e)) {
                return DB::transaction(function () use ($user, $targetLesson, $now) {
                    $progress = LessonProgress::where('user_id', $user->id)
                        ->where('lesson_id', $targetLesson->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $updates = [
                        'last_accessed_at' => $now,
                    ];

                    if ($progress->first_started_at === null) {
                        $updates['first_started_at'] = $now;
                    }

                    if ($progress->status === LessonProgress::STATUS_NOT_STARTED) {
                        $updates['status'] = LessonProgress::STATUS_IN_PROGRESS;
                    }

                    $progress->update($updates);

                    return $progress;
                });
            }

            throw $e;
        }
    }

    /**
     * Mark an eligible lesson as completed by the student.
     */
    public function markLessonComplete(User $user, Course $course, Lesson $lesson): LessonProgress
    {
        $this->validateLessonEligibility($user, $course, $lesson);

        $now = now();

        try {
            return DB::transaction(function () use ($user, $lesson, $now) {
                $progress = LessonProgress::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->lockForUpdate()
                    ->first();

                if (! $progress) {
                    return LessonProgress::create([
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                        'status' => LessonProgress::STATUS_COMPLETED,
                        'first_started_at' => $now,
                        'last_accessed_at' => $now,
                        'completed_at' => $now,
                    ]);
                }

                $updates = [
                    'status' => LessonProgress::STATUS_COMPLETED,
                    'last_accessed_at' => $now,
                ];

                // Preserve original completed_at if already set
                if ($progress->completed_at === null) {
                    $updates['completed_at'] = $now;
                }

                if ($progress->first_started_at === null) {
                    $updates['first_started_at'] = $now;
                }

                $progress->update($updates);

                return $progress;
            });
        } catch (QueryException $e) {
            if ($this->isDuplicateKeyException($e)) {
                return DB::transaction(function () use ($user, $lesson, $now) {
                    $progress = LessonProgress::where('user_id', $user->id)
                        ->where('lesson_id', $lesson->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $updates = [
                        'status' => LessonProgress::STATUS_COMPLETED,
                        'last_accessed_at' => $now,
                    ];

                    if ($progress->completed_at === null) {
                        $updates['completed_at'] = $now;
                    }

                    if ($progress->first_started_at === null) {
                        $updates['first_started_at'] = $now;
                    }

                    $progress->update($updates);

                    return $progress;
                });
            }

            throw $e;
        }
    }

    /**
     * Reopen a completed lesson and mark it in_progress.
     */
    public function markLessonIncomplete(User $user, Course $course, Lesson $lesson): LessonProgress
    {
        $this->validateLessonEligibility($user, $course, $lesson);

        $now = now();

        try {
            return DB::transaction(function () use ($user, $lesson, $now) {
                $progress = LessonProgress::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->lockForUpdate()
                    ->first();

                if (! $progress) {
                    return LessonProgress::create([
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                        'status' => LessonProgress::STATUS_IN_PROGRESS,
                        'first_started_at' => $now,
                        'last_accessed_at' => $now,
                        'completed_at' => null,
                    ]);
                }

                $progress->update([
                    'status' => LessonProgress::STATUS_IN_PROGRESS,
                    'completed_at' => null,
                    'last_accessed_at' => $now,
                ]);

                return $progress;
            });
        } catch (QueryException $e) {
            if ($this->isDuplicateKeyException($e)) {
                return DB::transaction(function () use ($user, $lesson, $now) {
                    $progress = LessonProgress::where('user_id', $user->id)
                        ->where('lesson_id', $lesson->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $progress->update([
                        'status' => LessonProgress::STATUS_IN_PROGRESS,
                        'completed_at' => null,
                        'last_accessed_at' => $now,
                    ]);

                    return $progress;
                });
            }

            throw $e;
        }
    }

    /**
     * Calculate course progress percentage and lesson counts for a student.
     *
     * @return array{
     *     total_lessons: int,
     *     completed_lessons: int,
     *     remaining_lessons: int,
     *     percentage: int,
     *     is_completed: bool
     * }
     */
    public function getCourseProgress(User $user, Course $course): array
    {
        // 1. Total eligible published lessons in published modules
        $totalLessons = Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('status', 'published');
        })->where('status', 'published')->count();

        if ($totalLessons === 0) {
            return [
                'total_lessons' => 0,
                'total' => 0,
                'completed_lessons' => 0,
                'completed' => 0,
                'remaining_lessons' => 0,
                'percentage' => 0,
                'is_completed' => false,
            ];
        }

        // 2. Completed eligible published lessons
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->where('status', LessonProgress::STATUS_COMPLETED)
            ->whereHas('lesson', function ($lQ) use ($course) {
                $lQ->where('status', 'published')
                    ->whereHas('module', function ($mQ) use ($course) {
                        $mQ->where('course_id', $course->id)->where('status', 'published');
                    });
            })
            ->count();

        $percentage = (int) round(($completedLessons / $totalLessons) * 100);
        $remaining = max(0, $totalLessons - $completedLessons);

        return [
            'total_lessons' => $totalLessons,
            'total' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'completed' => $completedLessons,
            'remaining_lessons' => $remaining,
            'percentage' => $percentage,
            'is_completed' => $completedLessons >= $totalLessons,
        ];
    }

    /**
     * Efficiently load progress statistics for multiple courses without N+1 queries.
     *
     * @param  Collection<int, Course>  $courses
     * @return array<int, array{total_lessons: int, completed_lessons: int, remaining_lessons: int, percentage: int, is_completed: bool}>
     */
    public function getMultipleCoursesProgress(User $user, Collection $courses): array
    {
        if ($courses->isEmpty()) {
            return [];
        }

        $courseIds = $courses->pluck('id')->all();

        // Query 1: Total published lessons per course
        $totals = DB::table('lessons')
            ->join('course_modules', 'lessons.course_module_id', '=', 'course_modules.id')
            ->whereIn('course_modules.course_id', $courseIds)
            ->where('course_modules.status', 'published')
            ->where('lessons.status', 'published')
            ->groupBy('course_modules.course_id')
            ->select('course_modules.course_id', DB::raw('COUNT(lessons.id) as total_count'))
            ->pluck('total_count', 'course_id')
            ->all();

        // Query 2: Completed published lessons per course for this user
        $completed = DB::table('lesson_progress')
            ->join('lessons', 'lesson_progress.lesson_id', '=', 'lessons.id')
            ->join('course_modules', 'lessons.course_module_id', '=', 'course_modules.id')
            ->where('lesson_progress.user_id', $user->id)
            ->where('lesson_progress.status', LessonProgress::STATUS_COMPLETED)
            ->whereIn('course_modules.course_id', $courseIds)
            ->where('course_modules.status', 'published')
            ->where('lessons.status', 'published')
            ->groupBy('course_modules.course_id')
            ->select('course_modules.course_id', DB::raw('COUNT(lesson_progress.id) as completed_count'))
            ->pluck('completed_count', 'course_id')
            ->all();

        $result = [];
        foreach ($courseIds as $cid) {
            $total = (int) ($totals[$cid] ?? 0);
            $done = (int) ($completed[$cid] ?? 0);
            $pct = $total > 0 ? (int) round(($done / $total) * 100) : 0;

            $result[$cid] = [
                'total_lessons' => $total,
                'total' => $total,
                'completed_lessons' => $done,
                'completed' => $done,
                'remaining_lessons' => max(0, $total - $done),
                'percentage' => $pct,
                'is_completed' => $total > 0 && $done >= $total,
            ];
        }

        return $result;
    }

    /**
     * Resolve the resume-learning lesson for a student in an enrolled course.
     */
    public function getResumeLesson(User $user, Course $course): ?Lesson
    {
        // 1. Check most recently accessed eligible lesson
        $lastAccessed = Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_modules.course_id', $course->id)->where('course_modules.status', 'published');
        })
            ->where('lessons.status', 'published')
            ->join('lesson_progress', function ($join) use ($user) {
                $join->on('lessons.id', '=', 'lesson_progress.lesson_id')
                    ->where('lesson_progress.user_id', '=', $user->id);
            })
            ->orderBy('lesson_progress.last_accessed_at', 'desc')
            ->select('lessons.*', 'lesson_progress.status as progress_status')
            ->first();

        // If the last accessed lesson is not yet completed, resume right there
        if ($lastAccessed && $lastAccessed->progress_status !== LessonProgress::STATUS_COMPLETED) {
            return Lesson::find($lastAccessed->id);
        }

        // 2. If last accessed is completed (or never accessed), find the first eligible incomplete lesson in course order
        $firstIncomplete = $this->getOrderedPublishedLessons($course)
            ->first(function (Lesson $lesson) use ($user) {
                $progress = LessonProgress::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->first();

                return ! $progress || $progress->status !== LessonProgress::STATUS_COMPLETED;
            });

        if ($firstIncomplete) {
            return $firstIncomplete;
        }

        // 3. Fallback: If all are completed, return the first eligible published lesson
        return $this->getOrderedPublishedLessons($course)->first();
    }

    /**
     * Calculate previous and next navigable lessons within course sequence.
     *
     * @return array{
     *     previous_lesson: ?Lesson,
     *     next_lesson: ?Lesson,
     *     prev: ?Lesson,
     *     next: ?Lesson
     * }
     */
    public function getLessonNavigation(Course $course, Lesson $currentLesson): array
    {
        $orderedLessons = $this->getOrderedPublishedLessons($course)->values();

        $currentIndex = $orderedLessons->search(fn (Lesson $l) => $l->id === $currentLesson->id);

        if ($currentIndex === false) {
            return [
                'previous_lesson' => null,
                'next_lesson' => null,
                'prev' => null,
                'next' => null,
            ];
        }

        $previous = $currentIndex > 0 ? $orderedLessons->get($currentIndex - 1) : null;
        $next = $currentIndex < ($orderedLessons->count() - 1) ? $orderedLessons->get($currentIndex + 1) : null;

        return [
            'previous_lesson' => $previous,
            'next_lesson' => $next,
            'prev' => $previous,
            'next' => $next,
        ];
    }

    /**
     * Get a keyed map of lesson_id => LessonProgress for syllabus rendering.
     *
     * @return Collection<int, LessonProgress>
     */
    public function getCourseProgressMap(User $user, Course $course): Collection
    {
        return LessonProgress::where('user_id', $user->id)
            ->whereHas('lesson', function ($lQ) use ($course) {
                $lQ->whereHas('module', function ($mQ) use ($course) {
                    $mQ->where('course_id', $course->id);
                });
            })
            ->get()
            ->keyBy('lesson_id');
    }

    /**
     * Helper to get all published lessons of a course in module and lesson sort order.
     *
     * @return Collection<int, Lesson>
     */
    public function getOrderedPublishedLessons(Course $course): Collection
    {
        return Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_modules.course_id', $course->id)->where('course_modules.status', 'published');
        })
            ->where('lessons.status', 'published')
            ->join('course_modules', 'lessons.course_module_id', '=', 'course_modules.id')
            ->orderBy('course_modules.sort_order', 'asc')
            ->orderBy('lessons.sort_order', 'asc')
            ->select('lessons.*')
            ->get();
    }

    /**
     * Security validation: Ensure student is active, enrolled, and lesson is published and belongs to course.
     */
    public function validateLessonEligibility(User $user, Course $course, Lesson $lesson): void
    {
        if (! $user->isStudent() || ! $user->isActive()) {
            abort(403, 'Only active students can track course progress.');
        }

        if (! $user->hasApprovedEnrollment($course)) {
            abort(403, 'You must have an approved enrollment in this course.');
        }

        $module = $lesson->module;

        if (! $module || (int) $module->course_id !== (int) $course->id) {
            abort(404, 'Lesson does not belong to this course.');
        }

        if (! $module->isPublished() || ! $lesson->isPublished() || ! $course->isAvailableForEnrollment()) {
            abort(404, 'This lesson is currently not open or published.');
        }
    }

    /**
     * Determine if a QueryException represents a unique key constraint violation across database engines.
     */
    public function isDuplicateKeyException(QueryException $e): bool
    {
        $code = (string) $e->getCode();
        $message = $e->getMessage();

        return $code === '23000'
            || str_contains($message, 'Duplicate entry')
            || str_contains($message, 'UNIQUE constraint failed')
            || str_contains($message, 'unique constraint')
            || str_contains($message, '1062');
    }
}
