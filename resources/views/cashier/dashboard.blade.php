@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Cashier Dashboard')

@section('content')
    <div class="dashboard-page">
        <div class="dashboard-header">
            <div class="greeting-section">
                <h1 class="greeting">Good morning, {{ auth()->user()->name }}</h1>
                <p class="greeting-subtitle">Welcome back! Here's your sales overview for today.</p>
            </div>
            <a href="{{ route('cashier.sales.create') }}" class="new-sale-btn">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                 New Sale
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                        <path d="M7 7h10M7 11h10M7 15h6"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Today's Sales</h3>
                    <p class="stat-value">{{ $todaySales }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Today's Revenue</h3>
                    <p class="stat-value">₱{{ number_format($todayRevenue, 2) }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3 class="stat-label">Transactions</h3>
                    <p class="stat-value">{{ $transactions }}</p>
                </div>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="dashboard-section main-section">
                <div class="section-header">
                    <h3 class="section-title">Recent Sales</h3>
                    <a href="{{ route('cashier.sales') }}" class="view-all-link">View all sales</a>
                </div>

                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="Search by receipt number...">
                </div>

                @if ($recentSales->isEmpty())
                    <div class="empty-state">No recent sales.</div>
                @else
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Total Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->receipt_number }}</td>
                                        <td>₱{{ number_format($sale->total_amount, 2) }}</td>
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

            <div class="dashboard-section quick-actions-section">
                <h3 class="section-title">Quick Actions</h3>
                <div class="quick-actions">
                    <a href="{{ route('cashier.sales.create') }}" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Create New Sale
                    </a>
                    <a href="{{ route('cashier.sales') }}" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 7.5A2.5 2.5 0 0 1 5.5 5h11A2.5 2.5 0 0 1 19 7.5v9A2.5 2.5 0 0 1 16.5 19h-11A2.5 2.5 0 0 1 3 16.5v-9Z"/>
                            <path d="M7 9h7M7 12h10M7 15h8"/>
                        </svg>
                        View Sales History
                    </a>
                    <a href="{{ route('cashier.sales') }}" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                        View Receipt
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
