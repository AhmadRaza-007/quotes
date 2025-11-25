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
                                                                <button type="button"
                                                                    class="btn btn-sm btn-warning px-1 send-notification-btn"
                                                                    title="Send Push Notification"
                                                                    data-wallpaper-id="{{ $wallpaper->id }}"
                                                                    data-category-id="{{ $category->id }}"
                                                                    data-parent-category-id="{{ $category?->parent_id }}"
                                                                    data-thumbnail-url="{{ $wallpaper->thumbnail_url }}"
                                                                    data-media-type="{{ $wallpaper->media_type }}"
                                                                    data-title="{{ $wallpaper->title ?: 'Untitled' }}">
                                                                    <i class="fas fa-bell"></i>
                                                                </button>
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

    <!-- Send Notification Modal -->
    <div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendNotificationModalLabel">Send Push Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="sendNotificationForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Wallpaper Thumbnail -->
                                <div class="mb-3">
                                    <label class="form-label">Wallpaper Thumbnail</label>
                                    <div class="thumbnail-preview">
                                        <img id="notificationThumbnail" src="" alt="Wallpaper Thumbnail"
                                             class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                                    </div>
                                    {{-- <small class="text-muted d-block mt-1">Thumbnail size: <span id="thumbnailSize">0 KB</span></small> --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Notification Details -->
                                <div class="mb-3">
                                    <label for="notificationTitle" class="form-label">Notification Title *</label>
                                    <input type="text" class="form-control" id="notificationTitle" name="title"
                                           placeholder="Enter notification title" required>
                                </div>

                                <div class="mb-3">
                                    <label for="notificationMessage" class="form-label">Notification Message *</label>
                                    <textarea class="form-control" id="notificationMessage" name="message"
                                              rows="3" placeholder="Enter notification message" required></textarea>
                                </div>

                                <!-- Hidden Fields -->
                                <input type="hidden" id="wallpaperId" name="wallpaper_id">
                                <input type="hidden" id="categoryId" name="category_id">
                                <input type="hidden" id="parentCategoryId" name="parent_id">
                                <input type="hidden" id="thumbnailUrl" name="thumbnail_url">
                                <input type="hidden" id="mediaType" name="media_type">

                                <!-- Display IDs -->
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Wallpaper ID: <span id="displayWallpaperId" class="fw-bold">-</span></small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Category ID: <span id="displayCategoryId" class="fw-bold">-</span></small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Parent ID: <span id="displayParentCategoryId" class="fw-bold">-</span></small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Media Type: <span id="displayMediaType" class="fw-bold">-</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-bell"></i> Send Notification
                        </button>
                    </div>
                </form>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('sendNotificationModal'));
            const sendNotificationForm = document.getElementById('sendNotificationForm');

            // Handle send notification button clicks
            document.querySelectorAll('.send-notification-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const wallpaperId = this.getAttribute('data-wallpaper-id');
                    const categoryId = this.getAttribute('data-category-id');
                    const parentCategoryId = this.getAttribute('data-parent-category-id');
                    const mediaType = this.getAttribute('data-media-type');
                    const thumbnailUrl = this.getAttribute('data-thumbnail-url');
                    const title = this.getAttribute('data-title');
                    // alert(parentCategoryId);x
                    // Set form values
                    document.getElementById('wallpaperId').value = wallpaperId;
                    document.getElementById('categoryId').value = categoryId;
                    document.getElementById('parentCategoryId').value = parentCategoryId;
                    document.getElementById('thumbnailUrl').value = thumbnailUrl;
                    document.getElementById('mediaType').value = mediaType;
                    document.getElementById('displayWallpaperId').textContent = wallpaperId;
                    document.getElementById('displayCategoryId').textContent = categoryId;
                    document.getElementById('displayParentCategoryId').textContent = parentCategoryId;
                    document.getElementById('displayMediaType').textContent = mediaType;

                    // Set default title
                    document.getElementById('notificationTitle').value = `${title}`;
                    document.getElementById('notificationMessage').value = ``;

                    // Load and check thumbnail size
                    loadThumbnail(thumbnailUrl);

                    // Set form action
                    sendNotificationForm.action = `{{ route('admin.notifications.send-wallpaper-notification') }}`;

                    // Show modal
                    modal.show();
                });
            });

            // Function to load thumbnail and check size
            function loadThumbnail(url) {
                const thumbnailImg = document.getElementById('notificationThumbnail');
                // const thumbnailSizeSpan = document.getElementById('thumbnailSize');

                // Show loading
                thumbnailImg.src = '';
                // thumbnailSizeSpan.textContent = 'Loading...';

                // Create image to check size
                const img = new Image();
                img.onload = function() {
                    thumbnailImg.src = url;

                    // Get image size by fetching it
                    fetch(url)
                        .then(response => response.blob())
                        .then(blob => {
                            const sizeInKB = Math.round(blob.size / 1024);
                            // thumbnailSizeSpan.textContent = `${sizeInKB} KB`;

                            // Add warning if size is too large
                            if (sizeInKB > 300) {
                                // thumbnailSizeSpan.innerHTML = `<span class="text-danger">${sizeInKB} KB - Too large! Should be < 300KB</span>`;
                            } else {
                                // thumbnailSizeSpan.innerHTML = `<span class="text-success">${sizeInKB} KB - OK</span>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error checking thumbnail size:', error);
                            // thumbnailSizeSpan.textContent = 'Error checking size';
                        });
                };

                img.onerror = function() {
                    thumbnailImg.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
                    // thumbnailSizeSpan.textContent = 'Image not available';
                };

                img.src = url;
            }

            // Handle form submission
            sendNotificationForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;

                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                // Submit form via AJAX
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showAlert('success', `Notification sent successfully! Sent to ${data.sent_count} devices.`);
                        modal.hide();
                    } else {
                        // Show error message
                        showAlert('danger', `Failed to send notification: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while sending the notification.');
                })
                .finally(() => {
                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
            });

            // Function to show alerts
            function showAlert(type, message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }

            // Reset form when modal is hidden
            document.getElementById('sendNotificationModal').addEventListener('hidden.bs.modal', function() {
                sendNotificationForm.reset();
                document.getElementById('notificationThumbnail').src = '';
                // document.getElementById('thumbnailSize').textContent = '0 KB';
            });
        });
    </script>
@endsection
