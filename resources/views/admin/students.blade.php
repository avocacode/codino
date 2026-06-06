@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="font-playful text-dark fw-bold mb-0"><i class="fa-solid fa-child-reaching text-primary me-2"></i> Kelola Programmer Cilik</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary border-2 font-playful"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
    </div>

    <div class="card card-playful border border-2 p-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light font-playful">
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Email</th>
                        <th>Usia</th>
                        <th>Tanggal Terdaftar</th>
                        <th class="text-center">Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <strong>{{ $student->name }}</strong>
                            </td>
                            <td>{{ $student->email }}</td>
                            <td>
                                @if($student->age)
                                    <span class="badge bg-info text-white font-playful">{{ $student->age }} Tahun</span>
                                @else
                                    <span class="text-muted font-playful">-</span>
                                @endif
                            </td>
                            <td>{{ $student->created_at->format('d M Y, H:i') }} WIB</td>
                            <td class="text-center">
                                <span class="badge bg-secondary font-playful text-white">Student</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted font-playful">Belum ada peserta yang mendaftar. Ayo ajak anak-anak belajar koding! 🚀</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
