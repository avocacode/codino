@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="row">
        <!-- Sidebar Navigation: Modules & Lessons -->
        <div class="col-lg-4 mb-4">
            <div class="card card-playful border border-2 p-4 bg-white shadow-sm sticky-top" style="top: 90px; max-height: calc(100vh - 120px); overflow-y: auto;">
                <h3 class="font-playful text-dark fw-bold mb-3">
                    <i class="fas fa-gamepad text-primary"></i> Peta Belajar
                </h3>
                <h5 class="text-muted font-playful mb-4">{{ $course->title }}</h5>

                @foreach($course->modules as $mIndex => $module)
                    <div class="mb-4">
                        <div class="bg-light p-3 rounded-4 mb-2">
                            <span class="badge bg-primary text-white font-playful rounded-pill px-2 py-1 mb-1" style="font-size: 0.75rem;">Bab {{ $mIndex + 1 }}</span>
                            <h5 class="font-playful text-dark fw-bold mb-0" style="font-size: 1rem;">{{ $module->title }}</h5>
                        </div>

                        <!-- Lessons list -->
                        <div class="list-group list-group-flush ps-2">
                            @foreach($module->lessons as $lesson)
                                @php 
                                    $isCompleted = in_array($lesson->id, $completedLessonIds);
                                    $isUnlocked = $unlockedLessons[$lesson->id] ?? false;
                                @endphp
                                @if($isUnlocked)
                                    <a href="{{ route('student.classroom', $course->slug) }}?lesson={{ $lesson->id }}" 
                                       class="list-group-item list-group-item-action bg-transparent border-0 px-2 py-2 d-flex align-items-center gap-2 rounded-3 {{ $activeLesson && $activeLesson->id === $lesson->id ? 'text-primary fw-bold bg-light' : 'text-muted' }}">
                                        @if($isCompleted)
                                            <i class="fa-solid fa-circle-check text-success fs-5"></i>
                                        @elseif($lesson->content_type === 'video')
                                            <i class="fas fa-play-circle fs-5"></i>
                                        @elseif($lesson->content_type === 'ebook')
                                            <i class="fas fa-book-open fs-5"></i>
                                        @else
                                            <i class="fas fa-file-alt fs-5"></i>
                                        @endif
                                        <span class="font-playful" style="font-size: 0.9rem; {{ $isCompleted ? 'text-decoration: line-through; opacity: 0.7;' : '' }}">{{ $lesson->title }}</span>
                                    </a>
                                @else
                                    <div class="list-group-item bg-transparent border-0 px-2 py-2 d-flex align-items-center gap-2 rounded-3 text-muted" style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="fa-solid fa-lock fs-5"></i>
                                        <span class="font-playful" style="font-size: 0.9rem;">{{ $lesson->title }}</span>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Quizzes under this module -->
                            @php
                                $moduleQuizzes = $course->quizzes->where('module_id', $module->id);
                            @endphp
                            @foreach($moduleQuizzes as $quiz)
                                @php
                                    $isQuizUnlocked = $unlockedQuizzes[$quiz->id] ?? false;
                                    $quizSubmitted = in_array($quiz->id, $submittedQuizIds);
                                @endphp
                                @if($isQuizUnlocked)
                                    @if($quizSubmitted)
                                        <a href="{{ route('student.quiz.results', [$course->slug, $quiz->id]) }}" 
                                           class="list-group-item list-group-item-action bg-transparent border-0 px-2 py-2 d-flex align-items-center gap-2 text-success rounded-3">
                                            <i class="fa-solid fa-circle-check fs-5"></i>
                                            <span class="font-playful" style="font-size: 0.9rem; text-decoration: line-through; opacity: 0.7;">Kuis: {{ $quiz->title }}</span>
                                            <span class="badge bg-success text-white font-playful" style="font-size: 0.75rem;">Selesai</span>
                                        </a>
                                    @else
                                        <a href="{{ route('student.quiz.show', [$course->slug, $quiz->id]) }}" 
                                           class="list-group-item list-group-item-action bg-transparent border-0 px-2 py-2 d-flex align-items-center gap-2 text-warning rounded-3">
                                            <i class="fas fa-puzzle-piece fs-5"></i>
                                            <span class="font-playful" style="font-size: 0.9rem;">Kuis: {{ $quiz->title }}</span>
                                            <span class="badge bg-warning text-dark font-playful" style="font-size: 0.75rem;">Mulai</span>
                                        </a>
                                    @endif
                                @else
                                    <div class="list-group-item bg-transparent border-0 px-2 py-2 d-flex align-items-center gap-2 text-muted rounded-3" style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="fa-solid fa-lock fs-5"></i>
                                        <span class="font-playful" style="font-size: 0.9rem;">Kuis: {{ $quiz->title }}</span>
                                        <span class="badge bg-secondary text-white font-playful" style="font-size: 0.75rem;">Terkunci</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                <!-- Global Quizzes (no module) -->
                @php
                    $globalQuizzes = $course->quizzes->whereNull('module_id');
                @endphp
                @if($globalQuizzes->count() > 0)
                    <div class="mt-2 border-top pt-3">
                        <h5 class="font-playful text-dark fw-bold mb-2">Kuis Akhir Kelas</h5>
                        <div class="list-group list-group-flush">
                            @foreach($globalQuizzes as $quiz)
                                @php
                                    $isQuizUnlocked = $unlockedQuizzes[$quiz->id] ?? false;
                                    $quizSubmitted = in_array($quiz->id, $submittedQuizIds);
                                @endphp
                                @if($isQuizUnlocked)
                                    @if($quizSubmitted)
                                        <a href="{{ route('student.quiz.results', [$course->slug, $quiz->id]) }}" 
                                           class="list-group-item list-group-item-action bg-transparent border-0 px-2 py-2 d-flex align-items-center gap-2 text-success rounded-3">
                                            <i class="fa-solid fa-circle-check fs-5"></i>
                                            <span class="font-playful" style="font-size: 0.9rem; text-decoration: line-through; opacity: 0.7;">{{ $quiz->title }}</span>
                                            <span class="badge bg-success text-white font-playful" style="font-size: 0.75rem;">Selesai</span>
                                        </a>
                                    @else
                                        <a href="{{ route('student.quiz.show', [$course->slug, $quiz->id]) }}" 
                                           class="list-group-item list-group-item-action bg-transparent border-0 px-2 py-2 d-flex align-items-center gap-2 text-warning rounded-3">
                                            <i class="fas fa-graduation-cap fs-5"></i>
                                            <span class="font-playful" style="font-size: 0.9rem;">{{ $quiz->title }}</span>
                                            <span class="badge bg-warning text-dark font-playful" style="font-size: 0.75rem;">Mulai</span>
                                        </a>
                                    @endif
                                @else
                                    <div class="list-group-item bg-transparent border-0 px-2 py-2 d-flex align-items-center gap-2 text-muted rounded-3" style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="fa-solid fa-lock fs-5"></i>
                                        <span class="font-playful" style="font-size: 0.9rem;">{{ $quiz->title }} (Selesaikan semua materi)</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Workspace Area: Active Lesson Content -->
        <div class="col-lg-8">
            @if($activeLesson)
                <div class="card card-playful border border-2 p-4 p-md-5 bg-white mb-4 shadow-sm">
                    <!-- Lesson Header -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        @if($activeLesson->content_type === 'video')
                            <span class="badge bg-primary text-white font-playful px-3 py-2 rounded-pill"><i class="fas fa-play-circle"></i> Video Lesson</span>
                        @elseif($activeLesson->content_type === 'ebook')
                            <span class="badge bg-success text-white font-playful px-3 py-2 rounded-pill"><i class="fas fa-book-open"></i> Ebook</span>
                        @else
                            <span class="badge bg-secondary text-white font-playful px-3 py-2 rounded-pill"><i class="fas fa-file-alt"></i> Tutorial</span>
                        @endif
                        <h2 class="font-playful text-dark fw-bold mb-0">{{ $activeLesson->title }}</h2>
                    </div>
                    <p class="text-muted lead font-playful mb-4">{{ $activeLesson->description }}</p>

                    <!-- Content Display -->
                    <div class="mb-5">
                        @if($activeLesson->content_type === 'video' && $activeLesson->video_url)
                            <div class="ratio ratio-16x9 rounded-5 overflow-hidden border border-3 border-light shadow-sm">
                                <iframe src="{{ $activeLesson->getEmbedUrl() }}" title="Lesson Video" allowfullscreen></iframe>
                            </div>
                        @elseif($activeLesson->content_type === 'ebook')
                            <div class="p-5 bg-light rounded-5 text-center border border-2 border-dashed">
                                <i class="fas fa-file-pdf display-1 text-danger mb-3"></i>
                                <h4 class="font-playful text-dark fw-bold">Buku Panduan Siap Dibaca!</h4>
                                <p class="text-muted mb-4 font-playful">Kakak sudah sediakan buku petunjuk gambar penuh warna untuk membantumu koding.</p>
                                <a href="#" class="btn btn-playful-primary font-playful fs-5 px-4"><i class="fas fa-download"></i> Download Ebook PDF</a>
                            </div>
                        @else
                            <div class="p-4 bg-light rounded-4 border">
                                <p class="mb-0 fs-5 font-playful">Silakan baca instruksi materi pada modul panduan belajar atau download resource pendukung di bawah!</p>
                            </div>
                        @endif
                    </div>

                    <!-- Section Source (Resource Pendukung) -->
                    @if($activeLesson->sources->count() > 0)
                        <div class="p-4 bg-light rounded-5 border border-2 border-primary mb-4">
                            <h4 class="font-playful text-dark fw-bold mb-3"><i class="fas fa-download text-primary me-2"></i> Download Bahan Belajar Kita (Section Source)</h4>
                            <p class="text-muted font-playful mb-4">Yuk download bahan koding, template project, atau aplikasi pendukung di bawah ini agar belajarmu makin asyik!</p>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($activeLesson->sources as $source)
                                    <a href="{{ $source->url }}" target="_blank" class="btn btn-playful-secondary font-playful text-white fs-5 px-4 shadow-sm">
                                        <i class="fas fa-arrow-circle-down me-1"></i> {{ $source->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @php 
                        $activeLessonCompleted = auth()->user()->completedLessons->where('lesson_id', $activeLesson->id)->count() > 0;
                    @endphp
                    <div class="border-top pt-4 text-center mt-2">
                        <form action="{{ route('student.lesson.complete', [$course->slug, $activeLesson->id]) }}" method="POST">
                            @csrf
                            @if($activeLessonCompleted)
                                <button type="submit" class="btn btn-outline-success font-playful rounded-4 px-4 py-2 border-2">
                                    <i class="fa-solid fa-circle-check"></i> Sudah Selesai Belajar (Klik untuk Batalkan)
                                </button>
                            @else
                                <button type="submit" class="btn btn-playful-primary font-playful fs-5 px-5 py-3 shadow">
                                    <i class="fa-solid fa-circle-check"></i> Selesai Belajar Bab Ini! 🚀
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @else
                <div class="card card-playful border border-2 p-5 bg-white text-center shadow-sm">
                    <i class="fas fa-map-signs display-1 text-muted mb-3"></i>
                    <h3 class="font-playful text-dark fw-bold">Selamat Datang di Ruang Belajar!</h3>
                    <p class="text-muted font-playful">Klik salah satu materi di Peta Belajar sebelah kiri untuk memulai petualangan coding-mu! 🚀</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
