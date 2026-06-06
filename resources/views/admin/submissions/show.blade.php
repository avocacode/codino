@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 font-playful">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.submissions.index') }}" class="text-decoration-none">Daftar Penilaian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Periksa Jawaban</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="p-4 bg-white rounded-5 shadow-sm border border-2 mb-4 d-flex flex-wrap align-items-center justify-content-between">
        <div>
            <h1 class="font-playful text-dark fw-bold mb-1"><i class="fa-solid fa-square-poll-horizontal text-primary me-2"></i> Periksa Jawaban Kuis</h1>
            <p class="text-muted mb-0">Siswa: <strong>{{ $submission->user->name }}</strong> ({{ $submission->user->email }}) | Kuis: <strong>{{ $submission->quiz->title }}</strong></p>
        </div>
        <div>
            @if($submission->status === 'graded')
                <span class="badge bg-success font-playful fs-5 py-2 px-3 rounded-pill"><i class="fa-solid fa-circle-check"></i> Sudah Dinilai: {{ $submission->score }}/100</span>
            @else
                <span class="badge bg-warning text-dark font-playful fs-5 py-2 px-3 rounded-pill"><i class="fa-solid fa-hourglass-half"></i> Menunggu Nilai</span>
            @endif
        </div>
    </div>

    <!-- Grading Form -->
    <form action="{{ route('admin.submissions.grade', $submission->id) }}" method="POST">
        @csrf

        @foreach($submission->answers as $index => $answer)
            <div class="card card-playful border border-2 p-4 bg-white mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white rounded-pill font-playful">Soal {{ $index + 1 }}</span>
                        <span class="text-muted font-playful">Tipe: <strong>{{ strtoupper(str_replace('_', ' ', $answer->question->type)) }}</strong></span>
                    </div>
                    <span class="badge bg-light text-dark font-playful fs-6">Poin Maksimal: {{ $answer->question->points }}</span>
                </div>

                <div class="font-playful text-dark mb-4 markdown-content">
                    {!! $answer->question->question_text !!}
                </div>

                <div class="row g-4">
                    <!-- Student Answer Column -->
                    <div class="col-md-8 border-end">
                        <h5 class="font-playful text-secondary fw-bold mb-3"><i class="fa-solid fa-reply"></i> Jawaban Siswa:</h5>
                        
                        @if($answer->question->type === 'multiple_choice')
                            <div class="p-3 bg-light rounded-4 border">
                                <span class="d-block mb-1 text-muted font-playful">Jawaban yang dipilih siswa:</span>
                                <strong class="text-dark fs-5 font-playful">{!! \Illuminate\Support\Str::inlineMarkdown($answer->selected_option) !!}</strong>
                            </div>
                            <div class="mt-2 font-playful">
                                @if(strtolower(trim($answer->selected_option)) === strtolower(trim($answer->question->correct_answer)))
                                    <span class="text-success"><i class="fa-solid fa-circle-check"></i> Otomatis Benar (Kunci Jawaban: {!! \Illuminate\Support\Str::inlineMarkdown($answer->question->correct_answer) !!})</span>
                                @else
                                    <span class="text-danger"><i class="fa-solid fa-circle-xmark"></i> Otomatis Salah (Kunci Jawaban: {!! \Illuminate\Support\Str::inlineMarkdown($answer->question->correct_answer) !!})</span>
                                @endif
                            </div>
                        @elseif($answer->question->type === 'essay')
                            <div class="p-3 bg-light rounded-4 border font-playful markdown-content">{!! \Illuminate\Support\Str::markdown($answer->answer_text) !!}</div>
                        @elseif($answer->question->type === 'coding_test')
                            <pre class="p-3 bg-dark text-white rounded-4 font-monospace m-0">{{ $answer->answer_text }}</pre>
                        @elseif($answer->question->type === 'submission')
                            <div class="p-3 bg-light rounded-4 border font-playful">
                                @if($answer->file_path)
                                    <span class="d-block mb-1 text-muted fw-bold"><i class="fa-solid fa-file-arrow-down text-primary"></i> File / Gambar yang Diunggah:</span>
                                    @php
                                        $extension = strtolower(pathinfo($answer->file_path, PATHINFO_EXTENSION));
                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                    @endphp
                                    @if($isImage)
                                        <div class="mb-3 mt-2">
                                            <a href="{{ asset('storage/' . $answer->file_path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $answer->file_path) }}" class="img-fluid rounded-3 border shadow-sm" style="max-height: 350px; cursor: zoom-in;" alt="Screenshot Jawaban Siswa">
                                            </a>
                                        </div>
                                    @endif
                                    <a href="{{ asset('storage/' . $answer->file_path) }}" target="_blank" class="btn btn-primary font-playful rounded-3 py-1 px-3 mb-3 text-white"><i class="fa-solid fa-download"></i> Unduh File Siswa ({{ strtoupper($extension) }})</a>
                                @endif
                                
                                @if($answer->answer_text)
                                    <div class="{{ $answer->file_path ? 'mt-3 pt-3 border-top' : '' }}">
                                        <span class="d-block mb-1 text-muted fw-bold"><i class="fa-solid fa-link text-primary"></i> Link Project Scratch / File:</span>
                                        <a href="{{ $answer->answer_text }}" target="_blank" class="btn btn-outline-primary font-playful border-2 rounded-3 py-1"><i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Link Project Siswa</a>
                                    </div>
                                @endif

                                @if(!$answer->file_path && !$answer->answer_text)
                                    <span class="text-danger font-playful"><i class="fa-solid fa-triangle-exclamation"></i> Tidak ada jawaban yang dikirimkan.</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Grading Input Column -->
                    <div class="col-md-4 d-flex flex-column justify-content-center">
                        <label class="form-label font-playful fw-bold text-dark fs-5"><i class="fa-solid fa-award"></i> Berikan Nilai Poin</label>
                        @if($answer->question->type === 'multiple_choice')
                            <input type="number" class="form-control rounded-3 p-3 bg-light font-playful" value="{{ $answer->points_awarded }}" readonly>
                            <small class="text-muted d-block mt-2">Poin pilihan ganda otomatis terkunci.</small>
                        @else
                            <input type="number" class="form-control rounded-3 p-3 font-playful @error('points_'.$answer->id) is-invalid @enderror" 
                                   name="points_{{ $answer->id }}" 
                                   value="{{ $answer->points_awarded ?? 0 }}" 
                                   max="{{ $answer->question->points }}" 
                                   min="0" required>
                            <small class="text-muted d-block mt-2">Masukkan nilai antara 0 sampai {{ $answer->question->points }} poin.</small>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Teacher Notes & Submit -->
        <div class="card card-playful border border-2 p-4 bg-white mb-5">
            <h3 class="font-playful text-dark fw-bold mb-3"><i class="fa-solid fa-comments text-success"></i> Catatan & Feedback Kakak Guru</h3>
            <div class="mb-4">
                <textarea class="form-control rounded-4 p-3" name="feedback" rows="4" placeholder="Tuliskan komentar penyemangat, pujian, atau saran bagi anak agar terus semangat belajar koding!">{{ $submission->feedback }}</textarea>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-playful-primary fs-5 px-5"><i class="fa-solid fa-square-check"></i> Simpan & Publikasikan Nilai Kuis</button>
            </div>
        </div>
    </form>
</div>

<style>
.markdown-content {
    font-weight: 400;
}
.markdown-content p, 
.markdown-content li, 
.markdown-content span,
.markdown-content div,
.markdown-content pre,
.markdown-content code {
    font-weight: 400;
}
.markdown-content strong,
.markdown-content b {
    font-weight: 700;
}
.markdown-content p:last-child {
    margin-bottom: 0 !important;
}
.markdown-content pre {
    background-color: #282a36;
    color: #f8f8f2;
    padding: 1rem;
    border-radius: 0.75rem;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    overflow-x: auto;
}
.markdown-content code {
    background-color: #f8f9fa;
    color: #d63384;
    padding: 0.15rem 0.3rem;
    border-radius: 0.25rem;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.9em;
}
.markdown-content pre code {
    background-color: transparent;
    color: inherit;
    padding: 0;
    font-size: 1em;
}
</style>
@endsection
