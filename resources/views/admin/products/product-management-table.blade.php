@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Products')

@section('content')
    <div class="inventory-table-card">
        <div class="table-topbar">
            <div class="table-title-wrap">
                <h3 class="table-title">Products</h3>
            </div>
            <button type="button" class="add-table-btn" onclick="openProductModal()">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Add
            </button>
        </div>

        <div class="filter-bar">
            <form action="{{ route('admin.products') }}" method="GET" class="filter-form">
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}">
                <select name="category">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
                <select name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('admin.products') }}" class="btn btn-secondary">Clear</a>
            </form>
        </div>

        @if ($products->isEmpty())
            <div class="empty-state">
                No products available.
            </div>
        @else
            <div class="inventory-table-wrapper">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Barcode</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Current Stock</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="{{ $product->isLowStock() ? 'low-stock-row' : '' }}">
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                <td>{{ $product->barcode ?? 'N/A' }}</td>
                                <td>₱{{ number_format($product->cost_price, 2) }}</td>
                                <td>₱{{ number_format($product->selling_price, 2) }}</td>
                                <td class="{{ $product->isLowStock() ? 'text-danger' : '' }}">
                                    {{ $product->current_stock }}
                                    @if ($product->isLowStock())
                                        <span class="badge badge-warning">Low</span>
                                    @endif
                                </td>
                                <td>{{ $product->unit }}</td>
                                <td>
                                    <span class="badge badge-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="inventory-actions">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="inventory-action" aria-label="Edit product">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3Z"/>
                                                <path d="m13.5 6.5 4 4"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inventory-action text-danger" aria-label="Delete product" onclick="return confirm('Are you sure you want to delete this product?')">
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M4 7h16M8 7V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V7M7 7l1 13h8l1-13"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Product Modal -->
    <div class="modal-overlay" id="productModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Product</h3>
                <button type="button" class="modal-close-btn" onclick="closeProductModal()">×</button>
            </div>

            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category <span class="required">*</span></label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
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
                        <input type="text" id="barcode" name="barcode" maxlength="20" value="{{ old('barcode') }}">
                        @error('barcode')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="product_name">Product Name <span class="required">*</span></label>
                    <input type="text" id="product_name" name="product_name" maxlength="30" value="{{ old('product_name') }}" required>
                    @error('product_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="unit">Unit <span class="required">*</span></label>
                        <input type="text" id="unit" maxlength="10" name="unit" value="{{ old('unit') }}" placeholder="e.g., pcs, kg, box" required>
                        @error('unit')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="cost_price">Cost Price <span class="required">*</span></label>
                        <input type="number" id="cost_price" name="cost_price" step="0.01" min="0" value="{{ old('cost_price') }}" required>
                        @error('cost_price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="selling_price">Selling Price <span class="required">*</span></label>
                        <input type="number" id="selling_price" name="selling_price" step="0.01" min="0" value="{{ old('selling_price') }}" required>
                        @error('selling_price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="current_stock">Current Stock <span class="required">*</span></label>
                        <input type="number" id="current_stock" name="current_stock" min="0" value="{{ old('current_stock', 0) }}" required>
                        @error('current_stock')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="minimum_stock">Minimum Stock <span class="required">*</span></label>
                        <input type="number" id="minimum_stock" name="minimum_stock" min="0" value="{{ old('minimum_stock', 0) }}" required>
                        @error('minimum_stock')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="required">*</span></label>
                        <select id="status" name="status" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openProductModal() {
            document.getElementById('productModal').style.display = 'flex';
        }

        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductModal();
            }
        });
    </script>
@endsection
