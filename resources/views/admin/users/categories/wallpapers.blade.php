{{-- <!-- resources/views/admin/users/categories/wallpapers.blade.php -->
@extends('app')

@section('content')
    <div class="container-fluid">
        <!-- Add this to resources/views/admin/users/categories/wallpapers.blade.php -->
        @if (session('upload_errors'))
            <div class="alert alert-warning">
                <h6>Upload Errors:</h6>
                <ul class="mb-0">
                    @foreach (session('upload_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h3>Wallpapers in "{{ $category->category_name }}"</h3>
                            <p class="mb-0 text-muted">
                                User: {{ $user->name }} |
                                Main Category: <span
                                    class="badge {{ $category->parent->category_name == 'Wallpapers' ? 'bg-success' : 'bg-warning' }}">{{ $category->parent->category_name }}</span>
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.users.categories.wallpapers.create', [$user->id, $category->id]) }}"
                                class="btn btn-primary">
                                <i class="fas fa-plus"></i> Upload Wallpaper
                            </a>
                            <a href="{{ route('admin.users.categories.index', $user->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Categories
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($wallpapers->count() > 0)
                            <div class="row">
                                @foreach ($wallpapers as $wallpaper)
                                    <div class="col-md-3 mb-4">
                                        <div class="card h-100">
                                            <img src="{{ $wallpaper->media_type == 'video' ? $wallpaper->thumbnail_url : $wallpaper->file_url }}"
                                                class="card-img-top" style="height: 200px; object-fit: cover;"
                                                alt="{{ $wallpaper->title }}">
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="card-title">{{ $wallpaper->title ?: 'Untitled' }}</h6>
                                                <div class="mt-auto">
                                                    <p class="card-text">
                                                        <small class="text-muted">
                                                            <strong>Type:</strong>
                                                            {{ strtoupper($wallpaper->media_type) }}<br>
                                                            <strong>Uploaded:</strong>
                                                            {{ $wallpaper->created_at->format('M d, Y') }}
                                                        </small>
                                                    </p>
                                                    <div class="btn-group w-100">
                                                        <a href="{{ $wallpaper->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.users.wallpapers.delete', [$user->id, $wallpaper->id]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Delete this wallpaper?')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $wallpapers->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No Wallpapers Found</h4>
                                <p class="text-muted">No wallpapers uploaded to this category yet.</p>
                                <a href="{{ route('admin.users.categories.wallpapers.create', [$user->id, $category->id]) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Upload First Wallpaper
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}


<!-- resources/views/admin/users/categories/wallpapers.blade.php -->
@extends('app')

@section('content')
    <div class="container-fluid">
        <!-- Add this to resources/views/admin/users/categories/wallpapers.blade.php -->
        @if (session('upload_errors'))
            <div class="alert alert-warning">
                <h6>Upload Errors:</h6>
                <ul class="mb-0">
                    @foreach (session('upload_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h3>Wallpapers in "{{ $category->category_name }}"</h3>
                            <p class="mb-0 text-muted">
                                User: {{ $user->name }} |
                                Main Category: <span
                                    class="badge {{ $category->parent->category_name == 'Wallpapers' ? 'bg-success' : 'bg-warning' }}">{{ $category->parent->category_name }}</span>
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.users.categories.wallpapers.create', [$user->id, $category->id]) }}"
                                class="btn btn-primary">
                                <i class="fas fa-plus"></i> Upload Wallpaper
                            </a>
                            <a href="{{ route('admin.users.categories.index', $user->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Categories
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form method="GET"
                                    action="{{ route('admin.users.categories.wallpapers', [$user->id, $category->id]) }}">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search by wallpaper ID..." value="{{ request('search') }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        @if (request('search'))
                                            <a href="{{ route('admin.users.categories.wallpapers', [$user->id, $category->id]) }}"
                                                class="btn btn-outline-danger">
                                                <i class="fas fa-times"></i> Clear
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="text-muted">
                                    Total: {{ $wallpapers->total() }} wallpapers
                                    @if (request('search'))
                                        | Search results: {{ $wallpapers->count() }} found
                                    @endif
                                </span>
                            </div>
                        </div>

                        @if ($wallpapers->count() > 0)
                            <div class="row">
                                @foreach ($wallpapers as $wallpaper)
                                    <div class="col-md-3 mb-4">
                                        <div class="card h-100">
                                            <img src="{{ $wallpaper->media_type == 'video' ? $wallpaper->thumbnail_url : $wallpaper->file_url }}"
                                                class="card-img-top" style="height: 200px; object-fit: cover;"
                                                alt="{{ $wallpaper->title }}">
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="card-title">
                                                    <span class="badge bg-secondary me-2">ID: {{ $wallpaper->id }}</span>
                                                    {{ $wallpaper->title ?: 'Untitled' }}
                                                </h6>
                                                <div class="mt-auto">
                                                    <p class="card-text">
                                                        <small class="text-muted">
                                                            <strong>Type:</strong>
                                                            {{ strtoupper($wallpaper->media_type) }}<br>
                                                            <strong>Uploaded:</strong>
                                                            {{ $wallpaper->created_at->format('M d, Y') }}<br>
                                                            <strong>Size:</strong>
                                                            {{ number_format($wallpaper->file_size / 1024 / 1024, 2) }} MB
                                                        </small>
                                                    </p>
                                                    <div class="btn-group w-100">
                                                        <a href="{{ $wallpaper->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.users.wallpapers.delete', [$user->id, $wallpaper->id]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Delete this wallpaper?')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $wallpapers->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">
                                    @if (request('search'))
                                        No Wallpapers Found
                                    @else
                                        No Wallpapers Found
                                    @endif
                                </h4>
                                <p class="text-muted">
                                    @if (request('search'))
                                        No wallpapers found matching ID "{{ request('search') }}"
                                    @else
                                        No wallpapers uploaded to this category yet.
                                    @endif
                                </p>
                                @if (!request('search'))
                                    <a href="{{ route('admin.users.categories.wallpapers.create', [$user->id, $category->id]) }}"
                                        class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Upload First Wallpaper
                                    </a>
                                @else
                                    <a href="{{ route('admin.users.categories.wallpapers', [$user->id, $category->id]) }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to All Wallpapers
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
