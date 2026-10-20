    @extends('layout.app')

    @section('style')
        @vite(['resources/css/dashboard.css'])
    @endsection

    @section('title', 'Create Supplier')

    @section('content')
        <div class="form-card">
            <div class="form-header">
                <h3 class="form-title">Create Supplier</h3>
                <a href="{{ route('suppliers') }}" class="back-btn">Back</a>
            </div>

            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="supplier_name">Supplier Name <span class="required">*</span></label>
                    <input type="text" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required>
                    @error('supplier_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                        @error('contact_person')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3">{{ old('address') }}</textarea>
                    @error('address')
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

                <div class="form-actions">
                    <a href="{{ route('suppliers') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Supplier</button>
                </div>
            </form>
        </div>
    @endsection
