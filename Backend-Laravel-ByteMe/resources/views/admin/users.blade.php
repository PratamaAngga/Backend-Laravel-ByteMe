@extends('admin.layout')

@section('title', 'Manage Users')

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

    .user-name {
        font-weight: 700;
        color: #2B3674;
        font-size: 1.05rem;
    }

    /* Badge Custom Styling */
    .badge-custom {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 0.3px;
    }

    /* Role Badges */
    .role-admin { background: rgba(107, 122, 255, 0.1); color: #6B7AFF; }
    .role-seller { background: rgba(16, 185, 129, 0.1); color: #10B981; }
    .role-buyer { background: rgba(56, 189, 248, 0.1); color: #0284C7; }

    /* Status Badges */
    .status-active { background: rgba(16, 185, 129, 0.1); color: #10B981; }
    .status-banned { background: rgba(239, 68, 68, 0.1); color: #EF4444; }
    .status-warning { background: rgba(245, 158, 11, 0.1); color: #F59E0B; }

    /* Tombol Aksi */
    .btn-ban {
        background: rgba(239, 68, 68, 0.1);
        color: #EF4444;
        border: none;
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .btn-ban:hover {
        background: #EF4444;
        color: #FFFFFF;
        transform: translateY(-2px);
    }

    .btn-unban {
        background: rgba(16, 185, 129, 0.1);
        color: #10B981;
        border: none;
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .btn-unban:hover {
        background: #10B981;
        color: #FFFFFF;
        transform: translateY(-2px);
    }
    
    .btn-disabled {
        background: #F1F5F9;
        color: #94A3B8;
        border: none;
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: not-allowed;
    }
</style>

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="page-header-title mb-1">Manage Users</h2>
        <p class="text-muted mb-0">View and manage all registered users on the platform.</p>
    </div>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table custom-table mb-0">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Date Registered</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="user-name">{{ $user->username }}</td>
                    <td style="color: #7B8AB8;">{{ $user->email }}</td>
                    <td>
                        <span class="badge-custom {{ $user->role === 'admin' ? 'role-admin' : ($user->role === 'seller' ? 'role-seller' : 'role-buyer') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-custom {{ 
                            $user->status === 'banned' ? 'status-banned' : 
                            ($user->status === 'suspended' || $user->status === 'warning' ? 'status-warning' : 'status-active') 
                        }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td style="color: #7B8AB8; font-size: 0.9rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @if($user->role === 'admin')
                            <button class="btn-disabled" disabled>Restricted</button>
                        @elseif($user->status !== 'banned')
                            <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-ban">Ban User</button>
                            </form>
                        @elseif($user->status === 'banned')
                            <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-unban">Unban</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#8F9BBA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </div>
                            <h5 style="color: #2B3674; font-weight: 700;">No Users Found</h5>
                            <p class="text-muted mb-0">There are currently no registered users to display.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-end">
    {{ $users->links('pagination::bootstrap-5') }}
</div>
@endsection