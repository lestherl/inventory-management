@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Reports')

@section('content')
    <div class="dashboard-page">
        <h3 class="section-title">Reports</h3>

        <div class="stats-grid">
            <a href="{{ route('reports.inventory') }}" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon stat-icon-blue">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                        <path d="M7 7h10M7 11h10M7 15h6"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Inventory Report</h3>
                    <p class="stat-value">View all products</p>
                </div>
            </a>

            <a href="{{ route('reports.stock-in') }}" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon stat-icon-green">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Stock In Report</h3>
                    <p class="stat-value">View stock additions</p>
                </div>
            </a>

            <a href="{{ route('reports.stock-out') }}" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon stat-icon-orange">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Stock Out Report</h3>
                    <p class="stat-value">View stock removals</p>
                </div>
            </a>

            <a href="{{ route('reports.low-stock') }}" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon stat-icon-purple">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 9v4M12 17h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Low Stock Report</h3>
                    <p class="stat-value">View low stock items</p>
                </div>
            </a>

            <a href="{{ route('reports.sales') }}" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon stat-icon-purple">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Sales Report</h3>
                    <p class="stat-value">View sales data</p>
                </div>
            </a>
        </div>
    </div>
@endsection
