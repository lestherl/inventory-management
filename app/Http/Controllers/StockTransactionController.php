<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockTransaction::with(['product', 'supplier', 'user']);

        if (auth()->user()->role === 'staff') {
            $query->where('user_id', auth()->id())->where('transaction_type', 'stock_in');
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $products = Product::orderBy('product_name')->get();

        $view = auth()->user()->role === 'staff' ? 'staff.stock.stock-transaction-table' : 'admin.stock.stock-transaction-table';

        return view($view, compact('transactions', 'products'));
    }

    public function create(): View
    {
        $products = Product::with('category')->where('status', 'active')->orderBy('product_name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('supplier_name')->get();
        $categories = \App\Models\Category::orderBy('category_name')->get();

        $view = auth()->user()->role === 'staff' ? 'staff.stock.stock-create' : 'admin.stock.stock-create';

        return view($view, compact('products', 'suppliers', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->has('stock_items')) {
            $rules = [
                'stock_items' => ['required', 'string'],
                'supplier_id' => ['nullable', 'exists:suppliers,id'],
                'reference' => ['nullable', 'string', 'max:100'],
                'notes' => ['nullable', 'string'],
            ];

            if (auth()->user()->role === 'admin') {
                $rules['transaction_type'] = ['required', 'in:stock_in,stock_out'];
            } else {
                $request->merge(['transaction_type' => 'stock_in']);
            }

            $validated = $request->validate($rules);

            $items = json_decode($validated['stock_items'], true);
            $transactionType = $validated['transaction_type'] ?? 'stock_in';

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $stockBefore = $product->current_stock;
                $quantity = $item['quantity'];

                if ($transactionType === 'stock_out' && $stockBefore < $quantity) {
                    return back()->withInput()->withErrors(['error' => "Insufficient stock for product: {$product->product_name}"]);
                }

                $stockAfter = $transactionType === 'stock_in'
                    ? $stockBefore + $quantity
                    : $stockBefore - $quantity;

                StockTransaction::create([
                    'product_id' => $product->product_id,
                    'supplier_id' => $validated['supplier_id'] ?? null,
                    'user_id' => $request->user()->id,
                    'transaction_type' => $transactionType,
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => $validated['reference'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);

                $product->update(['current_stock' => $stockAfter]);
            }

            $message = $transactionType === 'stock_in'
                ? 'Stock added successfully.'
                : 'Stock removed successfully.';

            $redirectRoute = auth()->user()->role === 'staff' ? 'staff.stock' : 'stock.transactions';

            return redirect()->route($redirectRoute)->with('success', $message);
        }

        $rules = [
            'product_id' => ['required', 'exists:products,product_id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];

        if (auth()->user()->role === 'admin') {
            $rules['transaction_type'] = ['required', 'in:stock_in,stock_out'];
        } else {
            $request->merge(['transaction_type' => 'stock_in']);
        }

        $validated = $request->validate($rules);

        $product = Product::findOrFail($validated['product_id']);
        $stockBefore = $product->current_stock;
        $transactionType = $validated['transaction_type'] ?? 'stock_in';
        $quantity = $validated['quantity'];

        if ($transactionType === 'stock_out' && $stockBefore < $quantity) {
            return back()->withInput()->withErrors(['quantity' => 'Insufficient stock for stock out operation.']);
        }

        $stockAfter = $transactionType === 'stock_in'
            ? $stockBefore + $quantity
            : $stockBefore - $quantity;

        $validated['user_id'] = $request->user()->id;
        $validated['transaction_type'] = $transactionType;
        $validated['stock_before'] = $stockBefore;
        $validated['stock_after'] = $stockAfter;

        StockTransaction::create($validated);

        $product->update(['current_stock' => $stockAfter]);

        $message = $transactionType === 'stock_in'
            ? 'Stock added successfully.'
            : 'Stock removed successfully.';

        $redirectRoute = auth()->user()->role === 'staff' ? 'staff.stock' : 'stock.transactions';

        return redirect()->route($redirectRoute)->with('success', $message);
    }

    public function show(StockTransaction $stockTransaction): View
    {
        $stockTransaction->load(['product', 'supplier', 'user']);

        $view = auth()->user()->role === 'staff' ? 'staff.stock.stock-show' : 'admin.stock.stock-show';

        return view($view, compact('stockTransaction'));
    }
}
