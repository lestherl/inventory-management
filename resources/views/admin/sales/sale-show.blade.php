@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Sale Details')

@section('content')
    <div class="form-card">
        <div class="form-header">
            <h3 class="form-title">Sale Details</h3>
            <a href="{{ route('admin.sales') }}" class="back-btn">Back</a>
        </div>

        <div class="detail-view">
            <div class="detail-row">
                <span class="detail-label">Receipt Number:</span>
                <span class="detail-value">{{ $sale->receipt_number }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $sale->created_at->format('M d, Y H:i:s') }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Cashier:</span>
                <span class="detail-value">{{ $sale->cashier->name ?? 'N/A' }}</span>
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
@endsection
