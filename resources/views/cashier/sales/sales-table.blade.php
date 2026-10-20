@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'My Sales')

@section('content')
    <div class="sales-table-layout">
        <div class="sales-table-section" id="salesTableSection">
            <div class="inventory-table-card">
                <div class="table-topbar">
                    <div class="table-title-wrap">
                        <h3 class="table-title">My Sales</h3>
                    </div>
                    <a href="{{ route('cashier.sales.create') }}" class="add-table-btn">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        New Sale
                    </a>
                </div>

                @if ($sales->isEmpty())
                    <div class="empty-state">
                        No sales found.
                    </div>
                @else
                    <div class="inventory-table-wrapper">
                        <table class="inventory-table" id="salesTable">
                            <thead>
                                <tr>
                                    <th class="col-receipt">Receipt #</th>
                                    <th class="col-date">Date</th>
                                    <th class="col-amount">Total Amount</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td class="col-receipt">{{ $sale->receipt_number }}</td>
                                        <td class="col-date">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                                        <td class="col-amount">₱{{ number_format($sale->total_amount, 2) }}</td>
                                        <td class="col-status">
                                            <span class="badge badge-{{ $sale->status === 'completed' ? 'success' : 'danger' }}">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        </td>
                                        <td class="col-actions">
                                            <div class="inventory-actions">
                                                <button type="button" class="inventory-action" onclick="showSaleDetails({{ $sale->id }})" aria-label="View sale">
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

        <div class="sale-details-section" id="saleDetailsSection" style="display: none;">
            <div class="form-card">
                <div class="form-header">
                    <h3 class="form-title">Sale Details</h3>
                    <button type="button" class="back-btn" onclick="closeSaleDetails()">Close</button>
                </div>

                <div class="detail-view" id="saleDetailsContent">
                </div>
            </div>
        </div>
    </div>

    @foreach ($sales as $sale)
        <div id="saleDetails-{{ $sale->id }}" style="display: none;">
            <div class="detail-row">
                <span class="detail-label">Receipt Number:</span>
                <span class="detail-value">{{ $sale->receipt_number }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $sale->created_at->format('M d, Y H:i:s') }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value">₱{{ number_format($sale->total_amount, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Discount Amount:</span>
                <span class="detail-value">₱{{ number_format($sale->discount_amount, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Amount Paid:</span>
                <span class="detail-value">₱{{ number_format($sale->amount_paid, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Change Amount:</span>
                <span class="detail-value">₱{{ number_format($sale->change_amount, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ ucfirst($sale->payment_method) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="badge badge-{{ $sale->status === 'completed' ? 'success' : 'danger' }}">
                        {{ ucfirst($sale->status) }}
                    </span>
                </span>
            </div>

            <h4 class="section-title" style="margin-top: 2rem;">Sale Items</h4>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->saleItems as $item)
                            <tr>
                                <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                <td>₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <script>
        function showSaleDetails(saleId) {
            const detailsSection = document.getElementById('saleDetailsSection');
            const detailsContent = document.getElementById('saleDetailsContent');
            const hiddenDetails = document.getElementById('saleDetails-' + saleId);
            const layout = document.querySelector('.sales-table-layout');

            if (hiddenDetails) {
                detailsContent.innerHTML = hiddenDetails.innerHTML;

                // Remove Date and Status columns to make space
                const table = document.getElementById('salesTable');
                const colsToRemove = table.querySelectorAll('.col-date, .col-status');

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

        function closeSaleDetails() {
            const detailsSection = document.getElementById('saleDetailsSection');
            const table = document.getElementById('salesTable');
            const colsToShow = table.querySelectorAll('.col-date, .col-status');
            const layout = document.querySelector('.sales-table-layout');

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
