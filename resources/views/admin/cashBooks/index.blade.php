@extends('layouts.admin')
@section('content')
    <style>
        :root {
            --primary-blue: #0d47a1;
            --primary-dark: #0a3a82;
            --surface-bg: #f7f9fb;
            --emerald-accent: #85f8c4;
            --emerald-deep: #00573b;
            --text-main: #191c1e;
            --text-secondary: #57657a;
            --card-shadow: 0 12px 40px rgba(25, 28, 30, 0.06);
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
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            border: none;
            box-shadow: var(--card-shadow);
            border-left: 4px solid var(--emerald-accent);
            transition: transform 0.2s;
            position: relative;
            height: 100%;
        }

        .fund-card:hover {
            transform: translateY(-5px);
        }

        .fund-card.empty {
            border-left-color: #cbd5e1;
            background-color: #f8fafc;
        }

        .fund-card.add-new {
            border: 2px dashed #cbd5e1;
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-left: 2px dashed #cbd5e1;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .bg-tertiary-light {
            background-color: rgba(133, 248, 196, 0.2);
            color: var(--emerald-deep);
        }

        .bg-blue-light {
            background-color: #d5e3fc;
            color: var(--primary-blue);
        }

        .bg-gray-light {
            background-color: #e2e8f0;
            color: #64748b;
        }

        .fund-title-en {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .fund-title-bn {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }

        .fund-amount {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--emerald-deep);
            margin: 0;
        }

        .status-tag {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            color: var(--emerald-deep);
        }

        .fund-card-image {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 50%;
        }

        .add-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: 400px;
            max-width: 90vw;
            height: 100vh;
            background: white;
            box-shadow: -4px 0 20px rgba(0,0,0,0.15);
            z-index: 1050;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .drawer.open {
            transform: translateX(0);
        }

        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .drawer-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .drawer-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .drawer-body {
            padding: 1rem 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .drawer-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
            padding: 0;
            line-height: 1;
        }

        .drawer-close:hover {
            color: #1e293b;
        }

        .transaction-item {
            padding: 1rem;
            border-radius: 0.5rem;
            background: #f8fafc;
            margin-bottom: 0.75rem;
            border-left: 3px solid #0d47a1;
        }

        .transaction-item.create {
            border-left-color: #22c55e;
        }

        .transaction-item.update {
            border-left-color: #0d47a1;
        }

        .transaction-item.delete {
            border-left-color: #ef4444;
        }

        .transaction-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .transaction-type {
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
        }

        .transaction-date {
            font-size: 0.75rem;
            color: #64748b;
        }

        .transaction-amounts {
            display: flex;
            gap: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .transaction-old {
            color: #ef4444;
        }

        .transaction-new {
            color: #22c55e;
        }

        .transaction-note {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
        }

        .transaction-user {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.5rem;
        }

        .btn-details {
            top: 1rem;
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

    <div class="row g-4" style="row-gap: 1.5rem;">
        @forelse($cashBooks as $cashBook)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="fund-card">
                    @php
                        $icons = [
                            'wallet' => '💰',
                            'money' => '💵',
                            'bank' => '🏦',
                            'mobile' => '📱',
                            'card' => '💳',
                            'gift' => '🎁',
                            'gold' => '🪙',
                            'dollar' => '💲',
                        ];
                    @endphp
                    @if ($cashBook->image)
                        <div class="icon-box">
                            <img src="{{ Storage::url($cashBook->image) }}" alt="{{ $cashBook->title }}"
                                class="fund-card-image">
                        </div>
                    @elseif($cashBook->icon && isset($icons[$cashBook->icon]))
                        <div class="icon-box bg-tertiary-light" style="font-size: 24px;">
                            {{ $icons[$cashBook->icon] }}
                        </div>
                    @else
                        <div class="icon-box bg-tertiary-light">
                            <i class="fas fa-wallet"></i>
                        </div>
                    @endif
                    <p class="fund-title-en">{{ $cashBook->title }}</p>
                    <h4 class="fund-title-bn">{{ $cashBook->title }}</h4>
                    <p class="fund-amount">Tk {{ number_format($cashBook->amount, 2) }}</p>
                    <span class="status-tag">{{ $cashBook->status === 'active' ? 'Active' : 'Inactive' }}</span>
                    @can('cash_book_edit')
                        <button class="btn btn-sm btn-outline-primary position-absolute btn-details" style="top: 1rem; right: 1.5rem;"
                            onclick="openEditModal({{ $cashBook->id }}, {{ json_encode($cashBook->title) }}, {{ $cashBook->amount }}, {{ json_encode($cashBook->image ?? '') }}, {{ json_encode($cashBook->icon ?? '') }})">
                            <i class="fas fa-edit"></i>
                        </button>
                    @endcan
                    @can('cash_book_access')
                        <button class="btn btn-sm btn-outline-info position-absolute" style="top: 1rem; right: {{ Auth::user()->can('cash_book_edit') ? '5.5rem' : '1.5rem' }};"
                            onclick="openTransactionDrawer({{ $cashBook->id }}, {{ json_encode($cashBook->title) }})">
                            <i class="fas fa-info-circle"></i>
                        </button>
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
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ route('admin.cash-books.create') }}" class="fund-card add-new text-center text-decoration-none">
                    <div class="add-card-icon mx-auto mb-2">
                        <i class="fas fa-plus text-muted"></i>
                    </div>
                    <p class="fw-bold text-primary mb-1">Add Wallet</p>
                    <p class="text-secondary small">Create a custom fund location</p>
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
                                <input type="number" name="amount" id="editAmount" class="form-control" step="0.01"
                                    min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Icon (choose or upload)</label>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <select name="icon_select" id="editIconSelect" class="form-select"
                                            onchange="toggleEditIcon()">
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
                                        <input type="file" name="image" id="editImageUpload" class="form-control"
                                            accept="image/*" onchange="toggleEditIcon()">
                                        <small class="text-muted">Or upload image</small>
                                    </div>
                                </div>
                                <input type="hidden" name="icon" id="editIconValue">
                                <input type="hidden" name="remove_image" id="editRemoveImage" value="0">
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
                console.log("ddd");
                
                function openEditModal(id, title, amount, image, icon) {
                    document.getElementById('editTitle').value = title;
                    document.getElementById('editAmount').value = amount;
                    document.getElementById('editNote').value = '';
                    document.getElementById('editImageUpload').value = '';
                    document.getElementById('editMethod').value = 'PUT';
                    document.getElementById('editRemoveImage').value = '0';
                    document.getElementById('editIconSelect').value = icon || '';
                    document.getElementById('editIconValue').value = icon || '';

                    document.getElementById('editCashBookForm').action = '/admin/cash-books/' + id;
                    document.getElementById('deleteBtn').onclick = function() {
                        if (confirm('Delete this entry? This will be logged.')) {
                            document.getElementById('editMethod').value = 'DELETE';
                            document.getElementById('editCashBookForm').submit();
                        }
                    };

                    var modalEl = document.getElementById('editCashBookModal');
                    if (modalEl) {
                        var modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                }

                function toggleEditIcon() {
                    var iconSelect = document.getElementById('editIconSelect');
                    var imageUpload = document.getElementById('editImageUpload');
                    var iconValue = document.getElementById('editIconValue');
                    var removeImage = document.getElementById('editRemoveImage');

                    if (imageUpload && imageUpload.files.length > 0) {
                        iconSelect.value = '';
                        iconValue.value = '';
                        removeImage.value = '1';
                    } else if (iconSelect && iconSelect.value) {
                        iconValue.value = iconSelect.value;
                        removeImage.value = '0';
                    } else if (iconValue) {
                        iconValue.value = '';
                        removeImage.value = '0';
                    }
                }
            </script>
        @endpush
    @endcan

    @if (!Auth::user()->can('cash_book_edit'))
        @section('scripts')
            <script>
                function openEditModal() {
                    return false;
                }
            </script>
        @endsection
    @endif

    <div class="drawer-overlay" id="transactionOverlay" onclick="closeTransactionDrawer()"></div>
    <div class="drawer" id="transactionDrawer">
        <div class="drawer-header">
            <h5 class="mb-0 fw-bold" id="drawerTitle">Transaction History</h5>
            <button class="drawer-close" onclick="closeTransactionDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="transactionList">
            <div class="text-center text-muted py-5">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Loading...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // console.log("hello");
        
        let currentCashBookId = null;

        function openTransactionDrawer(id, title) {
            currentCashBookId = id;
            document.getElementById('drawerTitle').textContent = title + ' - History';
            document.getElementById('transactionOverlay').classList.add('open');
            document.getElementById('transactionDrawer').classList.add('open');

            loadTransactions(id);
        }

        function closeTransactionDrawer() {
            document.getElementById('transactionOverlay').classList.remove('open');
            document.getElementById('transactionDrawer').classList.remove('open');
        }

        function loadTransactions(id) {
            const listEl = document.getElementById('transactionList');
            listEl.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading...</p></div>';

            fetch('/admin/cash-books/' + id + '/transactions')
                .then(response => response.json())
                .then(data => {
                    if (data.transactions.length === 0) {
                        listEl.innerHTML = '<div class="text-center text-muted py-5"><p>No transactions found.</p></div>';
                        return;
                    }

                    let html = '';
                    const icons = {
                        'wallet' => '💰',
                        'money' => '💵',
                        'bank' => '🏦',
                        'mobile' => '📱',
                        'card' => '💳',
                        'gift' => '🎁',
                        'gold' => '🪙',
                        'dollar' => '💲',
                    };

                    data.transactions.forEach(function(tx) {
                        const typeLabels = {
                            'create': 'Created',
                            'update': 'Updated',
                            'delete': 'Deleted'
                        };
                        const type = tx.action_type;
                        const typeLabel = typeLabels[type] || type;

                        const date = new Date(tx.created_at);
                        const formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        }) + ' ' + date.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        html += '<div class="transaction-item ' + type + '">';
                        html += '<div class="transaction-header">';
                        html += '<span class="transaction-type">' + typeLabel + '</span>';
                        html += '<span class="transaction-date">' + formattedDate + '</span>';
                        html += '</div>';

                        if (type === 'create') {
                            html += '<div class="transaction-amounts">';
                            html += '<span>New: <span class="transaction-new">Tk ' + parseFloat(tx.new_amount).toFixed(2) + '</span></span>';
                            html += '</div>';
                        } else if (type === 'update') {
                            html += '<div class="transaction-amounts">';
                            html += '<span>Old: <span class="transaction-old">Tk ' + parseFloat(tx.old_amount).toFixed(2) + '</span></span>';
                            html += '<span>→ New: <span class="transaction-new">Tk ' + parseFloat(tx.new_amount).toFixed(2) + '</span></span>';
                            html += '</div>';
                        } else if (type === 'delete') {
                            html += '<div class="transaction-amounts">';
                            html += '<span>Old: <span class="transaction-old">Tk ' + parseFloat(tx.old_amount).toFixed(2) + '</span></span>';
                            html += '</div>';
                        }

                        if (tx.note) {
                            html += '<p class="transaction-note">' + tx.note + '</p>';
                        }

                        if (tx.created_by) {
                            html += '<div class="transaction-user">By: ' + tx.created_by.name + '</div>';
                        }

                        html += '</div>';
                    });

                    listEl.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    listEl.innerHTML = '<div class="text-center text-danger py-5"><p>Error loading transactions.</p></div>';
                });
        }
    </script>
@endpush
