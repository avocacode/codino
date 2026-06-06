@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="font-playful text-dark fw-bold mb-0"><i class="fa-solid fa-rocket text-primary me-2"></i> Petualanganku (Dashboard Siswa)</h1>
        <a href="{{ url('/') }}" class="btn btn-playful-secondary"><i class="fa-solid fa-circle-plus me-1"></i> Cari Kelas Baru</a>
    </div>

    <div class="row g-4">
        @forelse($enrollments as $enrollment)
            <div class="col-md-6 col-lg-4">
                <div class="card card-playful h-100 border border-2">
                    <div class="p-4 bg-light text-center border-bottom">
                        <i class="fa-solid fa-laptop-code display-3 text-primary opacity-75 mb-2"></i>
                        <h4 class="font-playful text-dark fw-bold mb-0">{{ $enrollment->course->title }}</h4>
                    </div>
                    
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="mb-4">
                            <span class="text-muted d-block font-playful">Status Kelas:</span>
                            @if($enrollment->payment_status === 'pending')
                                <span class="badge bg-warning text-dark font-playful fs-6 px-3 py-2 rounded-pill mt-1">
                                    <i class="fa-solid fa-hourglass-half"></i> Menunggu Persetujuan Kakak Admin
                                </span>
                            @elseif($enrollment->payment_status === 'paid')
                                <span class="badge bg-success text-white font-playful fs-6 px-3 py-2 rounded-pill mt-1">
                                    <i class="fa-solid fa-circle-check"></i> Siap Belajar!
                                </span>
                            @else
                                <span class="badge bg-danger text-white font-playful fs-6 px-3 py-2 rounded-pill mt-1">
                                    <i class="fa-solid fa-circle-xmark"></i> Dibatalkan
                                </span>
                            @endif
                        </div>

                        <p class="text-muted mb-4 fs-6">
                            {{ Str::limit($enrollment->course->description, 100) }}
                        </p>

                        @if($enrollment->payment_status === 'paid')
                            <a href="{{ route('student.classroom', $enrollment->course->slug) }}" class="btn btn-playful-primary w-100 font-playful mt-auto">
                                <i class="fa-solid fa-circle-play me-1"></i> Masuk Ruang Belajar
                            </a>
                        @else
                            <button class="btn btn-secondary w-100 font-playful mt-auto disabled" style="border-radius: 12px; padding: 10px 24px;">
                                <i class="fa-solid fa-lock me-1"></i> Belum Dibuka
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-5 shadow-sm inline-block">
                    <i class="fa-regular fa-smile-wink display-1 text-muted mb-3"></i>
                    <h3 class="font-playful text-dark fw-bold">Belum Ada Kelas yang Diikuti</h3>
                    <p class="text-muted font-playful">Ayo cari petualangan coding pertamamu di katalog kelas kami!</p>
                    <a href="{{ url('/') }}" class="btn btn-playful-primary font-playful fs-5 px-4 mt-3"><i class="fa-solid fa-magnifying-glass"></i> Jelajah Katalog</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
