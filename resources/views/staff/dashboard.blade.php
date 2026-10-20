
@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Staff Dashboard')

@section('content')

<div class="dashboard-page">

    <!-- PAGE HEADER -->
    <div class="dashboard-header">
        <div class="greeting-section">
            <h1 class="greeting">Staff Dashboard</h1>
            <p class="greeting-subtitle">
                Monitor inventory, suppliers, and stock activities.
            </p>
        </div>
    </div>


    <!-- STATISTICS -->
    <div class="stats-grid">

        <!-- TOTAL SUPPLIERS -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 7h11v11H3z"/>
                    <path d="M14 10h4l3 4v4h-7"/>
                    <circle cx="7" cy="18" r="2"/>
                    <circle cx="18" cy="18" r="2"/>
                </svg>
            </div>

            <div class="stat-content">
                <h3 class="stat-label">Total Suppliers</h3>
                <p class="stat-value">{{ $totalSuppliers }}</p>
            </div>
        </div>


        <!-- ACTIVE SUPPLIERS -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m8 12 3 3 5-6"/>
                </svg>
            </div>

            <div class="stat-content">
                <h3 class="stat-label">Active Suppliers</h3>
                <p class="stat-value">{{ $activeSuppliers }}</p>
            </div>
        </div>


        <!-- TOTAL PRODUCTS -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m12 2 9 5-9 5-9-5 9-5Z"/>
                    <path d="M3 7v10l9 5 9-5V7"/>
                    <path d="M12 12v10"/>
                </svg>
            </div>

            <div class="stat-content">
                <h3 class="stat-label">Total Products</h3>
                <p class="stat-value">{{ $totalProducts }}</p>
            </div>
        </div>


        <!-- TOTAL STOCK -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 8h18v13H3z"/>
                    <path d="M3 8 7 3h10l4 5"/>
                    <path d="M12 8v13"/>
                </svg>
            </div>

            <div class="stat-content">
                <h3 class="stat-label">Total Stock Units</h3>
                <p class="stat-value">
                    {{ number_format($totalStock) }}
                </p>
            </div>
        </div>


        <!-- LOW STOCK ALERTS -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3 2 21h20L12 3Z"/>
                    <path d="M12 9v5"/>
                    <path d="M12 18h.01"/>
                </svg>
            </div>

            <div class="stat-content">
                <h3 class="stat-label">Low Stock Alerts</h3>
                <p class="stat-value">{{ $lowStockCount }}</p>
            </div>
        </div>


        <!-- TODAY STOCK IN -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 10v11h16V10"/>
                    <path d="M12 3v13"/>
                    <path d="m7 11 5 5 5-5"/>
                </svg>
            </div>

            <div class="stat-content">
                <h3 class="stat-label">Units Stocked In Today</h3>
                <p class="stat-value">
                    {{ number_format($todayStockIn) }}
                </p>
            </div>
        </div>

    </div>


    <!-- DASHBOARD TABLES -->
    <div class="dashboard-sections">

        <!-- RECENT STOCK IN -->
        <div class="dashboard-section">

            <div class="section-header">
                <h3 class="section-title">Recent Stock In</h3>

                <a href="{{ route('staff.stock') }}"
                   class="view-all-link">
                    View All
                </a>
            </div>

            @if ($recentStockIn->isEmpty())

                <div class="empty-state">
                    No recent stock-in transactions.
                </div>

            @else

                <div class="table-wrapper">
                    <table class="data-table">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Supplier</th>
                                <th>Quantity</th>
                                <th>Recorded By</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($recentStockIn as $transaction)
                                <tr>
                                    <td>
                                        {{ $transaction->product->product_name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $transaction->supplier->supplier_name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        <span class="badge badge-success">
                                            +{{ $transaction->quantity }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $transaction->user->name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $transaction->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            @endif

        </div>


        <!-- LOW STOCK PRODUCTS -->
        <div class="dashboard-section">

            <div class="section-header">
                <h3 class="section-title">Low Stock Products</h3>

                <a href="{{ route('products') }}"
                   class="view-all-link">
                    View Products
                </a>
            </div>

            @if ($lowStockProducts->isEmpty())

                <div class="empty-state">
                    No low stock products.
                </div>

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
                            @foreach ($lowStockProducts as $product)

                                <tr>
                                    <td>
                                        {{ $product->product_name }}
                                    </td>

                                    <td>
                                        {{ $product->category->category_name ?? 'N/A' }}
                                    </td>

                                    <td class="{{ $product->current_stock == 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $product->current_stock }}
                                    </td>

                                    <td>
                                        {{ $product->minimum_stock }}
                                    </td>

                                    <td>
                                        @if ($product->current_stock == 0)

                                            <span class="badge badge-danger">
                                                Out of Stock
                                            </span>

                                        @else

                                            <span class="badge badge-warning">
                                                Low Stock
                                            </span>

                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>

                    </table>
                </div>

            @endif

        </div>

    </div>


    <!-- QUICK ACTIONS -->
    <div class="dashboard-section" style="margin-top: 1.5rem;">

        <h3 class="section-title">Quick Actions</h3>

        <div class="staff-quick-actions">

            <a href="{{ route('staff.stock') }}"
               class="quick-action-btn">
                <span>Stock Transactions</span>
            </a>

            <a href="{{ route('suppliers') }}"
               class="quick-action-btn">
                <span>Manage Suppliers</span>
            </a>

            <a href="{{ route('products') }}"
               class="quick-action-btn">
                <span>View Products</span>
            </a>

        </div>
    </div>

</div>

@endsection
