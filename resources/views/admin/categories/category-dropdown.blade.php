@foreach ($categories as $category)
    <li class="nav-item">
        <a class="nav-link" href="{{ route('category.wallpapers', $category->id) }}">
            {{ $category->category_name }}
        </a>
        @if ($category->children->isNotEmpty())
            <ul class="nav flex-column ms-3">
                @include('admin.categories.category-dropdown', ['categories' => $category->children])
            </ul>
        @endif
    </li>
@endforeach
