<?php

use Illuminate\Support\Facades\Route;

// Import Controllers
use App\Http\Controllers\PublicCourseController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminCourseController;
use App\Http\Controllers\AdminSubmissionController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\QuizController;

// Public Routes
Route::get('/', [PublicCourseController::class, 'index']);
Route::get('/kelas/{slug}', [PublicCourseController::class, 'show'])->name('kelas.show');

// Auth Routes Scaffolded by Laravel UI
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin Panel Routes (Protected by auth and admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/students', [AdminDashboardController::class, 'students'])->name('admin.students');
    Route::post('/enrollments/{id}/approve', [AdminDashboardController::class, 'approveEnrollment'])->name('admin.enrollments.approve');
    Route::post('/enrollments/{id}/reject', [AdminDashboardController::class, 'rejectEnrollment'])->name('admin.enrollments.reject');

    // Course CRUD
    Route::resource('courses', AdminCourseController::class, ['names' => 'admin.courses']);
    Route::post('/courses/{courseId}/modules', [AdminCourseController::class, 'storeModule'])->name('admin.modules.store');
    Route::put('/modules/{id}', [AdminCourseController::class, 'updateModule'])->name('admin.modules.update');
    Route::delete('/modules/{id}', [AdminCourseController::class, 'destroyModule'])->name('admin.modules.destroy');

    Route::post('/modules/{moduleId}/lessons', [AdminCourseController::class, 'storeLesson'])->name('admin.lessons.store');
    Route::put('/lessons/{id}', [AdminCourseController::class, 'updateLesson'])->name('admin.lessons.update');
    Route::delete('/lessons/{id}', [AdminCourseController::class, 'destroyLesson'])->name('admin.lessons.destroy');

    Route::post('/courses/{courseId}/quizzes', [AdminCourseController::class, 'storeQuiz'])->name('admin.quizzes.store');
    Route::put('/quizzes/{id}', [AdminCourseController::class, 'updateQuiz'])->name('admin.quizzes.update');
    Route::delete('/quizzes/{id}', [AdminCourseController::class, 'destroyQuiz'])->name('admin.quizzes.destroy');

    Route::post('/quizzes/{quizId}/questions', [AdminCourseController::class, 'storeQuestion'])->name('admin.questions.store');
    Route::put('/questions/{id}', [AdminCourseController::class, 'updateQuestion'])->name('admin.questions.update');
    Route::delete('/questions/{id}', [AdminCourseController::class, 'destroyQuestion'])->name('admin.questions.destroy');
    
    // Submissions
    Route::get('/submissions', [AdminSubmissionController::class, 'index'])->name('admin.submissions.index');
    Route::get('/submissions/{id}', [AdminSubmissionController::class, 'show'])->name('admin.submissions.show');
    Route::post('/submissions/{id}/grade', [AdminSubmissionController::class, 'grade'])->name('admin.submissions.grade');
});

// Student Panel Routes (Protected by auth and student middleware)
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::post('/kelas/{slug}/enroll', [StudentDashboardController::class, 'enroll'])->name('student.enroll');
    Route::get('/kelas/{slug}/belajar', [StudentDashboardController::class, 'classroom'])->name('student.classroom');
    Route::post('/kelas/{slug}/materi/{lessonId}/complete', [StudentDashboardController::class, 'toggleComplete'])->name('student.lesson.complete');
    
    // Quizzes
    Route::get('/kelas/{slug}/kuis/{quizId}', [QuizController::class, 'show'])->name('student.quiz.show');
    Route::post('/kelas/{slug}/kuis/{quizId}/submit', [QuizController::class, 'submit'])->name('student.quiz.submit');
    Route::get('/kelas/{slug}/kuis/{quizId}/hasil', [QuizController::class, 'results'])->name('student.quiz.results');
});
