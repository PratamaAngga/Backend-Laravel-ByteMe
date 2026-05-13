@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-up {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }

    .profile-badge {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #FF9A9E 0%, #FECFEF 100%);
        color: #D53F8C;
        font-size: 28px;
        font-weight: 800;
        box-shadow: 0 8px 16px rgba(255, 154, 158, 0.2);
    }
    
    .stat-card-inner {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 24px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }
    
    .stat-card-inner:hover {
        transform: translateY(-8px);
        border-color: #6B7AFF;
        box-shadow: 0 15px 30px rgba(107, 122, 255, 0.12);
    }

    .icon-box {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }
    .stat-card-inner:hover .icon-box {
        transform: scale(1.1) rotate(5deg);
    }

    .action-button {
        border-radius: 16px;
        background: #F4F7FE;
        color: #6B7AFF;
        font-weight: 700;
        border: 1px solid transparent;
        transition: all 0.25s ease;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .action-button:hover {
        background: #6B7AFF;
        color: #FFFFFF;
        transform: translateX(6px);
        box-shadow: 0 8px 15px rgba(107, 122, 255, 0.2);
    }

    .big-number {
        font-weight: 800; 
        color: #2B3674; 
        font-size: 2.8rem; 
        line-height: 1;
        margin-top: 10px;
    }
</style>

<div class="container-fluid p-0">
    <div class="card p-4 border-0 animate-up mb-4" style="border-radius: 28px; background: linear-gradient(to right, #ffffff, #f0f4ff); box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-4">
                <div class="profile-badge">{{ strtoupper(substr(Auth::user()->username ?? 'A', 0, 1)) }}</div>
                <div>
                    <p class="mb-0 text-muted" style="font-weight: 500;">Welcome back,</p>
                    <h2 class="mb-1" style="font-weight: 800; color: #2B3674; letter-spacing: -0.5px;">{{ Auth::user()->username ?? 'Admin' }}</h2>
                    <p class="mb-0" style="color: #8F9BBA; font-size: 0.95rem;">{{ Auth::user()->email ?? 'admin@byteme.com' }}</p>
                </div>
            </div>
            <div class="bg-white p-3 rounded-4 shadow-sm border px-4">
                <p class="text-uppercase mb-1" style="font-size: 0.7rem; font-weight: 800; color: #8F9BBA; letter-spacing: 1.5px;">Security Status</p>
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: #10B981; box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);"></div>
                    <span style="font-weight: 800; color: #2B3674; font-size: 1rem;">
                        {{ ucfirst(Auth::user()->role ?? 'Admin') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4 animate-up delay-1">
            <div class="stat-card-inner p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-bold" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Products</p>
                        <h2 class="mb-0" style="color: #2B3674; font-weight: 800;">{{ $stats['total_produk'] }}</h2>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 animate-up delay-2">
            <div class="stat-card-inner p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-bold" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Pending Reviews</p>
                        <h2 class="mb-0" style="color: #F59E0B; font-weight: 800;">{{ $stats['produk_pending'] }}</h2>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 animate-up delay-3">
            <div class="stat-card-inner p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-bold" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Pending Withdrawals</p>
                        <h2 class="mb-0" style="color: #10B981; font-weight: 800;">{{ $stats['withdraw_pending'] }}</h2>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-4 h-100">
                <div class="col-md-6 animate-up delay-4">
                    <div class="stat-card-inner p-4 text-center d-flex flex-column align-items-center justify-content-center h-100">
                        <div class="icon-box bg-info bg-opacity-10 text-info mb-3" style="width: 60px; height: 60px;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <p class="text-muted mb-0 fw-bold" style="font-size: 0.9rem;">Total Active Users</p>
                        <div class="big-number">{{ $stats['total_users'] }}</div>
                    </div>
                </div>
                <div class="col-md-6 animate-up delay-4">
                    <div class="stat-card-inner p-4 text-center d-flex flex-column align-items-center justify-content-center h-100">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger mb-3" style="width: 60px; height: 60px;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        </div>
                        <p class="text-muted mb-0 fw-bold" style="font-size: 0.9rem;">Banned Users</p>
                        <div class="big-number" style="color: #EF4444;">{{ $stats['users_banned'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 animate-up delay-5">
            <div class="card p-4 border-0 h-100" style="border-radius: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid #E2E8F0 !important;">
                <h5 class="mb-4" style="font-weight: 800; color: #2B3674;">Quick Actions</h5>
                <div class="d-grid gap-3">
                    <a href="{{ route('admin.produk.pending') }}" class="action-button text-decoration-none">
                        <span>Review Products</span>
                        <svg class="action-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('admin.users') }}" class="action-button text-decoration-none">
                        <span>Manage Users</span>
                        <svg class="action-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('admin.withdraws') }}" class="action-button text-decoration-none">
                        <span>Handle Withdrawals</span>
                        <svg class="action-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection