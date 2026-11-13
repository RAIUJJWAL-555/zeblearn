<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizAttemptQuestion;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class QuizService
{
    /**
     * Enforce strict student eligibility to access a quiz.
     */
    public function validateStudentAccess(User $user, Quiz $quiz, bool $requirePublished = true): void
    {
        if (! $user->isStudent() || ! $user->isActive()) {
            abort(403, 'Only active students can access assessments.');
        }

        $course = $quiz->course;

        if (! $course || ! $user->hasApprovedEnrollment($course)) {
            abort(403, 'You must have an approved enrollment in this course to take this assessment.');
        }

        if (! $course->isAvailableForEnrollment()) {
            abort(404, 'This course is currently not published or available.');
        }

        if ($requirePublished && ! $quiz->isPublished()) {
            abort(404, 'This assessment is currently not published.');
        }

        // If module-level or lesson-level, verify module is published
        if ($quiz->course_module_id) {
            $module = $quiz->module;
            if (! $module || (int) $module->course_id !== (int) $course->id || ($requirePublished && ! $module->isPublished())) {
                abort(404, 'The module associated with this assessment is not available.');
            }
        }

        // If lesson-level, verify lesson is published and belongs to module
        if ($quiz->lesson_id) {
            $lesson = $quiz->lesson;
            if (! $lesson || (int) $lesson->course_module_id !== (int) $quiz->course_module_id || ($requirePublished && ! $lesson->isPublished())) {
                abort(404, 'The lesson associated with this assessment is not available.');
            }
        }
    }

    /**
     * Start a new attempt or resume an active in-progress attempt for a student.
     * Concurrency-hardened against duplicate attempts and race conditions.
     */
    public function startOrResumeAttempt(User $user, Quiz $quiz): QuizAttempt
    {
        $this->validateStudentAccess($user, $quiz, true);

        try {
            return DB::transaction(function () use ($user, $quiz) {
                // 1. Serialize concurrent start requests for this student in this course via row lock
                Enrollment::where('user_id', $user->id)
                    ->where('course_id', $quiz->course_id)
                    ->lockForUpdate()
                    ->first();

                // 2. Check for existing in-progress attempt
                $activeAttempt = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->where('status', QuizAttempt::STATUS_IN_PROGRESS)
                    ->lockForUpdate()
                    ->first();

                if ($activeAttempt) {
                    if ($activeAttempt->hasExpired()) {
                        $this->finalizeAttempt($activeAttempt, QuizAttempt::STATUS_EXPIRED);
                    } else {
                        return $activeAttempt;
                    }
                }

                // 3. Check if attempts limit is reached across all statuses (in_progress, submitted, expired)
                $totalAttempts = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->whereIn('status', [
                        QuizAttempt::STATUS_IN_PROGRESS,
                        QuizAttempt::STATUS_SUBMITTED,
                        QuizAttempt::STATUS_EXPIRED,
                    ])
                    ->lockForUpdate()
                    ->count();

                if ($totalAttempts >= $quiz->attempts_allowed) {
                    abort(403, "You have reached the maximum allowed attempts ({$quiz->attempts_allowed}) for this quiz.");
                }

                // 4. Calculate next attempt number under lock
                $lastAttemptNumber = (int) QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->max('attempt_number');

                $now = Carbon::now();
                $expiresAt = $quiz->time_limit_minutes
                    ? $now->copy()->addMinutes($quiz->time_limit_minutes)
                    : null;

                // Recalculate/verify total marks
                $totalMarks = $quiz->total_marks > 0
                    ? $quiz->total_marks
                    : (int) $quiz->questions()->sum('marks');

                $attempt = QuizAttempt::create([
                    'quiz_id' => $quiz->id,
                    'user_id' => $user->id,
                    'attempt_number' => $lastAttemptNumber + 1,
                    'status' => QuizAttempt::STATUS_IN_PROGRESS,
                    'started_at' => $now,
                    'expires_at' => $expiresAt,
                    'total_marks' => $totalMarks,
                ]);

                // 5. Persist locked question & option ordering once for this attempt
                $this->initializeAttemptLayout($attempt, $quiz);

                return $attempt;
            });
        } catch (QueryException $e) {
            // Collision recovery: if competing transaction committed, return active or enforce limit
            $activeAttempt = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->where('status', QuizAttempt::STATUS_IN_PROGRESS)
                ->first();

            if ($activeAttempt && ! $activeAttempt->hasExpired()) {
                return $activeAttempt;
            }

            if ($activeAttempt && $activeAttempt->hasExpired()) {
                $this->finalizeAttempt($activeAttempt, QuizAttempt::STATUS_EXPIRED);
            }

            $totalAttempts = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->whereIn('status', [
                    QuizAttempt::STATUS_IN_PROGRESS,
                    QuizAttempt::STATUS_SUBMITTED,
                    QuizAttempt::STATUS_EXPIRED,
                ])
                ->count();

            if ($totalAttempts >= $quiz->attempts_allowed) {
                abort(403, "You have reached the maximum allowed attempts ({$quiz->attempts_allowed}) for this quiz.");
            }

            throw $e;
        }
    }

    /**
     * Determine and persist question and option ordering once when an attempt starts.
     * Never exposes correct-answer flags in the persisted student-facing order data.
     */
    public function initializeAttemptLayout(QuizAttempt $attempt, Quiz $quiz): void
    {
        if ($attempt->attemptQuestions()->exists()) {
            return;
        }

        $questions = $quiz->questions()
            ->with(['options' => fn ($q) => $q->orderBy('sort_order', 'asc')])
            ->orderBy('sort_order', 'asc')
            ->get();

        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle()->values();
        }

        foreach ($questions as $index => $question) {
            $options = $question->options;

            if ($quiz->shuffle_options) {
                $optionIds = $options->pluck('id')->shuffle()->values()->all();
            } else {
                $optionIds = $options->sortBy('sort_order')->pluck('id')->values()->all();
            }

            QuizAttemptQuestion::create([
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' => $question->id,
                'sort_order' => $index + 1,
                'option_order' => $optionIds,
            ]);
        }
    }

    /**
     * Securely save or update an answer for an in-progress attempt.
     */
    public function saveAnswer(QuizAttempt $attempt, int $questionId, ?int $optionId): QuizAttemptAnswer
    {
        $current = QuizAttempt::where('id', $attempt->id)->firstOrFail();

        if (! $current->isInProgress()) {
            abort(403, 'Cannot update answers for an attempt that is not in progress.');
        }

        if ($current->hasExpired()) {
            $this->finalizeAttempt($current, QuizAttempt::STATUS_EXPIRED);
            abort(422, 'This quiz attempt has expired.');
        }

        return DB::transaction(function () use ($attempt, $questionId, $optionId) {
            $lockedAttempt = QuizAttempt::where('id', $attempt->id)->lockForUpdate()->firstOrFail();

            if (! $lockedAttempt->isInProgress()) {
                abort(403, 'Cannot update answers for an attempt that is not in progress.');
            }

            if ($lockedAttempt->hasExpired()) {
                $this->finalizeAttempt($lockedAttempt, QuizAttempt::STATUS_EXPIRED);
                abort(422, 'This quiz attempt has expired.');
            }

            // Validate question belongs to this quiz
            $question = QuizQuestion::where('id', $questionId)
                ->where('quiz_id', $lockedAttempt->quiz_id)
                ->firstOrFail();

            // Validate option belongs to this question if provided
            if ($optionId !== null) {
                $option = QuizOption::where('id', $optionId)
                    ->where('quiz_question_id', $question->id)
                    ->first();

                if (! $option) {
                    abort(422, 'Selected option does not belong to this question.');
                }
            }

            return QuizAttemptAnswer::updateOrCreate(
                [
                    'quiz_attempt_id' => $lockedAttempt->id,
                    'quiz_question_id' => $question->id,
                ],
                [
                    'quiz_option_id' => $optionId,
                    'awarded_marks' => 0, // Calculated upon submission
                ]
            );
        });
    }

    /**
     * Submit and score a student attempt.
     */
    public function submitAttempt(QuizAttempt $attempt): QuizAttempt
    {
        // Idempotent return if already submitted or expired
        if ($attempt->isSubmitted() || $attempt->isExpired()) {
            return $attempt;
        }

        if (! $attempt->isInProgress()) {
            abort(422, 'Only in-progress attempts can be submitted.');
        }

        $finalStatus = $attempt->hasExpired()
            ? QuizAttempt::STATUS_EXPIRED
            : QuizAttempt::STATUS_SUBMITTED;

        return $this->finalizeAttempt($attempt, $finalStatus);
    }

    /**
     * Calculate score and finalize an attempt.
     */
    protected function finalizeAttempt(QuizAttempt $attempt, string $status): QuizAttempt
    {
        return DB::transaction(function () use ($attempt, $status) {
            // Lock attempt row
            $attempt = QuizAttempt::where('id', $attempt->id)->lockForUpdate()->firstOrFail();

            if (! $attempt->isInProgress()) {
                return $attempt;
            }

            // Authoritative re-check of expiry under the lock
            $finalStatus = $attempt->hasExpired()
                ? QuizAttempt::STATUS_EXPIRED
                : $status;

            $quiz = $attempt->quiz()->with(['questions.options'])->firstOrFail();
            $answers = $attempt->answers()->get()->keyBy('quiz_question_id');

            $totalAwardedMarks = 0;

            foreach ($quiz->questions as $question) {
                $answer = $answers->get($question->id);
                $selectedOptionId = $answer?->quiz_option_id;

                $correctOption = $question->options->firstWhere('is_correct', true);
                $isCorrect = $correctOption && $selectedOptionId !== null && (int) $correctOption->id === (int) $selectedOptionId;

                $awarded = $isCorrect ? (int) $question->marks : 0;
                $totalAwardedMarks += $awarded;

                // Update or record answer with awarded marks
                if ($answer) {
                    $answer->update(['awarded_marks' => $awarded]);
                } elseif ($selectedOptionId !== null) {
                    QuizAttemptAnswer::create([
                        'quiz_attempt_id' => $attempt->id,
                        'quiz_question_id' => $question->id,
                        'quiz_option_id' => $selectedOptionId,
                        'awarded_marks' => $awarded,
                    ]);
                }
            }

            $totalPossibleMarks = $attempt->total_marks > 0
                ? $attempt->total_marks
                : (int) $quiz->questions->sum('marks');

            $percentage = $totalPossibleMarks > 0
                ? (int) round(($totalAwardedMarks / $totalPossibleMarks) * 100)
                : 0;

            $isPassed = $percentage >= $quiz->passing_percentage;

            $attempt->update([
                'status' => $finalStatus,
                'submitted_at' => Carbon::now(),
                'score' => $totalAwardedMarks,
                'total_marks' => $totalPossibleMarks,
                'percentage' => $percentage,
                'is_passed' => $isPassed,
            ]);

            return $attempt;
        });
    }

    /**
     * Prepare student attempt data for active taking view.
     * Guaranteed: is_correct is NEVER passed or leaked to student browser.
     * Guaranteed: Question and option order is persistent and stable across refreshes.
     */
    public function getAttemptTakingData(QuizAttempt $attempt): array
    {
        $quiz = $attempt->quiz;

        // Load or initialize attempt layout
        $attemptQuestions = $attempt->attemptQuestions()
            ->with(['question.options'])
            ->orderBy('sort_order', 'asc')
            ->get();

        if ($attemptQuestions->isEmpty()) {
            $this->initializeAttemptLayout($attempt, $quiz);
            $attemptQuestions = $attempt->attemptQuestions()
                ->with(['question.options'])
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        // Map questions and sort options strictly according to persisted option_order
        $cleanQuestions = $attemptQuestions->map(function (QuizAttemptQuestion $aq) {
            $question = $aq->question;
            $optionsMap = $question->options->keyBy('id');
            $orderedOptions = collect();

            if (! empty($aq->option_order) && is_array($aq->option_order)) {
                foreach ($aq->option_order as $optId) {
                    if ($opt = $optionsMap->get($optId)) {
                        $orderedOptions->push($opt);
                    }
                }
            }

            // Fallback for any options not in option_order
            if ($orderedOptions->count() < $question->options->count()) {
                $missing = $question->options->whereNotIn('id', $orderedOptions->pluck('id'));
                $orderedOptions = $orderedOptions->concat($missing);
            }

            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'marks' => $question->marks,
                'sort_order' => $aq->sort_order,
                'options' => $orderedOptions->map(fn (QuizOption $opt) => [
                    'id' => $opt->id,
                    'option_text' => $opt->option_text,
                    'sort_order' => $opt->sort_order,
                ])->values()->all(),
            ];
        });

        // Key current answers by question_id
        $savedAnswers = $attempt->answers()
            ->pluck('quiz_option_id', 'quiz_question_id')
            ->all();

        return [
            'attempt' => $attempt,
            'quiz' => $quiz,
            'course' => $quiz->course,
            'questions' => $cleanQuestions,
            'savedAnswers' => $savedAnswers,
            'timeRemainingSeconds' => $attempt->time_remaining_seconds,
        ];
    }

    /**
     * Prepare student result review data.
     * Shows correct options only if show_correct_answers is enabled.
     * Preserves exact question and option order from the attempt.
     */
    public function getAttemptResultData(QuizAttempt $attempt): array
    {
        $quiz = $attempt->quiz;
        $canReviewAnswers = $quiz->show_correct_answers && ($attempt->isSubmitted() || $attempt->isExpired());

        $attemptQuestions = $attempt->attemptQuestions()
            ->with(['question.options'])
            ->orderBy('sort_order', 'asc')
            ->get();

        $answers = $attempt->answers()->get()->keyBy('quiz_question_id');

        if ($attemptQuestions->isNotEmpty()) {
            $reviewQuestions = $attemptQuestions->map(function (QuizAttemptQuestion $aq) use ($answers, $canReviewAnswers) {
                $question = $aq->question;
                $optionsMap = $question->options->keyBy('id');
                $orderedOptions = collect();

                if (! empty($aq->option_order) && is_array($aq->option_order)) {
                    foreach ($aq->option_order as $optId) {
                        if ($opt = $optionsMap->get($optId)) {
                            $orderedOptions->push($opt);
                        }
                    }
                }

                if ($orderedOptions->count() < $question->options->count()) {
                    $missing = $question->options->whereNotIn('id', $orderedOptions->pluck('id'));
                    $orderedOptions = $orderedOptions->concat($missing);
                }

                $answer = $answers->get($question->id);
                $selectedOptionId = $answer?->quiz_option_id;

                $correctOption = $canReviewAnswers
                    ? $question->options->firstWhere('is_correct', true)
                    : null;

                return [
                    'question' => $question,
                    'options' => $orderedOptions,
                    'selected_option_id' => $selectedOptionId,
                    'correct_option_id' => $correctOption?->id,
                    'awarded_marks' => $answer?->awarded_marks ?? 0,
                    'is_correct' => $correctOption && $selectedOptionId && (int) $selectedOptionId === (int) $correctOption->id,
                    'explanation' => $canReviewAnswers ? $question->explanation : null,
                ];
            });
        } else {
            // Fallback for attempts prior to layout persistence
            $questions = $quiz->questions()->with('options')->orderBy('sort_order', 'asc')->get();
            $reviewQuestions = $questions->map(function (QuizQuestion $question) use ($answers, $canReviewAnswers) {
                $answer = $answers->get($question->id);
                $selectedOptionId = $answer?->quiz_option_id;

                $correctOption = $canReviewAnswers
                    ? $question->options->firstWhere('is_correct', true)
                    : null;

                return [
                    'question' => $question,
                    'options' => $question->options,
                    'selected_option_id' => $selectedOptionId,
                    'correct_option_id' => $correctOption?->id,
                    'awarded_marks' => $answer?->awarded_marks ?? 0,
                    'is_correct' => $correctOption && $selectedOptionId && (int) $selectedOptionId === (int) $correctOption->id,
                    'explanation' => $canReviewAnswers ? $question->explanation : null,
                ];
            });
        }

        // Previous attempts history for this student on this quiz
        $history = $quiz->attempts()
            ->where('user_id', $attempt->user_id)
            ->whereIn('status', [QuizAttempt::STATUS_SUBMITTED, QuizAttempt::STATUS_EXPIRED])
            ->orderBy('attempt_number', 'desc')
            ->get();

        return [
            'attempt' => $attempt,
            'quiz' => $quiz,
            'course' => $quiz->course,
            'canReviewAnswers' => $canReviewAnswers,
            'reviewQuestions' => $reviewQuestions,
            'history' => $history,
        ];
    }
}
