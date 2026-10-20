@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Stock Transactions')

@section('content')
    <div class="inventory-table-card">
        <div class="table-topbar">
            <div class="table-title-wrap">
                <h3 class="table-title">Stock Transactions</h3>
            </div>
            <a href="{{ route('stock.create') }}" class="add-table-btn">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Add Transaction
            </a>
        </div>

        <div class="filter-bar">
            <form action="{{ route('stock.transactions') }}" method="GET" class="filter-form">
                <select name="transaction_type">
                    <option value="">All Types</option>
                    <option value="stock_in" {{ request('transaction_type') === 'stock_in' ? 'selected' : '' }}>Stock In</option>
                    <option value="stock_out" {{ request('transaction_type') === 'stock_out' ? 'selected' : '' }}>Stock Out</option>
                </select>
                <select name="product">
                    <option value="">All Products</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->product_id }}" {{ request('product') == $product->product_id ? 'selected' : '' }}>
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('stock.transactions') }}" class="btn btn-secondary">Clear</a>
            </form>
        </div>

        @if ($transactions->isEmpty())
            <div class="empty-state">
                No transactions found.
            </div>
        @else
            <div class="inventory-table-wrapper">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Stock Before</th>
                            <th>Stock After</th>
                            <th>Supplier</th>
                            <th>User</th>
                            <th>Reference</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                <td>{{ $transaction->product->product_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $transaction->transaction_type === 'stock_in' ? 'success' : 'danger' }}">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->quantity }}</td>
                                <td>{{ $transaction->stock_before }}</td>
                                <td>{{ $transaction->stock_after }}</td>
                                <td>{{ $transaction->supplier->supplier_name ?? 'N/A' }}</td>
                                <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->reference ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('stock.show', $transaction) }}" class="inventory-action" aria-label="View details">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M3 7.5A2.5 2.5 0 0 1 5.5 5h11A2.5 2.5 0 0 1 19 7.5v9A2.5 2.5 0 0 1 16.5 19h-11A2.5 2.5 0 0 1 3 16.5v-9Z"/>
                                            <path d="M7 9h7M7 12h10M7 15h8"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
