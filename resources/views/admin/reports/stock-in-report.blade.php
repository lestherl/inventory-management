@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Stock In Report')

@section('content')
    <div class="inventory-table-card">
        <div class="table-topbar">
            <div class="table-title-wrap">
                <h3 class="table-title">Stock In Report</h3>
            </div>
        </div>

        <div class="filter-bar">
            <form action="{{ route('reports.stock-in') }}" method="GET" class="filter-form">
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                <select name="product">
                    <option value="">All Products</option>
                    @foreach (\App\Models\Product::orderBy('product_name')->get() as $product)
                        <option value="{{ $product->product_id }}" {{ request('product') == $product->product_id ? 'selected' : '' }}>
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('reports.stock-in') }}" class="btn btn-secondary">Clear</a>
            </form>
        </div>

        <div class="report-summary">
            <div class="summary-card">
                <h4>Total Quantity</h4>
                <p>{{ $totalQuantity }}</p>
            </div>
        </div>

        @if ($transactions->isEmpty())
            <div class="empty-state">
                No stock in transactions found.
            </div>
        @else
            <div class="inventory-table-wrapper">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                            <th>Stock Before</th>
                            <th>Stock After</th>
                            <th>User</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                <td>{{ $transaction->product->product_name ?? 'N/A' }}</td>
                                <td>{{ $transaction->supplier->supplier_name ?? 'N/A' }}</td>
                                <td>{{ $transaction->quantity }}</td>
                                <td>{{ $transaction->stock_before }}</td>
                                <td>{{ $transaction->stock_after }}</td>
                                <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->reference ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
