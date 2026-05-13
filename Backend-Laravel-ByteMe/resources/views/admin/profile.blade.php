@extends('admin.layout')

@section('title', 'Edit Profile')

@section('content')
<style>
    .content-container {
        max-width: 900px;
        margin: 0 auto;
        padding-top: 5px; 
    }

    .page-header-title {
        font-weight: 800;
        color: #2B3674;
        letter-spacing: -0.5px;
    }

    .form-card {
        background: #FFFFFF;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(112, 144, 176, 0.08);
        border: none;
        width: 100%;
    }

    .form-label {
        font-weight: 700;
        color: #2B3674;
        margin-bottom: 8px; 
        font-size: 0.95rem;
    }

    .form-control-custom {
        background-color: #F4F7FE;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 12px 16px; 
        color: #2B3674;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s ease-in-out;
    }

    .form-control-custom:focus {
        background-color: #FFFFFF;
        border-color: #6B7AFF;
        box-shadow: 0 0 0 4px rgba(107, 122, 255, 0.1);
        outline: none;
    }

    .btn-update {
        background: #6B7AFF;
        color: #FFFFFF;
        border: none;
        border-radius: 16px;
        padding: 12px 30px;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(107, 122, 255, 0.2);
        transition: all 0.3s;
    }

    .btn-update:hover {
        background: #5465FF;
        transform: translateY(-2px);
    }

    .instruction-text {
        color: #A3AED0;
        font-size: 0.8rem;
        margin-top: 6px;
    }
</style>

<div class="content-container">
    <div class="mb-4">
        <h2 class="page-header-title mb-1">Edit Profile</h2>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Manage your personal information and account security.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success-custom mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card form-card p-4 p-md-5">
        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control-custom w-100" id="username" name="username" value="{{ old('username', Auth::user()->username) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control-custom w-100" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                </div>

                <div class="col-12 mt-2 mb-1">
                    <hr style="border-color: #E2E8F0;">
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control-custom w-100" id="password" name="password" placeholder="Optional">
                    <p class="instruction-text">Min. 8 characters for security.</p>
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control-custom w-100" id="password_confirmation" name="password_confirmation" placeholder="Repeat password">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-update">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection