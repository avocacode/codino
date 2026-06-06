<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\LessonSource;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // List all courses
    public function index()
    {
        $courses = Course::withCount(['modules', 'students'])->get();
        return view('admin.courses.index', compact('courses'));
    }

    // Create course form
    public function create()
    {
        return view('admin.courses.create');
    }

    // Store course
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'age_range' => 'required|string|max:100',
            'prerequisites' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        Course::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'age_range' => $request->age_range,
            'prerequisites' => $request->prerequisites,
            'price' => $request->price,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Kelas baru berhasil dibuat!');
    }

    // Show course syllabus & modules management
    public function show($id)
    {
        $course = Course::with(['modules.lessons.sources', 'quizzes.questions'])->findOrFail($id);
        return view('admin.courses.show', compact('course'));
    }

    // Edit course form
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('admin.courses.edit', compact('course'));
    }

    // Update course
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'age_range' => 'required|string|max:100',
            'prerequisites' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $course->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'age_range' => $request->age_range,
            'prerequisites' => $request->prerequisites,
            'price' => $request->price,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.courses.show', $course->id)->with('success', 'Detail kelas berhasil diperbarui!');
    }

    // Delete course
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Kelas berhasil dihapus.');
    }

    // Store Module inside a course
    public function storeModule(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course = Course::findOrFail($courseId);
        $order = $course->modules()->count() + 1;

        Module::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $order
        ]);

        return redirect()->back()->with('success', 'Bab/Modul baru berhasil ditambahkan!');
    }

    // Store Lesson inside a module
    public function storeLesson(Request $request, $moduleId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,ebook,text',
            'video_url' => 'nullable|string',
            'source_title' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
        ]);

        $module = Module::findOrFail($moduleId);
        $order = $module->lessons()->count() + 1;

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => $request->title,
            'description' => $request->description,
            'content_type' => $request->content_type,
            'video_url' => $request->video_url,
            'order' => $order
        ]);

        // If source provided
        if ($request->filled('source_title') && $request->filled('source_url')) {
            LessonSource::create([
                'lesson_id' => $lesson->id,
                'title' => $request->source_title,
                'url' => $request->source_url
            ]);
        }

        return redirect()->back()->with('success', 'Materi/Lesson baru berhasil ditambahkan!');
    }

    // Store Quiz inside a course/module
    public function storeQuiz(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'nullable|exists:modules,id',
        ]);

        Quiz::create([
            'course_id' => $courseId,
            'module_id' => $request->module_id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Kuis baru berhasil disisipkan!');
    }

    // Store Quiz Question
    public function storeQuestion(Request $request, $quizId)
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,essay,coding_test,submission',
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
            'option_a' => 'nullable|string',
            'option_b' => 'nullable|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_answer' => 'nullable|string',
        ]);

        $options = null;
        if ($request->type === 'multiple_choice') {
            $options = [
                $request->option_a,
                $request->option_b,
                $request->option_c,
                $request->option_d
            ];
        }

        QuizQuestion::create([
            'quiz_id' => $quizId,
            'type' => $request->type,
            'question_text' => $request->question_text,
            'points' => $request->points,
            'options' => $options,
            'correct_answer' => $request->type === 'multiple_choice' ? $request->correct_answer : null,
        ]);

        return redirect()->back()->with('success', 'Pertanyaan kuis baru berhasil ditambahkan!');
    }

    // Update Module
    public function updateModule(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $module = Module::findOrFail($id);
        $module->update($request->only('title', 'description'));

        return redirect()->back()->with('success', 'Bab/Modul berhasil diperbarui!');
    }

    // Delete Module
    public function destroyModule($id)
    {
        $module = Module::findOrFail($id);
        $module->delete();
        return redirect()->back()->with('success', 'Bab/Modul berhasil dihapus!');
    }

    // Update Lesson
    public function updateLesson(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,ebook,text',
            'video_url' => 'nullable|string',
            'source_title' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
        ]);

        $lesson = Lesson::findOrFail($id);
        $lesson->update([
            'title' => $request->title,
            'description' => $request->description,
            'content_type' => $request->content_type,
            'video_url' => $request->video_url,
        ]);

        if ($request->filled('source_title') && $request->filled('source_url')) {
            $source = $lesson->sources()->first();
            if ($source) {
                $source->update([
                    'title' => $request->source_title,
                    'url' => $request->source_url
                ]);
            } else {
                LessonSource::create([
                    'lesson_id' => $lesson->id,
                    'title' => $request->source_title,
                    'url' => $request->source_url
                ]);
            }
        } else {
            $lesson->sources()->delete();
        }

        return redirect()->back()->with('success', 'Materi/Lesson berhasil diperbarui!');
    }

    // Delete Lesson
    public function destroyLesson($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();
        return redirect()->back()->with('success', 'Materi/Lesson berhasil dihapus!');
    }

    // Update Quiz
    public function updateQuiz(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'nullable|exists:modules,id',
        ]);

        $quiz = Quiz::findOrFail($id);
        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'module_id' => $request->module_id ?: null,
        ]);

        return redirect()->back()->with('success', 'Kuis berhasil diperbarui!');
    }

    // Delete Quiz
    public function destroyQuiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();
        return redirect()->back()->with('success', 'Kuis berhasil dihapus!');
    }

    // Update Question
    public function updateQuestion(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,essay,coding_test,submission',
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
            'option_a' => 'nullable|string',
            'option_b' => 'nullable|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_answer' => 'nullable|string',
        ]);

        $question = QuizQuestion::findOrFail($id);

        $options = null;
        if ($request->type === 'multiple_choice') {
            $options = [
                $request->option_a,
                $request->option_b,
                $request->option_c,
                $request->option_d
            ];
        }

        $question->update([
            'type' => $request->type,
            'question_text' => $request->question_text,
            'points' => $request->points,
            'options' => $options,
            'correct_answer' => $request->type === 'multiple_choice' ? $request->correct_answer : null,
        ]);

        return redirect()->back()->with('success', 'Soal kuis berhasil diperbarui!');
    }

    // Delete Question
    public function destroyQuestion($id)
    {
        $question = QuizQuestion::findOrFail($id);
        $question->delete();
        return redirect()->back()->with('success', 'Soal kuis berhasil dihapus!');
    }
}
