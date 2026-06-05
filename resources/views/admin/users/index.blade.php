@extends('layouts.app')

@section('title', 'User Management - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>User Management</h2>
        <p class="text-muted">Manage system users and their roles</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New User
        </a>
        <button type="button" class="btn btn-success" onclick="exportUsers()">
            <i class="fas fa-download"></i> Export
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="userTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-status="all" href="#">All Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-role="admin" href="#">Admins</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-role="head-office" href="#">Head Office</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-role="area-manager" href="#">Area Managers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-role="marketing-officer" href="#">Marketing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-role="outlet-owner" href="#">Outlet Owners</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-role="rider" href="#">Riders</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover" id="usersTable">
                <thead>
                    <tr>
                        <th width="30"><input type="checkbox" id="selectAll"></th>
                        <th width="50">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Area</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="{{ !$user->is_active ? 'table-secondary' : '' }}">
                        <td>
                            <input type="checkbox" class="userCheckbox" value="{{ $user->id }}">
                        </td>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    @if(!$user->is_active)
                                        <br><small class="text-danger">(Inactive)</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $user->email }}<br>
                            <small class="text-muted">{{ $user->email_verified_at ? '✓ Verified' : 'Not verified' }}</small>
                        </td>
                        <td>
                            {{ $user->phone }}<br>
                            <small class="text-muted">Joined: {{ $user->created_at->format('d M Y') }}</small>
                        </td>
                        <td>
                            @php
                                $roleColors = [
                                    'admin' => 'danger',
                                    'head-office' => 'primary',
                                    'area-manager' => 'info',
                                    'marketing-officer' => 'success',
                                    'outlet-owner' => 'warning',
                                    'rider' => 'secondary'
                                ];
                                $roleColor = $roleColors[$user->role->name ?? ''] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $roleColor }}">
                                {{ $user->role->display_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if($user->area)
                                <i class="fas fa-map-marker-alt text-muted"></i> {{ $user->area->name }}<br>
                                <small class="text-muted">{{ $user->area->city }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Active</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Inactive</span>
                            @endif
                        </td>
                        <td>
                            {{ $user->created_at->format('d M Y') }}<br>
                            <small class="text-muted">{{ $user->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id != auth()->id())
                                    <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                                @if($user->is_active)
                                    <button type="button" class="btn btn-warning" onclick="toggleStatus({{ $user->id }}, 'deactivate')" title="Deactivate">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success" onclick="toggleStatus({{ $user->id }}, 'activate')" title="Activate">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                @endif
                                @if($user->id != auth()->id())
                                    <button type="button" class="btn btn-secondary" onclick="impersonateUser({{ $user->id }})" title="Impersonate">
                                        <i class="fas fa-mask"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-3">No users found in the system.</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First User
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
        
        <!-- Bulk Actions -->
        <div class="row mt-3" id="bulkActions" style="display: none;">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fas fa-check-circle"></i>
                    <strong><span id="selectedCount">0</span> users selected</strong>
                    <div class="float-end">
                        <button type="button" class="btn btn-sm btn-success me-1" onclick="bulkActivate()">
                            <i class="fas fa-check"></i> Activate All
                        </button>
                        <button type="button" class="btn btn-sm btn-warning me-1" onclick="bulkDeactivate()">
                            <i class="fas fa-ban"></i> Deactivate All
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash"></i> Delete All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalTitle">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="statusModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="statusForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" id="statusBtn">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Impersonate Modal -->
<div class="modal fade" id="impersonateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Impersonate User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><i class="fas fa-mask"></i> You are about to impersonate this user.</p>
                <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> You will be logged in as this user. To return to admin, click "Stop Impersonating" in the top bar.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="impersonateForm" method="POST" action="{{ route('admin.users.impersonate', 0) }}">
                    @csrf
                    <button type="submit" class="btn btn-warning">Impersonate</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .table-secondary {
        opacity: 0.7;
    }
    .nav-tabs .nav-link {
        cursor: pointer;
    }
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>

@endsection

@push('scripts')
<script>
    let selectedUsers = [];
    
    // Select All checkbox
    $('#selectAll').change(function() {
        $('.userCheckbox').prop('checked', $(this).prop('checked'));
        updateBulkActions();
    });
    
    $('.userCheckbox').change(function() {
        updateBulkActions();
    });
    
    function updateBulkActions() {
        selectedUsers = [];
        $('.userCheckbox:checked').each(function() {
            selectedUsers.push($(this).val());
        });
        
        if(selectedUsers.length > 0) {
            $('#bulkActions').fadeIn();
            $('#selectedCount').text(selectedUsers.length);
        } else {
            $('#bulkActions').fadeOut();
        }
    }
    
    function deleteUser(id) {
        $('#deleteForm').attr('action', '/admin/users/' + id);
        $('#deleteModal').modal('show');
    }
    
    function toggleStatus(id, action) {
        if(action === 'activate') {
            $('#statusModalTitle').text('Activate User');
            $('#statusModalMessage').html('<i class="fas fa-check-circle"></i> Are you sure you want to <strong>activate</strong> this user?');
            $('#statusBtn').removeClass('btn-warning').addClass('btn-success').html('<i class="fas fa-check"></i> Activate');
            $('#statusForm').attr('action', '/admin/users/' + id + '/activate');
        } else {
            $('#statusModalTitle').text('Deactivate User');
            $('#statusModalMessage').html('<i class="fas fa-ban"></i> Are you sure you want to <strong>deactivate</strong> this user?');
            $('#statusBtn').removeClass('btn-success').addClass('btn-warning').html('<i class="fas fa-ban"></i> Deactivate');
            $('#statusForm').attr('action', '/admin/users/' + id + '/deactivate');
        }
        $('#statusModal').modal('show');
    }
    
    function impersonateUser(id) {
        $('#impersonateForm').attr('action', '/admin/users/' + id + '/impersonate');
        $('#impersonateModal').modal('show');
    }
    
    function bulkActivate() {
        if(confirm('Activate ' + selectedUsers.length + ' selected users?')) {
            $.ajax({
                url: '{{ route("admin.users.bulk-activate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    users: selectedUsers
                },
                success: function(response) {
                    if(response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Something went wrong!');
                }
            });
        }
    }
    
    function bulkDeactivate() {
        if(confirm('Deactivate ' + selectedUsers.length + ' selected users?')) {
            $.ajax({
                url: '{{ route("admin.users.bulk-deactivate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    users: selectedUsers
                },
                success: function(response) {
                    if(response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Something went wrong!');
                }
            });
        }
    }
    
    function bulkDelete() {
        if(confirm('Delete ' + selectedUsers.length + ' selected users? This action cannot be undone!')) {
            $.ajax({
                url: '{{ route("admin.users.bulk-delete") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    users: selectedUsers
                },
                success: function(response) {
                    if(response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Something went wrong!');
                }
            });
        }
    }
    
function exportUsers() {
    // Direct URL without named route
    window.location.href = '/admin/users/export';
}
    
    // Filter by role
// Filter by role - Updated version
$('.nav-link').click(function() {
    var role = $(this).data('role');
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    
    if (!role || role === 'all') {
        $('tbody tr').show();
    } else {
        $('tbody tr').each(function() {
            // Get the role text from the badge
            var roleText = $(this).find('td:eq(5) .badge').text().trim().toLowerCase();
            
            // Map role display names to role keys
            var roleMap = {
                'administrator': 'admin',
                'admin': 'admin',
                'head office': 'head-office',
                'area manager': 'area-manager',
                'marketing officer': 'marketing-officer',
                'marketing': 'marketing-officer',
                'outlet owner': 'outlet-owner',
                'outlet': 'outlet-owner',
                'delivery rider': 'rider',
                'rider': 'rider'
            };
            
            var roleKey = roleMap[roleText] || roleText.replace(' ', '-');
            
            if (roleKey === role) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});
    
    // Search functionality
    $('#searchUser').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
</script>
@endpush