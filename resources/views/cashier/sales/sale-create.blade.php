@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'New Sale')

@section('content')
    <div class="sale-page-layout">
        <div class="sale-page-header">
            <div class="page-heading">
                <h3 class="sale-page-title">New Sale</h3>
                <p class="sale-page-description">Search and select products, then add them to the cart to complete the sale.</p>
            </div>
            <a href="{{ route('cashier.sales') }}" class="back-button">← Back to Sales</a>
        </div>

        <div class="sales-content">
            <section class="products-panel">
                <div class="products-section">
                <h4 class="section-title">Products</h4>

                <div class="products-filter-bar">
                    <input type="text" id="productSearch" class="search-input" placeholder="Search product by name or barcode...">
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
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            @foreach ($products as $product)
                                <tr class="product-row" data-product-id="{{ $product->product_id }}" data-category-id="{{ $product->category_id }}" data-name="{{ strtolower($product->product_name) }}" data-barcode="{{ strtolower($product->barcode ?? '') }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->current_stock }}" data-name-display="{{ $product->product_name }}">
                                    <td>
                                        <div class="product-cell">
                                            <div class="product-image-placeholder">
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                                                </svg>
                                            </div>
                                            <div class="product-info">
                                                <div class="product-name">{{ $product->product_name }}</div>
                                                <div class="product-barcode">{{ $product->barcode ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $product->unit ?? 'N/A' }}</td>
                                    <td>₱{{ number_format($product->selling_price, 2) }}</td>
                                    <td>
                                        <span class="stock-badge {{ $product->isLowStock() ? 'stock-low' : 'stock-available' }}">
                                            {{ $product->current_stock }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="add-product-btn" onclick="addToCart({{ $product->product_id }}, '{{ $product->product_name }}', {{ $product->selling_price }}, {{ $product->current_stock }})">
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
            </section>

            <aside class="cart-panel">
                <div class="cart-section">
                <div class="cart-header">
                    <h4 class="section-title">Added Items</h4>
                    <button type="button" class="clear-cart-btn" onclick="clearCart()">Clear Cart</button>
                </div>

                <div class="cart-items-wrapper" id="cartItemsWrapper">
                    <div class="empty-cart-message" id="emptyCartMessage">
                        No items in cart
                    </div>
                    <table class="cart-table" id="cartTable" style="display: none;">
                        <tbody id="cartTableBody">
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Total Items:</span>
                        <span id="totalItems">0</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Amount:</span>
                        <span id="totalAmount">₱0.00</span>
                    </div>
                    <div class="discount-row">
                        <label for="discount_amount">Discount</label>
                        <div class="discount-input-group">
                            <input type="number" id="discount_amount" step="0.01" min="0" value="0" oninput="calculateTotal()">
                            <button type="button" class="apply-discount-btn" onclick="calculateTotal()">Apply</button>
                        </div>
                    </div>
                    <div class="summary-row summary-row-highlight">
                        <span>Total After Discount:</span>
                        <span id="totalAfterDiscount">₱0.00</span>
                    </div>
                </div>

                <button type="button" class="proceed-payment-btn" onclick="showPaymentModal()">Proceed to Payment</button>
            </div>
            </aside>
        </div>
    </div>

    <div class="payment-modal" id="paymentModal" style="display: none;">
        <div class="payment-modal-content">
            <div class="payment-modal-header">
                <h3 class="payment-modal-title">Complete Payment</h3>
                <button type="button" class="close-modal-btn" onclick="closePaymentModal()">×</button>
            </div>

            <form action="{{ route('cashier.sales.store') }}" method="POST" id="saleForm">
                @csrf

                <div class="payment-summary">
                    <div class="summary-row">
                        <span>Total Amount:</span>
                        <span id="modalTotalAmount">₱0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Discount:</span>
                        <span id="modalDiscount">₱0.00</span>
                    </div>
                    <div class="summary-row summary-row-highlight">
                        <span>Total to Pay:</span>
                        <span id="modalTotalToPay">₱0.00</span>
                    </div>
                </div>

                <div class="payment-form">
                    <div class="form-group">
                        <label for="payment_method">Payment Method <span class="required">*</span></label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="card">Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount_paid">Amount Paid <span class="required">*</span></label>
                        <input type="number" id="amount_paid" name="amount_paid" step="0.01" min="0" required oninput="calculateChange()">
                    </div>
                    <div class="summary-row">
                        <span>Change:</span>
                        <span id="changeAmount">₱0.00</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closePaymentModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Complete Sale</button>
                </div>

                <input type="hidden" id="cartItemsInput" name="cart_items">
            </form>
        </div>
    </div>

    <script>
        let cart = {};
        let itemCount = 0;

        function addToCart(productId, productName, price, stock) {
            if (cart[productId]) {
                if (cart[productId].quantity < stock) {
                    cart[productId].quantity++;
                } else {
                    alert('Cannot add more than available stock');
                    return;
                }
            } else {
                cart[productId] = {
                    productId: productId,
                    productName: productName,
                    price: price,
                    quantity: 1,
                    stock: stock
                };
            }
            updateCartDisplay();
        }

        function removeFromCart(productId) {
            delete cart[productId];
            updateCartDisplay();
        }

        function updateQuantity(productId, change) {
            if (cart[productId]) {
                const newQuantity = cart[productId].quantity + change;
                if (newQuantity > 0 && newQuantity <= cart[productId].stock) {
                    cart[productId].quantity = newQuantity;
                    updateCartDisplay();
                } else if (newQuantity <= 0) {
                    removeFromCart(productId);
                } else {
                    alert('Cannot exceed available stock');
                }
            }
        }

        function clearCart() {
            if (Object.keys(cart).length === 0) return;
            if (confirm('Are you sure you want to clear the cart?')) {
                cart = {};
                updateCartDisplay();
            }
        }

        function updateCartDisplay() {
            const cartTableBody = document.getElementById('cartTableBody');
            const cartTable = document.getElementById('cartTable');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            const totalItemsEl = document.getElementById('totalItems');
            const totalAmountEl = document.getElementById('totalAmount');
            const totalAfterDiscountEl = document.getElementById('totalAfterDiscount');

            cartTableBody.innerHTML = '';
            let totalItems = 0;
            let totalAmount = 0;

            if (Object.keys(cart).length === 0) {
                cartTable.style.display = 'none';
                emptyCartMessage.style.display = 'block';
            } else {
                cartTable.style.display = 'table';
                emptyCartMessage.style.display = 'none';

                for (const productId in cart) {
                    const item = cart[productId];
                    const subtotal = item.price * item.quantity;
                    totalItems += item.quantity;
                    totalAmount += subtotal;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <div class="cart-product-cell">
                                <div class="product-image-placeholder small">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                                    </svg>
                                </div>
                                <div class="cart-product-name">${item.productName}</div>
                            </div>
                        </td>
                        <td>₱${item.price.toFixed(2)}</td>
                        <td>
                            <div class="quantity-control">
                                <button type="button" class="qty-btn" onclick="updateQuantity(${productId}, -1)">-</button>
                                <span class="qty-value">${item.quantity}</span>
                                <button type="button" class="qty-btn" onclick="updateQuantity(${productId}, 1)">+</button>
                            </div>
                        </td>
                        <td>₱${subtotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="delete-cart-item-btn" onclick="removeFromCart(${productId})">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </td>
                    `;
                    cartTableBody.appendChild(row);
                }
            }

            totalItemsEl.textContent = totalItems;
            totalAmountEl.textContent = '₱' + totalAmount.toFixed(2);

            const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const totalAfterDiscount = totalAmount - discount;
            totalAfterDiscountEl.textContent = '₱' + totalAfterDiscount.toFixed(2);
        }

        function calculateTotal() {
            updateCartDisplay();
        }

        function showPaymentModal() {
            if (Object.keys(cart).length === 0) {
                alert('Please add items to the cart first');
                return;
            }

            const totalAmount = parseFloat(document.getElementById('totalAmount').textContent.replace('₱', '')) || 0;
            const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const totalAfterDiscount = totalAmount - discount;

            document.getElementById('modalTotalAmount').textContent = '₱' + totalAmount.toFixed(2);
            document.getElementById('modalDiscount').textContent = '₱' + discount.toFixed(2);
            document.getElementById('modalTotalToPay').textContent = '₱' + totalAfterDiscount.toFixed(2);

            document.getElementById('paymentModal').style.display = 'flex';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        function calculateChange() {
            const totalAfterDiscount = parseFloat(document.getElementById('modalTotalToPay').textContent.replace('₱', '')) || 0;
            const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
            const change = amountPaid - totalAfterDiscount;
            document.getElementById('changeAmount').textContent = '₱' + change.toFixed(2);
        }

        document.getElementById('saleForm').addEventListener('submit', function(e) {
            const cartItems = [];
            for (const productId in cart) {
                cartItems.push({
                    product_id: cart[productId].productId,
                    quantity: cart[productId].quantity
                });
            }
            document.getElementById('cartItemsInput').value = JSON.stringify(cartItems);
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
