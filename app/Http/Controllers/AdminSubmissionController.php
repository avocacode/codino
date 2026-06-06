<?php

namespace App\Http\Controllers;

use App\Models\QuizSubmission;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class AdminSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // List all submissions needing review
    public function index()
    {
        $submissions = QuizSubmission::with(['quiz.course', 'user'])
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.submissions.index', compact('submissions'));
    }

    // Show grading page for a submission
    public function show($id)
    {
        $submission = QuizSubmission::with(['quiz.questions', 'answers.question', 'user'])->findOrFail($id);
        return view('admin.submissions.show', compact('submission'));
    }

    // Save grades & feedback
    public function grade(Request $request, $id)
    {
        $submission = QuizSubmission::with('answers.question')->findOrFail($id);
        $totalEarnedPoints = 0;
        $totalPossiblePoints = 0;

        foreach ($submission->answers as $answer) {
            $question = $answer->question;
            $totalPossiblePoints += $question->points;

            if ($question->type === 'multiple_choice') {
                $totalEarnedPoints += $answer->points_awarded;
            } else {
                $pointsField = 'points_' . $answer->id;
                $pointsAwarded = $request->input($pointsField, 0);
                
                // cap points
                if ($pointsAwarded > $question->points) {
                    $pointsAwarded = $question->points;
                }

                $answer->update(['points_awarded' => $pointsAwarded]);
                $totalEarnedPoints += $pointsAwarded;
            }
        }

        $scorePercentage = $totalPossiblePoints > 0 ? round(($totalEarnedPoints / $totalPossiblePoints) * 100) : 100;

        $submission->update([
            'score' => $scorePercentage,
            'status' => 'graded',
            'feedback' => $request->feedback
        ]);

        return redirect()->route('admin.submissions.index')->with('success', 'Penilaian kuis berhasil disimpan & dipublish!');
    }
}
