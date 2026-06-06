@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="font-playful text-dark fw-bold mb-0"><i class="fa-solid fa-gauge-high text-primary me-2"></i> Admin Dashboard</h1>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-playful-primary"><i class="fa-solid fa-folder-open me-1"></i> Kelola Kelas</a>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-playful border border-2 p-4 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-4 bg-light text-primary">
                        <i class="fa-solid fa-child-reaching fs-2"></i>
                    </div>
                    <div>
                        <h5 class="text-muted mb-1 font-playful">Programmer Cilik</h5>
                        <h2 class="text-dark fw-bold mb-0 font-playful">{{ $totalStudents }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-playful border border-2 p-4 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-4 bg-light text-success">
                        <i class="fa-solid fa-graduation-cap fs-2"></i>
                    </div>
                    <div>
                        <h5 class="text-muted mb-1 font-playful">Total Kelas</h5>
                        <h2 class="text-dark fw-bold mb-0 font-playful">{{ $totalCourses }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-playful border border-2 p-4 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-4 bg-light text-warning">
                        <i class="fa-solid fa-receipt fs-2"></i>
                    </div>
                    <div>
                        <h5 class="text-muted mb-1 font-playful">Pendaftaran</h5>
                        <h2 class="text-dark fw-bold mb-0 font-playful">{{ $totalEnrollments }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendaftaran Kelas Menunggu Persetujuan -->
    <div class="card card-playful border border-2 p-4 bg-white mb-5">
        <h3 class="font-playful text-dark fw-bold mb-4"><i class="fa-solid fa-hourglass-half text-warning me-2"></i> Menunggu Persetujuan Pendaftaran</h3>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light font-playful">
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Harga</th>
                        <th>Tanggal Pengajuan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingEnrollments as $enrollment)
                        <tr>
                            <td>
                                <strong>{{ $enrollment->user->name }}</strong><br>
                                <small class="text-muted">{{ $enrollment->user->email }} (Usia: {{ $enrollment->user->age ?? '-' }} thn)</small>
                            </td>
                            <td>
                                <span class="badge bg-primary text-white font-playful px-2 py-1">{{ $enrollment->course->title }}</span>
                            </td>
                            <td>Rp{{ number_format($enrollment->course->price, 0, ',', '.') }}</td>
                            <td>{{ $enrollment->created_at->format('d M Y, H:i') }} WIB</td>
                            <td class="text-center">
                                <form action="{{ route('admin.enrollments.approve', $enrollment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success rounded-3 px-3 me-2 font-playful"><i class="fa-solid fa-circle-check"></i> Setujui</button>
                                </form>
                                <form action="{{ route('admin.enrollments.reject', $enrollment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger rounded-3 px-3 font-playful"><i class="fa-solid fa-circle-xmark"></i> Tolak</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted font-playful">Tidak ada pendaftaran pending. Semua siswa sudah mulai belajar! 🎉</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
