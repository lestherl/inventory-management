@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Stock Transactions')

@section('content')
    <div class="stock-transaction-layout">
        <div class="stock-transaction-section" id="stockTransactionSection">
            <div class="inventory-table-card">
                <div class="table-topbar">
                    <div class="table-title-wrap">
                        <h3 class="table-title">Stock Transactions</h3>
                    </div>
                    <a href="{{ route('staff.stock.create') }}" class="add-table-btn">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Add Stock In
                    </a>
                </div>

                <div class="filter-bar">
                    <form action="{{ route('staff.stock') }}" method="GET" class="filter-form">
                        <select name="product">
                            <option value="">All Products</option>
                            @foreach (\App\Models\Product::orderBy('product_name')->get() as $product)
                                <option value="{{ $product->product_id }}" {{ request('product') == $product->product_id ? 'selected' : '' }}>
                                    {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="{{ route('staff.stock') }}" class="btn btn-secondary">Clear</a>
                    </form>
                </div>

                @if ($transactions->isEmpty())
                    <div class="empty-state">
                        No transactions found.
                    </div>
                @else
                    <div class="inventory-table-wrapper">
                        <table class="inventory-table" id="stockTransactionTable">
                            <thead>
                                <tr>
                                    <th class="col-date">Date</th>
                                    <th class="col-product">Product</th>
                                    <th class="col-type">Type</th>
                                    <th class="col-quantity">Quantity</th>
                                    <th class="col-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td class="col-date">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                        <td class="col-product">{{ $transaction->product->product_name ?? 'N/A' }}</td>
                                        <td class="col-type">
                                            <span class="badge badge-{{ $transaction->transaction_type === 'stock_in' ? 'success' : 'danger' }}">
                                                {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                            </span>
                                        </td>
                                        <td class="col-quantity">{{ $transaction->quantity }}</td>
                                        <td class="col-actions">
                                            <div class="inventory-actions">
                                                <button type="button" class="inventory-action" onclick="showTransactionDetails({{ $transaction->id }})" aria-label="View details">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M3 7.5A2.5 2.5 0 0 1 5.5 5h11A2.5 2.5 0 0 1 19 7.5v9A2.5 2.5 0 0 1 16.5 19h-11A2.5 2.5 0 0 1 3 16.5v-9Z"/>
                                                        <path d="M7 9h7M7 12h10M7 15h8"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="transaction-details-section" id="transactionDetailsSection" style="display: none;">
            <div class="form-card">
                <div class="form-header">
                    <h3 class="form-title">Transaction Details</h3>
                    <button type="button" class="back-btn" onclick="closeTransactionDetails()">Close</button>
                </div>

                <div class="detail-view" id="transactionDetailsContent">
                </div>
            </div>
        </div>
    </div>

    @foreach ($transactions as $transaction)
        <div id="transactionDetails-{{ $transaction->id }}" style="display: none;">
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $transaction->created_at->format('M d, Y H:i:s') }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Product:</span>
                <span class="detail-value">{{ $transaction->product->product_name ?? 'N/A' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Transaction Type:</span>
                <span class="detail-value">
                    <span class="badge badge-{{ $transaction->transaction_type === 'stock_in' ? 'success' : 'danger' }}">
                        {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                    </span>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Quantity:</span>
                <span class="detail-value">{{ $transaction->quantity }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Stock Before:</span>
                <span class="detail-value">{{ $transaction->stock_before }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Stock After:</span>
                <span class="detail-value">{{ $transaction->stock_after }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Supplier:</span>
                <span class="detail-value">{{ $transaction->supplier->supplier_name ?? 'N/A' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Reference:</span>
                <span class="detail-value">{{ $transaction->reference ?? 'N/A' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Processed By:</span>
                <span class="detail-value">{{ $transaction->user->name ?? 'N/A' }}</span>
            </div>

            @if ($transaction->notes)
                <div class="detail-row">
                    <span class="detail-label">Notes:</span>
                    <span class="detail-value">{{ $transaction->notes }}</span>
                </div>
            @endif
        </div>
    @endforeach

    <script>
        function showTransactionDetails(transactionId) {
            const detailsSection = document.getElementById('transactionDetailsSection');
            const detailsContent = document.getElementById('transactionDetailsContent');
            const hiddenDetails = document.getElementById('transactionDetails-' + transactionId);
            const layout = document.querySelector('.stock-transaction-layout');

            if (hiddenDetails) {
                detailsContent.innerHTML = hiddenDetails.innerHTML;

                // Remove columns to make space
                const table = document.getElementById('stockTransactionTable');
                const colsToRemove = table.querySelectorAll('.col-date, .col-type');

                colsToRemove.forEach(col => {
                    col.style.opacity = '0';
                    setTimeout(() => {
                        col.style.display = 'none';
                    }, 300);
                });

                // Show details section with animation
                setTimeout(() => {
                    detailsSection.style.display = 'block';
                    layout.classList.add('has-details');
                    setTimeout(() => {
                        detailsSection.classList.add('details-visible');
                    }, 50);
                }, 300);
            }
        }

        function closeTransactionDetails() {
            const detailsSection = document.getElementById('transactionDetailsSection');
            const table = document.getElementById('stockTransactionTable');
            const colsToShow = table.querySelectorAll('.col-date, .col-type');
            const layout = document.querySelector('.stock-transaction-layout');

            // Hide details section with animation
            detailsSection.classList.remove('details-visible');
            layout.classList.remove('has-details');

            setTimeout(() => {
                detailsSection.style.display = 'none';

                // Restore columns
                colsToShow.forEach(col => {
                    col.style.display = '';
                    setTimeout(() => {
                        col.style.opacity = '1';
                    }, 50);
                });
            }, 300);
        }
    </script>
@endsection
