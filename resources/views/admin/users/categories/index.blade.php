<!-- resources/views/admin/users/categories/index.blade.php -->
@extends('app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h3>{{ $user->name }}'s Categories</h3>
                            <p class="mb-0 text-muted">Email: {{ $user->email }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.users.categories.create', $user->id) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create Category
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($categories->count() > 0)
                            <div class="row">
                                @foreach ($categories as $category)
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <a href="{{ route('admin.users.categories.wallpapers', [$user->id, $category->id]) }}"
                                                        class="text-decoration-none">
                                                        {{ $category->category_name }}
                                                    </a>
                                                </h5>
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span
                                                            class="badge {{ $category->is_active ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                        <form
                                                            action="{{ route('admin.users.categories.toggle', [$user->id, $category->id]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="is_active"
                                                                    {{ $category->is_active ? 'checked' : '' }}
                                                                    onchange="this.form.submit()">
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <span
                                                        class="badge {{ $category->parent->category_name == 'Wallpapers' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $category->parent->category_name }}
                                                    </span>
                                                    <small class="text-muted">
                                                        @if ($category->parent->category_name != 'Live Wallpapers')
                                                            (Images only)
                                                        @else
                                                            (Videos/GIFs only)
                                                        @endif
                                                    </small>
                                                </div>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <strong>Wallpapers:</strong> {{ $category->wallpapers_count }}<br>
                                                        <strong>Created:</strong>
                                                        {{ $category->created_at->format('M d, Y') }}
                                                    </small>
                                                </p>
                                            </div>
                                            <div class="card-footer">
                                                <div class="btn-group w-100">
                                                    <a href="{{ route('admin.users.categories.wallpapers', [$user->id, $category->id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fas fa-eye"></i> View Wallpapers
                                                    </a>
                                                    <a href="{{ route('admin.users.categories.wallpapers.create', [$user->id, $category->id]) }}"
                                                        class="btn btn-success btn-sm">
                                                        <i class="fas fa-upload"></i> Upload
                                                    </a>
                                                    <form class="btn btn-danger btn-sm p-0 m-0"
                                                        action="{{ route('admin.users.categories.destroy', [$user->id, $category->id]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn bg-danger"
                                                            onclick="return confirm('Delete this category and all its wallpapers?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-folder fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No Categories Found</h4>
                                <p class="text-muted">This user hasn't created any categories yet.</p>
                                <a href="{{ route('admin.users.categories.create', $user->id) }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Create First Category
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
