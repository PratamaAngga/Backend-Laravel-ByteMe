@extends('admin.layout')

@section('title', 'Kelola Kategori')

@section('content')
<h2>Kelola Kategori</h2>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted">Kelola kategori produk yang dapat dipilih oleh seller.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-borderless align-middle text-white mb-0">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>{{ $category->nama }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-secondary btn-sm me-2">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center">Belum ada kategori</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
