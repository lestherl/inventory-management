<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sale::with(['cashier', 'saleItems.product']);

        if (auth()->user()->role === 'cashier') {
            $query->where('cashier_id', auth()->id());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        $view = auth()->user()->role === 'cashier' ? 'cashier.sales.sales-table' : 'admin.sales.sales-table';

        return view($view, compact('sales'));
    }

    public function create(): View
    {
        $products = Product::with('category')
            ->where('status', 'active')
            ->where('current_stock', '>', 0)
            ->orderBy('product_name')
            ->get();

        $categories = \App\Models\Category::orderBy('category_name')->get();

        return view('cashier.sales.sale-create', compact('products', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cart_items' => ['required', 'string'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,gcash,card,other'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ]);

        $items = json_decode($validated['cart_items'], true);
        $discountAmount = $validated['discount_amount'] ?? 0;
        $paymentMethod = $validated['payment_method'];
        $amountPaid = $validated['amount_paid'];

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $saleItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->current_stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->product_name}");
                }

                $subtotal = $product->selling_price * $item['quantity'];
                $totalAmount += $subtotal;

                $saleItems[] = [
                    'product_id' => $product->product_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->selling_price,
                    'subtotal' => $subtotal,
                    'product' => $product,
                ];
            }

            $totalAfterDiscount = $totalAmount - $discountAmount;

            if ($amountPaid < $totalAfterDiscount) {
                throw new \Exception('Insufficient payment amount.');
            }

            $changeAmount = $amountPaid - $totalAfterDiscount;

            $receiptNumber = 'REC-'.strtoupper(uniqid());

            $sale = Sale::create([
                'receipt_number' => $receiptNumber,
                'cashier_id' => $request->user()->id,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
            ]);

            foreach ($saleItems as $saleItem) {
                $product = $saleItem['product'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->product_id,
                    'quantity' => $saleItem['quantity'],
                    'unit_price' => $saleItem['unit_price'],
                    'subtotal' => $saleItem['subtotal'],
                ]);

                $stockBefore = $product->current_stock;
                $stockAfter = $stockBefore - $saleItem['quantity'];

                StockTransaction::create([
                    'product_id' => $product->product_id,
                    'user_id' => $request->user()->id,
                    'transaction_type' => 'stock_out',
                    'quantity' => $saleItem['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => $receiptNumber,
                    'notes' => 'Sale transaction',
                ]);

                $product->update(['current_stock' => $stockAfter]);
            }

            DB::commit();

            return redirect()->route('cashier.sales')->with('success', 'Sale completed successfully. Receipt: '.$receiptNumber);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Sale $sale): View
    {
        $sale->load(['cashier', 'saleItems.product']);

        $view = auth()->user()->role === 'cashier' ? 'cashier.sales.sale-show' : 'admin.sales.sale-show';

        return view($view, compact('sale'));
    }

    public function voidSale(Sale $sale): RedirectResponse
    {
        if ($sale->status === 'voided') {
            return back()->with('error', 'Sale is already voided.');
        }

        DB::beginTransaction();

        try {
            foreach ($sale->saleItems as $saleItem) {
                $product = $saleItem->product;
                $stockBefore = $product->current_stock;
                $stockAfter = $stockBefore + $saleItem->quantity;

                StockTransaction::create([
                    'product_id' => $product->product_id,
                    'user_id' => auth()->id(),
                    'transaction_type' => 'stock_in',
                    'quantity' => $saleItem->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => 'VOID-'.$sale->receipt_number,
                    'notes' => 'Void sale transaction',
                ]);

                $product->update(['current_stock' => $stockAfter]);
            }

            $sale->update(['status' => 'voided']);

            DB::commit();

            return back()->with('success', 'Sale voided successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to void sale: '.$e->getMessage());
        }
    }
}
