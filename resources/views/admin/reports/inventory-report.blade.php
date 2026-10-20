
@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])

    <style>
        .report-page {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .report-header h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 5px;
        }

        .report-header p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }

        .report-print-btn {
            background: #0f766e;
            border: none;
            color: white;
            border-radius: 8px;
            padding: 11px 18px;
            font-weight: 600;
            cursor: pointer;
        }

        .report-print-btn:hover {
            background: #115e59;
        }

        .report-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .report-stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
        }

        .report-stat-card span {
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
        }

        .report-stat-card h3 {
            margin: 12px 0 4px;
            font-size: 25px;
            font-weight: 700;
            color: #0f172a;
        }

        .report-stat-card small {
            color: #94a3b8;
            font-size: 12px;
        }

        .report-panel {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .report-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 20px;
            flex-wrap: wrap;
        }

        .report-panel-header h3 {
            font-size: 17px;
            font-weight: 700;
            margin: 0;
        }

        .report-panel-header p {
            font-size: 13px;
            color: #64748b;
            margin: 5px 0 0;
        }

        .report-filters {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            padding: 0 20px 20px;
        }

        .report-filters input,
        .report-filters select {
            min-height: 41px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13px;
            background: white;
            color: #334155;
        }

        .report-filters input {
            flex: 1;
            min-width: 180px;
        }

        .report-filters select {
            min-width: 150px;
        }

        .report-filter-btn {
            background: #0f766e;
            color: white;
            border: none;
            padding: 11px 17px;
            border-radius: 8px;
            cursor: pointer;
        }

        .report-clear-btn {
            background: #f1f5f9;
            color: #334155;
            text-decoration: none;
            padding: 11px 17px;
            border-radius: 8px;
            font-size: 13px;
        }

        .report-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            white-space: nowrap;
            font-size: 13px;
        }

        .report-table th {
            background: #f8fafc;
            color: #475569;
            padding: 15px 18px;
            font-weight: 600;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .report-table td {
            padding: 15px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .report-table tbody tr:hover {
            background: #f8fafc;
        }

        .report-product-name {
            font-weight: 600;
            color: #0f172a;
        }

        .report-badge {
            display: inline-flex;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .report-badge.success {
            color: #15803d;
            background: #dcfce7;
        }

        .report-badge.warning {
            color: #a16207;
            background: #fef9c3;
        }

        .report-badge.danger {
            color: #b91c1c;
            background: #fee2e2;
        }

        .report-badge.secondary {
            color: #475569;
            background: #e2e8f0;
        }

        .report-alert-section {
            padding: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .report-alert-section h4 {
            margin: 0 0 15px;
            font-size: 16px;
            font-weight: 700;
        }

        .report-alert-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .report-alert-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border: 1px solid #fde68a;
            border-radius: 8px;
            background: #fffbeb;
        }

        .report-alert-item strong {
            font-size: 13px;
            color: #92400e;
        }

        .report-alert-item span {
            font-size: 12px;
            color: #b45309;
        }

        .report-empty {
            padding: 35px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        @media (max-width: 1000px) {
            .report-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .report-stats {
                grid-template-columns: 1fr;
            }

            .report-filters input,
            .report-filters select,
            .report-filter-btn,
            .report-clear-btn {
                width: 100%;
            }
        }

        @media print {
            .report-print-btn,
            .report-filters,
            .report-clear-btn,
            aside,
            nav {
                display: none !important;
            }

            .report-page {
                gap: 15px;
            }

            .report-panel {
                border: none;
            }

            .report-table {
                white-space: normal;
            }

            body {
                background: white !important;
            }
        }
    </style>
@endsection

@section('title', 'Reports')

@section('content')

@php
    $totalProducts = $products->count();
    $totalStock = $products->sum('current_stock');

    $inventoryValue = $products->sum(function ($product) {
        return $product->cost_price * $product->current_stock;
    });

    $totalLowStock = $lowStockProducts->count();
@endphp

<div class="report-page">

    {{-- PAGE HEADER --}}
    <div class="report-header">
        <div>
            <h2>Inventory Report</h2>
            <p>
                Monitor product inventory, stock levels,
                and inventory value.
            </p>
        </div>

        <button type="button"
                class="report-print-btn"
                onclick="window.print()">
            Print Report
        </button>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="report-stats">

        <div class="report-stat-card">
            <span>Total Products</span>
            <h3>{{ number_format($totalProducts) }}</h3>
            <small>Products in current results</small>
        </div>

        <div class="report-stat-card">
            <span>Total Stock Quantity</span>
            <h3>{{ number_format($totalStock) }}</h3>
            <small>Total available units</small>
        </div>

        <div class="report-stat-card">
            <span>Low Stock Products</span>
            <h3>{{ number_format($totalLowStock) }}</h3>
            <small>Products needing attention</small>
        </div>

        <div class="report-stat-card">
            <span>Total Inventory Value</span>
            <h3>₱{{ number_format($inventoryValue, 2) }}</h3>
            <small>Based on product cost price</small>
        </div>

    </div>

    {{-- INVENTORY REPORT --}}
    <div class="report-panel">

        <div class="report-panel-header">
            <div>
                <h3>Product Inventory</h3>
                <p>
                    View current stock information
                    for all products.
                </p>
            </div>
        </div>

        {{-- FILTERS --}}
        <form action="{{ route('reports.inventory') }}"
              method="GET"
              class="report-filters">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search product or barcode...">

            <select name="category">
                <option value="">All Categories</option>

                @foreach ($categories as $category)
                    <option
                        value="{{ $category->category_id }}"
                        {{ request('category') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>

            <select name="status">
                <option value="">All Status</option>

                <option value="active"
                    {{ request('status') === 'active' ? 'selected' : '' }}>
                    Active
                </option>

                <option value="inactive"
                    {{ request('status') === 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>

            <button type="submit" class="report-filter-btn">
                Filter
            </button>

            <a href="{{ route('reports.inventory') }}"
               class="report-clear-btn">
                Clear
            </a>

        </form>

        {{-- INVENTORY TABLE --}}
        @if ($products->isEmpty())

            <div class="report-empty">
                No inventory products found.
            </div>

        @else

            <div class="report-table-wrapper">
                <table class="report-table">

                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Barcode</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Current Stock</th>
                            <th>Minimum Stock</th>
                            <th>Stock Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($products as $product)

                            <tr>
                                <td>
                                    <span class="report-product-name">
                                        {{ $product->product_name }}
                                    </span>
                                </td>

                                <td>
                                    {{ $product->category->category_name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $product->barcode ?? 'N/A' }}
                                </td>

                                <td>
                                    ₱{{ number_format($product->cost_price, 2) }}
                                </td>

                                <td>
                                    ₱{{ number_format($product->selling_price, 2) }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $product->current_stock }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $product->minimum_stock }}
                                </td>

                                <td>
                                    @if ($product->current_stock == 0)

                                        <span class="report-badge danger">
                                            Out of Stock
                                        </span>

                                    @elseif ($product->isLowStock())

                                        <span class="report-badge warning">
                                            Low Stock
                                        </span>

                                    @else

                                        <span class="report-badge success">
                                            In Stock
                                        </span>

                                    @endif
                                </td>

                                <td>
                                    <span class="report-badge {{ $product->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                </table>
            </div>

        @endif

        {{-- LOW STOCK ALERTS --}}
        <div class="report-alert-section">

            <h4>Low Stock Alerts</h4>

            @if ($lowStockProducts->isEmpty())

                <p style="color: #64748b; font-size: 13px;">
                    No low stock products found.
                </p>

            @else

                <div class="report-alert-list">

                    @foreach ($lowStockProducts as $product)

                        <div class="report-alert-item">

                            <strong>
                                {{ $product->product_name }}
                            </strong>

                            <span>
                                {{ $product->current_stock }}
                                /
                                {{ $product->minimum_stock }}
                                units
                            </span>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

    </div>

</div>

@endsection
