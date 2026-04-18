@extends('layouts.admin')
@section('content')
<style>
    :root {
        --primary-blue: #0d47a1;
        --emerald-accent: #85f8c4;
        --emerald-deep: #00573b;
    }
    .hero-card {
        background: linear-gradient(135deg, #0d47a1, #1a237e);
        border-radius: 1rem;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .balance-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.85;
        margin-bottom: 0.25rem;
    }
    .balance-amount {
        font-size: 2rem;
        font-weight: 800;
    }
    .fund-card {
        background: #fff;
        border-radius: 0.75rem;
        padding: 1.25rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        position: relative;
    }
    .fund-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .fund-card-image {
        width: 100%;
        height: 100px;
        object-fit: contain;
        margin-bottom: 0.75rem;
    }
    .fund-card-placeholder {
        width: 100%;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .fund-card-placeholder img {
        max-height: 70px;
        max-width: 80%;
    }
    .fund-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }
    .fund-amount {
        font-size: 1.25rem;
        font-weight: 800;
        color: #059669;
    }
    .fund-status {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .add-card {
        border: 2px dashed #cbd5e1;
        background: transparent;
        min-height: 250px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .add-card:hover {
        border-color: #0d47a1;
        background: rgba(13, 71, 161, 0.02);
    }
    .add-card-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="hero-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <p class="balance-label">Total Balance</p>
                    <h2 class="balance-amount">Tk {{ number_format($totalBalance, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-end">
        <div>
            <h4 class="fw-bold text-primary mb-1">Fund Locations</h4>
            <p class="text-muted small mb-0">Manage your financial assets.</p>
        </div>
        @can('cash_book_create')
        <a href="{{ route('admin.cash-books.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add New
        </a>
        @endcan
    </div>
</div>

<div class="row g-4">
    @forelse($cashBooks as $cashBook)
    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
        <div class="fund-card h-100 d-flex flex-column" style="min-height: 280px;">
            <div class="fund-card-placeholder">
                @if($cashBook->image)
                <img src="{{ Storage::url($cashBook->image) }}" alt="{{ $cashBook->title }}">
                @else
                <img src="https://via.placeholder.com/120x80?text={{ urlencode($cashBook->title) }}" alt="{{ $cashBook->title }}">
                @endif
            </div>
            <h5 class="fund-title">{{ $cashBook->title }}</h5>
            <p class="fund-amount">Tk {{ number_format($cashBook->amount, 2) }}</p>
            <span class="fund-status" style="color: {{ $cashBook->status === 'active' ? '#059669' : '#94a3b8' }};">
                {{ $cashBook->status === 'active' ? 'Active' : 'Inactive' }}
            </span>
            @can('cash_book_edit')
            <div class="mt-auto pt-2 d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm flex-grow-1" onclick="openEditModal({{ $cashBook->id }}, '{{ $cashBook->title }}', {{ $cashBook->amount }}, '{{ $cashBook->image ?? '' }}')">
                    <i class="fa fa-edit"></i> Edit
                </button>
            </div>
            @endcan
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <p class="text-muted">No cash book entries yet.</p>
            @can('cash_book_create')
            <a href="{{ route('admin.cash-books.create') }}" class="btn btn-primary">Add First Entry</a>
            @endcan
        </div>
    </div>
    @endforelse

    @can('cash_book_create')
    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
        <a href="{{ route('admin.cash-books.create') }}" class="fund-card add-card h-100 text-decoration-none">
            <div class="add-card-icon">
                <i class="fa fa-plus text-muted" style="font-size: 24px;"></i>
            </div>
            <p class="fw-bold text-primary mb-1">Add New Location</p>
            <p class="text-muted small">Create a fund location</p>
        </a>
    </div>
    @endcan
</div>

@can('cash_book_edit')
<div class="modal fade" id="editCashBookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCashBookForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="editMethod" value="PUT">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Cash Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" id="editTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (Tk) *</label>
                        <input type="number" name="amount" id="editAmount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" id="editImage" class="form-control" accept="image/*">
                        <small class="text-muted">Upload logo or icon (png, jpg, svg)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" id="editNote" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    @can('cash_book_delete')
                    <button type="button" class="btn btn-danger me-auto" id="deleteBtn">Delete</button>
                    @endcan
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@can('cash_book_edit')
@push('scripts')
<script>
    let currentCashBookId = null;

    function openEditModal(id, title, amount, image) {
        currentCashBookId = id;
        
        document.getElementById('editTitle').value = title;
        document.getElementById('editAmount').value = amount;
        document.getElementById('editNote').value = '';
        document.getElementById('editImage').value = '';
        document.getElementById('editMethod').value = 'PUT';
        
        document.getElementById('editCashBookForm').action = '/admin/cash-books/' + id;
        document.getElementById('deleteBtn').onclick = function() {
            if (confirm('Are you sure you want to delete this? This will be logged in history.')) {
                document.getElementById('editMethod').value = 'DELETE';
                document.getElementById('editCashBookForm').submit();
            }
        };
        
        var modal = new bootstrap.Modal(document.getElementById('editCashBookModal'));
        modal.show();
    }
</script>
@endpush
@endcan
@endsection