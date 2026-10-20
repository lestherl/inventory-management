<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'cashier' => redirect()->route('cashier.dashboard'),
            default => abort(403),
        };
    }

    
public function adminDashboard(): View
{
    $totalProducts = Product::count();

    $totalStock = Product::sum('current_stock');

    $lowStockCount = Product::whereColumn(
        'current_stock', '<=', 'minimum_stock'
    )
        ->where('status', 'active')
        ->count();

    $todaySales = Sale::whereDate('created_at', today())
        ->where('status', 'completed')
        ->sum('total_amount');

    $recentTransactions = StockTransaction::with(['product', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    $lowStockProducts = Product::with('category')
        ->whereColumn('current_stock', '<=', 'minimum_stock')
        ->where('status', 'active')
        ->orderBy('current_stock', 'asc')
        ->limit(10)
        ->get();

    // Generate daily, weekly, monthly chart data
    $dashboardCharts = $this->getAdminDashboardCharts();

    return view('admin.dashboard', compact(
        'totalProducts',
        'totalStock',
        'lowStockCount',
        'todaySales',
        'recentTransactions',
        'lowStockProducts',
        'dashboardCharts'
    ));
}

    
private function getAdminDashboardCharts(): array
{
    $now = now();

    // Load records covering the last 12 calendar months.
    $startDate = $now->copy()
        ->startOfMonth()
        ->subMonths(11);

    // Only completed sales count toward the sales chart.
    $sales = Sale::query()
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, $now])
        ->get(['created_at', 'total_amount']);

    // Stock movements recorded by the system.
    $transactions = StockTransaction::query()
        ->whereIn('transaction_type', ['stock_in', 'stock_out'])
        ->whereBetween('created_at', [$startDate, $now])
        ->get(['created_at', 'transaction_type', 'quantity']);

    $chartData = [];

    $periods = [
        'daily' => 7,
        'weekly' => 8,
        'monthly' => 12,
    ];

    foreach ($periods as $period => $numberOfPoints) {

        $labels = [];
        $indexes = [];

        // Build empty chronological time buckets.
        for ($i = $numberOfPoints - 1; $i >= 0; $i--) {

            $date = match ($period) {
                'daily' => $now->copy()
                    ->subDays($i)
                    ->startOfDay(),

                'weekly' => $now->copy()
                    ->startOfWeek(\Carbon\Carbon::MONDAY)
                    ->subWeeks($i),

                'monthly' => $now->copy()
                    ->startOfMonth()
                    ->subMonths($i),
            };

            $key = $date->toDateString();

            $indexes[$key] = count($labels);

            $labels[] = match ($period) {
                'daily' => $date->format('M d'),
                'weekly' => $date->format('M d'),
                'monthly' => $date->format('M Y'),
            };
        }

        $salesValues = array_fill(0, $numberOfPoints, 0);
        $stockInValues = array_fill(0, $numberOfPoints, 0);
        $stockOutValues = array_fill(0, $numberOfPoints, 0);

        // Sum completed sales in the appropriate buckets.
        foreach ($sales as $sale) {

            $date = $sale->created_at->copy();

            $key = match ($period) {
                'daily' => $date->toDateString(),
                'weekly' => $date
                    ->startOfWeek(\Carbon\Carbon::MONDAY)
                    ->toDateString(),
                'monthly' => $date
                    ->startOfMonth()
                    ->toDateString(),
            };

            if (isset($indexes[$key])) {
                $index = $indexes[$key];

                $salesValues[$index] += (float) $sale->total_amount;
            }
        }

        // Sum stock-in and stock-out quantities.
        foreach ($transactions as $transaction) {

            $date = $transaction->created_at->copy();

            $key = match ($period) {
                'daily' => $date->toDateString(),
                'weekly' => $date
                    ->startOfWeek(\Carbon\Carbon::MONDAY)
                    ->toDateString(),
                'monthly' => $date
                    ->startOfMonth()
                    ->toDateString(),
            };

            if (!isset($indexes[$key])) {
                continue;
            }

            $index = $indexes[$key];

            if ($transaction->transaction_type === 'stock_in') {
                $stockInValues[$index] += (int) $transaction->quantity;
            }

            if ($transaction->transaction_type === 'stock_out') {
                $stockOutValues[$index] += (int) $transaction->quantity;
            }
        }

        $chartData[$period] = [
            'labels' => $labels,
            'sales' => array_map(
                fn ($value) => round($value, 2),
                $salesValues
            ),
            'stockIn' => $stockInValues,
            'stockOut' => $stockOutValues,
        ];
    }

    // Inventory Health: only active products.
    $chartData['inventory'] = [
        'inStock' => Product::query()
            ->where('status', 'active')
            ->whereColumn('current_stock', '>', 'minimum_stock')
            ->count(),

        'lowStock' => Product::query()
            ->where('status', 'active')
            ->where('current_stock', '>', 0)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count(),

        'outOfStock' => Product::query()
            ->where('status', 'active')
            ->where('current_stock', '<=', 0)
            ->count(),
    ];

    return $chartData;
}


   
public function staffDashboard(): View
{
    // SUPPLIERS
    $totalSuppliers = Supplier::count();

    $activeSuppliers = Supplier::where('status', 'active')->count();

    // PRODUCTS
    $totalProducts = Product::count();

    $totalStock = Product::sum('current_stock');

    // LOW STOCK
    $lowStockCount = Product::where('status', 'active')
        ->whereColumn('current_stock', '<=', 'minimum_stock')
        ->count();

    $lowStockProducts = Product::with('category')
        ->where('status', 'active')
        ->whereColumn('current_stock', '<=', 'minimum_stock')
        ->orderBy('current_stock', 'asc')
        ->limit(5)
        ->get();

    // STOCK IN TODAY
    $todayStockIn = StockTransaction::where('transaction_type', 'stock_in')
        ->whereDate('created_at', today())
        ->sum('quantity');

    // RECENT STOCK IN
    $recentStockIn = StockTransaction::with([
            'product',
            'supplier',
            'user'
        ])
        ->where('transaction_type', 'stock_in')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    return view('staff.dashboard', compact(
        'totalSuppliers',
        'activeSuppliers',
        'totalProducts',
        'totalStock',
        'lowStockCount',
        'lowStockProducts',
        'todayStockIn',
        'recentStockIn'
    ));
}


    public function cashierDashboard(): View
    {
        $todaySales = Sale::whereDate('created_at', today())
            ->where('cashier_id', auth()->id())
            ->where('status', 'completed')
            ->count();

        $todayRevenue = Sale::whereDate('created_at', today())
            ->where('cashier_id', auth()->id())
            ->where('status', 'completed')
            ->sum('total_amount');

        $transactions = Sale::whereDate('created_at', today())
            ->where('cashier_id', auth()->id())
            ->where('status', 'completed')
            ->count();

        $recentSales = Sale::with(['saleItems.product'])
            ->where('cashier_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('cashier.dashboard', compact(
            'todaySales',
            'todayRevenue',
            'transactions',
            'recentSales'
        ));
    }
}
