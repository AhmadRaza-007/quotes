<!-- resources/views/admin/users/index.blade.php -->
@extends('app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>User Management</h3>
                        <div>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add New User</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.users.index') }}">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search users..." value="{{ request('search') }}">
                                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Categories</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge {{ $user->user_type ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $user->user_type ? 'Admin' : 'User' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($user->categories_count > 0)
                                                    <a href="{{ route('admin.users.categories.index', $user->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-folder"></i> View Categories
                                                        ({{ $user->categories_count }})
                                                    </a>
                                                @else
                                                    <span class="text-muted">No categories</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <a href="{{ route('admin.users.categories.index', $user->id) }}"
                                                    class="btn btn-sm btn-success">Categories</a>
                                                @if ($user->user_type != 1)
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure?')">Delete</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
