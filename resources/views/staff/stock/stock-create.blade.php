@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Add Stock In')

@section('content')
    <div class="stock-in-layout">
        <div class="stock-products-section">
            <div class="stock-page-header">
                <h3 class="stock-page-title">Stock In</h3>
                <a href="{{ route('staff.stock') }}" class="back-btn">← Back to Stock In</a>
            </div>

            <div class="products-section">
                <h4 class="section-title">Select Products</h4>

                <div class="products-filter-bar">
                    <input type="text" id="productSearch" class="search-input" placeholder="Search by product name, barcode, or category...">
                    <select id="categoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="products-table-wrapper">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Barcode</th>
                                <th>Current Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            @foreach ($products as $product)
                                <tr class="product-row" data-product-id="{{ $product->product_id }}" data-category-id="{{ $product->category_id }}" data-name="{{ strtolower($product->product_name) }}" data-barcode="{{ strtolower($product->barcode ?? '') }}" data-stock="{{ $product->current_stock }}" data-name-display="{{ $product->product_name }}" data-price="{{ $product->cost_price }}">
                                    <td>
                                        <div class="product-cell">
                                            <div class="product-info">
                                                <div class="product-name">{{ $product->product_name }}</div>
                                                <div class="product-details">{{ $product->unit ?? 'N/A' }}, ₱{{ number_format($product->cost_price, 2) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $product->barcode ?? 'N/A' }}</td>
                                    <td>{{ $product->current_stock }}</td>
                                    <td>
                                        <button type="button" class="add-product-btn" onclick="addToStock({{ $product->product_id }}, '{{ $product->product_name }}', {{ $product->current_stock }}, {{ $product->cost_price }})">
                                            Add
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-info">
                    Showing 1-{{ $products->count() }} of {{ $products->count() }} products
                </div>
            </div>
        </div>

        <div class="stock-details-section">
            <div class="stock-details-card">
                <h4 class="section-title">Stock In Details</h4>

                <form action="{{ route('staff.stock.store') }}" method="POST" id="stockForm">
                    @csrf
                    <input type="hidden" name="transaction_type" value="stock_in">
                    <input type="hidden" id="stockItemsInput" name="stock_items">

                    <div class="form-group">
                        <label for="supplier_id">Supplier (Optional)</label>
                        <select id="supplier_id" name="supplier_id">
                            <option value="">Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reference">Reference</label>
                        <input type="text" id="reference" name="reference" placeholder="e.g., PO #123, Invoice #456">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="3"></textarea>
                    </div>

                    <div class="added-items-section">
                        <div class="added-items-header">
                            <h5>Added Items</h5>
                            <button type="button" class="clear-all-btn" onclick="clearAllItems()">Clear All</button>
                        </div>

                        <div class="added-items-wrapper" id="addedItemsWrapper">
                            <div class="empty-cart-message" id="emptyCartMessage">
                                No items added
                            </div>
                            <table class="added-items-table" id="addedItemsTable" style="display: none;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Add Quantity*</th>
                                        <th>New Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="addedItemsTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('staff.stock') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Stock In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let stockItems = {};
        let itemCount = 0;

        function addToStock(productId, productName, currentStock, price) {
            if (stockItems[productId]) {
                alert('Product already added to the list');
                return;
            } else {
                stockItems[productId] = {
                    productId: productId,
                    productName: productName,
                    currentStock: currentStock,
                    addQuantity: 1,
                    price: price
                };
            }
            updateAddedItemsDisplay();
        }

        function removeFromStock(productId) {
            delete stockItems[productId];
            updateAddedItemsDisplay();
        }

        function updateQuantity(productId, newQuantity) {
            if (stockItems[productId]) {
                if (newQuantity > 0) {
                    stockItems[productId].addQuantity = newQuantity;
                    updateAddedItemsDisplay();
                } else {
                    alert('Quantity must be greater than 0');
                }
            }
        }

        function clearAllItems() {
            if (Object.keys(stockItems).length === 0) return;
            if (confirm('Are you sure you want to clear all items?')) {
                stockItems = {};
                updateAddedItemsDisplay();
            }
        }

        function updateAddedItemsDisplay() {
            const tableBody = document.getElementById('addedItemsTableBody');
            const table = document.getElementById('addedItemsTable');
            const emptyMessage = document.getElementById('emptyCartMessage');

            tableBody.innerHTML = '';
            let index = 1;

            if (Object.keys(stockItems).length === 0) {
                table.style.display = 'none';
                emptyMessage.style.display = 'block';
            } else {
                table.style.display = 'table';
                emptyMessage.style.display = 'none';

                for (const productId in stockItems) {
                    const item = stockItems[productId];
                    const newStock = item.currentStock + item.addQuantity;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index}</td>
                        <td>
                            <div class="stock-product-name">${item.productName}</div>
                            <div class="stock-product-price">₱${item.price.toFixed(2)}</div>
                        </td>
                        <td>${item.currentStock}</td>
                        <td>
                            <input type="number" class="quantity-input" value="${item.addQuantity}" min="1" onchange="updateQuantity(${productId}, this.value)">
                        </td>
                        <td class="new-stock">${newStock}</td>
                        <td>
                            <button type="button" class="delete-item-btn" onclick="removeFromStock(${productId})">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                    index++;
                }
            }
        }

        document.getElementById('stockForm').addEventListener('submit', function(e) {
            const items = [];
            for (const productId in stockItems) {
                items.push({
                    product_id: stockItems[productId].productId,
                    quantity: stockItems[productId].addQuantity
                });
            }
            document.getElementById('stockItemsInput').value = JSON.stringify(items);
        });

        document.getElementById('productSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.product-row');
            rows.forEach(row => {
                const name = row.dataset.name;
                const barcode = row.dataset.barcode;
                if (name.includes(searchTerm) || barcode.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.getElementById('categoryFilter').addEventListener('change', function() {
            const categoryId = this.value;
            const rows = document.querySelectorAll('.product-row');
            rows.forEach(row => {
                if (categoryId === '' || row.dataset.categoryId === categoryId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection
