@extends('admin.layout')

@section('title', 'Kelola User')

@section('content')
<h2>Kelola User</h2>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'seller' ? 'success' : 'info') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ 
                        $user->status === 'banned' ? 'danger' : 
                        ($user->status === 'suspended' ? 'warning' : 
                        ($user->status === 'warning' ? 'warning' : 'success'))
                    }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($user->status !== 'banned' && $user->role !== 'admin')
                        <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-sm">Ban</button>
                        </form>
                    @elseif($user->status === 'banned')
                        <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm">Unban</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada user</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $users->links() }}
@endsection