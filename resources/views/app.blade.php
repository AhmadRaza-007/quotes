<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- /* Webpixels CSS */
    /* Utility and component-centric Design System based on Bootstrap for fast, responsive UI development */
    /* URL: https://github.com/webpixels/css */ --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.4.0/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('dist/dashboard.css') }}">

    <!--    font Awesome     -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!--    Data Tables     -->
    <link href="{{ asset('dist/css/datatablestyle.css') }}" rel="stylesheet" />
</head>

<body>
    <style>
        .page-item.active .page-link {
            background-color: #4e52d0;
        }

        .dataTable-dropdown {
            /* display: none; */
        }
    </style>
    <!-- Dashboard -->
    <div class="d-flex flex-column flex-lg-row h-lg-full bg-surface-secondary">
        @include('admin.sidebar')
        <!-- Main content -->
        <div class="h-screen flex-grow-1 overflow-y-lg-auto">
            @include('admin.header')
            @yield('content')
            {{-- @include('admin.footer') --}}
        </div>
    </div>




    <!--    JQuery CDN     -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-compat/3.0.0-alpha1/jquery.min.js"
        integrity="sha512-4GsgvzFFry8SXj8c/VcCjjEZ+du9RZp/627AEQRVLatx6d60AUnUYXg0lGn538p44cgRs5E2GXq+8IOetJ+6ow=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    @yield('script')

    <!--    Data Tables     -->
    <script src="{{ asset('dist/js/simple-datatables.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('dist/js/datatables-simple-demo.js') }}"></script>

    <!--    Bootstrap 5     -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>
