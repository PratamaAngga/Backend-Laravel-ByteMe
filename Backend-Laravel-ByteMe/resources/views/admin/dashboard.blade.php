@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="card profile-card p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-badge">A</div>
                <div>
                    <h3 class="mb-1">{{ Auth::user()->username ?? 'Admin' }}</h3>
                    <p class="mb-0 text-muted">{{ Auth::user()->email ?? 'admin@byteme.com' }}</p>
                </div>
            </div>
            <div class="text-end">
                <p class="text-uppercase text-muted mb-1">Role</p>
                <h5 class="mb-0">{{ ucfirst(Auth::user()->role ?? 'admin') }}</h5>
            </div>
        </div>
        <div class="row mt-4 g-3">
            <div class="col-sm-4">
                <div class="card p-3 h-100" style="background: rgba(255,255,255,0.08);">
                    <p class="text-muted mb-2">Total Produk</p>
                    <h4>{{ $stats['total_produk'] }}</h4>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card p-3 h-100" style="background: rgba(255,255,255,0.08);">
                    <p class="text-muted mb-2">Pending Review</p>
                    <h4>{{ $stats['produk_pending'] }}</h4>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card p-3 h-100" style="background: rgba(255,255,255,0.08);">
                    <p class="text-muted mb-2">Withdraw Pending</p>
                    <h4>{{ $stats['withdraw_pending'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <p class="text-muted mb-2">Total User</p>
                <h4>{{ $stats['total_users'] }}</h4>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <p class="text-muted mb-2">User Banned</p>
                <h4>{{ $stats['users_banned'] }}</h4>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <p class="text-muted mb-2">Quick Actions</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.produk.pending') }}" class="btn action-button">Review Produk Pending</a>
                    <a href="{{ route('admin.users') }}" class="btn action-button">Kelola User</a>
                    <a href="{{ route('admin.withdraws') }}" class="btn action-button">Handle Withdraw</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection