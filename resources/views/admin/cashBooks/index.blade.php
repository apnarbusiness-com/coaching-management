@extends('layouts.admin')
@section('title', 'Cash Books — List')
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

        .transaction-item.earning_added,
        .transaction-item.student_payment_added {
            border-left-color: #22c55e;
        }

        .transaction-item.expense_subtracted {
            border-left-color: #f59e0b;
        }

        .transaction-item.transfer_out {
            border-left-color: #f97316;
        }

        .transaction-item.transfer_in {
            border-left-color: #22c55e;
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

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .transaction-item.new {
            animation: slideInUp 0.4s ease-out;
        }

        .transaction-loader {
            text-align: center;
            padding: 20px;
            color: #64748b;
        }
        .transaction-loader i {
            font-size: 24px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        .btn-details {
            top: 1rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 0.65rem;
            border: none;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: hidden;
        }

        .action-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0);
            transition: background 0.2s ease;
            border-radius: inherit;
        }

        .action-btn:hover::after {
            background: rgba(255, 255, 255, 0.15);
        }

        .action-btn:active {
            transform: translateY(1px);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }

        .action-btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .action-btn-text {
            line-height: 1;
        }

        .action-btn-primary {
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            color: #fff;
        }

        .action-btn-primary .action-btn-icon {
            background: rgba(255, 255, 255, 0.2);
        }

        .action-btn-warning {
            background: linear-gradient(135deg, #e65100, #f57c00);
            color: #fff;
        }

        .action-btn-warning .action-btn-icon {
            background: rgba(255, 255, 255, 0.2);
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
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold text-primary mb-1" style="font-size: 1.25rem;">
                    <i class="fas fa-wallet me-2" style="opacity: 0.7;"></i>Fund Locations
                </h4>
                <p class="text-muted small mb-0">Manage your financial assets.</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                @can('cash_book_edit')
                    <form method="POST" action="{{ route('admin.cash-books.update-display-type') }}" class="d-flex align-items-center gap-2 me-2">
                        @csrf
                        <span class="text-muted small">Display:</span>
                        <select name="display_type" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            <option value="select" {{ setting('cashbook_display_type') == 'select' ? 'selected' : '' }}>Select Box</option>
                            <option value="card" {{ setting('cashbook_display_type') == 'card' ? 'selected' : '' }}>Cards</option>
                        </select>
                    </form>
                @endcan
                @can('cash_book_create')
                    <a href="{{ route('admin.cash-books.create') }}" class="action-btn action-btn-primary">
                        <span class="action-btn-icon"><i class="fa fa-plus"></i></span>
                        <span class="action-btn-text">Add New</span>
                    </a>
                @endcan
                @can('cash_book_edit')
                    <button class="action-btn action-btn-warning" onclick="openTransferModal()">
                        <span class="action-btn-icon"><i class="fas fa-exchange-alt"></i></span>
                        <span class="action-btn-text">Transfer</span>
                    </button>
                @endcan
            </div>
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
                    @if ($cashBook->is_default)
                        <span class="badge bg-success position-absolute" style="bottom: 5px; left: 24px; font-size: 9px;">DEFAULT</span>
                    @endif
                    @if ($cashBook->is_financial_account)
                        <span class="badge bg-info position-absolute" style="bottom: 5px; left: 50%; font-size: 9px;transform: translateX(-50%);">FINANCIAL</span>
                    @endif
                    <div class="position-absolute d-flex gap-1" style="top: 0.75rem; right: 1.5rem;">
                        @can('cash_book_access')
                            <button class="btn btn-sm btn-outline-info" style="padding: 0.25rem 0.5rem; font-size: 11px;"
                                onclick="openTransactionDrawer({{ $cashBook->id }}, {{ json_encode($cashBook->title) }})">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        @endcan
                        @can('cash_book_edit')
                            <button class="btn btn-sm btn-outline-primary" style="padding: 0.25rem 0.5rem; font-size: 11px;"
                                onclick="openEditModal({{ $cashBook->id }}, {{ json_encode($cashBook->title) }}, {{ $cashBook->amount }}, {{ json_encode($cashBook->image ?? '') }}, {{ json_encode($cashBook->icon ?? '') }}, {{ $cashBook->is_financial_account ? 'true' : 'false' }}, {{ $cashBook->is_default ? 'true' : 'false' }}, {{ $cashBook->order }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if (!$cashBook->is_default)
                                <form method="POST" action="{{ route('admin.cash-books.set-default', $cashBook->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" style="padding: 0.25rem 0.5rem; font-size: 11px;" title="Set as Default">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
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
                                <div class="form-check">
                                    <input type="checkbox" name="is_financial_account" class="form-check-input" id="editIsFinancialAccount" value="1">
                                    <label class="form-check-label" for="editIsFinancialAccount">
                                        <strong>Financial Account</strong>
                                    </label>
                                    <small class="d-block text-muted">Appears in Earning & Expense forms for auto balance update.</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="order" id="editOrder" class="form-control" min="0">
                                </div>
                                <div class="col-6 d-flex align-items-end">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_default" class="form-check-input" id="editIsDefault" value="1">
                                        <label class="form-check-label" for="editIsDefault">
                                            <strong>Default Account</strong>
                                        </label>
                                    </div>
                                </div>
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
    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.cash-books.transfer') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Transfer Funds</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Account <span class="text-danger">*</span></label>
                            <select name="from_cash_book_id" id="transferFrom" class="form-control select2-transfer" required style="width: 100%;">
                                <option value="">— Select Source —</option>
                                @foreach ($cashBooks as $cb)
                                    <option value="{{ $cb->id }}" data-image="{{ $cb->image ? Storage::url($cb->image) : '' }}" data-icon="{{ $cb->icon ?? '' }}" data-balance="{{ $cb->amount }}" {{ old('from_cash_book_id') == $cb->id ? 'selected' : '' }}>{{ $cb->title }} (Tk {{ number_format($cb->amount, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">To Account <span class="text-danger">*</span></label>
                            <select name="to_cash_book_id" id="transferTo" class="form-control select2-transfer" required style="width: 100%;">
                                <option value="">— Select Destination —</option>
                                @foreach ($cashBooks as $cb)
                                    <option value="{{ $cb->id }}" data-image="{{ $cb->image ? Storage::url($cb->image) : '' }}" data-icon="{{ $cb->icon ?? '' }}" data-balance="{{ $cb->amount }}" {{ old('to_cash_book_id') == $cb->id ? 'selected' : '' }}>{{ $cb->title }} (Tk {{ number_format($cb->amount, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="transferBalanceWarning" style="display:none;">
                            <div class="alert alert-warning mb-0 py-2 small" id="transferBalanceMsg"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount (Tk) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="transferAmount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required value="{{ old('amount') }}">
                            @error('amount')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note (optional)</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Reason for transfer...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeTransferModal()">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="transferSubmitBtn">Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

@can('cash_book_access')
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

    @push('scripts')
        <script>
            let currentCashBookId = null;
            let transactionPage = 1;
            let hasMoreTransactions = false;
            let isLoadingTransactions = false;
            const txTypeLabels = {
                'create': 'Created',
                'update': 'Updated',
                'delete': 'Deleted',
                'earning_added': 'Earning Added',
                'student_payment_added': 'Student Payment Added',
                'expense_subtracted': 'Expense Subtracted',
                'transfer_out': 'Transfer Out',
                'transfer_in': 'Transfer In'
            };

            function openTransactionDrawer(id, title) {
                currentCashBookId = id;
                transactionPage = 1;
                hasMoreTransactions = false;
                isLoadingTransactions = false;
                document.getElementById('drawerTitle').textContent = title + ' - History';
                document.getElementById('transactionOverlay').classList.add('open');
                document.getElementById('transactionDrawer').classList.add('open');

                loadTransactions(id, false);
            }

            function closeTransactionDrawer() {
                document.getElementById('transactionOverlay').classList.remove('open');
                document.getElementById('transactionDrawer').classList.remove('open');
            }

            function buildTransactionHtml(tx, isNew) {
                const type = tx.action_type;
                const typeLabel = txTypeLabels[type] || type;

                const date = new Date(tx.created_at);
                const formattedDate = date.toLocaleDateString('en-US', {
                    year: 'numeric', month: 'short', day: 'numeric'
                }) + ' ' + date.toLocaleTimeString('en-US', {
                    hour: '2-digit', minute: '2-digit'
                });

                let html = '<div class="transaction-item ' + type + (isNew ? ' new' : '') + '">';
                html += '<div class="transaction-header">';
                html += '<span class="transaction-type">' + typeLabel + '</span>';
                html += '<span class="transaction-date">' + formattedDate + '</span>';
                html += '</div>';

                const showAmount = ['update', 'earning_added', 'student_payment_added', 'expense_subtracted', 'transfer_out', 'transfer_in'];
                if (type === 'create') {
                    html += '<div class="transaction-amounts"><span>New: <span class="transaction-new">Tk ' + parseFloat(tx.new_amount).toFixed(2) + '</span></span></div>';
                } else if (type === 'delete') {
                    html += '<div class="transaction-amounts"><span>Old: <span class="transaction-old">Tk ' + parseFloat(tx.old_amount).toFixed(2) + '</span></span></div>';
                } else if (showAmount.includes(type)) {
                    html += '<div class="transaction-amounts"><span>Old: <span class="transaction-old">Tk ' + parseFloat(tx.old_amount).toFixed(2) + '</span></span><span>→ New: <span class="transaction-new">Tk ' + parseFloat(tx.new_amount).toFixed(2) + '</span></span></div>';
                }

                if (tx.note) {
                    html += '<p class="transaction-note">' + tx.note + '</p>';
                }
                if (tx.created_by) {
                    html += '<div class="transaction-user">By: ' + tx.created_by.name + '</div>';
                }
                html += '</div>';
                return html;
            }

            function loadTransactions(id, append) {
                const listEl = document.getElementById('transactionList');

                if (!append) {
                    listEl.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading...</p></div>';
                } else {
                    listEl.insertAdjacentHTML('beforeend', '<div class="transaction-loader" id="txLoader"><i class="fas fa-spinner"></i><p>Loading more...</p></div>');
                }

                isLoadingTransactions = true;

                fetch('/admin/cash-books/' + id + '/transactions?page=' + transactionPage)
                    .then(response => response.json())
                    .then(data => {
                        const loaderEl = document.getElementById('txLoader');
                        if (loaderEl) loaderEl.remove();

                        if (data.transactions.length === 0) {
                            if (!append) {
                                listEl.innerHTML = '<div class="text-center text-muted py-5"><p>No transactions found.</p></div>';
                            }
                            isLoadingTransactions = false;
                            return;
                        }

                        let html = '';
                        data.transactions.forEach(function(tx) {
                            html += buildTransactionHtml(tx, append);
                        });

                        if (!append) {
                            listEl.innerHTML = html;
                        } else {
                            listEl.insertAdjacentHTML('beforeend', html);
                        }

                        hasMoreTransactions = data.has_more;
                        if (hasMoreTransactions) {
                            transactionPage = data.next_page;
                        }
                        isLoadingTransactions = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const loaderEl = document.getElementById('txLoader');
                        if (loaderEl) loaderEl.remove();
                        if (!append) {
                            listEl.innerHTML = '<div class="text-center text-danger py-5"><p>Error loading transactions.</p></div>';
                        }
                        isLoadingTransactions = false;
                    });
            }

            // Infinite scroll
            document.getElementById('transactionList').addEventListener('scroll', function() {
                if (isLoadingTransactions || !hasMoreTransactions) return;
                if (this.scrollHeight - this.scrollTop - this.clientHeight < 100) {
                    loadTransactions(currentCashBookId, true);
                }
            });
        </script>
    @endpush
@endcan

@can('cash_book_edit')
    @push('scripts')
        <script>
            function openEditModal(id, title, amount, image, icon, isFinancial, isDefault = false, order = 0) {
                document.getElementById('editTitle').value = title;
                document.getElementById('editAmount').value = amount;
                document.getElementById('editNote').value = '';
                document.getElementById('editImageUpload').value = '';
                document.getElementById('editMethod').value = 'PUT';
                document.getElementById('editRemoveImage').value = '0';
                document.getElementById('editIconSelect').value = icon || '';
                document.getElementById('editIconValue').value = icon || '';
                document.getElementById('editIsFinancialAccount').checked = isFinancial === true || isFinancial === 1 || isFinancial === '1';
                document.getElementById('editIsDefault').checked = isDefault === true || isDefault === 1 || isDefault === '1';
                document.getElementById('editOrder').value = order;

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

            var cashBookIcons = {
                'wallet': '💰',
                'money': '💵',
                'bank': '🏦',
                'mobile': '📱',
                'card': '💳',
                'gift': '🎁',
                'gold': '🪙',
                'dollar': '💲'
            };

            function formatCashBookOption(state) {
                if (!state.id) return state.text;
                var $el = $(state.element);
                var img = $el.data('image');
                var icon = $el.data('icon');
                var balance = parseFloat($el.data('balance') || 0);
                var thumbHtml = '';
                if (img) {
                    thumbHtml = '<div class="cb-thumb" style="background-image: url(' + img + '); background-size: cover; background-position: center; width: 28px; height: 28px; border-radius: 50%; display: inline-block; margin-right: 8px; vertical-align: middle; flex-shrink: 0;"></div>';
                } else if (icon && cashBookIcons[icon]) {
                    thumbHtml = '<div class="cb-thumb" style="width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #f0fdf4; margin-right: 8px; vertical-align: middle; flex-shrink: 0; font-size: 14px;">' + cashBookIcons[icon] + '</div>';
                } else {
                    thumbHtml = '<div class="cb-thumb" style="width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #e8eaf6; margin-right: 8px; vertical-align: middle; flex-shrink: 0; font-size: 12px;"><i class="fas fa-wallet"></i></div>';
                }
                return $('<div class="cb-option" style="display: flex; align-items: center;">' + thumbHtml + '<span class="cb-title" style="vertical-align: middle; font-size: 14px;">' + state.text + '</span></div>');
            }

            var transferModalInstance = null;

            function openTransferModal() {
                var modalEl = document.getElementById('transferModal');
                if (modalEl) {
                    transferModalInstance = new bootstrap.Modal(modalEl);
                    transferModalInstance.show();
                    setTimeout(function() {
                        $('.select2-transfer').select2({
                            dropdownParent: $('#transferModal'),
                            templateResult: formatCashBookOption,
                            templateSelection: formatCashBookOption,
                            width: '100%'
                        });
                    }, 300);
                }
            }

            function closeTransferModal() {
                if (transferModalInstance) {
                    $('.select2-transfer').select2('destroy');
                    transferModalInstance.hide();
                    transferModalInstance = null;
                }
            }

            function checkTransferBalance() {
                var fromId = $('#transferFrom').val();
                var amount = parseFloat($('#transferAmount').val()) || 0;
                if (!fromId || amount <= 0) {
                    $('#transferBalanceWarning').hide();
                    return;
                }
                var opt = $('#transferFrom').find('option[value="' + fromId + '"]');
                var balance = parseFloat(opt.data('balance') || 0);
                if (amount > balance) {
                    $('#transferBalanceMsg').text('Insufficient balance. Available: Tk ' + balance.toFixed(2));
                    $('#transferBalanceWarning').show();
                    $('#transferSubmitBtn').prop('disabled', true);
                } else {
                    $('#transferBalanceWarning').hide();
                    $('#transferSubmitBtn').prop('disabled', false);
                }
            }

            $(document).ready(function() {
                $('#transferFrom').on('change', checkTransferBalance);
                $('#transferAmount').on('input', checkTransferBalance);
            });
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
@endsection
