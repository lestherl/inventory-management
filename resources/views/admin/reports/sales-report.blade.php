@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Sales Report')

@section('content')
    <div class="inventory-table-card">
        <div class="table-topbar">
            <div class="table-title-wrap">
                <h3 class="table-title">Sales Report</h3>
            </div>
        </div>

        <div class="filter-bar">
            <form action="{{ route('reports.sales') }}" method="GET" class="filter-form">
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="voided" {{ request('status') === 'voided' ? 'selected' : '' }}>Voided</option>
                </select>
                <select name="payment_method">
                    <option value="">All Payment Methods</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="gcash" {{ request('payment_method') === 'gcash' ? 'selected' : '' }}>GCash</option>
                    <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                    <option value="other" {{ request('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('reports.sales') }}" class="btn btn-secondary">Clear</a>
            </form>
        </div>

        <div class="report-summary">
            <div class="summary-card">
                <h4>Total Sales</h4>
                <p>₱{{ number_format($totalSales, 2) }}</p>
            </div>
            <div class="summary-card">
                <h4>Total Discount</h4>
                <p>₱{{ number_format($totalDiscount, 2) }}</p>
            </div>
            <div class="summary-card">
                <h4>Total Revenue</h4>
                <p>₱{{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>

        @if ($sales->isEmpty())
            <div class="empty-state">
                No sales found.
            </div>
        @else
            <div class="inventory-table-wrapper">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Cashier</th>
                            <th>Total Amount</th>
                            <th>Discount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td>{{ $sale->receipt_number }}</td>
                                <td>{{ $sale->created_at->format('M d, Y H:i') }}</td>
                                <td>{{ $sale->cashier->name ?? 'N/A' }}</td>
                                <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td>₱{{ number_format($sale->discount_amount, 2) }}</td>
                                <td>{{ ucfirst($sale->payment_method) }}</td>
                                <td>
                                    <span class="badge badge-{{ $sale->status === 'completed' ? 'success' : 'danger' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
