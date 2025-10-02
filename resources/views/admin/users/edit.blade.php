{{-- <!-- resources/views/admin/users/edit.blade.php -->
@extends('app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- User Information Column -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Edit User Information</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="user_type" class="form-label">User Type</label>
                                <select class="form-select" id="user_type" name="user_type" required>
                                    <option value="0" {{ $user->user_type == '0' ? 'selected' : '' }}>User</option>
                                    <option value="1" {{ $user->user_type == '1' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Update User</button>
                        </form>
                    </div>
                </div>

                <!-- Password Reset Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Reset Password</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required
                                    minlength="8">
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="new_password_confirmation"
                                    name="new_password_confirmation" required>
                            </div>

                            <button type="submit" class="btn btn-warning">Reset Password</button>
                        </form>
                    </div>
                </div>

                <!-- Create Category Section -->
                <div class="card">
                    <div class="card-header">
                        <h4>Create Category</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.categories.store', $user->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="parent_id" class="form-label">Parent Category</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">No Parent (Main Category)</option>
                                    @foreach ($user->categories->where('parent_id', null) as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">Create Category</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User Categories Table Column -->
            <div class="col-md-8">
                <!-- Categories Table Section -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>User Categories</h4>
                        <span class="badge bg-primary">{{ $user->categories->count() }} categories</span>
                    </div>
                    <div class="card-body">
                        @if ($user->categories->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category Name</th>
                                            <th>Type</th>
                                            <th>Wallpapers</th>
                                            <th>Subcategories</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->categories->where('parent_id', null) as $category)
    <tr>
        <td>
            <strong>
                <a href="{{ route('admin.users.categories.wallpapers', [$user->id, $category->id]) }}" class="text-decoration-none">
                    {{ $category->category_name }}
                </a>
            </strong>
        </td>
        <td>
            <span class="badge bg-info">Main Category</span>
        </td>
        <td>
            <!-- Use direct_wallpapers_count instead of wallpapers_count -->
            <span class="badge bg-primary">{{ $category->direct_wallpapers_count ?? $category->wallpapers_count }} wallpapers</span>
        </td>
        <td>
            @if ($category->children_count > 0)
                <span class="badge bg-secondary">{{ $category->children_count }} subcategories</span>
            @else
                <span class="text-muted">None</span>
            @endif
        </td>
        <td>{{ $category->created_at->format('M d, Y') }}</td>
        <td>
            <div class="btn-group">
                <a href="{{ route('admin.users.categories.wallpapers', [$user->id, $category->id]) }}" class="btn btn-sm btn-primary">View Wallpapers</a>
                <a href="{{ route('admin.users.categories.wallpapers.create', [$user->id, $category->id]) }}" class="btn btn-sm btn-success">Upload</a>
                <form action="{{ route('admin.users.categories.destroy', [$user->id, $category->id]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category and all its wallpapers?')">Delete</button>
                </form>
            </div>
        </td>
    </tr>

    <!-- Show subcategories indented -->
    @foreach ($category->children as $subcategory)
        <tr>
            <td style="padding-left: 40px;">
                <a href="{{ route('admin.users.categories.wallpapers', [$user->id, $subcategory->id]) }}" class="text-decoration-none">
                    ↳ {{ $subcategory->category_name }}
                </a>
            </td>
            <td>
                <span class="badge bg-warning">Subcategory</span>
            </td>
            <td>
                <!-- Use direct_wallpapers_count for subcategories too -->
                <span class="badge bg-primary">{{ $subcategory->direct_wallpapers_count ?? $subcategory->wallpapers_count }} wallpapers</span>
            </td>
            <td>
                <span class="text-muted">-</span>
            </td>
            <td>{{ $subcategory->created_at->format('M d, Y') }}</td>
            <td>
                <div class="btn-group">
                    <a href="{{ route('admin.users.categories.wallpapers', [$user->id, $subcategory->id]) }}" class="btn btn-sm btn-primary">View</a>
                    <a href="{{ route('admin.users.categories.wallpapers.create', [$user->id, $subcategory->id]) }}" class="btn btn-sm btn-success">Upload</a>
                    <form action="{{ route('admin.users.categories.destroy', [$user->id, $subcategory->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this subcategory?')">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
@endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No categories created by this user yet.</p>
                                <p class="text-muted">Use the form on the left to create the first category.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h3>{{ $user->categories->count() }}</h3>
                                        <p class="mb-0">Total Categories</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h3>{{ $user->wallpapers->count() }}</h3>
                                        <p class="mb-0">Total Wallpapers</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h3>{{ $user->categories->where('parent_id', '!=', null)->count() }}</h3>
                                        <p class="mb-0">Subcategories</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}


<!-- resources/views/admin/users/edit.blade.php -->
@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Edit User Information</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="user_type" class="form-label">User Type</label>
                                <select class="form-select @error('user_type') is-invalid @enderror" id="user_type"
                                    name="user_type" required>
                                    <option value="0"
                                        {{ old('user_type', $user->user_type) == '0' ? 'selected' : '' }}>User</option>
                                    <option value="1"
                                        {{ old('user_type', $user->user_type) == '1' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('user_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Update User</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
                        </form>
                    </div>
                </div>

                <!-- Password Reset Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Reset Password</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                    id="new_password" name="new_password" required minlength="8">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="new_password_confirmation"
                                    name="new_password_confirmation" required>
                            </div>

                            <button type="submit" class="btn btn-warning">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
