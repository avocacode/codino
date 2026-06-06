<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'student']);
    }

    // Student Dashboard (My Courses)
    public function index()
    {
        $enrollments = Enrollment::with('course')->where('user_id', auth()->id())->get();
        return view('student.dashboard', compact('enrollments'));
    }

    // Enroll/Request Class Join
    public function enroll($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        // Check if already enrolled
        $existing = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return redirect()->route('student.dashboard')->with('error', 'Anda sudah terdaftar di kelas ini.');
        }

        // Create pending enrollment
        Enrollment::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'payment_status' => 'pending',
            'paid_amount' => $course->price
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Pendaftaran berhasil dikirim! Silakan hubungi admin untuk disetujui agar bisa mulai belajar.');
    }

    // Classroom Viewer (Belajar)
    public function classroom(Request $request, $slug)
    {
        $course = Course::with(['modules.lessons.sources', 'quizzes'])->where('slug', $slug)->firstOrFail();

        // Check if student is enrolled and paid
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->first();

        if (!$enrollment) {
            return redirect()->route('kelas.show', $slug)->with('error', 'Akses ditolak. Anda harus terdaftar dan disetujui oleh admin untuk mengikuti kelas ini.');
        }

        // Get all lessons in order
        $orderedLessons = [];
        foreach ($course->modules->sortBy('order') as $module) {
            foreach ($module->lessons->sortBy('order') as $lesson) {
                $orderedLessons[] = $lesson;
            }
        }

        $completedLessonIds = auth()->user()->completedLessons()->pluck('lesson_id')->toArray();
        $submittedQuizIds = \App\Models\QuizSubmission::where('user_id', auth()->id())->pluck('quiz_id')->toArray();

        // Determine which lessons and quizzes are unlocked sequentially
        $unlockedLessons = [];
        $unlockedQuizzes = [];
        $canAccessNext = true;

        foreach ($course->modules->sortBy('order') as $module) {
            // Check lessons in module
            foreach ($module->lessons->sortBy('order') as $les) {
                if ($canAccessNext) {
                    $unlockedLessons[$les->id] = true;
                    if (!in_array($les->id, $completedLessonIds)) {
                        $canAccessNext = false;
                    }
                } else {
                    $unlockedLessons[$les->id] = false;
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
                    $canAccessNext = false; // block next modules
                }
            }
        }

        // Global quizzes
        foreach ($course->quizzes->whereNull('module_id') as $gQuiz) {
            $unlockedQuizzes[$gQuiz->id] = $canAccessNext;
        }

        // Get the active lesson
        $activeLessonId = $request->query('lesson');
        $activeLesson = null;

        if ($activeLessonId) {
            if (isset($unlockedLessons[$activeLessonId]) && !$unlockedLessons[$activeLessonId]) {
                // Redirect/fallback to first incomplete unlocked lesson
                $fallbackLesson = null;
                foreach ($orderedLessons as $les) {
                    if ($unlockedLessons[$les->id]) {
                        $fallbackLesson = $les;
                        if (!in_array($les->id, $completedLessonIds)) {
                            break;
                        }
                    }
                }
                $activeLesson = $fallbackLesson;
                session()->flash('error', 'Materi ini masih dikunci! Selesaikan kuis/materi sebelumnya dulu ya! 🔒');
            } else {
                $activeLesson = Lesson::with('sources')->where('id', $activeLessonId)->first();
            }
        } else {
            // Default to first incomplete unlocked lesson
            $fallbackLesson = null;
            foreach ($orderedLessons as $les) {
                if ($unlockedLessons[$les->id]) {
                    $fallbackLesson = $les;
                    if (!in_array($les->id, $completedLessonIds)) {
                        break;
                    }
                }
            }
            $activeLesson = $fallbackLesson;
        }

        return view('student.classroom', compact('course', 'activeLesson', 'unlockedLessons', 'completedLessonIds', 'unlockedQuizzes', 'submittedQuizIds'));
    }

    // Toggle Lesson Completion
    public function toggleComplete(Request $request, $slug, $lessonId)
    {
        $user = auth()->user();
        $completion = \App\Models\LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->first();

        if ($completion) {
            $completion->delete();
            $message = 'Materi ditandai belum selesai.';
            return redirect()->back()->with('success', $message);
        } else {
            \App\Models\LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lessonId
            ]);
            $message = 'Keren! Materi berhasil diselesaikan! 🚀';

            $course = Course::with(['modules.lessons', 'quizzes'])->where('slug', $slug)->firstOrFail();
            
            // Check if all lessons in the current lesson's module are completed
            $currentLesson = Lesson::findOrFail($lessonId);
            $module = $course->modules->where('id', $currentLesson->module_id)->first();
            
            if ($module) {
                $completedLessonIds = $user->completedLessons()->pluck('lesson_id')->toArray();
                $allModuleLessonsCompleted = $module->lessons->every(fn($l) => in_array($l->id, $completedLessonIds));
                
                if ($allModuleLessonsCompleted) {
                    $moduleQuiz = $course->quizzes->where('module_id', $module->id)->first();
                    if ($moduleQuiz) {
                        $quizSubmitted = \App\Models\QuizSubmission::where('quiz_id', $moduleQuiz->id)
                            ->where('user_id', $user->id)
                            ->exists();
                        if (!$quizSubmitted) {
                            return redirect()->route('student.quiz.show', [$slug, $moduleQuiz->id])
                                ->with('success', $message . ' Sekarang, yuk kerjakan kuis bab ini untuk membuka bab selanjutnya! 📝');
                        }
                    }
                }
            }

            // Find next lesson
            $orderedLessons = [];
            foreach ($course->modules->sortBy('order') as $m) {
                foreach ($m->lessons->sortBy('order') as $lesson) {
                    $orderedLessons[] = $lesson;
                }
            }

            $nextLesson = null;
            $foundCurrent = false;
            foreach ($orderedLessons as $les) {
                if ($foundCurrent) {
                    $nextLesson = $les;
                    break;
                }
                if ($les->id == $lessonId) {
                    $foundCurrent = true;
                }
            }

            if ($nextLesson) {
                return redirect()->route('student.classroom', [$slug, 'lesson' => $nextLesson->id])
                    ->with('success', $message);
            }

            return redirect()->back()->with('success', $message . ' Kamu telah menyelesaikan semua materi di kelas ini! 🎉');
        }
    }
}
