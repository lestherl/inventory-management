<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

        Route::get('/admin/users', [UserController::class, 'index'])->name('users');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products');
        Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

        Route::get('/stock/transactions', [StockTransactionController::class, 'index'])->name('stock.transactions');
        Route::get('/stock/create', [StockTransactionController::class, 'create'])->name('stock.create');
        Route::post('/stock', [StockTransactionController::class, 'store'])->name('stock.store');
        Route::get('/stock/{stockTransaction}', [StockTransactionController::class, 'show'])->name('stock.show');

        Route::get('/admin/sales', [SalesController::class, 'index'])->name('admin.sales');
        Route::get('/admin/sales/{sale}', [SalesController::class, 'show'])->name('admin.sales.show');
        Route::post('/admin/sales/{sale}/void', [SalesController::class, 'voidSale'])->name('admin.sales.void');

        
        Route::get('/admin/reports', [ReportController::class, 'inventory'])->name('reports');
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');

      
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/stock-in', [ReportController::class, 'stockIn'])->name('reports.stock-in');
        Route::get('/reports/stock-out', [ReportController::class, 'stockOut'])->name('reports.stock-out');
        Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    });

    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('staff.dashboard');


        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::get('/products', [ProductController::class, 'index'])->name('products');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');

        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::get('/staff/stock', [StockTransactionController::class, 'index'])->name('staff.stock');
        Route::get('/staff/stock/create', [StockTransactionController::class, 'create'])->name('staff.stock.create');
        Route::post('/staff/stock', [StockTransactionController::class, 'store'])->name('staff.stock.store');
        Route::get('/staff/stock/{stockTransaction}', [StockTransactionController::class, 'show'])->name('staff.stock.show');
    });

    Route::middleware('role:cashier')->group(function () {
        Route::get('/cashier/dashboard', [DashboardController::class, 'cashierDashboard'])->name('cashier.dashboard');

        Route::get('/cashier/sales', [SalesController::class, 'index'])->name('cashier.sales');
        Route::get('/cashier/sales/create', [SalesController::class, 'create'])->name('cashier.sales.create');
        Route::post('/cashier/sales', [SalesController::class, 'store'])->name('cashier.sales.store');
        Route::get('/cashier/sales/{sale}', [SalesController::class, 'show'])->name('cashier.sales.show');
    });
});
