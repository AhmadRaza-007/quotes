@extends('admin.main')

@section('content')
    <div class="container-fluid">
        <!-- Page header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">Push Notifications</h1>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendToAllModal">
                            <i class="bi bi-broadcast me-2"></i>Send to All Users
                        </button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sendToUserModal">
                            <i class="bi bi-person me-2"></i>Send to Specific User
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-6">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Total Users</span>
                                <span class="h3 font-bold mb-0" id="totalUsers">{{ $totalUsers }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Active Devices</span>
                                <span class="h3 font-bold mb-0" id="totalDevices">{{ $totalDevices }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-success text-white text-lg rounded-circle">
                                    <i class="bi bi-phone"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">iOS Devices</span>
                                <span class="h3 font-bold mb-0" id="iosDevices">0</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-dark text-white text-lg rounded-circle">
                                    <i class="bi bi-apple"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Android Devices</span>
                                <span class="h3 font-bold mb-0" id="androidDevices">0</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                    <i class="bi bi-android2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Devices -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Registered Devices</h5>
                    </div>
                    <div class="card-body">
                        @if ($recentDevices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Device Type</th>
                                            <th>Platform</th>
                                            <th>App Version</th>
                                            <th>Registered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentDevices as $device)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <img src="{{ $device->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($device->user->name) . '&background=random' }}"
                                                                alt="{{ $device->user->name }}" class="rounded-circle">
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $device->user->name }}</h6>
                                                            <small class="text-muted">{{ $device->user->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-primary">{{ $device->device_type ?? 'Unknown' }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-secondary">{{ $device->platform ?? 'Unknown' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $device->app_version ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <small
                                                        class="text-muted">{{ $device->created_at->diffForHumans() }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-phone display-1 text-muted"></i>
                                <h5 class="mt-3 text-muted">No devices registered yet</h5>
                                <p class="text-muted">Devices will appear here when users register for push notifications.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Send to All Users Modal -->
    <div class="modal fade" id="sendToAllModal" tabindex="-1" aria-labelledby="sendToAllModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendToAllModalLabel">Send Notification to All Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sendToAllForm">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required
                                placeholder="Enter notification title" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="body" class="form-label">Message *</label>
                            <textarea class="form-control" id="body" name="body" rows="4" required
                                placeholder="Enter notification message" maxlength="500"></textarea>
                            <div class="form-text">Maximum 500 characters</div>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Notification Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="admin_announcement">Admin Announcement</option>
                                <option value="system_update">System Update</option>
                                <option value="new_feature">New Feature</option>
                                <option value="maintenance">Maintenance Notice</option>
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            This notification will be sent to all {{ $totalDevices }} active devices.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendToAllBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Send Notification
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send to Specific User Modal -->
    <div class="modal fade" id="sendToUserModal" tabindex="-1" aria-labelledby="sendToUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendToUserModalLabel">Send Notification to Specific User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sendToUserForm">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select User *</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Loading users...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="user_title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="user_title" name="title" required
                                placeholder="Enter notification title" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="user_body" class="form-label">Message *</label>
                            <textarea class="form-control" id="user_body" name="body" rows="4" required
                                placeholder="Enter notification message" maxlength="500"></textarea>
                            <div class="form-text">Maximum 500 characters</div>
                        </div>
                        <div class="mb-3">
                            <label for="user_type" class="form-label">Notification Type</label>
                            <select class="form-select" id="user_type" name="type">
                                <option value="admin_message">Admin Message</option>
                                <option value="personal_notification">Personal Notification</option>
                                <option value="support_reply">Support Reply</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="sendToUserBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Send to User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Modal -->
    <div class="modal fade" id="resultsModal" tabindex="-1" aria-labelledby="resultsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultsModalLabel">Notification Results</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="resultsContent">
                    <!-- Results will be displayed here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- @push('scripts') --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-compat/3.0.0-alpha1/jquery.min.js"
    integrity="sha512-4GsgvzFFry8SXj8c/VcCjjEZ+du9RZp/627AEQRVLatx6d60AUnUYXg0lGn538p44cgRs5E2GXq+8IOetJ+6ow=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        // Load statistics
        loadStats();

        // Load users for dropdown
        loadUsers();

        // Send to All Users
        $('#sendToAllBtn').click(function() {
            sendNotification('all');
        });

        // Send to Specific User
        $('#sendToUserBtn').click(function() {
            sendNotification('user');
        });

        function loadStats() {
            $.ajax({
                url: '{{ route('admin.notifications.stats') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#totalUsers').text(response.stats.total_users);
                        $('#totalDevices').text(response.stats.total_devices);

                        // Update platform-specific counts
                        let iosCount = 0;
                        let androidCount = 0;

                        response.stats.devices_by_platform.forEach(platform => {
                            if (platform.platform === 'ios') {
                                iosCount = platform.count;
                            } else if (platform.platform === 'android') {
                                androidCount = platform.count;
                            }
                        });

                        $('#iosDevices').text(iosCount);
                        $('#androidDevices').text(androidCount);
                    }
                }
            });
        }

        function loadUsers() {
            $.ajax({
                url: '{{ route('admin.notifications.users') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">Select a user</option>';
                        response.users.forEach(user => {
                            options +=
                                `<option value="${user.id}">${user.name} (${user.email})</option>`;
                        });
                        $('#user_id').html(options);
                    }
                }
            });
        }

        function sendNotification(type) {
            const formId = type === 'all' ? '#sendToAllForm' : '#sendToUserForm';
            const btnId = type === 'all' ? '#sendToAllBtn' : '#sendToUserBtn';
            const modalId = type === 'all' ? '#sendToAllModal' : '#sendToUserModal';
            const url = type === 'all' ? '{{ route('admin.notifications.send-to-all') }}' :
                '{{ route('admin.notifications.send-to-user') }}';

            const formData = new FormData($(formId)[0]);

            // Show loading state
            $(btnId).prop('disabled', true);
            $(btnId).find('.spinner-border').removeClass('d-none');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $(btnId).prop('disabled', false);
                    $(btnId).find('.spinner-border').addClass('d-none');

                    if (response.success) {
                        // Close the send modal
                        $(modalId).modal('hide');

                        // Show results
                        showResults(response);

                        // Reset form
                        $(formId)[0].reset();

                        // Reload stats
                        loadStats();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    $(btnId).prop('disabled', false);
                    $(btnId).find('.spinner-border').addClass('d-none');

                    let errorMessage = 'An error occurred while sending the notification.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert('Error: ' + errorMessage);
                }
            });
        }

        function showResults(response) {
            let content = '';

            if (response.data) {
                const data = response.data;
                content += `
                <div class="alert alert-success">
                    <h6><i class="bi bi-check-circle me-2"></i>Notification Sent Successfully</h6>
                    <p class="mb-0">${response.message}</p>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>${data.sent_count || 0}</h4>
                                <small>Successful</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4>${data.failure_count || 0}</h4>
                                <small>Failed</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                if (data.failures && data.failures.length > 0) {
                    content += `
                    <div class="mt-3">
                        <h6>Failed Devices:</h6>
                        <div class="small text-muted">
                            ${data.failures.map(failure =>
                                `<div>${failure.device_token.substring(0, 20)}... - ${failure.error}</div>`
                            ).join('')}
                        </div>
                    </div>
                `;
                }
            } else {
                content = `
                <div class="alert alert-success">
                    <h6><i class="bi bi-check-circle me-2"></i>Success</h6>
                    <p class="mb-0">${response.message}</p>
                </div>
            `;
            }

            $('#resultsContent').html(content);
            $('#resultsModal').modal('show');
        }

        // Refresh stats every 30 seconds
        setInterval(loadStats, 30000);
    });
</script>
