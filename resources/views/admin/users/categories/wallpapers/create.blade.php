<!-- resources/views/admin/users/categories/wallpapers/create.blade.php -->
@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Upload Wallpapers to "{{ $category->category_name }}"</h3>
                        <p class="mb-0 text-muted">Main Category: {{ $category->parent->category_name }}</p>
                    </div>
                    <div class="card-body">
                        <form method="post"
                            action="{{ route('admin.users.categories.wallpapers.store', [$user->id, $category->id]) }}"
                            enctype="multipart/form-data" id="uploadForm">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id }}">

                            <!-- Category Type Alert -->
                            <div
                                class="alert {{ $category->parent->category_name == 'Wallpapers' ? 'alert-info' : 'alert-warning' }}">
                                <strong>Category Type:</strong> {{ $category->parent->category_name }}<br>
                                @if ($category->parent->category_name == 'Wallpapers')
                                    <strong>Allowed:</strong> Static images only (JPG, PNG, WEBP)<br>
                                    <strong>Not Allowed:</strong> Videos, GIFs, or any animated content
                                @else
                                    <strong>Allowed:</strong> Videos and GIFs only (MP4, WebM, MOV, AVI, GIF)<br>
                                    <strong>Not Allowed:</strong> Static images (JPG, PNG, WEBP)
                                @endif
                            </div>

                            <!-- Multiple File Upload -->
                            <div class="mb-3">
                                <label class="form-label">Select Wallpapers (Multiple)</label>
                                {{-- <input type="file" name="files[]" class="form-control"
                                    accept="{{ $category->parent->category_name == 'Wallpapers' ? 'image/jpeg,image/png,image/webp' : 'video/*,.gif' }}"
                                    multiple required> --}}
                                <input type="file" name="files[]" class="form-control"
                                    accept="{{ $category->parent->category_name == 'Wallpapers' ? 'image/jpeg,image/png,image/webp' : 'video/*,.gif' }}"
                                    multiple required>
                                <small class="text-muted">
                                    @if ($category->parent->category_name == 'Wallpapers')
                                        <strong>Accepted formats:</strong> JPG, PNG, WEBP<br>
                                        <strong>Max files:</strong> 20 at once | <strong>Max size per file:</strong> 100MB
                                    @else
                                        <strong>Accepted formats:</strong> MP4, WebM, MOV, AVI, GIF<br>
                                        <strong>Max files:</strong> 10 at once | <strong>Max size per file:</strong> 100MB
                                    @endif
                                </small>
                            </div>

                            <!-- Selected Files Preview -->
                            <div class="mb-3" id="filePreview" style="display: none;">
                                <label class="form-label">Selected Files:</label>
                                <div id="fileList" class="border rounded p-2 bg-light"></div>
                            </div>

                            <!-- Thumbnail Upload for Live Wallpapers (Single file for all) -->
                            @if ($category->parent->category_name == 'Live Wallpapers')
                                <div class="mb-3">
                                    <label class="form-label">Default Thumbnail (for all videos)</label>
                                    <input type="file" name="thumbnail" class="form-control"
                                        accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted">Upload a default thumbnail image for all videos
                                        (optional)</small>
                                </div>
                            @endif

                            <!-- Progress Bar -->
                            <div class="mb-3" id="progressContainer" style="display: none;">
                                <div class="progress">
                                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small id="progressText" class="text-muted">Uploading...</small>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary" id="uploadBtn">
                                    <i class="fas fa-upload"></i> Upload Selected Files
                                </button>
                                <a href="{{ route('admin.users.categories.wallpapers', [$user->id, $category->id]) }}"
                                    class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.querySelector('input[name="files[]"]');
            const filePreview = document.getElementById('filePreview');
            const fileList = document.getElementById('fileList');
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const uploadBtn = document.getElementById('uploadBtn');

            // Show selected files
            fileInput.addEventListener('change', function() {
                fileList.innerHTML = '';

                if (this.files.length > 0) {
                    filePreview.style.display = 'block';

                    for (let i = 0; i < this.files.length; i++) {
                        const file = this.files[i];
                        const fileItem = document.createElement('div');
                        fileItem.className = 'd-flex justify-content-between align-items-center mb-1';
                        fileItem.innerHTML = `
                    <span>${file.name}</span>
                    <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                `;
                        fileList.appendChild(fileItem);
                    }

                    // Update button text with file count
                    uploadBtn.innerHTML = `<i class="fas fa-upload"></i> Upload ${this.files.length} Files`;
                } else {
                    filePreview.style.display = 'none';
                    uploadBtn.innerHTML = `<i class="fas fa-upload"></i> Upload Selected Files`;
                }
            });

            // Show progress during upload
            document.getElementById('uploadForm').addEventListener('submit', function() {
                progressContainer.style.display = 'block';
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Uploading...`;
            });
        });
    </script>
@endsection
