@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="font-playful text-dark fw-bold mb-0"><i class="fa-solid fa-list-check text-primary me-2"></i> Penilaian Kuis Peserta</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary border-2 font-playful"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a>
    </div>

    <div class="card card-playful border border-2 p-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light font-playful">
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Kuis</th>
                        <th>Tanggal Kirim</th>
                        <th>Status</th>
                        <th>Skor</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                        <tr>
                            <td>
                                <strong>{{ $sub->user->name }}</strong><br>
                                <small class="text-muted">{{ $sub->user->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary text-white font-playful px-2 py-1">{{ $sub->quiz->course->title }}</span>
                            </td>
                            <td>{{ $sub->quiz->title }}</td>
                            <td>{{ $sub->created_at->format('d M Y, H:i') }} WIB</td>
                            <td>
                                @if($sub->status === 'graded')
                                    <span class="badge bg-success font-playful text-white">Graded</span>
                                @else
                                    <span class="badge bg-warning font-playful text-dark">Pending Review</span>
                                @endif
                            </td>
                            <td>
                                @if($sub->score !== null)
                                    <strong class="text-primary font-playful fs-5">{{ $sub->score }} / 100</strong>
                                @else
                                    <span class="text-muted font-playful">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($sub->status === 'graded')
                                    <a href="{{ route('admin.submissions.show', $sub->id) }}" class="btn btn-sm btn-outline-primary border-2 rounded-3 px-3 font-playful"><i class="fa-solid fa-eye"></i> Detail</a>
                                @else
                                    <a href="{{ route('admin.submissions.show', $sub->id) }}" class="btn btn-sm btn-warning text-white rounded-3 px-3 font-playful"><i class="fa-solid fa-pen-to-square"></i> Periksa</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted font-playful">Belum ada pengiriman kuis dari siswa. 🚀</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
