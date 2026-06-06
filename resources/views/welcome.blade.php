@extends('layouts.app')

@section('content')
<!-- Hero Area -->
<section class="hero-section text-center py-5 mb-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6 text-start mb-4 mb-lg-0">
                <span class="badge bg-primary text-white font-playful mb-3 px-3 py-2 fs-6 rounded-pill"><i class="fas fa-gamepad"></i> Belajar Sambil Bermain!</span>
                <h1 class="display-4 font-playful mb-3 text-dark fw-bold" style="line-height: 1.2;">
                    Petualangan <span class="text-primary">Coding</span> Terbesar untuk Programmer Cilik! 🚀
                </h1>
                <p class="lead text-muted fs-5 mb-4 font-playful">
                    Belajar membuat game, website, dan aplikasi buatanmu sendiri dengan metode interaktif, seru, dan mudah dipahami untuk anak-anak usia 7-15 tahun!
                </p>
                <div class="d-flex gap-3">
                    <a href="#katalog" class="btn btn-playful-primary fs-5 px-4"><i class="fas fa-flag"></i> Mulai Petualangan</a>
                    <a href="https://scratch.mit.edu" target="_blank" class="btn btn-outline-secondary border-2 font-playful fs-5 rounded-4 px-4"><i class="fas fa-play-circle"></i> Apa itu Scratch?</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <!-- We can represent an abstract graphic or child coding illustration via styling -->
                <div class="p-5 bg-white rounded-5 shadow-lg border border-3 border-primary position-relative overflow-hidden" style="max-width: 500px; margin: 0 auto; transform: rotate(-2deg);">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-light opacity-50 pattern"></div>
                    <i class="fas fa-code display-1 text-primary mb-3"></i>
                    <h3 class="font-playful text-dark fw-bold">Codino Playground</h3>
                    <p class="text-muted font-playful">Mari belajar logika dasar komputer, Scratch, HTML, dan python dengan cara yang asyik!</p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <span class="badge bg-warning text-dark font-playful py-2 px-3 rounded-pill"><i class="fas fa-star"></i> Interaktif</span>
                        <span class="badge bg-success text-white font-playful py-2 px-3 rounded-pill"><i class="fas fa-puzzle-piece"></i> Kuis Asyik</span>
                        <span class="badge bg-info text-white font-playful py-2 px-3 rounded-pill"><i class="fas fa-graduation-cap"></i> Ebook</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Catalog -->
<section id="katalog" class="container py-4">
    <div class="text-center mb-5">
        <h2 class="display-5 font-playful fw-bold text-dark"><i class="fas fa-graduation-cap text-primary me-2"></i> Pilihan Kelas Petualangan Kita</h2>
        <p class="lead text-muted font-playful">Pilih kelas pemrograman yang sesuai dengan minat dan usiamu!</p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($courses as $course)
            <div class="col">
                <div class="card card-playful h-100 border border-2">
                    <div class="position-relative">
                        @if($course->cover_image)
                            <img src="{{ asset('storage/' . $course->cover_image) }}" class="card-img-top" alt="{{ $course->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-laptop-code display-2 opacity-50"></i>
                            </div>
                        @endif
                        <span class="position-absolute top-0 start-0 m-3 badge-age shadow-sm">
                            <i class="fas fa-child me-1"></i> {{ $course->age_range }}
                        </span>
                        <span class="position-absolute top-0 end-0 m-3 badge-price shadow-sm">
                            <i class="fas fa-tag me-1"></i> Rp{{ number_format($course->price, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    <div class="card-body p-4 d-flex flex-column">
                        <h4 class="card-title text-dark fw-bold mb-2">{{ $course->title }}</h4>
                        <p class="card-text text-muted mb-4 flex-grow-1" style="font-size: 0.95rem;">
                            {{ Str::limit($course->description, 120) }}
                        </p>
                        
                        @if($course->prerequisites)
                            <div class="mb-4 bg-light p-3 rounded-4" style="font-size: 0.85rem;">
                                <strong class="text-secondary font-playful"><i class="fas fa-lightbulb me-1"></i> Harus sudah bisa:</strong>
                                <p class="mb-0 text-muted mt-1">{{ Str::limit($course->prerequisites, 80) }}</p>
                            </div>
                        @endif
                        
                        <a href="{{ url('/kelas/' . $course->slug) }}" class="btn btn-playful-primary w-100 font-playful mt-auto">
                            <i class="fas fa-door-open me-1"></i> Lihat Detail Kelas
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-5 shadow-sm inline-block">
                    <i class="far fa-folder-open display-1 text-muted mb-3"></i>
                    <h3 class="font-playful text-dark fw-bold">Kelas Belum Tersedia</h3>
                    <p class="text-muted font-playful">Admin sedang merancang petualangan coding baru yang sangat seru. Ditunggu ya!</p>
                </div>
            </div>
        @endforelse
    </div>
</section>
@endsection
