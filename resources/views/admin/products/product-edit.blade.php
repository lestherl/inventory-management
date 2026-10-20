@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Edit Product')

@section('content')
    <div class="form-card">
        <div class="form-header">
            <h3 class="form-title">Edit Product</h3>
            <a href="{{ route(
    auth()->user()->role === 'admin'
        ? 'admin.products'
        : 'products'
) }}" class="back-btn">
    Back to Products
</a>
        </div>

        

        <form action="{{ route(auth()->user()->role === 'admin'
            ? 'admin.products.update'
            : 'products.update',
        $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->category_id }}" {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="barcode">Barcode</label>
                    <input type="text" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                    @error('barcode')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="product_name">Product Name <span class="required">*</span></label>
                <input type="text" id="product_name" name="product_name" value="{{ old('product_name', $product->product_name) }}" required>
                @error('product_name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="unit">Unit <span class="required">*</span></label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $product->unit) }}" placeholder="e.g., pcs, kg, box" required>
                    @error('unit')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="cost_price">Cost Price <span class="required">*</span></label>
                    <input type="number" id="cost_price" name="cost_price" step="0.01" min="0" value="{{ old('cost_price', $product->cost_price) }}" required>
                    @error('cost_price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="selling_price">Selling Price <span class="required">*</span></label>
                    <input type="number" id="selling_price" name="selling_price" step="0.01" min="0" value="{{ old('selling_price', $product->selling_price) }}" required>
                    @error('selling_price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="current_stock">Current Stock <span class="required">*</span></label>
                    <input type="number" id="current_stock" name="current_stock" min="0" value="{{ old('current_stock', $product->current_stock) }}" required>
                    @error('current_stock')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="minimum_stock">Minimum Stock <span class="required">*</span></label>
                    <input type="number" id="minimum_stock" name="minimum_stock" min="0" value="{{ old('minimum_stock', $product->minimum_stock) }}" required>
                    @error('minimum_stock')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" required>
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route(
                        auth()->user()->role === 'admin'
                            ? 'admin.products'
                            : 'products'
                    ) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
@endsection
