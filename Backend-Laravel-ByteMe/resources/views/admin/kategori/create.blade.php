@extends('admin.layout')

@section('title', 'Tambah Kategori')

@section('content')
<h2>Tambah Kategori</h2>

<div class="card p-4">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Kategori</label>
            <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama') }}" required>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.categories') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
@endsection
