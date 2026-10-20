
@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Suppliers')

@section('content')

<div class="inventory-table-card">

    <!-- HEADER -->
    <div class="table-topbar">
        <div class="table-title-wrap">
            <h3 class="table-title">Suppliers</h3>
        </div>

        <button type="button" class="add-table-btn" id="openSupplierModal">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Add Supplier
        </button>
    </div>

    <!-- FILTER -->
    <div class="filter-bar">
        <form action="{{ route('suppliers') }}" method="GET" class="filter-form">

            <input type="text"
                   name="search"
                   placeholder="Search suppliers..."
                   value="{{ request('search') }}">

            <select name="status">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>

            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="{{ route('suppliers') }}" class="btn btn-secondary">Clear</a>

        </form>
    </div>

    <!-- TABLE -->
    @if ($suppliers->isEmpty())

        <div class="empty-state">No suppliers available.</div>

    @else

        <div class="inventory-table-wrapper">
            <table class="inventory-table">

                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->supplier_name }}</td>
                            <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
                            <td>{{ $supplier->phone ?? 'N/A' }}</td>
                            <td>{{ $supplier->email ?? 'N/A' }}</td>

                            <td>
                                <span class="badge badge-{{ $supplier->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($supplier->status) }}
                                </span>
                            </td>

                            <td>
                                <div class="inventory-actions">

                                    <!-- EDIT -->
                                    <button
                                        type="button"
                                        class="inventory-action edit-supplier-btn"
                                        aria-label="Edit supplier"
                                        data-id="{{ $supplier->getKey() }}"
                                        data-url="{{ route('suppliers.update', $supplier) }}"
                                        data-name="{{ $supplier->supplier_name }}"
                                        data-contact="{{ $supplier->contact_person }}"
                                        data-phone="{{ $supplier->phone }}"
                                        data-email="{{ $supplier->email }}"
                                        data-address="{{ $supplier->address }}"
                                        data-status="{{ $supplier->status }}"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3Z"/>
                                            <path d="m13.5 6.5 4 4"/>
                                        </svg>
                                    </button>

                                    <!-- DELETE -->
                                    <form action="{{ route('suppliers.destroy', $supplier) }}"
                                          method="POST"
                                          class="inline-form">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inventory-action text-danger"
                                            aria-label="Delete supplier"
                                            onclick="return confirm('Are you sure you want to delete this supplier?')"
                                        >
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


<!-- ADD / EDIT SUPPLIER MODAL -->

<div class="modal-overlay simple-supplier-modal" id="supplierModal" hidden>

    <div class="modal-content simple-supplier-content"
         role="dialog"
         aria-modal="true"
         aria-labelledby="supplierModalTitle">

        <!-- MODAL HEADER -->
        <div class="modal-header">
            <h3 class="modal-title" id="supplierModalTitle">Add Supplier</h3>

            <button type="button"
                    class="modal-close-btn"
                    id="closeSupplierModal"
                    aria-label="Close modal">
                &times;
            </button>
        </div>

        <!-- FORM -->
        <form id="supplierForm"
              action="{{ route('suppliers.store') }}"
              data-store-url="{{ route('suppliers.store') }}"
              method="POST">

            @csrf

            <input type="hidden" name="_method" id="supplierMethod" value="PUT" disabled>
            <input type="hidden" name="_modal_type" id="supplierModalType" value="create">
            <input type="hidden" name="_modal_supplier_id" id="supplierModalId">

            <!-- SUPPLIER NAME -->
            <div class="form-group">
                <label for="supplier_name">
                    Supplier Name <span class="required">*</span>
                </label>

                <input type="text"
                       id="supplier_name"
                       name="supplier_name"
                       required>

                @error('supplier_name')
                    <span class="error-message supplier-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- CONTACT AND PHONE -->
            <div class="form-row">

                <div class="form-group">
                    <label for="contact_person">Contact Person</label>

                    <input type="text"
                           id="contact_person"
                           name="contact_person">

                    @error('contact_person')
                        <span class="error-message supplier-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>

                    <input type="tel"
                           id="phone"
                           name="phone">

                    @error('phone')
                        <span class="error-message supplier-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <!-- EMAIL AND STATUS -->
            <div class="form-row">

                <div class="form-group">
                    <label for="email">Email</label>

                    <input type="email"
                           id="email"
                           name="email">

                    @error('email')
                        <span class="error-message supplier-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">
                        Status <span class="required">*</span>
                    </label>

                    <select id="status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                    @error('status')
                        <span class="error-message supplier-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <!-- ADDRESS -->
            <div class="form-group">
                <label for="address">Address</label>

                <textarea id="address"
                          name="address"
                          rows="2"
                          placeholder="Enter supplier address"></textarea>

                @error('address')
                    <span class="error-message supplier-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- ACTIONS -->
            <div class="form-actions simple-supplier-actions">

                <button type="button"
                        class="btn btn-secondary"
                        id="cancelSupplierModal">
                    Cancel
                </button>

                <button type="submit"
                        class="btn btn-primary"
                        id="supplierSubmitBtn">
                    Create Supplier
                </button>

            </div>

        </form>
    </div>
</div>


<!-- MODAL SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('supplierModal');
    const form = document.getElementById('supplierForm');

    const addBtn = document.getElementById('openSupplierModal');
    const closeBtn = document.getElementById('closeSupplierModal');
    const cancelBtn = document.getElementById('cancelSupplierModal');

    const title = document.getElementById('supplierModalTitle');
    const submitBtn = document.getElementById('supplierSubmitBtn');

    const method = document.getElementById('supplierMethod');
    const modalType = document.getElementById('supplierModalType');
    const modalId = document.getElementById('supplierModalId');

    const fields = {
        supplier_name: document.getElementById('supplier_name'),
        contact_person: document.getElementById('contact_person'),
        phone: document.getElementById('phone'),
        email: document.getElementById('email'),
        address: document.getElementById('address'),
        status: document.getElementById('status')
    };

    let previousOverflow = '';
    let lastTrigger = addBtn;

    // SHOW MODAL
    function showModal(trigger) {
        lastTrigger = trigger || addBtn;
        previousOverflow = document.body.style.overflow;

        modal.hidden = false;
        document.body.style.overflow = 'hidden';

        fields.supplier_name.focus();
    }

    // CLOSE MODAL
    function hideModal() {
        modal.hidden = true;
        document.body.style.overflow = previousOverflow;

        lastTrigger?.focus();
    }

    // CLEAR ERRORS
    function clearErrors() {
        document.querySelectorAll('.supplier-error').forEach(error => {
            error.remove();
        });
    }

    // FILL FIELDS
    function fillFields(data) {
        Object.keys(fields).forEach(key => {
            fields[key].value = data[key] ?? '';
        });
    }

    // ADD SUPPLIER
    function openCreate(clearValidation = true) {

        if (clearValidation) clearErrors();

        form.reset();
        form.action = form.dataset.storeUrl;

        method.disabled = true;
        modalType.value = 'create';
        modalId.value = '';

        title.textContent = 'Add Supplier';
        submitBtn.textContent = 'Create Supplier';

        fillFields({
            supplier_name: '',
            contact_person: '',
            phone: '',
            email: '',
            address: '',
            status: 'active'
        });

        showModal(addBtn);
    }

    addBtn.addEventListener('click', () => {
        openCreate();
    });

    // EDIT SUPPLIER
    function openEdit(button, clearValidation = true) {

        if (clearValidation) clearErrors();

        form.action = button.dataset.url;

        method.disabled = false;
        modalType.value = 'edit';
        modalId.value = button.dataset.id;

        title.textContent = 'Edit Supplier';
        submitBtn.textContent = 'Update Supplier';

        fillFields({
            supplier_name: button.dataset.name,
            contact_person: button.dataset.contact,
            phone: button.dataset.phone,
            email: button.dataset.email,
            address: button.dataset.address,
            status: button.dataset.status
        });

        showModal(button);
    }

    document.querySelectorAll('.edit-supplier-btn').forEach(button => {
        button.addEventListener('click', () => {
            openEdit(button);
        });
    });

    // CLOSE EVENTS
    closeBtn.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);

    modal.addEventListener('click', event => {
        if (event.target === modal) hideModal();
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !modal.hidden) {
            hideModal();
        }

        if (event.key === 'Tab' && !modal.hidden) {
            const focusable = [...modal.querySelectorAll(
                'button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled])'
            )].filter(el => el.getClientRects().length);

            if (!focusable.length) return;

            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    // REOPEN AFTER LARAVEL VALIDATION ERROR
    @if ($errors->any())

        const oldMode = @json(old('_modal_type'));
        const oldId = @json(old('_modal_supplier_id'));

        const oldValues = {
            supplier_name: @json(old('supplier_name')),
            contact_person: @json(old('contact_person')),
            phone: @json(old('phone')),
            email: @json(old('email')),
            address: @json(old('address')),
            status: @json(old('status'))
        };

        if (oldMode === 'create') {
            openCreate(false);
            fillFields(oldValues);
        }

        if (oldMode === 'edit') {

            const button = [...document.querySelectorAll('.edit-supplier-btn')]
                .find(btn => btn.dataset.id === String(oldId));

            if (button) {
                openEdit(button, false);
                fillFields(oldValues);
            }
        }

    @endif

});
</script>

@endsection
