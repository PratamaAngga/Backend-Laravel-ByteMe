@extends('admin.layout')

@section('title', 'Manage Categories')

@section('content')
<style>
    /* Styling Header */
    .page-header-title {
        font-weight: 800;
        color: #2B3674;
        letter-spacing: -0.5px;
    }
    
    .table-card {
        background: #FFFFFF;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(112, 144, 176, 0.08);
        border: none;
        overflow: hidden;
    }

    /* Styling Tabel */
    .custom-table th {
        background: #F8FAFC;
        color: #8F9BBA;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 20px 24px;
        border-bottom: 1px solid #E2E8F0;
    }

    .custom-table td {
        padding: 20px 24px;
        vertical-align: middle;
        color: #2B3674;
        font-weight: 600;
        border-bottom: 1px solid #F1F5F9;
    }

    .custom-table tbody tr:hover td {
        background-color: #F8FAFC;
    }

    /* Area Footer Card */
    .card-actions-footer {
        padding: 24px;
        background: #F8FAFC;
        border-top: 1px solid #E2E8F0;
        display: flex;
        justify-content: center;
    }
    .btn-add-modern {
        background: #6B7AFF;
        color: #FFFFFF;
        border: none;
        border-radius: 16px;
        padding: 10px 28px 10px 12px;
        font-weight: 800;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(107, 122, 255, 0.2);
    }

    .btn-add-modern:hover {
        background: #5465FF;
        color: #FFFFFF;
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(107, 122, 255, 0.3);
    }

    .btn-edit {
        background: rgba(107, 122, 255, 0.1);
        color: #6B7AFF;
        border: none;
        border-radius: 10px;
        padding: 8px 18px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-edit:hover { background: #6B7AFF; color: #FFFFFF; }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #EF4444;
        border: none;
        border-radius: 10px;
        padding: 8px 18px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .btn-delete:hover { background: #EF4444; color: #FFFFFF; }
</style>

<div class="mb-5">
    <h2 class="page-header-title mb-1">Manage Categories</h2>
    <p class="text-muted mb-0">Refine and organize your ByteMe digital product categories.</p>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table custom-table mb-0">
            <thead>
                <tr>
                    <th style="padding-left: 32px;">Category Name</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td style="padding-left: 32px; font-size: 1.05rem;">
                        {{ $category->nama }}
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-edit">
                                Edit
                            </a>
                            <form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<button type="button" class="btn btn-sm btn-danger px-3 rounded-pill" 
    onclick="confirmDelete('delete-form-{{ $category->id }}', 'This category will be gone forever!')">
    Delete
</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center py-5">
                        <p class="text-muted mb-0">No categories found yet.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-actions-footer">
        <a href="{{ route('admin.categories.create') }}" class="btn-add-modern">
            <div class="plus-icon-circle">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </div>
            Add New Category
        </a>
    </div>
</div>

<div class="mt-4 d-flex justify-content-end">
    {{ $categories->links('pagination::bootstrap-5') }}
</div>
@endsection