@extends('admin.layout')

@section('title', 'Add Category')

@section('content')
<style>
    .content-container {
        max-width: 1000px;
        margin: 0 auto;
        padding-top: 20px;
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
        margin-bottom: 12px;
        font-size: 1.1rem;
    }

    .input-wrapper {
        max-width: 700px; 
    }

    .form-control-custom {
        background-color: #F4F7FE;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 16px 20px;
        color: #2B3674;
        font-weight: 500;
        font-size: 1rem;
        transition: all 0.2s ease-in-out;
    }

    .form-control-custom:focus {
        background-color: #FFFFFF;
        border-color: #6B7AFF;
        box-shadow: 0 0 0 5px rgba(107, 122, 255, 0.1);
        outline: none;
    }

    /* Button Styling */
    .btn-save {
        background: #6B7AFF;
        color: #FFFFFF;
        border: none;
        border-radius: 16px;
        padding: 14px 35px;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(107, 122, 255, 0.2);
        transition: all 0.3s;
    }

    .btn-save:hover {
        background: #5A68D8;
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(107, 122, 255, 0.25);
        color: #FFFFFF;
    }

    .btn-cancel {
        background: #F4F7FE;
        color: #8F9BBA;
        border: none;
        border-radius: 16px;
        padding: 14px 35px;
        font-weight: 700;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-cancel:hover {
        background: #E2E8F0;
        color: #2B3674;
    }

    .instruction-text {
        color: #A3AED0;
        font-size: 0.9rem;
        margin-top: 10px;
    }
</style>

<div class="content-container">
    <div class="mb-5">
        <h2 class="page-header-title mb-2">Add New Category</h2>
        <p class="text-muted mb-0">Create a new classification to organize ByteMe digital products.</p>
    </div>

    <div class="card form-card p-4 p-md-5">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            
            <div class="mb-5">
                <div class="input-wrapper">
                    <label for="nama" class="form-label">Category Name</label>
                    <input type="text" 
                           name="nama" 
                           id="nama" 
                           class="form-control-custom w-100 @error('nama') is-invalid @enderror" 
                           placeholder="e.g. Website Templates, Mobile UI Kits, 3D Assets..."
                           value="{{ old('nama') }}" 
                           required>
                    <p class="instruction-text">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        Ensure the category name is clear and professional.
                    </p>
                    @error('nama')
                        <div class="invalid-feedback mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-top d-flex gap-3">
                <button type="submit" class="btn-save">
                    Create Category
                </button>
                <a href="{{ route('admin.categories') }}" class="btn-cancel">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection