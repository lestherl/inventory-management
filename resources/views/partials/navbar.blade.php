
<aside class="sidebar-nav">
    <div class="brand-box">
        <div class="brand-mark">
            <span class="brand-mark-inner"></span>
        </div>
        <div class="brand-text">
            <span>Inventory</span>
            <span>System</span>
        </div>
    </div>

    <nav class="side-menu" aria-label="Main navigation">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs(['dashboard', 'admin.dashboard', 'staff.dashboard', 'cashier.dashboard']) ? 'active' : '' }}">
            <span class="nav-icon">⌂</span>
            <span class="nav-label">Dashboard</span>
        </a>

        @if (auth()->user()->role === 'admin')
            <a href="{{ route('admin.products') }}" class="nav-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <span class="nav-icon">▣</span>
                <span class="nav-label">Products</span>
            </a>
            <a href="{{ route('categories') }}" class="nav-item {{ request()->routeIs('categories*') ? 'active' : '' }}">
                <span class="nav-icon">◌</span>
                <span class="nav-label">Categories</span>
            </a>
            <a href="{{ route('stock.transactions') }}" class="nav-item {{ request()->routeIs('stock*') ? 'active' : '' }}">
                <span class="nav-icon">⇅</span>
                <span class="nav-label">Stock</span>
            </a>
            <a href="{{ route('admin.sales') }}" class="nav-item {{ request()->routeIs('admin.sales*') ? 'active' : '' }}">
                <span class="nav-icon">⌖</span>
                <span class="nav-label">Sales</span>
            </a>
            <a href="{{ route('users') }}" class="nav-item {{ request()->routeIs('users*') ? 'active' : '' }}">
                <span class="nav-icon">◫</span>
                <span class="nav-label">Users</span>
            </a>
            <a href="{{ route('reports.inventory') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <span class="nav-icon">↗</span>
                <span class="nav-label">Reports</span>
            </a>
        @elseif (auth()->user()->role === 'staff')
            <a href="{{ route('products') }}" class="nav-item {{ request()->routeIs('products') ? 'active' : '' }}">
                <span class="nav-icon">▣</span>
                <span class="nav-label">Products</span>
            </a>
            <a href="{{ route('suppliers') }}" class="nav-item {{ request()->routeIs('suppliers*') ? 'active' : '' }}">
                <span class="nav-icon">☐</span>
                <span class="nav-label">Suppliers</span>
            </a>
            <a href="{{ route('staff.stock') }}" class="nav-item {{ request()->routeIs('staff.stock*') ? 'active' : '' }}">
                <span class="nav-icon">⇅</span>
                <span class="nav-label">Stock In</span>
            </a>
        @elseif (auth()->user()->role === 'cashier')
            <a href="{{ route('cashier.sales') }}" class="nav-item {{ request()->routeIs('cashier.sales*') ? 'active' : '' }}">
                <span class="nav-icon">⌖</span>
                <span class="nav-label">Sales</span>
            </a>
        @endif
    </nav>

    <div class="menu-separator"></div>

    <nav class="side-menu secondary-menu" aria-label="Secondary navigation">
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="nav-item logout-item">
                <span class="nav-icon">⇠</span>
                <span>Logout</span>
            </button>
        </form>
    </nav>
</aside>