@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Stock Transaction Details')

@section('content')
    <div class="form-card">
        <div class="form-header">
            <h3 class="form-title">Stock Transaction Details</h3>
            <a href="{{ route('staff.stock') }}" class="back-btn">Back</a>
        </div>

        <div class="detail-view">
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $stockTransaction->created_at->format('M d, Y H:i:s') }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Product:</span>
                <span class="detail-value">{{ $stockTransaction->product->product_name ?? 'N/A' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Transaction Type:</span>
                <span class="detail-value">
                    <span class="badge badge-{{ $stockTransaction->transaction_type === 'stock_in' ? 'success' : 'danger' }}">
                        {{ ucfirst(str_replace('_', ' ', $stockTransaction->transaction_type)) }}
                    </span>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Quantity:</span>
                <span class="detail-value">{{ $stockTransaction->quantity }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Stock Before:</span>
                <span class="detail-value">{{ $stockTransaction->stock_before }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Stock After:</span>
                <span class="detail-value">{{ $stockTransaction->stock_after }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Supplier:</span>
                <span class="detail-value">{{ $stockTransaction->supplier->supplier_name ?? 'N/A' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Reference:</span>
                <span class="detail-value">{{ $stockTransaction->reference ?? 'N/A' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Processed By:</span>
                <span class="detail-value">{{ $stockTransaction->user->name ?? 'N/A' }}</span>
            </div>

            @if ($stockTransaction->notes)
                <div class="detail-row">
                    <span class="detail-label">Notes:</span>
                    <span class="detail-value">{{ $stockTransaction->notes }}</span>
                </div>
            @endif
        </div>
    </div>
@endsection
