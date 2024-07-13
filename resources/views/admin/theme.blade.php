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
                                                <option value="1" selected disabled>Select Parent Category</option>
                                                @foreach (getCategories() as $item)
                                                    <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Theme Title</label>
                                            <input type="text" name="title" class="form-control" id="theme_number"
                                                placeholder="Enter Number" required>
                                        </div>
                                        {{-- <div class="mb-3">
                                            <label for="category" class="form-label">theme</label>
                                            <textarea name="theme" id="tiny"></textarea>
                                        </div> --}}
                                        <div class="mb-3 flex-column">
                                            <label for="inputEmail4" class="form-label">Theme</label>
                                            <input type="file" id="theme" name="theme" class="form-control"
                                                style="width: 100%">
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
                                        <input type="hidden" name="theme_id" class="form-control" id="theme_hidden"
                                            placeholder="Enter Number" required>
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Theme Category</label>
                                            <select class="form-select" name="category_id"
                                                aria-label="Default select example" required>
                                                <option id="category_edit" value=""></option>
                                            </select>
                                        </div>
                                        {{-- <div class="mb-3">
                                            <label for="category_id" class="form-label">Language</label>
                                            <select class="form-select" name="language_id" id="surah_id"
                                                aria-label="Default select example" required>
                                                <option disabled selected>Select Language</option>
                                                @foreach ($languages as $language)
                                                    <option value="{{ $language->id }}">{{ $language->language }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                        {{-- <input type="hidden" name="theme_id" value="" id="theme_id_hidden">
                                        <input type="hidden" name="category_id" value="" id="category_id_hidden"> --}}
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Theme Title</label>
                                            <input type="text" name="title" class="form-control" id="title"
                                                placeholder="Enter Number" required>
                                        </div>
                                        <div class="mb-3 flex-column">
                                            <label for="inputEmail4" class="form-label">Theme</label>
                                            <input type="file" id="theme" name="theme" class="form-control"
                                                style="width: 100%">
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
                                <th>theme</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($themes as $key => $theme)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $theme->name }}</td>
                                    <td>{{ $theme->name }}</td>
                                    <td>
                                        <img src="{{ asset($theme->theme) }}" alt="" width="100"
                                            style="height: 100px">
                                    </td>
                                    <td style="min-width: 11rem;">
                                        <a class="btn btn-sm" onclick="editCategory({{ $theme->id }})">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a onclick="" class="btn btn-sm">
                                            <i class="fas fa-bell"></i>
                                        </a>
                                        <a href="{{ url('admin/theme/delete') . '/' . $theme->id }}"
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
        $("#wallpaper_edit").on('change', function(e) {
            $("#wallpaperImage_edit").attr("src", URL.createObjectURL(e.target.files[0]));
        })

        function editCategory(id) {
            console.log(id);
            $.ajax({
                url: "{{ url('admin/theme/edit/') }}" + "/" + id,
                success: function(data) {
                    console.log(data);
                    $("#title").val(data.title);
                    $("#theme_hidden").val(data.id);
                    // $(tinymce.get('tiny2').getBody()).html(data.theme);
                    $("#category_edit").val(data.category_id);
                    $("#category_edit").text(data.category.category_name);
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
