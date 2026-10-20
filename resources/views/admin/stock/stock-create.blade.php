@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Add Stock Transaction')

@section('content')
    <div class="form-card">
        <div class="form-header">
            <h3 class="form-title">Add Stock Transaction</h3>
            <a href="{{ route('stock.transactions') }}" class="back-btn">Back</a>
        </div>

        <form action="{{ route('stock.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="product_id">Product <span class="required">*</span></label>
                <select id="product_id" name="product_id" required>
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->product_id }}" data-stock="{{ $product->current_stock }}">
                            {{ $product->product_name }} (Current: {{ $product->current_stock }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="transaction_type">Transaction Type <span class="required">*</span></label>
                <select id="transaction_type" name="transaction_type" required>
                    <option value="stock_in">Stock In</option>
                    <option value="stock_out">Stock Out</option>
                </select>
                @error('transaction_type')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="supplier_id">Supplier</label>
                <select id="supplier_id" name="supplier_id">
                    <option value="">Select Supplier (Optional)</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="quantity">Quantity <span class="required">*</span></label>
                <input type="number" id="quantity" name="quantity" min="1" value="{{ old('quantity') }}" required>
                @error('quantity')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="reference">Reference</label>
                <input type="text" id="reference" maxlength="20" name="reference" value="{{ old('reference') }}" placeholder="e.g., PO #123, Invoice #456">
                @error('reference')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" maxlength="70" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('stock.transactions') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Transaction</button>
            </div>
        </form>
    </div>
@endsection
