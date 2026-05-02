@extends('admin.layout')

@section('title', 'Withdraw Requests')

@section('content')
<h2>Withdraw Requests</h2>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Seller</th>
                <th>Amount</th>
                <th>Bank Account</th>
                <th>Status</th>
                <th>Tanggal Request</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($withdraws as $withdraw)
            <tr>
                <td>{{ $withdraw->user->username ?? 'Unknown' }}</td>
                <td>Rp {{ number_format($withdraw->amount, 0, ',', '.') }}</td>
                <td>{{ $withdraw->bank_account }}</td>
                <td>
                    <span class="badge bg-{{ $withdraw->status === 'approved' ? 'success' : ($withdraw->status === 'rejected' ? 'danger' : 'warning') }}">
                        {{ ucfirst($withdraw->status) }}
                    </span>
                </td>
                <td>{{ $withdraw->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($withdraw->status === 'pending')
                        <form action="{{ route('admin.withdraws.approve', $withdraw->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectWithdrawModal{{ $withdraw->id }}">Reject</button>
                    @else
                        <span class="text-muted">{{ ucfirst($withdraw->status) }}</span>
                    @endif
                </td>
            </tr>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectWithdrawModal{{ $withdraw->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Withdraw</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.withdraws.reject', $withdraw->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="alasan{{ $withdraw->id }}" class="form-label">Alasan Reject</label>
                                    <textarea class="form-control" id="alasan{{ $withdraw->id }}" name="alasan" rows="3" required></textarea>
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
                <td colspan="6" class="text-center">Tidak ada withdraw request</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $withdraws->links() }}
@endsection