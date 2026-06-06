@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="font-playful text-dark fw-bold mb-0"><i class="fa-solid fa-graduation-cap text-primary me-2"></i> Kelola Kelas Pemrograman</h1>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-playful-primary"><i class="fa-solid fa-circle-plus me-1"></i> Tambah Kelas Baru</a>
    </div>

    <div class="card card-playful border border-2 p-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light font-playful">
                    <tr>
                        <th>Gambar</th>
                        <th>Judul Kelas</th>
                        <th>Target Usia</th>
                        <th>Harga</th>
                        <th>Jumlah Bab</th>
                        <th>Jumlah Peserta</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>
                                @if($course->cover_image)
                                    <img src="{{ asset('storage/' . $course->cover_image) }}" alt="cover" class="rounded-3" style="width: 80px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded-3" style="width: 80px; height: 50px;">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $course->title }}</strong><br>
                                <small class="text-muted">{{ $course->slug }}</small>
                            </td>
                            <td><span class="badge-age">{{ $course->age_range }}</span></td>
                            <td>Rp{{ number_format($course->price, 0, ',', '.') }}</td>
                            <td>{{ $course->modules_count }} Bab</td>
                            <td>{{ $course->students_count }} Peserta</td>
                            <td>
                                @if($course->is_published)
                                    <span class="badge bg-success font-playful text-white">Aktif (Published)</span>
                                @else
                                    <span class="badge bg-secondary font-playful text-white">Draft (Unpublished)</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-sm btn-info text-white rounded-3 px-2 me-1 font-playful"><i class="fa-solid fa-route"></i> Silabus</a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-sm btn-warning text-white rounded-3 px-2 me-1 font-playful"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini beserta seluruh materi dan kuis di dalamnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-3 px-2 font-playful"><i class="fa-solid fa-trash-can"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted font-playful">Belum ada kelas yang terdaftar. Ayo buat kelas pertamamu! 🚀</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
