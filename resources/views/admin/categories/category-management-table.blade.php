
@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Categories')

@section('content')

<div class="inventory-table-card">

    <!-- HEADER -->
    <div class="table-topbar">
        <div class="table-title-wrap">
            <h3 class="table-title">Categories</h3>
        </div>

        <button type="button"
                class="add-table-btn"
                id="openCategoryModal">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Add Category
        </button>
    </div>

    <!-- CATEGORY TABLE -->
    @if ($categories->isEmpty())

        <div class="empty-state">
            No categories available.
        </div>

    @else

        <div class="inventory-table-wrapper">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->category_name }}</td>

                            <td>
                                {{ $category->description ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $category->created_at->format('M d, Y') }}
                            </td>

                            <td>
                                <div class="inventory-actions">

                                    <!-- EDIT BUTTON -->
                                    <button
                                        type="button"
                                        class="inventory-action edit-category-btn"
                                        aria-label="Edit category"
                                        data-id="{{ $category->getKey() }}"
                                        data-url="{{ route('categories.update', $category) }}"
                                        data-name="{{ $category->category_name }}"
                                        data-description="{{ $category->description }}"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3Z"/>
                                            <path d="m13.5 6.5 4 4"/>
                                        </svg>
                                    </button>

                                    <!-- DELETE BUTTON -->
                                    <form
                                        action="{{ route('categories.destroy', $category) }}"
                                        method="POST"
                                        class="inline-form"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inventory-action text-danger"
                                            aria-label="Delete category"
                                            onclick="return confirm('Are you sure you want to delete this category?')"
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


<!-- =====================================
     ADD / EDIT CATEGORY MODAL
===================================== -->

<div class="modal-overlay simple-category-modal"
     id="categoryModal"
     hidden>

    <div class="modal-content simple-category-content"
         role="dialog"
         aria-modal="true"
         aria-labelledby="categoryModalTitle">

        <!-- MODAL HEADER -->
        <div class="modal-header">

            <h3 class="modal-title" id="categoryModalTitle">
                Add Category
            </h3>

            <button
                type="button"
                class="modal-close-btn"
                id="closeCategoryModal"
                aria-label="Close modal"
            >
                &times;
            </button>

        </div>

        <!-- CATEGORY FORM -->
        <form
            id="categoryForm"
            action="{{ route('categories.store') }}"
            data-store-url="{{ route('categories.store') }}"
            method="POST"
        >
            @csrf

            <!-- ENABLED ONLY DURING EDIT -->
            <input type="hidden"
                   name="_method"
                   id="categoryMethod"
                   value="PUT"
                   disabled>

            <!-- MODAL STATE FOR VALIDATION -->
            <input type="hidden"
                   name="_modal_type"
                   id="categoryModalType"
                   value="create">

            <input type="hidden"
                   name="_modal_category_id"
                   id="categoryModalId">

            <!-- CATEGORY NAME -->
            <div class="form-group">
                <label for="category_name">
                    Category Name
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="category_name"
                    name="category_name"
                    value="{{ old('category_name') }}"
                    placeholder="Enter category name"
                    required
                    maxlength="30"
                >

                @error('category_name')
                    <span class="error-message category-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Enter category description (optional)"
                    maxlength="70"
                >{{ old('description') }}</textarea>

                @error('description')
                    <span class="error-message category-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- FORM ACTIONS -->
            <div class="form-actions simple-category-actions">

                <button
                    type="button"
                    class="btn btn-secondary"
                    id="cancelCategoryModal"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                    id="categorySubmitBtn"
                >
                    Create Category
                </button>

            </div>

        </form>

    </div>
</div>


<!-- =====================================
     CATEGORY MODAL JAVASCRIPT
===================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');

    const addBtn = document.getElementById('openCategoryModal');
    const closeBtn = document.getElementById('closeCategoryModal');
    const cancelBtn = document.getElementById('cancelCategoryModal');

    const modalTitle = document.getElementById('categoryModalTitle');
    const submitBtn = document.getElementById('categorySubmitBtn');

    const methodInput = document.getElementById('categoryMethod');
    const modalType = document.getElementById('categoryModalType');
    const modalId = document.getElementById('categoryModalId');

    const nameInput = document.getElementById('category_name');
    const descriptionInput = document.getElementById('description');

    let previousOverflow = '';
    let lastTrigger = addBtn;

    // OPEN MODAL
    function showModal(trigger = addBtn) {

        lastTrigger = trigger;
        previousOverflow = document.body.style.overflow;

        modal.hidden = false;
        document.body.style.overflow = 'hidden';

        nameInput.focus();
    }

    // CLOSE MODAL
    function hideModal() {

        modal.hidden = true;
        document.body.style.overflow = previousOverflow;

        lastTrigger?.focus();
    }

    // CLEAR VALIDATION ERRORS
    function clearErrors() {

        modal.querySelectorAll('.category-error').forEach(error => {
            error.remove();
        });
    }

    // ADD CATEGORY
    function openCreate(clearValidation = true) {

        if (clearValidation) {
            clearErrors();
        }

        form.reset();

        form.action = form.dataset.storeUrl;

        methodInput.disabled = true;

        modalType.value = 'create';
        modalId.value = '';

        modalTitle.textContent = 'Add Category';
        submitBtn.textContent = 'Create Category';

        nameInput.value = '';
        descriptionInput.value = '';

        showModal(addBtn);
    }

    addBtn.addEventListener('click', function () {
        openCreate();
    });

    // EDIT CATEGORY
    function openEdit(button, clearValidation = true) {

        if (clearValidation) {
            clearErrors();
        }

        form.action = button.dataset.url;

        methodInput.disabled = false;

        modalType.value = 'edit';
        modalId.value = button.dataset.id;

        modalTitle.textContent = 'Edit Category';
        submitBtn.textContent = 'Update Category';

        nameInput.value = button.dataset.name ?? '';
        descriptionInput.value = button.dataset.description ?? '';

        showModal(button);
    }

    document.querySelectorAll('.edit-category-btn').forEach(button => {

        button.addEventListener('click', function () {
            openEdit(button);
        });

    });

    // CLOSE EVENTS
    closeBtn.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);

    // CLOSE WHEN CLICKING OVERLAY
    modal.addEventListener('click', function (event) {

        if (event.target === modal) {
            hideModal();
        }

    });

    // ESCAPE KEY + FOCUS TRAP
    document.addEventListener('keydown', function (event) {

        if (modal.hidden) return;

        if (event.key === 'Escape') {
            hideModal();
            return;
        }

        if (event.key === 'Tab') {

            const focusable = [
                ...modal.querySelectorAll(
                    'button:not([disabled]), input:not([type="hidden"]):not([disabled]), textarea:not([disabled])'
                )
            ];

            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            }
            else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    // RESTORE MODAL AFTER LARAVEL VALIDATION ERROR
    @if ($errors->any())

        const oldMode = @json(old('_modal_type'));
        const oldId = @json(old('_modal_category_id'));

        const oldName = @json(old('category_name'));
        const oldDescription = @json(old('description'));

        if (oldMode === 'create') {

            openCreate(false);

            nameInput.value = oldName ?? '';
            descriptionInput.value = oldDescription ?? '';

        }

        if (oldMode === 'edit') {

            const editButton = [
                ...document.querySelectorAll('.edit-category-btn')
            ].find(button => button.dataset.id === String(oldId));

            if (editButton) {

                openEdit(editButton, false);

                nameInput.value = oldName ?? '';
                descriptionInput.value = oldDescription ?? '';

            }
        }

    @endif

});
</script>

@endsection
