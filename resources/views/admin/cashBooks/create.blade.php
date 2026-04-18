@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Add New Cash Book Entry</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cash-books.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g., Hand Cash, Personal bKash" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (Tk) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image / Logo</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Upload an image (png, jpg, svg) as logo or icon</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="{{ route('admin.cash-books.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection