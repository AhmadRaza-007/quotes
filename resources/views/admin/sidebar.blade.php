<!-- Vertical Navbar -->
<nav class="navbar show navbar-vertical h-lg-screen navbar-expand-lg px-0 py-3 navbar-light bg-white border-bottom border-bottom-lg-0 border-end-lg"
    id="navbarVertical">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler ms-n2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarCollapse"
            aria-controls="sidebarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand py-lg-2 mb-lg-5 px-lg-6 me-0" href="#">
            <img src="https://preview.webpixels.io/web/img/logos/clever-primary.svg" alt="...">
        </a>
        <!-- User menu (mobile) -->
        <div class="navbar-user d-lg-none">
            <!-- Dropdown -->
            <div class="dropdown">
                <!-- Toggle -->
                <a href="#" id="sidebarAvatar" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <div class="avatar-parent-child">
                        <img alt="Image Placeholder"
                            src="https://images.unsplash.com/photo-1548142813-c348350df52b?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=3&w=256&h=256&q=80"
                            class="avatar avatar- rounded-circle">
                        <span class="avatar-child avatar-badge bg-success"></span>
                    </div>
                </a>
                <!-- Menu -->
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sidebarAvatar">
                    <a href="#" class="dropdown-item">Profile</a>
                    <a href="#" class="dropdown-item">Settings</a>
                    <a href="#" class="dropdown-item">Billing</a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item">Logout</a>
                </div>
            </div>
        </div>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidebarCollapse">
            <!-- Navigation -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <div class="d-flex align-items-center">
                        <!-- This link goes to the category page -->
                        <a class="nav-link" href="{{ route('category') }}">Categories</a>

                        <!-- This span toggles the dropdown -->
                        <span class="ms-auto" data-bs-toggle="collapse" data-bs-target="#categories-menu" role="button"
                            aria-expanded="false" aria-controls="categories-menu" style="cursor:pointer;">
                            <i class="bi bi-chevron-down"></i>
                        </span>
                    </div>

                    <!-- Collapsible dropdown -->
                    <div class="collapse" id="categories-menu">
                        <ul class="nav flex-column ms-3">
                            <div class="mt-2" style="max-height: 200px; overflow-y: auto;">
                                @include('admin.categories.category-dropdown', [
                                    'categories' => $categories,
                                ])
                            </div>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.api-keys.apps.index') }}">
                        <i class="bi bi-key"></i> API Keys
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.users.categories.index', 1) }}">
                        <i class="bi bi-image"></i> Admin
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-image"></i> Users
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.notifications.index') }}">
                        <i class="bi bi-bell"></i> Push Notifications
                    </a>
                </li> --}}
            </ul>
            <!-- Divider -->
            <hr class="navbar-divider my-5 opacity-20">
            <!-- Push content down -->
            <div class="mt-auto"></div>
            <!-- User (md) -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-person-square"></i> Account
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('post.logout') }}">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script>
    document.querySelector('a[href="{{ route('category') }}"').addEventListener('click', function(e) {
        e.stopPropagation(); // prevent Bootstrap from treating it as collapse trigger
    });
</script>
