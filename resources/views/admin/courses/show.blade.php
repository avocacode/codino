@extends('layouts.app')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb font-playful">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Kelola Kelas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Silabus & Materi</li>
        </ol>
    </nav>

    <!-- Header Kelas -->
    <div class="p-4 bg-white rounded-5 shadow-sm border border-2 mb-4 d-flex flex-wrap align-items-center justify-content-between">
        <div>
            <span class="badge-age mb-2 d-inline-block"><i class="fa-solid fa-child me-1"></i> {{ $course->age_range }}</span>
            <h1 class="font-playful text-dark fw-bold mb-1">{{ $course->title }}</h1>
            <p class="text-muted mb-0">Atur kurikulum, modul pembelajaran, video link, ebook, dan kuis di sini.</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-warning text-white font-playful rounded-3"><i class="fa-solid fa-pen-to-square"></i> Edit Kelas</a>
            <button class="btn btn-playful-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal"><i class="fa-solid fa-circle-plus"></i> Tambah Bab/Modul</button>
            <button class="btn btn-playful-secondary" data-bs-toggle="modal" data-bs-target="#addQuizModal"><i class="fa-solid fa-puzzle-piece"></i> Sisipkan Kuis</button>
        </div>
    </div>

    <div class="row">
        <!-- Syllabus Sidebar / Quizzes List -->
        <div class="col-lg-4 mb-4">
            <!-- Quizzes List Card -->
            <div class="card card-playful border border-2 p-4 bg-white mb-4">
                <h3 class="font-playful text-dark fw-bold mb-3"><i class="fa-solid fa-puzzle-piece text-secondary me-1"></i> Kuis Kelas Ini</h3>
                <div class="list-group list-group-flush">
                    @forelse($course->quizzes as $quiz)
                        <div class="list-group-item bg-transparent px-0 py-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="font-playful text-dark fw-bold mb-1">{{ $quiz->title }}</h5>
                                    <small class="text-muted d-block">{{ $quiz->questions->count() }} Pertanyaan</small>
                                    @if($quiz->module)
                                        <small class="badge bg-light text-muted font-playful mt-1">Modul: {{ $quiz->module->title }}</small>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-link text-warning p-0" data-bs-toggle="modal" data-bs-target="#editQuizModal{{ $quiz->id }}"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Kakak yakin ingin menghapus kuis ini beserta semua soal di dalamnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-playful-primary py-1 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#addQuestionModal{{ $quiz->id }}"><i class="fa-solid fa-circle-plus"></i> Soal</button>
                                @if($quiz->questions->count() > 0)
                                    <button class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" type="button" data-bs-toggle="collapse" data-bs-target="#quizQuestionsList{{ $quiz->id }}" aria-expanded="false">
                                        <i class="fa-solid fa-eye"></i> Detail Soal ({{ $quiz->questions->count() }})
                                    </button>
                                @endif
                            </div>

                            <!-- Collapsible Quiz Questions Detail List -->
                            @if($quiz->questions->count() > 0)
                                <div class="collapse mt-3" id="quizQuestionsList{{ $quiz->id }}">
                                    <div class="bg-light p-3 rounded-4 border">
                                        <h6 class="font-playful text-dark fw-bold mb-2" style="font-size: 0.9rem;">Daftar Soal:</h6>
                                        <div class="list-group list-group-flush">
                                            @foreach($quiz->questions as $qIdx => $question)
                                                <div class="list-group-item bg-transparent px-0 py-2 border-0" style="font-size: 0.85rem;">
                                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                                        <div>
                                                            <span class="badge bg-secondary mb-1">No. {{ $qIdx + 1 }} - {{ Str::title(str_replace('_', ' ', $question->type)) }} ({{ $question->points }} Poin)</span>
                                                            <div class="mb-1 text-dark">{!! $question->question_text !!}</div>
                                                            @if($question->type === 'multiple_choice' && $question->options)
                                                                <ul class="ps-3 mb-1 text-muted" style="list-style-type: lower-alpha;">
                                                                    @foreach($question->options as $opt)
                                                                        @if($opt) <li>{{ $opt }}</li> @endif
                                                                    @endforeach
                                                                </ul>
                                                                @if($question->correct_answer)
                                                                    <p class="mb-0 text-success" style="font-size: 0.8rem;"><i class="fa-solid fa-circle-check"></i> Kunci: {{ $question->correct_answer }}</p>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <div class="d-flex gap-1">
                                                            <button class="btn btn-sm btn-link text-warning p-0" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $question->id }}"><i class="fa-solid fa-pen"></i></button>
                                                            <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus soal kuis ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0" style="font-size: 0.8rem;"><i class="fa-solid fa-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <span class="text-muted font-playful">Belum ada kuis yang disisipkan.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Modules & Lessons List -->
        <div class="col-lg-8">
            <div class="p-4 bg-white rounded-5 shadow-sm border border-2">
                <h2 class="font-playful text-dark fw-bold mb-4"><i class="fa-solid fa-route text-primary me-2"></i> Struktur Bab & Materi</h2>
                
                @forelse($course->modules as $mIndex => $module)
                    <div class="border-bottom pb-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill font-playful px-3 py-2 fs-6">Bab {{ $mIndex + 1 }}</span>
                                <h4 class="font-playful text-dark fw-bold mb-0">{{ $module->title }}</h4>
                                <div class="d-flex gap-1 ms-2">
                                    <button class="btn btn-sm btn-link text-warning p-0" data-bs-toggle="modal" data-bs-target="#editModuleModal{{ $module->id }}"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('admin.modules.destroy', $module->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Kakak yakin ingin menghapus Bab ini beserta semua materi di dalamnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-success border-2 font-playful rounded-3 py-1 px-3" data-bs-toggle="modal" data-bs-target="#addLessonModal{{ $module->id }}"><i class="fa-solid fa-circle-plus"></i> Materi</button>
                        </div>
                        <p class="text-muted ps-2">{{ $module->description }}</p>

                        <!-- Lessons list -->
                        <div class="list-group list-group-flush ps-4 border-start border-2 border-light">
                            @forelse($module->lessons as $lesson)
                                <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($lesson->content_type === 'video')
                                                <i class="fa-solid fa-circle-play text-primary fs-5"></i>
                                            @elseif($lesson->content_type === 'ebook')
                                                <i class="fa-solid fa-book-open text-success fs-5"></i>
                                            @else
                                                <i class="fa-solid fa-file-lines text-muted fs-5"></i>
                                            @endif
                                            <strong class="text-dark font-playful fs-6">{{ $lesson->title }}</strong>
                                        </div>
                                        <small class="text-muted d-block ps-4 mt-1">{{ $lesson->description }}</small>
                                        
                                        @if($lesson->video_url)
                                            <small class="text-primary d-block ps-4"><i class="fa-solid fa-link"></i> {{ $lesson->video_url }}</small>
                                        @endif
                                        
                                        <!-- Downloadable Sources -->
                                        @if($lesson->sources->count() > 0)
                                            <div class="ps-4 mt-2">
                                                @foreach($lesson->sources as $source)
                                                    <span class="badge bg-info text-white font-playful me-1"><i class="fa-solid fa-download"></i> {{ $source->title }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-warning font-playful rounded-3 py-1 px-2" data-bs-toggle="modal" data-bs-target="#editLessonModal{{ $lesson->id }}"><i class="fa-solid fa-pen"></i> Edit</button>
                                        <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Kakak yakin ingin menghapus materi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger font-playful rounded-3 py-1 px-2"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="ps-2 text-muted font-playful">Belum ada materi di bab ini. Tambahkan materi pertamamu!</div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-muted font-playful">Belum ada Bab/Modul untuk kelas ini. Klik "Tambah Bab/Modul" di atas untuk memulai!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal for Adding Module -->
<div class="modal fade" id="addModuleModal" tabindex="-1" aria-labelledby="addModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-5 border-0">
            <div class="modal-header border-bottom-0 pb-0">
                <h3 class="modal-title font-playful text-dark fw-bold" id="addModuleModalLabel"><i class="fa-solid fa-circle-plus text-primary me-2"></i> Tambah Bab Baru</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.modules.store', $course->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="module_title" class="form-label font-playful fw-bold text-dark">Judul Bab / Modul</label>
                        <input type="text" class="form-control rounded-3" id="module_title" name="title" placeholder="Contoh: Bab 1: Dunia Scratch Yang Seru!" required>
                    </div>
                    <div class="mb-3">
                        <label for="module_desc" class="form-label font-playful fw-bold text-dark">Deskripsi Ringkas</label>
                        <textarea class="form-control rounded-3" id="module_desc" name="description" rows="3" placeholder="Tulis ringkasan pelajaran dalam bab ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="submit" class="btn btn-playful-primary font-playful px-4"><i class="fa-solid fa-save"></i> Simpan Bab</button>
                    <button type="button" class="btn btn-light rounded-4 font-playful px-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Adding Quiz -->
<div class="modal fade" id="addQuizModal" tabindex="-1" aria-labelledby="addQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-5 border-0">
            <div class="modal-header border-bottom-0 pb-0">
                <h3 class="modal-title font-playful text-dark fw-bold" id="addQuizModalLabel"><i class="fa-solid fa-circle-plus text-primary me-2"></i> Sisipkan Kuis Baru</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.quizzes.store', $course->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quiz_title" class="form-label font-playful fw-bold text-dark">Judul Kuis</label>
                        <input type="text" class="form-control rounded-3" id="quiz_title" name="title" placeholder="Contoh: Kuis Seru: Balok Gerakan Scratch!" required>
                    </div>
                    <div class="mb-3">
                        <label for="quiz_desc" class="form-label font-playful fw-bold text-dark">Deskripsi Kuis</label>
                        <textarea class="form-control rounded-3" id="quiz_desc" name="description" rows="2" placeholder="Tuliskan petunjuk kuis untuk siswa cilik kita..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="quiz_module" class="form-label font-playful fw-bold text-dark">Hubungkan ke Bab (Optional)</label>
                        <select class="form-select rounded-3" id="quiz_module" name="module_id">
                            <option value="">-- Letakkan di Luar Bab (Kuis Global Kelas) --</option>
                            @foreach($course->modules as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="submit" class="btn btn-playful-primary font-playful px-4"><i class="fa-solid fa-save"></i> Sisipkan Kuis</button>
                    <button type="button" class="btn btn-light rounded-4 font-playful px-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals rendered at root level to prevent column layout conflicts and flickering -->
@foreach($course->quizzes as $quiz)
    <!-- Modal for Adding Question to this specific Quiz -->
    <div class="modal fade" id="addQuestionModal{{ $quiz->id }}" tabindex="-1" aria-labelledby="addQuestionModalLabel{{ $quiz->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-5 border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h3 class="modal-title font-playful text-dark fw-bold" id="addQuestionModalLabel{{ $quiz->id }}"><i class="fa-solid fa-circle-plus text-primary me-2"></i> Tambah Soal Kuis</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.questions.store', $quiz->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Tipe Pertanyaan</label>
                            <select class="form-select rounded-3" name="type" required onchange="toggleOptions(this.value, {{ $quiz->id }})">
                                <option value="multiple_choice">Pilihan Ganda</option>
                                <option value="essay">Essay / Jawaban Panjang</option>
                                <option value="coding_test">Coding Test / Logika Dasar</option>
                                <option value="submission">Submission Link / File Project</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Pertanyaan</label>
                            <textarea class="form-control rounded-3" name="question_text" rows="3" placeholder="Tuliskan isi pertanyaan kuis..." required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-playful fw-bold text-dark">Bobot Nilai (Poin)</label>
                                <input type="number" class="form-control rounded-3" name="points" value="10" min="1" required>
                            </div>
                        </div>

                        <!-- Options block for Multiple Choice only -->
                        <div id="mcOptionsBlock{{ $quiz->id }}">
                            <h5 class="font-playful text-dark fw-bold mt-4 mb-2"><i class="fa-solid fa-list-ul text-primary"></i> Pilihan Jawaban</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark font-playful">Opsi A</label>
                                    <input type="text" class="form-control rounded-3" name="option_a" placeholder="Pilihan A">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark font-playful">Opsi B</label>
                                    <input type="text" class="form-control rounded-3" name="option_b" placeholder="Pilihan B">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark font-playful">Opsi C</label>
                                    <input type="text" class="form-control rounded-3" name="option_c" placeholder="Pilihan C">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark font-playful">Opsi D</label>
                                    <input type="text" class="form-control rounded-3" name="option_d" placeholder="Pilihan D">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-playful fw-bold text-dark">Jawaban Benar</label>
                                <input type="text" class="form-control rounded-3" name="correct_answer" placeholder="Tuliskan persis opsi yang benar (misal: isi dari Opsi B)">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="submit" class="btn btn-playful-primary font-playful px-4"><i class="fa-solid fa-save"></i> Simpan Pertanyaan</button>
                        <button type="button" class="btn btn-light rounded-4 font-playful px-3" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@foreach($course->modules as $module)
    <!-- Modal for Adding Lesson to this specific Module -->
    <div class="modal fade" id="addLessonModal{{ $module->id }}" tabindex="-1" aria-labelledby="addLessonModalLabel{{ $module->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-5 border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h3 class="modal-title font-playful text-dark fw-bold" id="addLessonModalLabel{{ $module->id }}"><i class="fa-solid fa-circle-plus text-primary me-2"></i> Tambah Materi Pelajaran</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.lessons.store', $module->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Nama/Judul Materi</label>
                            <input type="text" class="form-control rounded-3" name="title" placeholder="Contoh: Koding Kucing Menari" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Deskripsi Singkat</label>
                            <textarea class="form-control rounded-3" name="description" rows="2" placeholder="Tuliskan petunjuk singkat materi..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Tipe Konten</label>
                            <select class="form-select rounded-3" name="content_type" required>
                                <option value="video">Video (YouTube)</option>
                                <option value="ebook">Ebook (PDF)</option>
                                <option value="text">Hanya Tulisan Panduan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">YouTube Link (Untuk Tipe Video)</label>
                            <input type="text" class="form-control rounded-3" name="video_url" placeholder="Contoh: https://www.youtube.com/embed/XXXXXX">
                        </div>

                        <!-- Sources / Downloadable Section -->
                        <h5 class="font-playful text-dark fw-bold mt-4 mb-2"><i class="fa-solid fa-download text-primary"></i> Sisipkan Resource Pendukung (Section Source)</h5>
                        <div class="mb-3">
                            <label class="form-label text-dark font-playful">Nama Resource</label>
                            <input type="text" class="form-control rounded-3" name="source_title" placeholder="Contoh: Download Scratch Desktop / Link Github">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark font-playful">URL Link Download</label>
                            <input type="text" class="form-control rounded-3" name="source_url" placeholder="Contoh: https://example.com/download">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="submit" class="btn btn-playful-primary font-playful px-4"><i class="fa-solid fa-save"></i> Simpan Materi</button>
                        <button type="button" class="btn btn-light rounded-4 font-playful px-3" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Edit Module Modals -->
@foreach($course->modules as $module)
    <div class="modal fade" id="editModuleModal{{ $module->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-5 border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h3 class="modal-title font-playful text-dark fw-bold"><i class="fa-solid fa-pen text-warning me-2"></i> Edit Bab / Modul</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.modules.update', $module->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Judul Bab / Modul</label>
                            <input type="text" class="form-control rounded-3" name="title" value="{{ $module->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Deskripsi Ringkas</label>
                            <textarea class="form-control rounded-3" name="description" rows="3">{{ $module->description }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="submit" class="btn btn-warning text-white font-playful px-4"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                        <button type="button" class="btn btn-light rounded-4 font-playful px-3" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Lesson Modals -->
    @foreach($module->lessons as $lesson)
        <div class="modal fade" id="editLessonModal{{ $lesson->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-5 border-0">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h3 class="modal-title font-playful text-dark fw-bold"><i class="fa-solid fa-pen text-warning me-2"></i> Edit Materi Pelajaran</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.lessons.update', $lesson->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label font-playful fw-bold text-dark">Nama/Judul Materi</label>
                                <input type="text" class="form-control rounded-3" name="title" value="{{ $lesson->title }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-playful fw-bold text-dark">Deskripsi Singkat</label>
                                <textarea class="form-control rounded-3" name="description" rows="2">{{ $lesson->description }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-playful fw-bold text-dark">Tipe Konten</label>
                                <select class="form-select rounded-3" name="content_type" required>
                                    <option value="video" {{ $lesson->content_type === 'video' ? 'selected' : '' }}>Video (YouTube)</option>
                                    <option value="ebook" {{ $lesson->content_type === 'ebook' ? 'selected' : '' }}>Ebook (PDF)</option>
                                    <option value="text" {{ $lesson->content_type === 'text' ? 'selected' : '' }}>Hanya Tulisan Panduan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-playful fw-bold text-dark">YouTube Link (Untuk Tipe Video)</label>
                                <input type="text" class="form-control rounded-3" name="video_url" value="{{ $lesson->video_url }}">
                            </div>
                            @php $source = $lesson->sources()->first(); @endphp
                            <h5 class="font-playful text-dark fw-bold mt-4 mb-2"><i class="fa-solid fa-download text-primary"></i> Sisipkan Resource Pendukung (Section Source)</h5>
                            <div class="mb-3">
                                <label class="form-label text-dark font-playful">Nama Resource</label>
                                <input type="text" class="form-control rounded-3" name="source_title" value="{{ $source ? $source->title : '' }}" placeholder="Contoh: Download Scratch Desktop">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark font-playful">URL Link Download</label>
                                <input type="text" class="form-control rounded-3" name="source_url" value="{{ $source ? $source->url : '' }}" placeholder="Contoh: https://example.com/download">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0">
                            <button type="submit" class="btn btn-warning text-white font-playful px-4"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                            <button type="button" class="btn btn-light rounded-4 font-playful px-3" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

<!-- Edit Quiz Modals -->
@foreach($course->quizzes as $quiz)
    <div class="modal fade" id="editQuizModal{{ $quiz->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-5 border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h3 class="modal-title font-playful text-dark fw-bold"><i class="fa-solid fa-pen text-warning me-2"></i> Edit Kuis</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Judul Kuis</label>
                            <input type="text" class="form-control rounded-3" name="title" value="{{ $quiz->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Deskripsi Kuis</label>
                            <textarea class="form-control rounded-3" name="description" rows="2">{{ $quiz->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-playful fw-bold text-dark">Hubungkan ke Bab</label>
                            <select class="form-select rounded-3" name="module_id">
                                <option value="">-- Letakkan di Luar Bab (Kuis Global Kelas) --</option>
                                @foreach($course->modules as $mod)
                                    <option value="{{ $mod->id }}" {{ $quiz->module_id == $mod->id ? 'selected' : '' }}>{{ $mod->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="submit" class="btn btn-warning text-white font-playful px-4"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                        <button type="button" class="btn btn-light rounded-4 font-playful px-3" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Question Modals -->
    @foreach($quiz->questions as $question)
        <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content rounded-5 border-0">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h3 class="modal-title font-playful text-dark fw-bold"><i class="fa-solid fa-pen text-warning me-2"></i> Edit Soal Kuis</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.questions.update', $question->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label font-playful fw-bold text-dark">Tipe Pertanyaan</label>
                                <select class="form-select rounded-3" name="type" required onchange="toggleEditOptions(this.value, {{ $question->id }})">
                                    <option value="multiple_choice" {{ $question->type === 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                                    <option value="essay" {{ $question->type === 'essay' ? 'selected' : '' }}>Essay / Jawaban Panjang</option>
                                    <option value="coding_test" {{ $question->type === 'coding_test' ? 'selected' : '' }}>Coding Test / Logika Dasar</option>
                                    <option value="submission" {{ $question->type === 'submission' ? 'selected' : '' }}>Submission Link / File Project</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label font-playful fw-bold text-dark">Pertanyaan</label>
                                <textarea class="form-control rounded-3" name="question_text" rows="3" required>{{ $question->question_text }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-playful fw-bold text-dark">Bobot Nilai (Poin)</label>
                                    <input type="number" class="form-control rounded-3" name="points" value="{{ $question->points }}" min="1" required>
                                </div>
                            </div>

                            <!-- Options block for Multiple Choice only -->
                            <div id="editMcOptionsBlock{{ $question->id }}" style="display: {{ $question->type === 'multiple_choice' ? 'block' : 'none' }};">
                                <h5 class="font-playful text-dark fw-bold mt-4 mb-2"><i class="fa-solid fa-list-ul text-primary"></i> Pilihan Jawaban</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-dark font-playful">Opsi A</label>
                                        <input type="text" class="form-control rounded-3" name="option_a" value="{{ $question->options[0] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-dark font-playful">Opsi B</label>
                                        <input type="text" class="form-control rounded-3" name="option_b" value="{{ $question->options[1] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-dark font-playful">Opsi C</label>
                                        <input type="text" class="form-control rounded-3" name="option_c" value="{{ $question->options[2] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-dark font-playful">Opsi D</label>
                                        <input type="text" class="form-control rounded-3" name="option_d" value="{{ $question->options[3] ?? '' }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label font-playful fw-bold text-dark">Jawaban Benar</label>
                                    <input type="text" class="form-control rounded-3" name="correct_answer" value="{{ $question->correct_answer }}" placeholder="Tuliskan persis opsi yang benar">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0">
                            <button type="submit" class="btn btn-warning text-white font-playful px-4"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
                            <button type="button" class="btn btn-light rounded-4 font-playful px-3" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

<script>
function toggleOptions(type, quizId) {
    var block = document.getElementById('mcOptionsBlock' + quizId);
    if (type === 'multiple_choice') {
        block.style.display = 'block';
    } else {
        block.style.display = 'none';
    }
}
function toggleEditOptions(type, questionId) {
    var block = document.getElementById('editMcOptionsBlock' + questionId);
    if (type === 'multiple_choice') {
        block.style.display = 'block';
    } else {
        block.style.display = 'none';
    }
}
</script>

<!-- CKEditor 5 Classic CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('textarea[name="question_text"]').forEach(function(textarea) {
            ClassicEditor
                .create(textarea, {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
                })
                .catch(error => {
                    console.error(error);
                });
        });
    });
</script>

<style>
.ck-editor__editable {
    min-height: 150px;
    max-height: 350px;
}
.ck-rounded-corners .ck.ck-balloon-panel,
.ck.ck-balloon-panel_visible {
    z-index: 10055 !important;
}
.markdown-content p:last-child {
    margin-bottom: 0 !important;
}
.markdown-content pre {
    background-color: #282a36;
    color: #f8f8f2;
    padding: 0.75rem;
    border-radius: 0.5rem;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
    overflow-x: auto;
    font-size: 0.8rem;
}
.markdown-content code {
    background-color: #f8f9fa;
    color: #d63384;
    padding: 0.1rem 0.2rem;
    border-radius: 0.2rem;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.85em;
}
.markdown-content pre code {
    background-color: transparent;
    color: inherit;
    padding: 0;
    font-size: 1em;
}
</style>
@endsection
