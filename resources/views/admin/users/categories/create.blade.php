<!-- resources/views/admin/users/categories/create.blade.php -->
@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Create Subcategory for {{ $user->name }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.categories.store', $user->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Subcategory Name</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="parent_id" class="form-label">Main Category</label>
                                <select class="form-select" id="parent_id" name="parent_id" required>
                                    <option value="">Select Main Category</option>
                                    @foreach ($defaultCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    • Wallpapers: For static images (JPG, PNG, WEBP)<br>
                                    • Live Wallpapers: For videos and GIFs
                                </small>
                            </div>

                            <button type="submit" class="btn btn-success">Create Subcategory</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
