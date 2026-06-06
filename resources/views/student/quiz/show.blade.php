@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 font-playful">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.classroom', $course->slug) }}" class="text-decoration-none">Kembali Ke Kelas</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $quiz->title }}</li>
        </ol>
    </nav>

    <!-- Quiz Header -->
    <div class="p-4 bg-white rounded-5 shadow-sm border border-2 mb-4">
        <span class="badge bg-warning text-dark font-playful mb-2 px-3 py-2 fs-6 rounded-pill"><i class="fa-solid fa-puzzle-piece"></i> Waktunya Petualangan Kuis!</span>
        <h1 class="font-playful text-dark fw-bold mb-2">{{ $quiz->title }}</h1>
        <div class="text-muted mb-0 font-playful markdown-content">{!! \Illuminate\Support\Str::markdown($quiz->description ?? 'Jawab pertanyaan-pertanyaan seru di bawah ini dengan teliti ya!') !!}</div>
    </div>

    <!-- Questions Form -->
    <form action="{{ route('student.quiz.submit', [$course->slug, $quiz->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        @foreach($quiz->questions as $index => $question)
            <div class="card card-playful border border-2 p-4 bg-white mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary text-white rounded-pill font-playful px-3 py-2">Soal {{ $index + 1 }}</span>
                    <span class="badge bg-light text-muted font-playful">{{ $question->points }} Poin</span>
                </div>
                
                <div class="font-playful text-dark mb-4 markdown-content">
                    {!! $question->question_text !!}
                </div>
                
                @if($question->type === 'multiple_choice')
                    <!-- Options List -->
                    <div class="d-flex flex-column gap-2">
                        @foreach($question->options as $optIndex => $option)
                            @if($option)
                                <label class="p-3 bg-light rounded-4 border border-2 border-light d-flex align-items-center gap-2 style-label" style="cursor: pointer; transition: all 0.2s;">
                                    <input type="radio" name="question_{{ $question->id }}" value="{{ $option }}" required class="form-check-input me-2">
                                    <span class="font-playful text-dark">{!! \Illuminate\Support\Str::inlineMarkdown($option) !!}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                @elseif($question->type === 'essay')
                    <!-- Essay Area -->
                    <div class="mb-3">
                        <textarea class="form-control rounded-4 p-3" name="question_{{ $question->id }}" rows="4" placeholder="Tuliskan jawaban hebatmu di sini ya..." required></textarea>
                    </div>
                @elseif($question->type === 'coding_test')
                    <!-- Coding Test Area -->
                    <div class="mb-3">
                        <label class="form-label text-muted font-playful">Tuliskan kode program / jawaban teks di sini:</label>
                        <textarea class="form-control rounded-4 p-3 font-monospace" name="question_{{ $question->id }}" rows="5" placeholder="// Contoh koding kamu di sini&#10;move_steps(10);" required></textarea>
                    </div>
                @elseif($question->type === 'submission')
                    <!-- Submission File or Link -->
                    <div class="mb-3">
                        <label class="form-label font-playful fw-bold text-dark mb-3"><i class="fa-solid fa-cloud-arrow-up text-primary"></i> Jawaban Tugas / Screenshot / Project (Pilih salah satu atau keduanya):</label>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 border border-2 border-dashed text-center h-100 d-flex flex-column justify-content-center">
                                    <i class="fa-solid fa-file-image text-muted fs-2 mb-2"></i>
                                    <p class="small text-muted font-playful mb-2">Unggah Gambar atau File (Screenshot / PDF / ZIP)</p>
                                    <input type="file" class="form-control rounded-3" name="file_{{ $question->id }}" id="file_{{ $question->id }}" onchange="validateSubmission({{ $question->id }})">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 border border-2 border-dashed text-center h-100 d-flex flex-column justify-content-center">
                                    <i class="fa-solid fa-link text-muted fs-2 mb-2"></i>
                                    <p class="small text-muted font-playful mb-2">Atau tempel Link Project (Scratch, Drive, dll)</p>
                                    <input type="url" class="form-control rounded-3" name="question_{{ $question->id }}" id="link_{{ $question->id }}" placeholder="Contoh: https://scratch.mit.edu/projects/XXXXXX" oninput="validateSubmission({{ $question->id }})">
                                </div>
                            </div>
                        </div>
                        <small class="text-danger d-none mt-2 font-playful" id="error_{{ $question->id }}">Harap unggah file atau tempel link project untuk menjawab soal ini! ⚠️</small>
                    </div>
                @endif
            </div>
        @endforeach
        
        <div class="text-center mt-5">
            <button type="submit" class="btn btn-playful-primary fs-4 px-5 py-3 shadow"><i class="fa-solid fa-paper-plane me-1"></i> Kirim Jawaban Kerenmu!</button>
        </div>
    </form>
</div>

<style>
.style-label:hover {
    background-color: #eef2ff !important;
    border-color: #6366f1 !important;
}
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

<script>
function validateSubmission(questionId) {
    const fileInput = document.getElementById('file_' + questionId);
    const linkInput = document.getElementById('link_' + questionId);
    const errorMsg = document.getElementById('error_' + questionId);
    
    if ((fileInput && fileInput.files.length > 0) || (linkInput && linkInput.value.trim() !== "")) {
        if (errorMsg) errorMsg.classList.add('d-none');
        return true;
    } else {
        if (errorMsg) errorMsg.classList.remove('d-none');
        return false;
    }
}

document.querySelector('form').addEventListener('submit', function (e) {
    let valid = true;
    @foreach($quiz->questions as $question)
        @if($question->type === 'submission')
            if (!validateSubmission({{ $question->id }})) {
                valid = false;
            }
        @endif
    @endforeach
    if (!valid) {
        e.preventDefault();
        alert('Harap lengkapi jawaban tugas (unggah file atau tempel link project) terlebih dahulu!');
    }
});
</script>
@endsection
