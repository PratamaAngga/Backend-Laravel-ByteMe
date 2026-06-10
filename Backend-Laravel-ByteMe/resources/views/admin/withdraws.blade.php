@extends('admin.layout')

@section('title', 'Withdraw Requests')

@section('content')
<style>
    .page-header-title { font-weight: 800; color: #2B3674; }
    .table-card {
        background: #FFFFFF;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(112, 144, 176, 0.08);
        border: none;
        overflow: hidden;
    }
    .custom-table th {
        background: #F8FAFC;
        color: #8F9BBA;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 20px 24px;
        border-bottom: 1px solid #E2E8F0;
    }
    .custom-table td {
        padding: 20px 24px;
        vertical-align: middle;
        color: #2B3674;
        font-weight: 600;
        border-bottom: 1px solid #F1F5F9;
    }
    .custom-table tbody tr:hover td { background-color: #F8FAFC; }
    .badge-status {
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.8rem;
    }
    .status-success  { background: rgba(16, 185, 129, 0.1); color: #10B981; }
    .status-rejected { background: rgba(239, 68, 68, 0.1);  color: #EF4444; }
    .status-pending  { background: rgba(245, 158, 11, 0.1); color: #F59E0B; }
    .status-handled  { background: rgba(107, 122, 255, 0.1); color: #6B7AFF; }
    .btn-approve {
        background: #6B7AFF; color: #FFFFFF; border: none;
        border-radius: 10px; padding: 8px 16px;
        font-weight: 700; font-size: 0.85rem; transition: all 0.2s;
    }
    .btn-approve:hover { background: #5465FF; transform: translateY(-2px); color: #FFFFFF; }
    .btn-reject {
        background: rgba(239, 68, 68, 0.1); color: #EF4444; border: none;
        border-radius: 10px; padding: 8px 16px;
        font-weight: 700; font-size: 0.85rem; transition: all 0.2s;
    }
    .btn-reject:hover { background: #EF4444; color: #FFFFFF; transform: translateY(-2px); }
    .btn-upload {
        background: rgba(16, 185, 129, 0.1); color: #10B981; border: none;
        border-radius: 10px; padding: 8px 16px;
        font-weight: 700; font-size: 0.85rem; transition: all 0.2s;
    }
    .btn-upload:hover { background: #10B981; color: #FFFFFF; transform: translateY(-2px); }
    .amount-text { font-weight: 800; color: #10B981; font-size: 1.05rem; }
    .modal-content { border-radius: 24px; border: none; }
    .modal-header { border-bottom: 1px solid #F1F5F9; padding: 24px; }
</style>

<div class="mb-5">
    <h2 class="page-header-title mb-1">Withdraw Requests</h2>
    <p class="text-muted mb-0">Review and process fund withdrawal requests from ByteMe sellers.</p>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
@endif

<div class="card table-card">
    <div class="table-responsive">
        <table class="table custom-table mb-0">
            <thead>
                <tr>
                    <th>Seller</th>
                    <th>Amount</th>
                    <th>Bank Info</th>
                    <th>Status</th>
                    <th>Request Date</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdraws as $withdraw)
                <tr>
                    <td>
                        <span style="color: #2B3674; font-weight: 700;">
                            {{ $withdraw->user->username ?? 'Unknown' }}
                        </span>
                        <div class="text-muted small">{{ $withdraw->user->email ?? '' }}</div>
                    </td>
                    <td class="amount-text">
                        Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $withdraw->bank_name }}</div>
                        <div class="text-muted small">{{ $withdraw->bank_account_number }}</div>
                        <div class="text-muted small">a.n. {{ $withdraw->bank_account_name }}</div>
                    </td>
                    <td>
                        <span class="badge-status status-{{ $withdraw->status }}">
                            {{ ucfirst($withdraw->status) }}
                        </span>
                        @if($withdraw->admin_note)
                            <div class="text-muted small mt-1">{{ $withdraw->admin_note }}</div>
                        @endif
                    </td>
                    <td style="color: #8F9BBA; font-size: 0.9rem;">
                        {{ $withdraw->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="text-center">
                        @if($withdraw->status === 'pending')
                            {{-- Tombol Approve dan Reject --}}
                            <div class="d-flex gap-2 justify-content-center">
                                <form action="{{ route('admin.withdraws.approve', $withdraw->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-approve">Approve</button>
                                </form>
                                <button type="button" class="btn-reject"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal{{ $withdraw->id }}">
                                    Reject
                                </button>
                            </div>

                        @elseif($withdraw->status === 'handled')
                            {{-- Tombol Upload Bukti Transfer --}}
                            <button type="button" class="btn-upload"
                                data-bs-toggle="modal"
                                data-bs-target="#uploadModal{{ $withdraw->id }}">
                                📤 Upload Bukti
                            </button>

                        @else
                            <span class="text-muted small" style="font-weight: 500;">
                                ✅ Selesai
                            </span>
                        @endif
                    </td>
                </tr>

                {{-- Modal Reject --}}
                @if($withdraw->status === 'pending')
                <div class="modal fade" id="rejectModal{{ $withdraw->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Reject Withdrawal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.withdraws.reject', $withdraw->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-body p-4">
                                    <p class="text-muted mb-3">
                                        Reject withdraw <strong>Rp {{ number_format($withdraw->amount, 0, ',', '.') }}</strong>
                                        dari <strong>{{ $withdraw->user->username ?? 'Unknown' }}</strong>?
                                    </p>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Alasan Penolakan</label>
                                        <textarea class="form-control rounded-3" name="alasan" rows="3"
                                            placeholder="Tulis alasan penolakan..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-3 fw-bold px-4"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn-reject px-4">Konfirmasi Tolak</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Modal Upload Bukti Transfer --}}
                @if($withdraw->status === 'handled')
                <div class="modal fade" id="uploadModal{{ $withdraw->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">📤 Upload Bukti Transfer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.withdraws.receipt', $withdraw->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="mb-3 p-3 rounded-3" style="background: #F8FAFC;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">Seller</span>
                                            <span class="fw-bold small">{{ $withdraw->user->username ?? 'Unknown' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">Bank</span>
                                            <span class="fw-bold small">{{ $withdraw->bank_name }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">No. Rekening</span>
                                            <span class="fw-bold small">{{ $withdraw->bank_account_number }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">Atas Nama</span>
                                            <span class="fw-bold small">{{ $withdraw->bank_account_name }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small">Jumlah</span>
                                            <span class="fw-bold" style="color: #10B981;">
                                                Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Bukti Transfer <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control rounded-3"
                                            name="receipt_file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            required>
                                        <div class="form-text">Format: JPG, PNG, atau PDF. Maks 5MB.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Catatan Admin (opsional)</label>
                                        <textarea class="form-control rounded-3" name="admin_note" rows="2"
                                            placeholder="Misal: Transfer via BCA..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-3 fw-bold px-4"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn-approve px-4">✅ Konfirmasi Transfer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="opacity-50">
                            <h5 class="fw-bold mb-1">All Settled!</h5>
                            <p class="mb-0">There are no pending withdraw requests at the moment.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-end">
    {{ $withdraws->links('pagination::bootstrap-5') }}
</div>
@endsection