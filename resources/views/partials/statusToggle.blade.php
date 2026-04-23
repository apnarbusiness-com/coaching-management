<div class="status-toggle">
    <label class="status-switch">
        <input type="checkbox" class="batch-status-toggle" data-url="{{ $toggleUrl }}" {{ $isActive ? 'checked' : '' }}>
        <span class="status-slider"></span>
    </label>
    <span class="status-label {{ $isActive ? 'is-active' : '' }}">{{ $isActive ? 'Active' : 'Inactive' }}</span>
</div>