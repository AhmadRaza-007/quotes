@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Upload Wallpaper for {{ $user->name }}</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.users.wallpapers.store', $user->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" name="category_id" id="category_id" required>
                                    <option value="" selected disabled>Select Category</option>
                                    @foreach (getCategories() as $item)
                                        <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Enter Title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Media Type</label>
                                <select class="form-select" name="media_type">
                                    <option value="">Auto detect</option>
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                    <option value="live">Live (GIF)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Wallpaper File</label>
                                <input type="file" name="file" class="form-control" accept="image/*,video/*" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thumbnail (optional, used for videos)</label>
                                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Upload Wallpaper</button>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
