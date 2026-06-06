<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizSubmission;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'student']);
    }

    private function getUnlockedQuizzesStatus($course, $user)
    {
        $completedLessonIds = $user->completedLessons()->pluck('lesson_id')->toArray();
        $submittedQuizIds = QuizSubmission::where('user_id', $user->id)->pluck('quiz_id')->toArray();

        $unlockedQuizzes = [];
        $canAccessNext = true;

        foreach ($course->modules->sortBy('order') as $module) {
            // Check lessons in module
            foreach ($module->lessons->sortBy('order') as $les) {
                if ($canAccessNext) {
                    if (!in_array($les->id, $completedLessonIds)) {
                        $canAccessNext = false;
                    }
                }
            }

            // Check quiz in module
            $moduleQuiz = $course->quizzes->where('module_id', $module->id)->first();
            if ($moduleQuiz) {
                $allLessonsCompleted = $module->lessons->every(fn($l) => in_array($l->id, $completedLessonIds));
                if ($allLessonsCompleted && $canAccessNext) {
                    $unlockedQuizzes[$moduleQuiz->id] = true;
                    if (!in_array($moduleQuiz->id, $submittedQuizIds)) {
                        $canAccessNext = false;
                    }
                } else {
                    $unlockedQuizzes[$moduleQuiz->id] = false;
                    $canAccessNext = false;
                }
            }
        }

        // Global quizzes
        foreach ($course->quizzes->whereNull('module_id') as $gQuiz) {
            $unlockedQuizzes[$gQuiz->id] = $canAccessNext;
        }

        return $unlockedQuizzes;
    }

    // Show Quiz Form
    public function show($slug, $quizId)
    {
        $course = Course::with(['modules.lessons', 'quizzes'])->where('slug', $slug)->firstOrFail();
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        // Check quiz lock state
        $unlockedQuizzes = $this->getUnlockedQuizzesStatus($course, auth()->user());
        if (!isset($unlockedQuizzes[$quiz->id]) || !$unlockedQuizzes[$quiz->id]) {
            return redirect()->route('student.classroom', $slug)->with('error', 'Kuis ini masih terkunci! Selesaikan materi/kuis sebelumnya dulu ya! 🔒');
        }

        // Check if student already submitted
        $submission = QuizSubmission::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($submission) {
            return redirect()->route('student.quiz.results', [$slug, $quiz->id]);
        }

        return view('student.quiz.show', compact('course', 'quiz'));
    }

    // Submit Quiz Answers
    public function submit(Request $request, $slug, $quizId)
    {
        $course = Course::with(['modules.lessons', 'quizzes'])->where('slug', $slug)->firstOrFail();
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        // Check quiz lock state
        $unlockedQuizzes = $this->getUnlockedQuizzesStatus($course, auth()->user());
        if (!isset($unlockedQuizzes[$quiz->id]) || !$unlockedQuizzes[$quiz->id]) {
            return redirect()->route('student.classroom', $slug)->with('error', 'Kuis ini masih terkunci! Selesaikan materi/kuis sebelumnya dulu ya! 🔒');
        }

        // Create submission
        $submission = QuizSubmission::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'score' => null,
            'status' => 'pending'
        ]);

        $totalPointsEarned = 0;
        $allMultipleChoice = true;

        foreach ($quiz->questions as $question) {
            $answerField = 'question_' . $question->id;
            $pointsAwarded = null;

            if ($question->type === 'multiple_choice') {
                $selectedOption = $request->input($answerField);
                $isCorrect = strtolower(trim($selectedOption)) === strtolower(trim($question->correct_answer));
                $pointsAwarded = $isCorrect ? $question->points : 0;
                $totalPointsEarned += $pointsAwarded;

                QuizAnswer::create([
                    'submission_id' => $submission->id,
                    'question_id' => $question->id,
                    'selected_option' => $selectedOption,
                    'points_awarded' => $pointsAwarded
                ]);
            } else {
                $allMultipleChoice = false;
                $answerText = $request->input($answerField);
                $filePath = null;

                if ($question->type === 'submission') {
                    if ($request->hasFile('file_' . $question->id)) {
                        $file = $request->file('file_' . $question->id);
                        $filePath = $file->store('quiz_submissions', 'public');
                    }
                }

                QuizAnswer::create([
                    'submission_id' => $submission->id,
                    'question_id' => $question->id,
                    'answer_text' => $answerText,
                    'file_path' => $filePath,
                    'points_awarded' => null // to be graded by admin
                ]);
            }
        }

        // If it's 100% multiple choice, we can auto-grade and mark as graded!
        if ($allMultipleChoice) {
            $totalPossiblePoints = $quiz->questions->sum('points');
            $scorePercentage = $totalPossiblePoints > 0 ? round(($totalPointsEarned / $totalPossiblePoints) * 100) : 100;
            
            $submission->update([
                'score' => $scorePercentage,
                'status' => 'graded',
                'feedback' => 'Hebat! Kuis Pilihan Ganda kamu otomatis dinilai.'
            ]);
        }

        return redirect()->route('student.quiz.results', [$slug, $quiz->id])->with('success', 'Kuis berhasil dikirim!');
    }

    // View Quiz Results / Feedback
    public function results($slug, $quizId)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        
        $submission = QuizSubmission::with('answers.question')
            ->where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('student.quiz.results', compact('course', 'quiz', 'submission'));
    }
}
