{{-- @extends('app')
@section('content')
    <main>
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-center">
                <h1 class="mt-4">Wallpapers</h1>
                <p class="mt-4">Count: </p>
            </div>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item active">Wallpapers</li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Wallpapers
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <div></div>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"
                            class="btn d-inline-flex btn-sm btn-primary mx-1">Add Wallpaper</button>
                    </div>
                    @error('*')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <!-- Create Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Add Wallpaper</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <form method="post" action="{{ route('wallpapers.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body" id="modal_body_create">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category</label>
                                            <select class="form-select" name="category_id" id="category_id" required>
                                                <option value="" selected disabled>Select Parent Category</option>
                                                @foreach (getCategories() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="title" class="form-control"
                                                placeholder="Enter Title">
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
                                            <input type="file" name="file" class="form-control"
                                                accept="image/*,video/*" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Thumbnail (optional, used for videos)</label>
                                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn d-inline-flex btn-sm btn-secondary mx-1"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit"
                                            class="btn d-inline-flex btn-sm btn-primary mx-1">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Update Modal -->
                    <div class="modal fade" id="updateModalLabel" tabindex="-1" aria-labelledby="updateModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel">Update Wallpaper</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('wallpapers.update') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="wp_id" required>
                                        <div class="mb-3">
                                            <label class="form-label">Category</label>
                                            <select class="form-select" name="category_id" id="category_id_edit"
                                                required>
                                                <option id="category_edit" value=""></option>
                                                @foreach (getCategories() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="title" id="wp_title" class="form-control"
                                                placeholder="Enter Title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Media Type</label>
                                            <select class="form-select" name="media_type" id="wp_media_type">
                                                <option value="">Auto detect</option>
                                                <option value="image">Image</option>
                                                <option value="video">Video</option>
                                                <option value="live">Live (GIF)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Replace File (optional)</label>
                                            <input type="file" name="file" class="form-control"
                                                accept="image/*,video/*">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Thumbnail (optional, used for videos)</label>
                                            <input type="file" name="thumbnail" class="form-control"
                                                accept="image/*">
                                            <img src="" id="thumb_preview" height="100" width="100"
                                                class="mt-2" style="display:none;">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <table class="table table-hover table-spaced" id="datatablesSimple">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wallpapers as $key => $wp)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><a href="{{ $wp->file_url }}" target="_blank">{{ $wp->title }}</a></td>
                                    <td>{{ strtoupper($wp->media_type ?? 'IMAGE') }}</td>
                                    <td>
                                        <img src="{{ $wp->media_type == 'video' ? $wp->thumbnail_url : $wp->file_url }}"
                                            width="140" style="height: 100px; object-fit: cover;">
                                    </td>
                                    <td style="min-width: 11rem;">
                                        <a class="btn btn-sm" onclick="editWallpaper({{ $wp->id }})">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('wallpapers.delete', $wp->id) }}" class="btn delete btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end align-items-center">
                        {{ $wallpapers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('script')
    <script>
        // function editWallpaper(id) {
        //     $.ajax({
        //         url: "{{ url('/wallpapers/edit') }}/" + id,
        //         // url: "{{ url('/wallpapers/edit/') }}" + id,

        //         success: function(data) {
        //             $('#wp_id').val(data.id);
        //             $('#wp_title').val(data.title);
        //             $('#category_edit').val(data.category_id);
        //             $('#category_edit').text(data.category?.category_name ?? '');
        //             $('#category_id_edit').val(data.category_id);
        //             $('#wp_media_type').val(data.media_type ?? '');
        //             if (data.thumbnail) {
        //                 $('#thumb_preview').attr('src', data.thumbnail.startsWith('http') ? data.thumbnail : (
        //                     '{{ url('/') }}/' + data.thumbnail)).show();
        //             } else {
        //                 $('#thumb_preview').hide();
        //             }
        //             $('#updateModalLabel').modal('show');
        //         }
        //     })
        // }

        function editWallpaper(id) {
            $.ajax({
                url: "{{ route('wallpapers.edit', ':id') }}".replace(':id', id),
                success: function(data) {
                    $('#wp_id').val(data.id);
                    $('#wp_title').val(data.title);
                    $('#category_edit').val(data.category_id);
                    $('#category_edit').text(data.category?.category_name ?? '');
                    $('#category_id_edit').val(data.category_id);
                    $('#wp_media_type').val(data.media_type ?? '');
                    if (data.thumbnail) {
                        $('#thumb_preview').attr(
                            'src',
                            data.thumbnail.startsWith('http') ?
                            data.thumbnail :
                            '{{ url('/') }}/' + data.thumbnail
                        ).show();

                    } else {
                        $('#thumb_preview').hide();
                    }
                    $('#updateModalLabel').modal('show');
                }
            })
        }
    </script>
@endsection --}}



@extends('app')
@section('content')
    <main>
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-center">
                <h1 class="mt-4">Wallpapers</h1>
                <p class="mt-4">Count: </p>
            </div>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item active">Wallpapers</li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Wallpapers
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <div></div>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"
                            class="btn d-inline-flex btn-sm btn-primary mx-1">Add Wallpaper</button>
                    </div>
                    @error('*')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <!-- Create Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Add Wallpaper</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <form method="post" action="{{ route('wallpapers.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body" id="modal_body_create">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Category</label>
                                            <select class="form-select" name="category_id" id="category_id" required>
                                                <option value="" selected disabled>Select Parent Category</option>
                                                @foreach (getCategories() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Add this after the category selection div in the create modal -->
                                        <div class="mb-3">
                                            <label for="owner_user_id" class="form-label">Assign to User (Optional)</label>
                                            <select class="form-select" name="owner_user_id" id="owner_user_id">
                                                <option value="">Select User (Leave empty for admin)</option>
                                                @foreach (getUsers() as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}
                                                        ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="title" class="form-control"
                                                placeholder="Enter Title">
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
                                            <input type="file" name="file" class="form-control"
                                                accept="image/*,video/*" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Thumbnail (optional, used for videos)</label>
                                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn d-inline-flex btn-sm btn-secondary mx-1"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit"
                                            class="btn d-inline-flex btn-sm btn-primary mx-1">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Update Modal -->
                    <div class="modal fade" id="updateModalLabel" tabindex="-1" aria-labelledby="updateModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel">Update Wallpaper</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('wallpapers.update') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="wp_id" required>
                                        <div class="mb-3">
                                            <label class="form-label">Category</label>
                                            <select class="form-select" name="category_id" id="category_id_edit"
                                                required>
                                                <option id="category_edit" value=""></option>
                                                @foreach (getCategories() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Add this after the category selection div in the update modal -->
                                        <div class="mb-3">
                                            <label class="form-label">Assign to User (Optional)</label>
                                            <select class="form-select" name="owner_user_id" id="wp_owner_user_id">
                                                <option value="">Select User (Leave empty for admin)</option>
                                                @foreach (getUsers() as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}
                                                        ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="title" id="wp_title" class="form-control"
                                                placeholder="Enter Title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Media Type</label>
                                            <select class="form-select" name="media_type" id="wp_media_type">
                                                <option value="">Auto detect</option>
                                                <option value="image">Image</option>
                                                <option value="video">Video</option>
                                                <option value="live">Live (GIF)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Replace File (optional)</label>
                                            <input type="file" name="file" class="form-control"
                                                accept="image/*,video/*">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Thumbnail (optional, used for videos)</label>
                                            <input type="file" name="thumbnail" class="form-control"
                                                accept="image/*">
                                            <img src="" id="thumb_preview" height="100" width="100"
                                                class="mt-2" style="display:none;">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <table class="table table-hover table-spaced" id="datatablesSimple">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Title</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wallpapers as $key => $wp)
                                <code>{{ $wp }}</code>
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><a href="{{ $wp->file_url }}" target="_blank">{{ $wp->title }}</a></td>
                                    <td>
                                        @if ($wp->owner_user_id)
                                            {{ $wp->owner->name ?? 'Unknown User' }}
                                        @else
                                            <span class="badge bg-primary">Admin</span>
                                        @endif
                                    </td>
                                    <td>{{ strtoupper($wp->media_type ?? 'IMAGE') }}</td>
                                    <td>
                                        {{-- @if (($wp->media_type ?? 'image') === 'video')
                                            <video width="140" height="100" controls preload="metadata"
                                                @if ($wp->thumbnail) poster="{{ asset($wp->thumbnail) }}" @endif>
                                                <source src="{{ asset($wp->file_path) }}"
                                                    type="{{ $wp->mime_type ?? 'video/mp4' }}">
                                            </video>
                                        @else --}}
                                        <img src="{{ $wp->media_type == 'video' ? $wp->thumbnail_url : $wp->file_url }}"
                                            width="140" style="height: 100px; object-fit: cover;">
                                        {{-- @endif --}}
                                    </td>
                                    <td style="min-width: 11rem;">
                                        <a class="btn btn-sm" onclick="editWallpaper({{ $wp->id }})">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('wallpapers.delete', $wp->id) }}" class="btn delete btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end align-items-center">
                        {{ $wallpapers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('script')
    <script>
        function editWallpaper(id) {
            $.ajax({
                url: "{{ route('wallpapers.edit', ':id') }}".replace(':id', id),
                success: function(data) {
                    $('#wp_id').val(data.id);
                    $('#wp_title').val(data.title);
                    $('#category_edit').val(data.category_id);
                    $('#category_edit').text(data.category?.category_name ?? '');
                    $('#category_id_edit').val(data.category_id);
                    $('#wp_media_type').val(data.media_type ?? '');
                    $('#wp_owner_user_id').val(data.owner_user_id ?? ''); // Add this line
                    if (data.thumbnail) {
                        $('#thumb_preview').attr(
                            'src',
                            data.thumbnail.startsWith('http') ?
                            data.thumbnail :
                            '{{ url('/') }}/' + data.thumbnail
                        ).show();
                    } else {
                        $('#thumb_preview').hide();
                    }
                    $('#updateModalLabel').modal('show');
                }
            })
        }
    </script>
@endsection
