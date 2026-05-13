<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - ByteMe</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            min-height: 100vh;
            background: #F4F7FE;
            color: #2B3674;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
        }

        /* --- SIDEBAR STYLING --- */
        .sidebar {
            min-height: 100vh;
            width: 280px;
            background: #EEF2FF;
            border-right: 1px solid #DCE4F7;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* --- Perbaikan Alignment Sidebar --- */

        .brand-container {
            padding: 35px 20px 20px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 15px;
        }

        .brand-logo-img {
            width: 50px;
            height: auto;
            margin-bottom: 10px;
            filter: drop-shadow(0 4px 8px rgba(107, 122, 255, 0.1));
        }

        .brand-text-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            line-height: 1.2;
        }

        .brand-title {
            color: #2B3674;
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #8F9BBA;
            margin-top: 4px;
        }

        .sidebar .nav-link {
            color: #7B8AB8;
            font-weight: 700;
            border-radius: 16px;
            padding: 12px 20px;
            margin: 0 18px 4px 18px;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: #6B7AFF;
            color: #FFFFFF;
            box-shadow: 0 10px 20px rgba(107, 122, 255, 0.15);
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            padding: 40px;
            width: 100%;
        }

        /* --- LOGOUT BUTTON --- */
        .btn-logout-wrapper {
            padding: 20px;
            margin-top: auto;
            border-top: 1px solid rgba(220, 228, 247, 0.8);
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.08);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.1);
            border-radius: 16px;
            padding: 12px;
            font-weight: 800;
            width: 100%;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-logout:hover {
            background: #EF4444;
            color: #FFFFFF;
            transform: translateY(-2px);
        }

        /* --- MODERN NOTIFICATION TOASTS --- */
        .notification-container {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 15px;
            pointer-events: none;
        }

        .custom-alert {
            pointer-events: auto;
            min-width: 320px;
            max-width: 400px;
            padding: 18px 24px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            animation: slideInRight 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
            transition: all 0.4s ease;
        }

        .custom-alert.hide {
            animation: slideOutRight 0.5s ease forwards;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(100px);
            }
        }

        .alert-success-custom {
            border-left: 6px solid #10B981;
        }

        .alert-error-custom {
            border-left: 6px solid #EF4444;
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .alert-success-custom .icon-circle {
            background: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }

        .alert-error-custom .icon-circle {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
        }

        .alert-message {
            font-weight: 700;
            color: #2B3674;
            margin: 0;
            font-size: 0.95rem;
        }

        .close-btn {
            background: none;
            border: none;
            color: #8F9BBA;
            cursor: pointer;
            padding: 5px;
        }

        /* SweetAlert Customization */
        .my-swal-popup {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 28px !important;
            padding: 2rem !important;
        }

        @media (max-width: 992px) {
            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
                border-bottom: 1px solid #DCE4F7;
            }

            .brand-container {
                padding: 25px;
            }

            .main-content {
                padding: 24px;
            }

            .notification-container {
                top: 20px;
                right: 20px;
                left: 20px;
                align-items: center;
            }

            .custom-alert {
                min-width: auto;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="notification-container">
        @if(session('success'))
        <div class="custom-alert alert-success-custom" id="toast-success">
            <div class="icon-circle">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="alert-content">
                <p class="alert-message">{{ session('success') }}</p>
            </div>
            <button class="close-btn" onclick="closeToast('toast-success')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        @endif

        @if($errors->any())
        @foreach($errors->all() as $error)
        <div class="custom-alert alert-error-custom" id="toast-error-{{ $loop->index }}">
            <div class="icon-circle">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <div class="alert-content">
                <p class="alert-message">{{ $error }}</p>
            </div>
            <button class="close-btn" onclick="closeToast('toast-error-{{ $loop->index }}')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        @endforeach
        @endif
    </div>

    <div class="d-flex flex-column flex-lg-row">
        <nav class="sidebar">
            <div class="brand-container">
                <div class="brand-text-wrapper">
                    <h4 class="brand-title">ByteMe</h4>
                    <p class="brand-subtitle mb-0">ADMIN PANEL</p>

                </div>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}" href="{{ route('admin.produk.pending') }}">
                        Pending Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                        Manage Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}">
                        Manage Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.withdraws') ? 'active' : '' }}" href="{{ route('admin.withdraws') }}">
                        Withdraw Requests
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">
                        Admin Profile
                    </a>
                </li>
            </ul>

            <div class="btn-logout-wrapper">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <div class="main-content flex-grow-1">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // --- SWEETALERT DELETE CONFIRMATION ---
        function confirmDelete(formId, message = "This data will be permanently deleted!") {
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6B7AFF',
                cancelButtonColor: '#EF4444',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'my-swal-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            })
        }

        // --- NOTIFICATION TOAST LOGIC ---
        function closeToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.classList.add('hide');
                setTimeout(() => {
                    toast.remove();
                }, 500);
            }
        }

        window.addEventListener('load', () => {
            const alerts = document.querySelectorAll('.custom-alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    closeToast(alert.id);
                }, 4500);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>