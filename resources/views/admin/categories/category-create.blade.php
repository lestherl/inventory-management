@extends('layout.app')

@section('style')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('title', 'Create Category')

@section('content')
    <div class="form-card">
        <div class="form-header">
            <h3 class="form-title">Create Category</h3>
            <a href="{{ route('categories') }}" class="back-btn">Back</a>
        </div>

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="category_name">Category Name <span class="required">*</span></label>
                <input type="text" id="category_name" name="category_name" maxlength="" value="{{ old('category_name') }}" required>
                @error('category_name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('categories') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Category</button>
            </div>
        </form>
    </div>
@endsection
