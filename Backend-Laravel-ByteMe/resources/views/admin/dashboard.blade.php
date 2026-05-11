@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<style>
    .profile-badge {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #EEF2FF; /* Biru sangat muda */
        color: #5465FF; /* Biru utama */
        font-size: 24px;
        font-weight: 800;
    }
    .stat-card-inner {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        transition: transform 0.2s ease;
    }
    .stat-card-inner:hover {
        transform: translateY(-3px);
        border-color: #5465FF;
    }
    .action-button {
        border-radius: 12px;
        background: #F4F7FE;
        color: #5465FF;
        font-weight: 600;
        border: 1px solid transparent;
        transition: all 0.2s;
        text-align: left;
        padding: 12px 16px;
    }
    .action-button:hover {
        background: #5465FF;
        color: #FFFFFF;
    }
</style>

<div class="d-flex flex-column gap-4">
    <div class="card p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-badge">{{ strtoupper(substr(Auth::user()->username ?? 'A', 0, 1)) }}</div>
                <div>
                    <h3 class="mb-1" style="font-weight: 700;">{{ Auth::user()->username ?? 'Admin' }}</h3>
                    <p class="mb-0 text-muted">{{ Auth::user()->email ?? 'admin@byteme.com' }}</p>
                </div>
            </div>
            <div class="text-end">
                <p class="text-uppercase text-muted mb-1" style="font-size: 0.8rem; font-weight: 600;">Role</p>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2" style="font-size: 0.9rem;">
                    {{ ucfirst(Auth::user()->role ?? 'admin') }}
                </span>
            </div>
        </div>

        <div class="row mt-4 g-3">
            <div class="col-sm-4">
                <div class="stat-card-inner p-3 h-100">
                    <p class="text-muted mb-2 fw-semibold">Total Produk</p>
                    <h3 class="mb-0" style="color: #5465FF; font-weight: 800;">{{ $stats['total_produk'] }}</h3>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-card-inner p-3 h-100">
                    <p class="text-muted mb-2 fw-semibold">Pending Review</p>
                    <h3 class="mb-0" style="color: #F59E0B; font-weight: 800;">{{ $stats['produk_pending'] }}</h3>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-card-inner p-3 h-100">
                    <p class="text-muted mb-2 fw-semibold">Withdraw Pending</p>
                    <h3 class="mb-0" style="color: #10B981; font-weight: 800;">{{ $stats['withdraw_pending'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card p-4 h-100 d-flex justify-content-center align-items-center text-center">
                <p class="text-muted mb-2 fw-semibold">Total User</p>
                <h1 style="font-weight: 800; color: #2B3674; font-size: 3rem;">{{ $stats['total_users'] }}</h1>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4 h-100 d-flex justify-content-center align-items-center text-center">
                <p class="text-muted mb-2 fw-semibold">User Banned</p>
                <h1 style="font-weight: 800; color: #EF4444; font-size: 3rem;">{{ $stats['users_banned'] }}</h1>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <p class="text-muted mb-3 fw-semibold">Quick Actions</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.produk.pending') }}" class="btn action-button">Review Produk Pending &rarr;</a>
                    <a href="{{ route('admin.users') }}" class="btn action-button">Kelola User &rarr;</a>
                    <a href="{{ route('admin.withdraws') }}" class="btn action-button">Handle Withdraw &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection