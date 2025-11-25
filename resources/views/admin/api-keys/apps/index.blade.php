<!-- resources/views/admin/api-keys/categories/index.blade.php -->
@extends('app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>API Key Apps</h3>
                        <a href="{{ route('admin.api-keys.apps.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create App
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($categories->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>API Keys Count</th>
                                            <th>Created</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categories as $category)
                                            <tr>
                                                <td>
                                                    <strong>{{ $category->name }}</strong>
                                                </td>
                                                <td>
                                                    {{ $category->description ?? 'No description' }}
                                                </td>
                                                <td>
                                                    <span class="badge {{ $category->apiKeys->count() > 0 ? 'bg-primary' : 'bg-secondary' }}">
                                                        {{ $category->apiKeys->count() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $category->created_at->format('M j, Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $category->created_at->format('g:i A') }}</small>
                                                </td>
                                                <td>
                                                    @if($category->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.api-keys', $category->id) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-key"></i> Keys
                                                        </a>
                                                        <a href="{{ route('admin.api-keys.apps.edit', $category->id) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="deleteCategory({{ $category->id }})">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i>
                                No app found.
                                <a href="{{ route('admin.api-keys.apps.create') }}" class="alert-link">
                                    Create your first app
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- @push('scripts') --}}
        <script>
            function deleteCategory(categoryId) {
                if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/api-keys/apps/${categoryId}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    {{-- @endpush --}}
@endsection
