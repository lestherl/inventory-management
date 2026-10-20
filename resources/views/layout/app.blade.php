<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite(['resources/css/navbar.css'])
    @yield('style')
</head>
<body>
    @auth
        @php
            $currentRoute = request()->route()?->getName();
            $defaultPageTitle = match ($currentRoute) {
                'dashboard', 'admin.dashboard', 'staff.dashboard', 'cashier.dashboard' => 'Dashboard',
                'admin.products', 'admin.products.create', 'admin.products.edit', 'products', 'products.create', 'products.edit' => 'Products',
                'categories', 'categories.create', 'categories.edit' => 'Categories',
                'users', 'users.create', 'users.edit' => 'Users',
                'stock.transactions', 'stock.create', 'stock.show' => 'Stock Transactions',
                'staff.stock', 'staff.stock.create' => 'Stock In',
                'suppliers', 'suppliers.create', 'suppliers.edit' => 'Suppliers',
                'admin.sales', 'admin.sales.show' => 'Sales',
                'cashier.sales', 'cashier.sales.create', 'cashier.sales.show' => 'Sales',
                'reports.inventory', 'reports.stock-in', 'reports.stock-out', 'reports.low-stock', 'reports.sales' => 'Reports',
                default => 'Dashboard',
            };
        @endphp

        <div class="admin-layout">
            @include('partials.navbar')

            <main class="main-panel">
                <header class="top-header">
                    <h1 class="page-heading">@yield('page-title', $defaultPageTitle)</h1>

                    <div class="user-profile">
                        <div class="user-avatar" aria-label="User profile">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>

                        <div class="user-meta">
                            <span class="username">{{ auth()->user()->name }}</span>
                            <span class="role-badge">{{ ucfirst(auth()->user()->role ?? 'user') }}</span>
                        </div>
                    </div>
                </header>

                <div class="page-content">
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        <div class="guest-page">
            @yield('content')
        </div>
    @endauth
</body>
</html>