@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb font-playful">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-primary"><i class="fas fa-home"></i> Katalog</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $course->title }}</li>
        </ol>
    </nav>

    <!-- Header Banner -->
    <div class="p-5 bg-white rounded-5 shadow-sm border border-2 mb-5 position-relative overflow-hidden">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge-age"><i class="fas fa-child me-1"></i> {{ $course->age_range }}</span>
                    <span class="badge-price"><i class="fas fa-tag me-1"></i> Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                </div>
                <h1 class="display-5 text-dark fw-bold font-playful mb-3">{{ $course->title }}</h1>
                <p class="lead text-muted font-playful mb-4">{{ $course->description }}</p>
                
                @auth
                    @if(Auth::user()->isStudent())
                        <form action="{{ route('student.enroll', $course->slug) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-playful-primary fs-5 px-4"><i class="fas fa-rocket me-1"></i> Mulai Petualangan Kelas</button>
                        </form>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-playful-primary fs-5 px-4"><i class="fas fa-chart-line me-1"></i> Dashboard Admin</a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn btn-playful-primary fs-5 px-4"><i class="fas fa-user-plus me-1"></i> Daftar Sekarang & Belajar</a>
                @endauth
            </div>
            
            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <div class="p-4 bg-light rounded-5 border border-2 border-primary d-inline-block shadow-sm" style="transform: rotate(2deg);">
                    <i class="fas fa-laptop-code display-1 text-primary mb-3"></i>
                    <h5 class="font-playful text-dark fw-bold mb-0">Coding Adventure!</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Details and Syllabus -->
    <div class="row">
        <!-- Sidebar Details -->
        <div class="col-lg-4 order-lg-2 mb-4">
            <div class="card card-playful border border-2 p-4 mb-4">
                <h3 class="font-playful text-dark fw-bold mb-4"><i class="fas fa-info-circle text-warning me-1"></i> Detail Petualangan</h3>
                
                <div class="mb-3">
                    <span class="text-muted d-block font-playful"><i class="fas fa-child text-primary me-1"></i> Target Usia:</span>
                    <strong class="text-dark fs-5 font-playful">{{ $course->age_range }}</strong>
                </div>

                @if($course->prerequisites)
                    <div class="mb-3">
                        <span class="text-muted d-block font-playful"><i class="fas fa-lightbulb text-secondary me-1"></i> Prasyarat Kelas:</span>
                        <strong class="text-dark font-playful">{{ $course->prerequisites }}</strong>
                    </div>
                @endif

                <div class="mb-3">
                    <span class="text-muted d-block font-playful"><i class="fas fa-dollar-sign text-success me-1"></i> Harga:</span>
                    <strong class="text-success fs-4 font-playful">Rp{{ number_format($course->price, 0, ',', '.') }}</strong>
                </div>
                
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-certificate text-warning fs-3"></i>
                        <span class="text-muted font-playful">Dapatkan e-Sertifikat Keren setelah menyelesaikan kuis!</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Syllabus -->
        <div class="col-lg-8 order-lg-1">
            <div class="p-4 bg-white rounded-5 shadow-sm border border-2 mb-4">
                <h2 class="font-playful text-dark fw-bold mb-4"><i class="fas fa-map-signs text-primary me-2"></i> Peta Kurikulum Kelas</h2>
                
                @forelse($course->modules as $index => $module)
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary text-white rounded-pill font-playful px-3 py-2 fs-6">Bab {{ $index + 1 }}</span>
                            <h4 class="font-playful text-dark fw-bold mb-0">{{ $module->title }}</h4>
                        </div>
                        <p class="text-muted ps-2">{{ $module->description }}</p>
                        
                        <!-- Lessons list in this module -->
                        <div class="list-group list-group-flush ps-4 border-start border-2 border-light">
                            @forelse($module->lessons as $lesson)
                                <div class="list-group-item bg-transparent border-0 px-0 py-2 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($lesson->content_type === 'video')
                                            <i class="fas fa-play-circle text-primary fs-5"></i>
                                        @elseif($lesson->content_type === 'ebook')
                                            <i class="fas fa-book-open text-success fs-5"></i>
                                        @else
                                            <i class="fas fa-file-alt text-muted fs-5"></i>
                                        @endif
                                        <span class="text-dark font-playful">{{ $lesson->title }}</span>
                                    </div>
                                    
                                    @if($lesson->sources->count() > 0)
                                        <span class="badge bg-info text-white font-playful"><i class="fas fa-download"></i> {{ $lesson->sources->count() }} Sumber</span>
                                    @endif
                                </div>
                            @empty
                                <span class="text-muted font-playful ps-2">Materi masih dipersiapkan...</span>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-muted font-playful">Belum ada peta materi untuk kelas ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
