<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - ByteMe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        body {
            min-height: 100vh;
            background: #F4F7FE; /* Latar belakang abu-abu sangat terang */
            color: #2B3674; /* Teks warna gelap kebiruan */
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            width: 260px;
            background: #FFFFFF;
            border-right: 1px solid #E2E8F0;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
        }
        .sidebar .nav-link {
            color: #8F9BBA;
            font-weight: 600;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: #5465FF; /* Warna biru utama ByteMe */
            color: #FFFFFF;
        }
        .main-content {
            padding: 32px;
            width: 100%;
        }
        .card {
            background: #FFFFFF;
            border: none;
            border-radius: 20px;
            color: #2B3674;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }
        .card-title {
            font-weight: 700;
        }
        .text-muted {
            color: #8F9BBA !important;
        }
        .btn-primary {
            background: #5465FF;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(84, 101, 255, 0.2);
        }
        .btn-primary:hover {
            background: #3B4DFF;
        }
        .btn-secondary {
            background: #F4F7FE;
            color: #2B3674;
            border: none;
            font-weight: 600;
            border-radius: 12px;
        }
        .btn-secondary:hover {
            background: #E2E8F0;
            color: #1A2035;
        }
        .form-control, .form-select {
            background: #F4F7FE;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            color: #2B3674;
            padding: 12px 16px;
        }
        .form-control:focus, .form-select:focus {
            background: #FFFFFF;
            border-color: #5465FF;
            box-shadow: 0 0 0 4px rgba(84, 101, 255, 0.1);
        }
        .alert {
            border-radius: 12px;
            border: none;
        }
        @media (max-width: 992px) {
            .sidebar { position: static; width: 100%; min-height: auto; border-bottom: 1px solid #E2E8F0;}
            .main-content { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column flex-lg-row">
        <nav class="sidebar p-4">
            <div class="mb-5 text-center">
                <h4 class="mb-1" style="color: #2B3674; font-weight: 800;">ByteMe Admin</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Dashboard & review</p>
            </div>
            <ul class="nav flex-column gap-1">
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
            <div class="mt-5 pt-4 border-top">
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary w-100 mt-2">Logout</button>
                </form>
            </div>
        </nav>

        <div class="main-content flex-grow-1">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success bg-success bg-opacity-10 text-success fw-semibold">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger bg-danger bg-opacity-10 text-danger">
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