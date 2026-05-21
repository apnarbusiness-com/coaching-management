@extends('layouts.admin')
@section('title', 'Cash Books — Create')
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
                        <label class="form-label">Icon (choose or upload)</label>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <select name="icon_select" class="form-select" id="iconSelect" onchange="toggleIconUpload()">
                                    <option value="">-- Select Icon --</option>
                                    <option value="wallet">💰 Wallet</option>
                                    <option value="money">💵 Money</option>
                                    <option value="bank">🏦 Bank</option>
                                    <option value="mobile">📱 Mobile Banking</option>
                                    <option value="card">💳 Card</option>
                                    <option value="gift">🎁 Gift/Prize Bond</option>
                                    <option value="gold">🪙 Gold</option>
                                    <option value="dollar">💲 Dollar</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="file" name="image" class="form-control" accept="image/*" id="imageUpload" onchange="toggleIconUpload()">
                                <small class="text-muted">Or upload image (png, jpg, svg)</small>
                            </div>
                        </div>
                        <input type="hidden" name="icon" id="iconValue">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_financial_account" class="form-check-input" id="is_financial_account" value="1" {{ old('is_financial_account') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_financial_account">
                                <strong>Financial Account</strong>
                            </label>
                            <small class="d-block text-muted">If checked, this account will appear as a selectable option in Earning & Expense forms. Earnings will add to, Expenses will subtract from this balance.</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Order</label>
                            <input type="number" name="order" class="form-control" min="0" value="{{ old('order', 0) }}">
                            <small class="text-muted">Lower numbers appear first.</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_default" class="form-check-input" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    <strong>Default Account</strong>
                                </label>
                                <small class="d-block text-muted">If checked, this account will be pre-selected in Earning & Expense forms.</small>
                            </div>
                        </div>
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
@push('scripts')
<script>
    function toggleIconUpload() {
        var iconSelect = document.getElementById('iconSelect');
        var imageUpload = document.getElementById('imageUpload');
        var iconValue = document.getElementById('iconValue');
        
        if (imageUpload.files.length > 0) {
            iconSelect.value = '';
            iconValue.value = '';
        } else if (iconSelect.value) {
            iconValue.value = iconSelect.value;
        } else {
            iconValue.value = '';
        }
    }
</script>
@endpush
@endsection