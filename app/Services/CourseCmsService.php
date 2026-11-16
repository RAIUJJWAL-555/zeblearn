<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseFaq;
use App\Models\CoursePartner;
use App\Models\CourseSlugRedirect;
use App\Models\CourseTestimonial;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CourseCmsService
{
    /**
     * Resolve a potential 301 redirect for a requested slug.
     */
    public function resolveRedirect(string $slug): ?string
    {
        return Cache::remember("zeblearn_slug_redirect_{$slug}", 3600, function () use ($slug) {
            $redirect = CourseSlugRedirect::where('old_slug', $slug)->first();

            if (! $redirect) {
                return null;
            }

            // Follow chain if any (up to 3 hops safety)
            $target = $redirect->new_slug;
            $hops = 0;
            while ($hops < 3) {
                $next = CourseSlugRedirect::where('old_slug', $target)->first();
                if (! $next || $next->new_slug === $target) {
                    break;
                }
                $target = $next->new_slug;
                $hops++;
            }

            return $target;
        });
    }

    /**
     * Handle slug update for a course, recording 301 history and preventing loops.
     */
    public function recordSlugChange(Course $course, string $oldSlug, string $newSlug): void
    {
        if ($oldSlug === $newSlug || empty($oldSlug) || empty($newSlug)) {
            return;
        }

        DB::transaction(function () use ($course, $oldSlug, $newSlug) {
            // Delete any existing redirect that had old_slug == new_slug (prevents loop)
            CourseSlugRedirect::where('old_slug', $newSlug)->delete();

            // Find and clear cache for all previous redirects pointing to $oldSlug
            $pointingToOld = CourseSlugRedirect::where('new_slug', $oldSlug)->get();
            foreach ($pointingToOld as $prevRedirect) {
                Cache::forget("zeblearn_slug_redirect_{$prevRedirect->old_slug}");
            }

            // Update existing redirects that pointed to the old slug so they point directly to the new slug
            CourseSlugRedirect::where('new_slug', $oldSlug)->update(['new_slug' => $newSlug]);

            // Create or update the redirect record
            CourseSlugRedirect::updateOrCreate(
                ['old_slug' => $oldSlug],
                [
                    'course_id' => $course->id,
                    'new_slug' => $newSlug,
                    'http_code' => 301,
                ]
            );

            // Invalidate redirect cache
            Cache::forget("zeblearn_slug_redirect_{$oldSlug}");
            Cache::forget("zeblearn_slug_redirect_{$newSlug}");
        });
    }

    /**
     * Sanitize and harmonize course pricing attributes.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function harmonizePricing(array $data): array
    {
        $originalFee = isset($data['original_fee']) && $data['original_fee'] !== '' ? (int) $data['original_fee'] : null;
        $fee = isset($data['fee']) && $data['fee'] !== '' ? (int) $data['fee'] : null;
        $discount = isset($data['discount_percent']) && $data['discount_percent'] !== '' ? (int) $data['discount_percent'] : null;

        if ($originalFee !== null && $originalFee > 0) {
            if ($fee !== null && $fee > $originalFee) {
                // Fee cannot be higher than original fee
                $fee = $originalFee;
            }

            if ($fee !== null && $discount === null) {
                $discount = (int) round((($originalFee - $fee) / $originalFee) * 100);
            } elseif ($discount !== null && $fee === null) {
                $discount = max(0, min(99, $discount));
                $fee = (int) round($originalFee * (1 - ($discount / 100)));
            } elseif ($fee !== null && $discount !== null) {
                // If both provided, ensure fee matches or adjust discount
                $expectedFee = (int) round($originalFee * (1 - ($discount / 100)));
                if (abs($fee - $expectedFee) > 100) {
                    $discount = (int) round((($originalFee - $fee) / $originalFee) * 100);
                }
            }
        } elseif ($fee !== null && ($originalFee === null || $originalFee <= 0)) {
            $originalFee = $fee;
            $discount = 0;
        }

        $data['original_fee'] = $originalFee;
        $data['fee'] = $fee;
        $data['discount_percent'] = $discount;

        return $data;
    }

    /**
     * Sync course FAQs repeater.
     *
     * @param  array<int, array{id?: int|string, question: string, answer: string, sort_order?: int, is_active?: bool}>  $faqs
     */
    public function syncFaqs(Course $course, array $faqs): void
    {
        $existingIds = [];

        foreach ($faqs as $index => $item) {
            if (empty($item['question']) || empty($item['answer'])) {
                continue;
            }

            $faqId = ! empty($item['id']) && is_numeric($item['id']) ? (int) $item['id'] : null;
            $payload = [
                'question' => trim($item['question']),
                'answer' => trim($item['answer']),
                'sort_order' => isset($item['sort_order']) ? (int) $item['sort_order'] : ($index + 1) * 10,
                'is_active' => isset($item['is_active']) ? (bool) $item['is_active'] : true,
            ];

            if ($faqId && $faq = CourseFaq::where('course_id', $course->id)->find($faqId)) {
                $faq->update($payload);
                $existingIds[] = $faq->id;
            } else {
                $created = $course->faqs()->create($payload);
                $existingIds[] = $created->id;
            }
        }

        // Delete removed items
        $course->faqs()->whereNotIn('id', $existingIds)->delete();
    }

    /**
     * Sync course upcoming batches repeater.
     *
     * @param  array<int, array<string, mixed>>  $batches
     */
    public function syncBatches(Course $course, array $batches): void
    {
        $existingIds = [];

        foreach ($batches as $index => $item) {
            if (empty($item['name']) || empty($item['start_date'])) {
                continue;
            }

            $batchId = ! empty($item['id']) && is_numeric($item['id']) ? (int) $item['id'] : null;
            $payload = [
                'name' => trim($item['name']),
                'start_date' => $item['start_date'],
                'end_date' => ! empty($item['end_date']) ? $item['end_date'] : null,
                'timings' => ! empty($item['timings']) ? trim($item['timings']) : '08:00 AM – 10:00 AM IST',
                'days' => ! empty($item['days']) ? trim($item['days']) : 'Sat & Sun (Weekend)',
                'delivery_mode' => ! empty($item['delivery_mode']) ? trim($item['delivery_mode']) : ($course->delivery_mode ?: 'Live Online'),
                'timezone' => ! empty($item['timezone']) ? trim($item['timezone']) : 'Asia/Kolkata',
                'total_seats' => isset($item['total_seats']) ? (int) $item['total_seats'] : 25,
                'available_seats' => isset($item['available_seats']) ? (int) $item['available_seats'] : 8,
                'status' => ! empty($item['status']) ? trim($item['status']) : 'filling_fast',
                'sort_order' => isset($item['sort_order']) ? (int) $item['sort_order'] : ($index + 1) * 10,
                'is_active' => isset($item['is_active']) ? (bool) $item['is_active'] : true,
            ];

            if ($batchId && $batch = CourseBatch::where('course_id', $course->id)->find($batchId)) {
                $batch->update($payload);
                $existingIds[] = $batch->id;
            } else {
                $created = $course->batches()->create($payload);
                $existingIds[] = $created->id;
            }
        }

        $course->batches()->whereNotIn('id', $existingIds)->delete();
    }

    /**
     * Sync course testimonials repeater.
     *
     * @param  array<int, array<string, mixed>>  $testimonials
     */
    public function syncTestimonials(Course $course, array $testimonials): void
    {
        $existingIds = [];

        foreach ($testimonials as $index => $item) {
            if (empty($item['student_name']) || empty($item['review_text'])) {
                continue;
            }

            $testId = ! empty($item['id']) && is_numeric($item['id']) ? (int) $item['id'] : null;
            $payload = [
                'student_name' => trim($item['student_name']),
                'student_title_or_company' => ! empty($item['student_title_or_company']) ? trim($item['student_title_or_company']) : null,
                'avatar_path' => ! empty($item['avatar_path']) ? trim($item['avatar_path']) : null,
                'review_text' => trim($item['review_text']),
                'rating' => isset($item['rating']) ? (float) $item['rating'] : 5.0,
                'video_url' => ! empty($item['video_url']) ? trim($item['video_url']) : null,
                'is_verified' => isset($item['is_verified']) ? (bool) $item['is_verified'] : true,
                'is_published' => isset($item['is_published']) ? (bool) $item['is_published'] : true,
                'sort_order' => isset($item['sort_order']) ? (int) $item['sort_order'] : ($index + 1) * 10,
            ];

            if ($testId && $testimonial = CourseTestimonial::where('course_id', $course->id)->find($testId)) {
                $testimonial->update($payload);
                $existingIds[] = $testimonial->id;
            } else {
                $created = $course->testimonials()->create($payload);
                $existingIds[] = $created->id;
            }
        }

        $course->testimonials()->whereNotIn('id', $existingIds)->delete();
    }

    /**
     * Sync course hiring & accreditation partners.
     *
     * @param  array<int, array<string, mixed>>  $partners
     */
    public function syncPartners(Course $course, array $partners): void
    {
        $existingIds = [];

        foreach ($partners as $index => $item) {
            if (empty($item['name'])) {
                continue;
            }

            $partId = ! empty($item['id']) && is_numeric($item['id']) ? (int) $item['id'] : null;
            $payload = [
                'name' => trim($item['name']),
                'logo_path' => ! empty($item['logo_path']) ? trim($item['logo_path']) : null,
                'description' => ! empty($item['description']) ? trim($item['description']) : null,
                'website_url' => ! empty($item['website_url']) ? trim($item['website_url']) : null,
                'sort_order' => isset($item['sort_order']) ? (int) $item['sort_order'] : ($index + 1) * 10,
                'is_active' => isset($item['is_active']) ? (bool) $item['is_active'] : true,
            ];

            if ($partId && $partner = CoursePartner::where('course_id', $course->id)->find($partId)) {
                $partner->update($payload);
                $existingIds[] = $partner->id;
            } else {
                $created = $course->partners()->create($payload);
                $existingIds[] = $created->id;
            }
        }

        $course->partners()->whereNotIn('id', $existingIds)->delete();
    }

    /**
     * Sync assigned faculty members with sort order and roles.
     *
     * @param  array<int, array{instructor_id: int, role_or_title?: string, sort_order?: int}>  $faculty
     */
    public function syncFaculty(Course $course, array $faculty): void
    {
        $syncData = [];
        foreach ($faculty as $index => $item) {
            if (empty($item['instructor_id'])) {
                continue;
            }

            $syncData[$item['instructor_id']] = [
                'role_or_title' => $item['role_or_title'] ?? 'Lead Instructor',
                'sort_order' => isset($item['sort_order']) ? (int) $item['sort_order'] : ($index + 1) * 10,
                'assigned_at' => now(),
            ];
        }

        $course->instructors()->sync($syncData);
    }

    /**
     * Invalidate all cached data for this course.
     */
    public function invalidateCourseCache(Course $course): void
    {
        Cache::forget("zeblearn_course_{$course->slug}");
        Cache::forget("zeblearn_course_id_{$course->id}");
        Cache::forget("zeblearn_slug_redirect_{$course->slug}");
        Cache::forget('zeblearn_sitemap_xml');
        app(TrustProofService::class)->invalidateCourseCache($course);
    }
}
