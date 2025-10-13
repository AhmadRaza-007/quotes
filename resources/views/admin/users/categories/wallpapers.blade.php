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
                                    | Showing: {{ $wallpapers->count() }} per page
                                </span>
                            </div>
                        </div>

                        @if ($wallpapers->count() > 0)
                            <!-- Wallpapers Grid - 10 rows with 10 per row -->
                            <div class="wallpapers-grid">
                                @php
                                    $wallpapersArray = $wallpapers->items();
                                    $rows = array_chunk($wallpapersArray, 10); // Split into rows of 10
                                @endphp

                                @foreach ($rows as $rowIndex => $row)
                                    <div class="row mb-4 wallpaper-row">
                                        @foreach ($row as $wallpaper)
                                            <div class="col-md-1-2 col-sm-3 col-6 mb-3">
                                                <div class="card wallpaper-card h-100">
                                                    <img src="{{ $wallpaper->media_type == 'video' ? $wallpaper->thumbnail_url : $wallpaper->file_url }}"
                                                        class="card-img-top" style="height: 120px; object-fit: cover;"
                                                        alt="{{ $wallpaper->title }}"
                                                        title="{{ $wallpaper->title ?: 'Untitled' }}">
                                                    <div class="card-body p-2 d-flex flex-column">
                                                        <div class="wallpaper-info">
                                                            <small class="text-muted d-block">
                                                                <strong>ID:</strong> {{ $wallpaper->id }}
                                                            </small>
                                                            <small class="text-truncate d-block"
                                                                title="{{ $wallpaper->title ?: 'Untitled' }}">
                                                                {{ Str::limit($wallpaper->title ?: 'Untitled', 15) }}
                                                            </small>
                                                            <small class="text-muted d-block">
                                                                {{ strtoupper($wallpaper->media_type) }}
                                                            </small>
                                                        </div>
                                                        <div class="mt-auto">
                                                            <div class="btn-group w-100" role="group">
                                                                <a href="{{ $wallpaper->file_url }}" target="_blank"
                                                                    class="btn btn-sm btn-info px-1" title="View">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <form
                                                                    action="{{ route('admin.users.wallpapers.delete', [$user->id, $wallpaper->id]) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-danger px-1"
                                                                        onclick="return confirm('Delete wallpaper ID: {{ $wallpaper->id }}?')"
                                                                        title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Add horizontal rule after each row except the last -->
                                    @if (!$loop->last)
                                        <hr class="my-3">
                                    @endif
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

    <style>
        /* Custom CSS for 10 columns layout */
        .col-md-1-2 {
            flex: 0 0 10%;
            max-width: 10%;
        }

        .wallpaper-card {
            transition: transform 0.2s;
        }

        .wallpaper-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .wallpaper-info {
            font-size: 0.75rem;
        }

        .wallpaper-row {
            border-radius: 5px;
            padding: 5px 0;
        }

        @media (max-width: 1200px) {
            .col-md-1-2 {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        @media (max-width: 768px) {
            .col-md-1-2 {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }

        @media (max-width: 576px) {
            .col-md-1-2 {
                flex: 0 0 33.333%;
                max-width: 33.333%;
            }
        }
    </style>
@endsection
