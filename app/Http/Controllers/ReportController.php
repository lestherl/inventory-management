<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Category;

class ReportController extends Controller
{
    
public function inventory(Request $request): View
{
    $request->validate([
        'search' => ['nullable', 'string', 'max:100'],
        'category' => ['nullable', 'integer', 'exists:categories,category_id'],
        'status' => ['nullable', 'in:active,inactive'],
    ]);

    $query = Product::with('category');

    // Search by product name or barcode
    if ($request->filled('search')) {
        $search = trim($request->input('search'));

        $query->where(function ($q) use ($search) {
            $q->where('product_name', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    // Filter by category
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // Filter by product status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Get inventory products
    $products = $query
        ->orderBy('product_name')
        ->get();

    // Get active low-stock products within filtered results
    $lowStockProducts = $products->filter(function ($product) {
        return $product->status === 'active'
            && $product->current_stock <= $product->minimum_stock;
    })->values();

    // Get categories for dropdown
    $categories = Category::orderBy('category_name')->get();

    // Inventory summary
    $totalProducts = $products->count();

    $totalStock = $products->sum('current_stock');

    $totalInventoryValue = $products->sum(function ($product) {
        return $product->cost_price * $product->current_stock;
    });

    $totalLowStock = $lowStockProducts->count();

    return view('admin.reports.inventory-report', compact(
        'products',
        'lowStockProducts',
        'categories',
        'totalProducts',
        'totalStock',
        'totalInventoryValue',
        'totalLowStock'
    ));
}


    public function stockIn(Request $request): View
    {
        $query = StockTransaction::with(['product', 'supplier', 'user'])
            ->where('transaction_type', 'stock_in');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $totalQuantity = $transactions->sum('quantity');

        return view('admin.reports.stock-in-report', compact('transactions', 'totalQuantity'));
    }

    public function stockOut(Request $request): View
    {
        $query = StockTransaction::with(['product', 'user'])
            ->where('transaction_type', 'stock_out');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $totalQuantity = $transactions->sum('quantity');

        return view('admin.reports.stock-out-report', compact('transactions', 'totalQuantity'));
    }

    public function lowStock(Request $request): View
    {
        $query = Product::with('category')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('status', 'active');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->orderBy('current_stock', 'asc')->get();

        return view('admin.reports.low-stock-report', compact('products'));
    }

    public function sales(Request $request): View
    {
        $query = Sale::with(['cashier', 'saleItems.product']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();
        $totalSales = $sales->where('status', 'completed')->sum('total_amount');
        $totalDiscount = $sales->where('status', 'completed')->sum('discount_amount');
        $totalRevenue = $totalSales - $totalDiscount;

        $salesByPaymentMethod = $sales->where('status', 'completed')
            ->groupBy('payment_method')
            ->map(function ($items) {
                return $items->sum('total_amount');
            });

        return view('admin.reports.sales-report', compact(
            'sales',
            'totalSales',
            'totalDiscount',
            'totalRevenue',
            'salesByPaymentMethod'
        ));
    }
}
