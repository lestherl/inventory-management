@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Low Stock Report')

@section('content')
    <div class="inventory-table-card">
        <div class="table-topbar">
            <div class="table-title-wrap">
                <h3 class="table-title">Low Stock Report</h3>
            </div>
        </div>

        <div class="filter-bar">
            <form action="{{ route('reports.low-stock') }}" method="GET" class="filter-form">
                <select name="category">
                    <option value="">All Categories</option>
                    @foreach (\App\Models\Category::orderBy('category_name')->get() as $category)
                        <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('reports.low-stock') }}" class="btn btn-secondary">Clear</a>
            </form>
        </div>

        @if ($products->isEmpty())
            <div class="empty-state">
                No low stock products. Great job!
            </div>
        @else
            <div class="inventory-table-wrapper">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Minimum Stock</th>
                            <th>Unit</th>
                            <th>Selling Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="low-stock-row">
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                <td class="text-danger">{{ $product->current_stock }}</td>
                                <td>{{ $product->minimum_stock }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>₱{{ number_format($product->selling_price, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $product->current_stock == 0 ? 'danger' : 'warning' }}">
                                        {{ $product->current_stock == 0 ? 'Out of Stock' : 'Low Stock' }}
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
