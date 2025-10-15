<!-- resources/views/admin/api-keys/index.blade.php -->
@extends('app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>API Keys Management</h3>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#generateApiKeyModal">
                            Generate API Key
                        </button>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                                @if (session('api_key'))
                                    <div class="mt-2">
                                        <strong>API Key:</strong>
                                        <code
                                            class="d-block mt-1 p-2 bg-light border rounded">{{ session('api_key') }}</code>
                                        <small class="text-danger">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            Save this key now! It will not be shown again.
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.api-keys.index') }}">
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
                                        <th>User ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>API Keys Count</th>
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
                                                <span
                                                    class="badge {{ $user->api_keys_count > 0 ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $user->api_keys_count }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($user->api_keys_count > 0)
                                                    {{-- <button type="button" class="btn btn-sm btn-info"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#userApiKeysModal"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}">
                                                        View Keys
                                                    </button> --}}
                                                    <a href="{{ route('admin.api-keys.user', $user->id) }}" class="btn btn-sm btn-info">View Keys</a>
                                                @else
                                                    <span class="text-muted">No API keys</span>
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

    <!-- Generate API Key Modal -->
    <div class="modal fade" id="generateApiKeyModal" tabindex="-1" aria-labelledby="generateApiKeyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateApiKeyModalLabel">Generate API Key</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.api-keys.generate') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">User</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select a user</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Key Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="e.g., Production API Key" required>
                        </div>
                        <div class="mb-3">
                            <label for="expires_in_days" class="form-label">Expires In (Days)</label>
                            <input type="number" class="form-control" id="expires_in_days" name="expires_in_days"
                                placeholder="Leave empty for no expiration" min="1" max="365">
                            <div class="form-text">Optional. Key will expire after specified number of days.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Generate Key</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User API Keys Modal -->
    <div class="modal fade" id="userApiKeysModal" tabindex="-1" aria-labelledby="userApiKeysModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userApiKeysModalLabel">API Keys for <span id="modalUserName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="apiKeysList">
                        <!-- API keys will be loaded here via AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle user API keys modal
                const userApiKeysModal = document.getElementById('userApiKeysModal');
                userApiKeysModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const userId = button.getAttribute('data-user-id');
                    const userName = button.getAttribute('data-user-name');

                    document.getElementById('modalUserName').textContent = userName;

                    // Show loading state
                    document.getElementById('apiKeysList').innerHTML =
                        '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

                    // Load user's API keys via AJAX with proper CSRF token
                    fetch(`/admin/api-keys/user/${userId}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            credentials: 'same-origin' // Include session cookies
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(html => {
                            document.getElementById('apiKeysList').innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Error loading API keys:', error);
                            document.getElementById('apiKeysList').innerHTML =
                                '<div class="alert alert-danger">Error loading API keys. Please try again.</div>';
                        });
                });
            });

            // Function to delete API key
            function deleteApiKey(keyId) {
                if (confirm('Are you sure you want to delete this API key?')) {
                    fetch(`/admin/api-keys/${keyId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                location.reload();
                            } else {
                                alert('Error deleting API key');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting API key');
                        });
                }
            }
        </script>
    @endpush
@endsection
