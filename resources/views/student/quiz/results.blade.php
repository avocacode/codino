@extends('layouts.app')

@section('content')
<div class="container py-4 text-center" style="max-width: 800px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 text-start font-playful">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.classroom', $course->slug) }}" class="text-decoration-none">Kembali Ke Kelas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Hasil Kuis</li>
        </ol>
    </nav>

    <div class="card card-playful border border-2 p-5 bg-white shadow-sm">
        @if($submission->status === 'graded')
            <div class="mb-4">
                <i class="fa-solid fa-trophy display-1 text-warning mb-3 animate__animated animate__bounceIn"></i>
                <h1 class="font-playful text-dark fw-bold mb-2">Nilai Kuis Kamu!</h1>
                <p class="text-muted font-playful fs-5">Luar biasa! Kakak admin sudah menilai kuis pertualanganmu.</p>
            </div>
            
            <div class="d-inline-block p-4 rounded-5 bg-light border border-3 border-primary mb-4" style="transform: rotate(-2deg);">
                <span class="text-muted d-block font-playful fs-5">Skor Kuis</span>
                <span class="display-3 text-primary fw-bold font-playful">{{ $submission->score }} / 100</span>
            </div>

            @if($submission->feedback)
                <div class="p-4 bg-light rounded-4 border border-2 border-dashed text-start mb-4">
                    <h5 class="font-playful text-secondary fw-bold mb-2"><i class="fa-solid fa-comments"></i> Catatan & Feedback Kakak Guru:</h5>
                    <p class="mb-0 text-muted font-playful fs-5">{{ $submission->feedback }}</p>
                </div>
            @endif
        @else
            <div class="mb-4">
                <i class="fa-solid fa-paper-plane display-1 text-primary mb-3"></i>
                <h1 class="font-playful text-dark fw-bold mb-2">Jawaban Berhasil Dikirim!</h1>
                <p class="text-muted font-playful fs-5">Jawaban kuis petualanganmu sudah terkirim ke Kakak Admin.</p>
            </div>
            
            <div class="p-4 bg-light rounded-4 border border-2 border-warning text-start mb-4">
                <h5 class="font-playful text-warning fw-bold mb-2"><i class="fa-solid fa-hourglass-half"></i> Sedang Menunggu Penilaian</h5>
                <p class="mb-0 text-muted font-playful fs-5">Kakak admin akan segera memeriksa jawaban kuis essay/coding/submission kamu dan memberikan nilai terbaik. Ditunggu ya!</p>
            </div>
        @endif

        <a href="{{ route('student.classroom', $course->slug) }}" class="btn btn-playful-primary fs-5 px-4 mt-3">
            <i class="fa-solid fa-door-open me-1"></i> Kembali ke Kelas Belajar
        </a>
    </div>
</div>
@endsection
