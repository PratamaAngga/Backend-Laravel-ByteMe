@extends('admin.layout')

@section('title', 'Produk Pending')

@section('content')
<h2>Produk Pending Review</h2>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Seller</th>
                <th>Tanggal Upload</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produk as $item)
            <tr>
                <td>{{ $item->nama_produk }}</td>
                <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td>{{ $item->user->username ?? 'Unknown' }}</td>
                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <form action="{{ route('admin.produk.approve', $item->produk_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                    </form>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->produk_id }}">Reject</button>
                </td>
            </tr>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal{{ $item->produk_id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Produk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.produk.reject', $item->produk_id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="alasan{{ $item->produk_id }}" class="form-label">Alasan Reject</label>
                                    <textarea class="form-control" id="alasan{{ $item->produk_id }}" name="alasan" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada produk pending</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $produk->links() }}
@endsection