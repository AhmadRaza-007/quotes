<!-- resources/views/admin/api-keys/user-keys.blade.php -->
@if($user->apiKeys->count() > 0)
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Created</th>
                    <th>Last Used</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user->apiKeys as $apiKey)
                    <tr>
                        <td>
                            <strong>{{ $apiKey->name }}</strong>
                            <br>
                            <small class="text-muted">ID: {{ $apiKey->id }}</small>
                        </td>
                        <td>
                            {{ $apiKey->created_at->format('M j, Y') }}
                            <br>
                            <small class="text-muted">{{ $apiKey->created_at->format('g:i A') }}</small>
                        </td>
                        <td>
                            @if($apiKey->last_used_at)
                                {{ $apiKey->last_used_at->format('M j, Y') }}
                                <br>
                                <small class="text-muted">{{ $apiKey->last_used_at->format('g:i A') }}</small>
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            @if($apiKey->expires_at)
                                @if($apiKey->expires_at->isFuture())
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
                            @if($apiKey->isValid())
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" 
                                    class="btn btn-sm btn-danger"
                                    onclick="deleteApiKey({{ $apiKey->id }})">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        No API keys found for this user.
    </div>
@endif