@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Sales')

@section('content')
    <div class="inventory-table-card">
        <div class="table-topbar">
            <div class="table-title-wrap">
                <h3 class="table-title">Sales</h3>
            </div>
        </div>

        <div class="filter-bar">
            <form action="{{ route('admin.sales') }}" method="GET" class="filter-form">
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="voided" {{ request('status') === 'voided' ? 'selected' : '' }}>Voided</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('admin.sales') }}" class="btn btn-secondary">Clear</a>
            </form>
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
                            <th>Actions</th>
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
                                <td>
                                    <div class="inventory-actions">
                                        <a href="{{ route('admin.sales.show', $sale) }}" class="inventory-action" aria-label="View sale">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M3 7.5A2.5 2.5 0 0 1 5.5 5h11A2.5 2.5 0 0 1 19 7.5v9A2.5 2.5 0 0 1 16.5 19h-11A2.5 2.5 0 0 1 3 16.5v-9Z"/>
                                                <path d="M7 9h7M7 12h10M7 15h8"/>
                                            </svg>
                                        </a>
                                        @if ($sale->status === 'completed')
                                            <form action="{{ route('admin.sales.void', $sale) }}" method="POST" class="inline-form">
                                                @csrf
                                                <button type="submit" class="inventory-action text-warning" aria-label="Void sale" onclick="return confirm('Are you sure you want to void this sale?')">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
