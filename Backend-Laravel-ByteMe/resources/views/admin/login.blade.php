<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ByteMe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #EEF2FF;
            color: #2b3674;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
        }

        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 32px;
            box-shadow: 0 20px 60px rgba(107, 122, 255, 0.1);
            width: 100%;
            max-width: 440px;
            padding: 50px 40px;
            text-align: center;
            position: relative;
        }

        .brand-logo-wrapper {
            opacity: 0;
            transform: translateY(20px); 
            height: 0; 
            overflow: hidden;
            transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); 
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo-wrapper.show {
            opacity: 1;
            transform: translateY(0);
            height: 120px; 
            margin-bottom: 10px;
        }

        .brand-logo-img {
            width: 250px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(107, 122, 255, 0.1));
        }

        .login-header h2 {
            font-weight: 800;
            color: #1a202c;
            letter-spacing: -1px;
            margin-bottom: 5px;
            font-size: 1.8rem;
        }

        .login-header p {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.85rem;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
            text-align: left;
        }

        .form-control {
            background: #ffffff;
            border: 1.5px solid #edf2f7;
            border-radius: 16px;
            color: #2d3748;
            padding: 14px 18px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #6b7aff;
            box-shadow: 0 0 0 4px rgba(107, 122, 255, 0.1);
            outline: none;
        }

        .btn-login {
            background: #6b7aff;
            color: white;
            border: none;
            border-radius: 16px;
            padding: 15px;
            font-weight: 700;
            font-size: 1rem;
            margin-top: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(107, 122, 255, 0.2);
            width: 100%;
        }

        .btn-login:hover {
            background: #5a68d8;
            transform: translateY(-2px);
        }

        .footer-text {
            margin-top: 30px;
            color: #a0aec0;
            font-size: 0.85rem;
        }

        .footer-text a {
            color: #6b7aff;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-logo-wrapper" id="logoWrapper">
            <img src="{{ asset('images/logoNoBG.png') }}" alt="ByteMe Logo" class="brand-logo-img">
        </div>

        <div class="login-header">
            <h2>Welcome Back!</h2>
            <p>Please enter your credentials.</p>
        </div>

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="mb-3 text-start">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required autofocus>
            </div>
            <div class="mb-4 text-start">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
            </div>
            
            <button type="submit" class="btn btn-login">Login</button>
        </form>

        <div class="footer-text">
            Secured Admin Access &bull; <a href="#">ByteMe Platform</a>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.form-control');
        const logoWrapper = document.getElementById('logoWrapper');

        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                logoWrapper.classList.add('show');
            });
        });
        
        window.addEventListener('load', () => {
            setTimeout(() => {
                logoWrapper.classList.add('show');
            }, 800);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>