<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', '%'.$search.'%')
                    ->orWhere('barcode', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->orderBy('product_name')->get();
        $categories = Category::orderBy('category_name')->get();

        $view = auth()->user()->role === 'staff' ? 'staff.products.product-view' : 'admin.products.product-management-table';

        return view($view, compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('category_name')->get();

        $view = auth()->user()->role === 'staff' ? 'staff.products.product-create' : 'admin.products.product-create';

        return view($view, compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,category_id'],
            'product_name' => ['required', 'string', 'max:150'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'unit' => ['required', 'string', 'max:30'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Product::create($validated);

        $redirectRoute = auth()->user()->role === 'staff' ? 'products' : 'admin.products';

        return redirect()->route($redirectRoute)->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        if ( auth()->user()->role === 'cashier') {
            abort(403, 'You cannot edit products.');
        }

        $categories = Category::orderBy('category_name')->get();

        return view('admin.products.product-edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if (auth()->user()->role === 'staff' || auth()->user()->role === 'cashier') {
            abort(403, 'You cannot update products.');
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,category_id'],
            'product_name' => ['required', 'string', 'max:150'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode,'.$product->product_id.',product_id'],
            'unit' => ['required', 'string', 'max:30'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $product->update($validated);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if (auth()->user()->role === 'staff' || auth()->user()->role === 'cashier') {
            abort(403, 'You cannot delete products.');
        }

        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }
}
