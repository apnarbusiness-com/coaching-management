@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Cash Book Entry</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cash-books.update', $cashBook->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $cashBook->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (Tk) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ $cashBook->amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image / Logo</label>
                        @if($cashBook->image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($cashBook->image) }}" style="max-height: 80px;" class="img-thumbnail">
                            <div class="form-check mt-2">
                                <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="remove_image">
                                <label class="form-check-label" for="remove_image">Remove image</label>
                            </div>
                        </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Upload new image to replace (png, jpg, svg)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="3">{{ $cashBook->note }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.cash-books.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection