@extends('admin.layout')

@section('title', 'Withdraw Requests')

@section('content')
<style>
    .page-header-title {
        font-weight: 800;
        color: #2B3674;
    }
    
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

    .custom-table tbody tr:hover td {
        background-color: #F8FAFC;
    }

    .badge-status {
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.8rem;
    }
    .status-approved { background: rgba(16, 185, 129, 0.1); color: #10B981; }
    .status-rejected { background: rgba(239, 68, 68, 0.1); color: #EF4444; }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #F59E0B; }

    .btn-approve {
        background: #6B7AFF;
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .btn-approve:hover {
        background: #5465FF;
        transform: translateY(-2px);
        color: #FFFFFF;
    }

    .btn-reject {
        background: rgba(239, 68, 68, 0.1);
        color: #EF4444;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .btn-reject:hover {
        background: #EF4444;
        color: #FFFFFF;
        transform: translateY(-2px);
    }

    .amount-text {
        font-weight: 800;
        color: #10B981;
        font-size: 1.05rem;
    }

    .modal-content {
        border-radius: 24px;
        border: none;
    }
    .modal-header {
        border-bottom: 1px solid #F1F5F9;
        padding: 24px;
    }
</style>

<div class="mb-5">
    <h2 class="page-header-title mb-1">Withdraw Requests</h2>
    <p class="text-muted mb-0">Review and process fund withdrawal requests from ByteMe sellers.</p>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table custom-table mb-0">
            <thead>
                <tr>
                    <th>Seller</th>
                    <th>Amount</th>
                    <th>Bank Account</th>
                    <th>Status</th>
                    <th>Request Date</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdraws as $withdraw)
                <tr>
                    <td>
                        <span style="color: #2B3674; font-weight: 700;">{{ $withdraw->user->username ?? 'Unknown' }}</span>
                    </td>
                    <td class="amount-text">
                        Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                    </td>
                    <td>
                        <div class="text-muted small">Account Number:</div>
                        <div style="font-weight: 600;">{{ $withdraw->bank_account }}</div>
                    </td>
                    <td>
                        <span class="badge-status {{ 
                            $withdraw->status === 'approved' ? 'status-approved' : 
                            ($withdraw->status === 'rejected' ? 'status-rejected' : 'status-pending') 
                        }}">
                            {{ ucfirst($withdraw->status) }}
                        </span>
                    </td>
                    <td style="color: #8F9BBA; font-size: 0.9rem;">
                        {{ $withdraw->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="text-center">
                        @if($withdraw->status === 'pending')
                            <div class="d-flex gap-2 justify-content-center">
                                <form action="{{ route('admin.withdraws.approve', $withdraw->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-approve">Approve</button>
                                </form>
                                <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectWithdrawModal{{ $withdraw->id }}">Reject</button>
                            </div>
                        @else
                            <span class="text-muted small" style="font-weight: 500;">Processed</span>
                        @endif
                    </td>
                </tr>

                <div class="modal fade" id="rejectWithdrawModal{{ $withdraw->id }}" tabindex="-1">
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
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Reason for Rejection</label>
                                        <textarea class="form-control rounded-3" name="alasan" rows="4" placeholder="Enter the specific reason for rejecting this request..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-3 fw-bold px-4" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn-reject px-4">Confirm Rejection</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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