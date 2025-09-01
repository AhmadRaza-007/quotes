@extends('app')
@section('content')
    <style>
        textarea:focus,
        textarea.form-control:focus,
        input.form-control:focus,
        input[type=text]:focus,
        input[type=password]:focus,
        input[type=email]:focus,
        input[type=number]:focus,
        [type=text].form-control:focus,
        [type=password].form-control:focus,
        [type=email].form-control:focus,
        [type=tel].form-control:focus,
        [contenteditable].form-control:focus {
            box-shadow: inset 0 -1px 0 #ddd;
        }
    </style>
    {{-- <link rel="stylesheet" href="{{ asset('dist/tinymce/skins/content/tinymce-5/content.min.css') }}"> --}}
    <main>
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-center">
                <h1 class="mt-4">Themes</h1>
                <p class="mt-4">Count: </p>
            </div>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item active">Themes</li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Themes
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="dropdown">
                            <ul class="dropdown-menu">
                                {{-- @foreach ($languages as $item)
                                    <li>
                                        <a class="dropdown-item" href="{{ url('') . '/theme/' . $id . '/' . $item->id }}">
                                            {{ $item->language }}
                                        </a>
                                    </li>
                                @endforeach --}}
                                {{-- @for ($index = 0; $index < count($languages); $index++)
                                    <li>
                                        <a class="dropdown-item" href="{{ url('') . '/theme/' . $id . '/' . $languages[$index]->id }}">
                                            {{ $languages[$index] }}
                                        </a>
                                    </li>
                                @endfor --}}
                            </ul>
                        </div>

                        <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"
                            class="btn d-inline-flex btn-sm btn-primary mx-1">Add
                            Theme</button>
                    </div>
                    @error('*')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Add Theme</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <form method="post" action="{{ route('themes.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body" id="modal_body_create">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Theme Category</label>
                                            <select class="form-select" name="category_id" id="category_id"
                                                aria-label="Default select example" required>
                                                <option value="" selected disabled>Select Parent Category</option>
                                                @foreach (getCategories() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Theme Title</label>
                                            <input type="text" name="title" class="form-control" id="theme_number"
                                                placeholder="Enter Title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="media_type" class="form-label">Media Type</label>
                                            <select class="form-select" name="media_type" id="media_type">
                                                <option value="">Auto detect</option>
                                                <option value="image">Image</option>
                                                <option value="video">Video</option>
                                                <option value="live">Live (GIF)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 flex-column">
                                            <label class="form-label">Theme File</label>
                                            <input type="file" id="theme" name="theme" class="form-control" accept="image/*,video/*" required>
                                            <small class="text-muted">Images (JPG, PNG, GIF) or videos (MP4, WebM, MOV). Max 50MB.</small>
                                        </div>
                                        <div class="mb-3 flex-column">
                                            <label class="form-label">Thumbnail (optional, used for videos)</label>
                                            <input type="file" id="thumbnail" name="thumbnail" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        {{-- <div class="add_field">
                                            <button type="button" class="btn d-inline-flex btn-sm btn-success mx-1"
                                                onclick="addField()">Add</button>
                                        </div> --}}
                                        <div class="buttons">
                                            <button type="button" class="btn d-inline-flex btn-sm btn-secondary mx-1"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit"
                                                class="btn d-inline-flex btn-sm btn-primary mx-1">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal2 -->
                    <div class="modal fade" id="updateModalLabel" tabindex="-1" aria-labelledby="updateModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel">Update theme</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('themes.update') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="theme_id" class="form-control" id="theme_hidden" required>
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Theme Category</label>
                                            <select class="form-select" name="category_id" id="category_id_edit" required>
                                                <option id="category_edit" value=""></option>
                                                @foreach (getCategories() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Theme Title</label>
                                            <input type="text" name="title" class="form-control" id="title" placeholder="Enter Title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="media_type_edit" class="form-label">Media Type</label>
                                            <select class="form-select" name="media_type" id="media_type_edit">
                                                <option value="">Auto detect</option>
                                                <option value="image">Image</option>
                                                <option value="video">Video</option>
                                                <option value="live">Live (GIF)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 flex-column">
                                            <label class="form-label">Replace Theme File (optional)</label>
                                            <input type="file" id="theme_edit" name="theme" class="form-control" accept="image/*,video/*">
                                        </div>
                                        <div class="mb-3 flex-column">
                                            <label class="form-label">Thumbnail (optional, used for videos)</label>
                                            <input class="form-control" name="thumbnail" type="file" id="thumbnail_edit" accept="image/*">
                                            <img src="" id="thumbnail_preview" height="100" width="100" class="mt-2" style="display:none;">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button onclick="save2()" type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Modal3 Push Notification -->
                    <div class="modal fade" id="NotifyModel" tabindex="-1" aria-labelledby="NotifyModel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="NotifyModel">Theme Notifications </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form method="post" action="" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="wallpaper_category" class="form-label">Category Name</label>
                                            <select class="form-select" name="category_id" id="wallpaper_category"
                                                aria-label="Default select example" required>
                                                <option>Select Parent Category</option>
                                                {{-- @foreach ($book as $category) --}}
                                                {{-- <option value="{{ $book->id }}">{{ $book->name }}</option> --}}
                                                {{-- @endforeach --}}
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notify_title_edit" class="form-label">Title </label>
                                            <input type="text" name="title" class="form-control"
                                                id="notify_title_edit" placeholder="Enter Title">
                                            <input name="wallpaper_id" type="hidden" id="notify_wallpaper_id">
                                        </div>
                                        <div class="mb-3">
                                            <label for="notify_wallpaper_image_url" class="form-label">Theme Image
                                                Url</label>
                                            <input type="text" name="wallpaper_image_url" class="form-control"
                                                id="notify_wallpaper_image_url" placeholder="Enter Theme imageurl">
                                        </div>
                                        <div class="mb-3">
                                            <label for="wallpaper_image" class="form-label">Theme Image</label>
                                            <input class="form-control" name="wallpaper_image" type="file"
                                                id="wallpaper_edit">
                                            <img src="" id="NotifywallpaperImage_edit" height="100"
                                                width="100">
                                        </div>
                                        <hr>
                                        <h5 class="text-center">Tooltips in a modal</h5>
                                        <div class="mb-3">
                                            <label for="notify_title" class="form-label">Notification Title </label>
                                            <input type="text" name="notify_title" class="form-control"
                                                id="notify_title" placeholder="Enter Notification Title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notify_body" class="form-label">Notification Body </label>
                                            <textarea type="text" name="body" class="form-control" id="notify_body" placeholder="Enter Notification Body"
                                                required></textarea>
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
                                <th>Theme Title</th>
                                <th>Type</th>
                                <th>Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($themes as $key => $theme)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $theme->name }}</td>
                                    <td>{{ strtoupper($theme->media_type ?? 'image') }}</td>
                                    <td>
                                        @if(($theme->media_type ?? 'image') === 'video')
                                            <video width="140" height="100" controls preload="metadata" @if($theme->thumbnail) poster="{{ asset($theme->thumbnail) }}" @endif>
                                                <source src="{{ asset($theme->theme) }}" type="{{ $theme->mime_type ?? 'video/mp4' }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <img src="{{ asset($theme->theme) }}" alt="" width="140" style="height: 100px; object-fit: cover;">
                                        @endif
                                    </td>
                                    <td style="min-width: 11rem;">
                                        <a class="btn btn-sm" onclick="editCategory({{ $theme->id }})">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a onclick="" class="btn btn-sm">
                                            <i class="fas fa-bell"></i>
                                        </a>
                                        <a href="{{ url('/theme/delete/' . $theme->id) }}"
                                            class="btn delete btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end align-items-center">
                        {{ $themes->Links() }}
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection
@section('script')
    {{-- <script src="{{ asset('dist/tinymce/plugins/template/plugin.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('dist/tinymce/tinymce.min.js') }}"></script> --}}
    {{-- <script>
        function save() {
            tinyMCE.triggerSave();
        }
        tinymce.init({
            min_height: 200,
            max_height: 500,
            selector: '#tiny',
            plugins: "advlist anchor autolink autoresize autosave charmap code codesample directionality emoticons fullscreen help image importcss insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars wordcount",
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code | insertdatetime link unlink image table hr | fullscreen searchreplace visualblocks visualchars code help',
        });
    </script>
    <script>
        function save2() {
            tinyMCE.triggerSave();
        }
        tinymce.init({
            min_height: 200,
            max_height: 500,
            selector: '#tiny2',
            plugins: "advlist anchor autolink autoresize autosave charmap code codesample directionality emoticons fullscreen help image importcss insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars wordcount",
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code | insertdatetime link unlink image table hr | fullscreen searchreplace visualblocks visualchars code help',
        });
    </script> --}}
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
    <script>
        $("#thumbnail_edit").on('change', function(e) {
            $("#thumbnail_preview").attr("src", URL.createObjectURL(e.target.files[0])).show();
        })

        function editCategory(id) {
            $.ajax({
                url: "{{ url('/theme/edit') }}" + "/" + id,
                success: function(data) {
                    $("#title").val(data.name);
                    $("#theme_hidden").val(data.id);
                    $("#category_edit").val(data.category_id);
                    $("#category_edit").text(data.category?.category_name ?? '');
                    $("#category_id_edit").val(data.category_id);
                    if (data.media_type) {
                        $("#media_type_edit").val(data.media_type);
                    } else {
                        $("#media_type_edit").val('');
                    }
                    if (data.thumbnail) {
                        $("#thumbnail_preview").attr('src', data.thumbnail.startsWith('http') ? data.thumbnail : ('{{ url('/') }}/' + data.thumbnail)).show();
                    } else {
                        $("#thumbnail_preview").hide();
                    }
                    $("#updateModalLabel").modal("show");
                }
            })
        }

        function notify(id) {
            $.ajax({
                url: "{{ url('NotifyDetail/edit') }}" + "/" + id,
                success: function(data) {
                    console.log(data);
                    $("#notify_wallpaper_id").val(data.id);
                    $("#wallpaper_category").val(data.category_id);
                    $("#notify_title_edit").val(data.title);
                    $("#notify_wallpaper_image_url").val(data.wallpaper_image_url);
                    $("#NotifywallpaperImage_edit").attr("src", data.wallpaper_image);
                    $("#NotifyModel").modal("show");
                }
            })
        }
    </script>
@endsection
