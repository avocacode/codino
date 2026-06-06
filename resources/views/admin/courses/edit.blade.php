@extends('layouts.app')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb font-playful">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Kelola Kelas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course->id) }}" class="text-decoration-none">{{ $course->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Kelas</li>
        </ol>
    </nav>

    <h1 class="font-playful text-dark fw-bold mb-4"><i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit Kelas: {{ $course->title }}</h1>

    <div class="card card-playful border border-2 p-4 bg-white" style="max-width: 800px;">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label font-playful fw-bold text-dark">Nama / Judul Kelas</label>
                <input type="text" class="form-control rounded-3 @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $course->title) }}" placeholder="Contoh: Scratch Game Creator Cilik" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label font-playful fw-bold text-dark">Deskripsi Kelas</label>
                <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Jelaskan keseruan yang dipelajari di kelas ini..." required>{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="age_range" class="form-label font-playful fw-bold text-dark">Target Usia Anak</label>
                    <input type="text" class="form-control rounded-3 @error('age_range') is-invalid @enderror" id="age_range" name="age_range" value="{{ old('age_range', $course->age_range) }}" placeholder="Contoh: 7 - 10 Tahun" required>
                    @error('age_range')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label font-playful fw-bold text-dark">Harga Kelas (Rp)</label>
                    <input type="number" class="form-control rounded-3 @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $course->price) }}" placeholder="Contoh: 150000" min="0" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="prerequisites" class="form-label font-playful fw-bold text-dark">Prasyarat / Bekal Kemampuan Siswa</label>
                <input type="text" class="form-control rounded-3 @error('prerequisites') is-invalid @enderror" id="prerequisites" name="prerequisites" value="{{ old('prerequisites', $course->prerequisites) }}" placeholder="Contoh: Bisa membaca lancar & mengoperasikan mouse komputer">
                @error('prerequisites')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" {{ $course->is_published ? 'checked' : '' }}>
                <label class="form-check-input-label font-playful fw-bold text-dark" for="is_published">Terbitkan Kelas Secara Publik</label>
            </div>

            <button type="submit" class="btn btn-playful-primary font-playful px-4"><i class="fa-solid fa-square-check"></i> Simpan Perubahan</button>
            <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-outline-secondary border-2 rounded-4 font-playful px-4 ms-2">Batal</a>
        </form>
    </div>
</div>
@endsection
