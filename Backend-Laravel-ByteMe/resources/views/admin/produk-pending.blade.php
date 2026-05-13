@extends('admin.layout')

@section('title', 'Pending Products')

@section('content')
<style>
    .page-header-title {
        font-weight: 800;
        color: #2B3674;
    }
    
    .table-card {
        background: #FFFFFF;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        border: none;
        overflow: hidden;
    }

    .custom-table th {
        background: #F8FAFC;
        color: #8F9BBA;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 18px 24px;
        border-bottom: 1px solid #E2E8F0;
    }

    .custom-table td {
        padding: 16px 24px;
        vertical-align: middle;
        color: #2B3674;
        font-weight: 500;
        border-bottom: 1px solid #F1F5F9;
    }

    .custom-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .custom-table tbody tr:hover {
        background-color: #F8FAFC;
    }

    .product-name {
        font-weight: 700;
        color: #2B3674;
        font-size: 1.05rem;
    }

    .btn-approve {
        background: rgba(16, 185, 129, 0.1);
        color: #10B981;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-approve:hover {
        background: #10B981;
        color: #FFFFFF;
        transform: translateY(-2px);
    }

    .btn-reject {
        background: rgba(239, 68, 68, 0.1);
        color: #EF4444;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-reject:hover {
        background: #EF4444;
        color: #FFFFFF;
        transform: translateY(-2px);
    }

    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .modal-header {
        border-bottom: 1px solid #E2E8F0;
        padding: 20px 24px;
    }
    .modal-footer {
        border-top: 1px solid #E2E8F0;
        padding: 20px 24px;
    }
</style>

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="page-header-title mb-1">Pending Product Reviews</h2>
        <p class="text-muted mb-0">List of products from sellers that require your approval.</p>
    </div>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table custom-table mb-0">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Seller</th>
                    <th>Upload Date</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produk as $item)
                <tr>
                    <td class="product-name">{{ $item->nama_produk }}</td>
                    <td style="color: #7B8AB8;">{{ Str::limit($item->deskripsi, 50) }}</td>
                    <td style="font-weight: 700; color: #10B981;">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill" style="font-size: 0.85rem;">
                            {{ $item->user->username ?? 'Unknown' }}
                        </span>
                    </td>
                    <td style="color: #7B8AB8; font-size: 0.9rem;">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <form action="{{ route('admin.produk.approve', $item->produk_id) }}" method="POST" class="m-0">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-approve">Approve</button>
                            </form>
                            <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->produk_id }}">Reject</button>
                        </div>
                    </td>
                </tr>

                <div class="modal fade" id="rejectModal{{ $item->produk_id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-weight: 700; color: #2B3674;">Reject Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.produk.reject', $item->produk_id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label for="alasan{{ $item->produk_id }}" class="form-label" style="font-weight: 600; color: #2B3674;">Reason for Rejection</label>
                                        <textarea class="form-control" id="alasan{{ $item->produk_id }}" name="alasan" rows="4" placeholder="Write a specific reason why this product is being rejected..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn-reject" style="background: #EF4444; color: white;">Confirm Rejection</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#8F9BBA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            </div>
                            <h5 style="color: #2B3674; font-weight: 700;">All Caught Up!</h5>
                            <p class="text-muted mb-0">There are no products pending review at the moment.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-end">
    {{ $produk->links('pagination::bootstrap-5') }}
</div>
@endsection