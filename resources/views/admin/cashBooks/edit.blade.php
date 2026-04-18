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
                        <label class="form-label">Icon (choose or upload)</label>
                        @if($cashBook->image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($cashBook->image) }}" style="max-height: 60px;" class="img-thumbnail">
                            <div class="form-check mt-1">
                                <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="remove_image">
                                <label class="form-check-label" for="remove_image">Remove image</label>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-6 mb-2">
                                <select name="icon_select" class="form-select" id="iconSelect" onchange="toggleIconUpload()">
                                    <option value="">-- Select Icon --</option>
                                    <option value="wallet" {{ $cashBook->icon == 'wallet' ? 'selected' : '' }}>💰 Wallet</option>
                                    <option value="money" {{ $cashBook->icon == 'money' ? 'selected' : '' }}>💵 Money</option>
                                    <option value="bank" {{ $cashBook->icon == 'bank' ? 'selected' : '' }}>🏦 Bank</option>
                                    <option value="mobile" {{ $cashBook->icon == 'mobile' ? 'selected' : '' }}>📱 Mobile Banking</option>
                                    <option value="card" {{ $cashBook->icon == 'card' ? 'selected' : '' }}>💳 Card</option>
                                    <option value="gift" {{ $cashBook->icon == 'gift' ? 'selected' : '' }}>🎁 Gift/Prize Bond</option>
                                    <option value="gold" {{ $cashBook->icon == 'gold' ? 'selected' : '' }}>🪙 Gold</option>
                                    <option value="dollar" {{ $cashBook->icon == 'dollar' ? 'selected' : '' }}>💲 Dollar</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="file" name="image" class="form-control" accept="image/*" id="imageUpload" onchange="toggleIconUpload()">
                                <small class="text-muted">Or upload new image</small>
                            </div>
                        </div>
                        <input type="hidden" name="icon" id="iconValue" value="{{ $cashBook->icon }}">
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
@push('scripts')
<script>
    function toggleIconUpload() {
        var iconSelect = document.getElementById('iconSelect');
        var imageUpload = document.getElementById('imageUpload');
        var iconValue = document.getElementById('iconValue');
        var removeCheckbox = document.getElementById('remove_image');
        
        if (imageUpload.files.length > 0) {
            iconSelect.value = '';
            iconValue.value = '';
            if (removeCheckbox) removeCheckbox.checked = true;
        } else if (iconSelect.value) {
            iconValue.value = iconSelect.value;
            if (removeCheckbox) removeCheckbox.checked = false;
        } else {
            iconValue.value = '';
        }
    }
</script>
@endpush
@endsection