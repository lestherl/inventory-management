@extends('layout.app')

@section('style')
    @vite([
        'resources/css/dashboard.css',
        'resources/js/admin-dashboard-charts.js'
    ])
@endsection


@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-page">
        
         <div class="dashboard-header">
         <div class="greeting-section">
            <h1 class="greeting">Admin Dashboard</h1>
            <p class="greeting-subtitle">
                Monitor inventory, suppliers, and stock activities.
            </p>
        </div>   
    </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                        <path d="M7 7h10M7 11h10M7 15h6"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Total Products</h3>
                    <p class="stat-value">{{ $totalProducts }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                        <path d="M7 7h10M7 11h10M7 15h6"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Total Stock</h3>
                    <p class="stat-value">{{ $totalStock }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-orange">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 9v4M12 17h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Low Stock Alerts</h3>
                    <p class="stat-value">{{ $lowStockCount }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Today's Sales</h3>
                    <p class="stat-value">₱{{ number_format($todaySales, 2) }}</p>
                </div>
            </div>
        </div>
        
{{-- DASHBOARD ANALYTICS --}}
<div class="analytics-section">

    <div class="analytics-header">
        <div>
            <h3>Dashboard Analytics</h3>
            <p>
                Analyze sales performance and inventory movement.
            </p>
        </div>

        <div class="analytics-period-switch" role="group"
             aria-label="Chart time period">

            <button type="button"
                    class="analytics-period-btn"
                    data-dashboard-period="daily"
                    aria-pressed="false">
                Daily
            </button>

            <button type="button"
                    class="analytics-period-btn"
                    data-dashboard-period="weekly"
                    aria-pressed="false">
                Weekly
            </button>

            <button type="button"
                    class="analytics-period-btn active"
                    data-dashboard-period="monthly"
                    aria-pressed="true">
                Monthly
            </button>
        </div>
    </div>

    <div class="analytics-grid">

        {{-- SALES TREND --}}
        <div class="analytics-card">
            <div class="analytics-card-header">
                <div>
                    <h4>Sales Performance</h4>
                    <p>
                        Completed sales over the selected period
                    </p>
                </div>
                <span class="analytics-chart-label">Sales</span>
            </div>

            <div class="analytics-chart-area">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        {{-- STOCK MOVEMENT --}}
        <div class="analytics-card">
            <div class="analytics-card-header">
                <div>
                    <h4>Stock Movement</h4>
                    <p>
                        Compare stock-in and stock-out quantities
                    </p>
                </div>
                <span class="analytics-chart-label">Units</span>
            </div>

            <div class="analytics-chart-area">
                <canvas id="stockMovementChart"></canvas>
            </div>
        </div>

        {{-- INVENTORY HEALTH --}}
        <div class="analytics-card analytics-card-wide">
            <div class="analytics-card-header">
                <div>
                    <h4>Inventory Health</h4>
                    <p>
                        Current stock condition of active products
                    </p>
                </div>
                <span class="analytics-chart-label">Products</span>
            </div>

            <div class="analytics-inventory-layout">

                <div class="analytics-doughnut-area">
                    <canvas id="inventoryHealthChart"></canvas>
                </div>

                <div class="analytics-inventory-legend">

                    <div class="analytics-legend-row">
                        <span class="analytics-legend-name">
                            <i class="analytics-dot dot-green"></i>
                            In Stock
                        </span>
                        <strong>Above minimum</strong>
                    </div>

                    <div class="analytics-legend-row">
                        <span class="analytics-legend-name">
                            <i class="analytics-dot dot-orange"></i>
                            Low Stock
                        </span>
                        <strong>At or below minimum</strong>
                    </div>

                    <div class="analytics-legend-row">
                        <span class="analytics-legend-name">
                            <i class="analytics-dot dot-red"></i>
                            Out of Stock
                        </span>
                        <strong>Zero available</strong>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

{{-- Data generated by DashboardController --}}
<script type="application/json" id="admin-dashboard-chart-data">
    @json($dashboardCharts)
</script>


        <div class="dashboard-sections">
            <div class="dashboard-section">
                <h3 class="section-title">Low Stock Products</h3>
                @if ($lowStockProducts->isEmpty())
                    <div class="empty-state">No low stock products.</div>
                @else
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Minimum Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lowStockProducts->take(5) as $product)
                                    <tr>
                                        <td>{{ $product->product_name }}</td>
                                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                        <td class="{{ $product->current_stock == 0 ? 'text-danger' : 'text-warning' }}">
                                            {{ $product->current_stock }}
                                        </td>
                                        <td>{{ $product->minimum_stock }}</td>
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

            <div class="dashboard-section">
                <h3 class="section-title">Recent Transactions</h3>
                @if ($recentTransactions->isEmpty())
                    <div class="empty-state">No recent transactions.</div>
                @else
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>User</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentTransactions->take(5) as $transaction)
                                    <tr>
                                        <td>{{ $transaction->product->product_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $transaction->transaction_type === 'stock_in' ? 'success' : 'danger' }}">
                                                {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->quantity }}</td>
                                        <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
