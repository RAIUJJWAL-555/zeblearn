<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminQuizQuestionController extends Controller
{
    /**
     * Store a new question with options for a quiz.
     */
    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        if ($quiz->attempts()->exists()) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'question' => 'Cannot add new questions to a quiz with existing student attempts.',
                ]);
            }

            return back()->with('error', 'Cannot add new questions to a quiz with existing student attempts.');
        }

        // Support structured array input: [['text' => 'A', 'is_correct' => 1], ...]
        if ($request->has('options') && is_array($request->input('options'))) {
            $rawOptions = $request->input('options');
            if (! empty($rawOptions) && is_array(reset($rawOptions))) {
                $optionsTexts = [];
                $correctIndices = [];
                foreach ($rawOptions as $idx => $opt) {
                    $optionsTexts[] = $opt['text'] ?? '';
                    if (! empty($opt['is_correct'])) {
                        $correctIndices[] = $idx;
                    }
                }

                if (count($correctIndices) !== 1) {
                    return back()->withInput()->withErrors(['correct_option' => 'Exactly one option must be marked as correct.']);
                }

                $request->merge([
                    'options' => $optionsTexts,
                    'correct_option' => $correctIndices[0],
                ]);
            }
        }

        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'marks' => ['required', 'integer', 'min:1', 'max:100'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string', 'max:1000'],
            'correct_option' => ['required', 'integer', 'min:0'],
        ]);

        if (! isset($validated['options'][$validated['correct_option']])) {
            return back()->withInput()->with('error', 'Please select a valid correct option.');
        }

        DB::transaction(function () use ($quiz, $validated) {
            $nextOrder = (int) $quiz->questions()->max('sort_order') + 1;

            $question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_text' => $validated['question_text'],
                'question_type' => QuizQuestion::TYPE_SINGLE_CHOICE,
                'marks' => (int) $validated['marks'],
                'explanation' => $validated['explanation'] ?? null,
                'sort_order' => $nextOrder,
            ]);

            foreach ($validated['options'] as $idx => $optText) {
                QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text' => $optText,
                    'is_correct' => (int) $idx === (int) $validated['correct_option'],
                    'sort_order' => $idx + 1,
                ]);
            }

            $quiz->recalculateTotalMarks();
        });

        return back()->with('success', 'Question added successfully.');
    }

    /**
     * Update an existing question and its options.
     */
    public function update(Request $request, Quiz $quiz, QuizQuestion $question): RedirectResponse
    {
        abort_if($question->quiz_id !== $quiz->id, 404);

        if ($quiz->attempts()->exists()) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'question' => 'Cannot edit question content for a quiz with existing student attempts.',
                ]);
            }

            return back()->with('error', 'Cannot edit question content for a quiz with existing student attempts.');
        }

        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'marks' => ['required', 'integer', 'min:1', 'max:100'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string', 'max:1000'],
            'correct_option' => ['required', 'integer', 'min:0'],
        ]);

        if (! isset($validated['options'][$validated['correct_option']])) {
            return back()->withInput()->with('error', 'Please select a valid correct option.');
        }

        DB::transaction(function () use ($quiz, $question, $validated) {
            $question->update([
                'question_text' => $validated['question_text'],
                'marks' => (int) $validated['marks'],
                'explanation' => $validated['explanation'] ?? null,
            ]);

            // Recreate options cleanly
            $question->options()->delete();

            foreach ($validated['options'] as $idx => $optText) {
                QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text' => $optText,
                    'is_correct' => (int) $idx === (int) $validated['correct_option'],
                    'sort_order' => $idx + 1,
                ]);
            }

            $quiz->recalculateTotalMarks();
        });

        return back()->with('success', 'Question updated successfully.');
    }

    /**
     * Delete an existing question.
     */
    public function destroy(Request $request, Quiz $quiz, QuizQuestion $question): RedirectResponse
    {
        abort_if($question->quiz_id !== $quiz->id, 404);

        if ($quiz->attempts()->exists()) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'question' => 'Cannot delete questions from a quiz that has existing student attempts.',
                ]);
            }

            return back()->with('error', 'Cannot delete questions from a quiz that has existing student attempts.');
        }

        DB::transaction(function () use ($quiz, $question) {
            $question->options()->delete();
            $question->delete();
            $quiz->recalculateTotalMarks();
        });

        return back()->with('success', 'Question deleted successfully.');
    }

    /**
     * Reorder questions in a quiz.
     */
    public function reorder(Request $request, Quiz $quiz): RedirectResponse
    {
        if ($quiz->attempts()->exists()) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'question' => 'Cannot reorder questions for a quiz that has existing student attempts.',
                ]);
            }

            return back()->with('error', 'Cannot reorder questions for a quiz that has existing student attempts.');
        }

        $validated = $request->validate([
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['required', 'integer'],
        ]);

        $ids = $validated['question_ids'];

        DB::transaction(function () use ($quiz, $ids) {
            foreach ($ids as $index => $id) {
                QuizQuestion::where('id', $id)
                    ->where('quiz_id', $quiz->id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('success', 'Questions reordered successfully.');
    }
}
