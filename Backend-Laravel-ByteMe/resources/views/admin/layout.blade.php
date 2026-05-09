<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - ByteMe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: radial-gradient(circle at top, #8074ff 0%, #4d4db6 40%, #27265d 100%);
            color: #eef2ff;
        }
        .sidebar {
            min-height: 100vh;
            width: 260px;
            background: rgba(16, 13, 55, 0.94);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.18);
        }
        .sidebar .nav-link {
            color: #d7dcff;
            border-radius: 16px;
            padding: 12px 18px;
            transition: all 0.25s ease;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .sidebar .nav-link .badge {
            background: rgba(255, 255, 255, 0.12);
            color: #eef2ff;
        }
        .main-content {
            padding: 32px;
            width: 100%;
        }
        .card {
            background: rgba(255, 255, 255, 0.08);
            border: none;
            border-radius: 28px;
            color: #eef2ff;
            box-shadow: 0 20px 60px rgba(10, 12, 40, 0.25);
        }
        .card .card-title,
        .card .card-text,
        .card .form-label,
        .card .list-group-item {
            color: #f3f5ff;
        }
        .btn-primary {
            background: #9d89ff;
            border: none;
            box-shadow: 0 12px 28px rgba(157, 137, 255, 0.25);
        }
        .btn-primary:hover,
        .btn-secondary:hover {
            opacity: 0.95;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #eef2ff;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .form-control,
        .form-select,
        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #eef2ff;
            box-shadow: none;
        }
        .form-control::placeholder,
        .form-select option {
            color: rgba(255, 255, 255, 0.65);
        }
        .alert {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #f8f9ff;
        }
        .profile-badge {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 24px;
        }
        .profile-card {
            background: linear-gradient(180deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.06) 100%);
            border: 1px solid rgba(255,255,255,0.12);
        }
        .action-button {
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.08);
            color: #eef2ff;
            transition: background 0.25s ease;
        }
        .action-button:hover {
            background: rgba(255,255,255,0.14);
        }
        @media (max-width: 992px) {
            .sidebar { position: static; width: 100%; min-height: auto; }
            .main-content { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column flex-lg-row">
        <!-- Sidebar -->
        <nav class="sidebar p-4">
            <div class="mb-5">
                <h4 class="text-white mb-1">ByteMe Admin</h4>
                <p class="text-muted mb-0">Dashboard & review</p>
            </div>
            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}" href="{{ route('admin.produk.pending') }}">Produk Pending</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">Kelola User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}">Kelola Kategori</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.withdraws') ? 'active' : '' }}" href="{{ route('admin.withdraws') }}">Withdraw Request</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">Profile</a>
                </li>
            </ul>
            <div class="mt-5 pt-4 border-top border-white border-opacity-10">
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary w-100 mt-3">Logout</button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>