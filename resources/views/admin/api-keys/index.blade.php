<!-- resources/views/admin/api-keys/app-keys.blade.php -->
@extends('app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>API Keys for {{ $apiKeyApp->name }}</h3>
                        <div>
                            <a href="{{ route('admin.api-keys.apps.index') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Back to Apps
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#generateApiKeyModal">
                                Generate API Key
                            </button>
                        </div>
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

                        @if ($apiKeyApp->description)
                            <div class="alert alert-info">
                                <strong>Description:</strong> {{ $apiKeyApp->description }}
                            </div>
                        @endif

                        @if ($apiKeyApp->apiKeys->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Key Name</th>
                                            <th>API Key</th>
                                            <th>Created</th>
                                            <th>Last Used</th>
                                            <th>Expires</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($apiKeyApp->apiKeys as $apiKey)
                                            <tr>
                                                <td>
                                                    <strong>{{ $apiKey->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">ID: {{ $apiKey->id }}</small>
                                                </td>
                                                <td>
                                                    <code class="api-key-value" data-key="{{ $apiKey->key }}">
                                                        {{ substr($apiKey->key, 0, 20) }}...
                                                    </code>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                                        onclick="toggleApiKeyVisibility(this)">
                                                        <i class="bi bi-eye"></i> Show
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary ms-1"
                                                        onclick="copyApiKey('{{ $apiKey->key }}')">
                                                        <i class="bi bi-clipboard"></i> Copy
                                                    </button>
                                                </td>
                                                <td>
                                                    {{ $apiKey->created_at->format('M j, Y') }}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $apiKey->created_at->format('g:i A') }}</small>
                                                </td>
                                                <td>
                                                    @if ($apiKey->last_used_at)
                                                        {{ $apiKey->last_used_at->format('M j, Y') }}
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ $apiKey->last_used_at->format('g:i A') }}</small>
                                                    @else
                                                        <span class="text-muted">Never</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($apiKey->expires_at)
                                                        @if ($apiKey->expires_at->isFuture())
                                                            <span class="text-success">
                                                                {{ $apiKey->expires_at->format('M j, Y') }}
                                                            </span>
                                                            <br>
                                                            <small class="text-muted">
                                                                ({{ $apiKey->expires_at->diffForHumans() }})
                                                            </small>
                                                        @else
                                                            <span class="text-danger">
                                                                Expired
                                                            </span>
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $apiKey->expires_at->format('M j, Y') }}
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Never</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($apiKey->isValid())
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            onclick="regenerateApiKey({{ $apiKey->id }})">
                                                            <i class="bi bi-arrow-repeat"></i> Regenerate
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteApiKey({{ $apiKey->id }})">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- {{ $apiKeyApp->apiKeys->links() }} --}}
                        @else
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i>
                                No API keys found for this app.
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#generateApiKeyModal">
                                    Generate your first API key
                                </button>
                            </div>
                        @endif
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
                    <h5 class="modal-title" id="generateApiKeyModalLabel">Generate API Key for {{ $apiKeyApp->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.api-keys.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="app_id" value="{{ $apiKeyApp->id }}">
                    <div class="modal-body">
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


    <script>
        function toggleApiKeyVisibility(button) {
            const codeElement = button.parentElement.querySelector('.api-key-value');
            const isHidden = codeElement.textContent.includes('...');

            if (isHidden) {
                codeElement.textContent = codeElement.getAttribute('data-key');
                button.innerHTML = '<i class="bi bi-eye-slash"></i> Hide';
            } else {
                const fullKey = codeElement.getAttribute('data-key');
                codeElement.textContent = fullKey.substring(0, 20) + '...';
                button.innerHTML = '<i class="bi bi-eye"></i> Show';
            }
        }

        function copyApiKey(key) {
            navigator.clipboard.writeText(key).then(function() {
                // Show success message
                const originalText = event.target.innerHTML;
                event.target.innerHTML = '<i class="bi bi-check"></i> Copied!';
                setTimeout(() => {
                    event.target.innerHTML = originalText;
                }, 2000);
            }).catch(function(err) {
                alert('Failed to copy API key: ' + err);
            });
        }

        function regenerateApiKey(keyId) {
            if (confirm('Are you sure you want to regenerate this API key? The old key will become invalid immediately.')) {
                fetch(`/admin/api-keys/${keyId}/regenerate`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('API key regenerated successfully! New key: ' + data.api_key);
                            location.reload();
                        } else {
                            alert('Error regenerating API key');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error regenerating API key');
                    });
            }
        }

        function deleteApiKey(keyId) {
            if (confirm('Are you sure you want to delete this API key? This action cannot be undone.')) {
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

        // Reset form when modal is hidden
        document.getElementById('generateApiKeyModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('name').value = '';
            document.getElementById('expires_in_days').value = '';
        });
    </script>
@endsection
