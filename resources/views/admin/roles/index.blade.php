@extends('layouts.app')

@section('title', 'Role Management - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Role Management</h2>
        <p class="text-muted">Manage system roles and permissions</p>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="fas fa-plus"></i> Add New Role
        </button>
    </div>
</div>

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

<div class="card">
    <div class="card-body">
        @if($roles->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Users Count</th>
                            <th>Created At</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>
                                <code>{{ $role->name }}</code>
                                @if($role->name == 'admin')
                                    <span class="badge bg-danger">System</span>
                                @endif
                            </td>
                            <td>{{ $role->display_name }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $role->users_count }}</span>
                            </td>
                            <td>
                                {{ $role->created_at->format('d M Y') }}<br>
                                <small class="text-muted">{{ $role->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-info" onclick="editRole({{ $role->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($role->name != 'admin')
                                        <button type="button" class="btn btn-danger" onclick="deleteRole({{ $role->id }}, {{ $role->users_count }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <p class="text-muted">No roles found.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                    <i class="fas fa-plus"></i> Create First Role
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                        <small class="text-muted">Example: super-admin, editor, viewer (use lowercase with hyphens)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" class="form-control" required>
                        <small class="text-muted">Example: Super Admin, Editor, Viewer (human readable name)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                        <small class="text-muted">Example: super-admin, editor, viewer (use lowercase with hyphens)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" id="edit_display_name" class="form-control" required>
                        <small class="text-muted">Example: Super Admin, Editor, Viewer (human readable name)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Role Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this role?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone!</p>
                <div id="deleteRoleWarning" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-warning"></i> This role has <span id="userCount"></span> user(s) assigned. You cannot delete roles with assigned users.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteRoleForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Role</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    code {
        background: #f4f4f4;
        padding: 2px 6px;
        border-radius: 4px;
    }
</style>

@endsection

@push('scripts')
<script>
    function editRole(id) {
        $.ajax({
            url: '/admin/roles/' + id + '/edit',
            method: 'GET',
            success: function(response) {
                $('#edit_name').val(response.name);
                $('#edit_display_name').val(response.display_name);
                $('#editRoleForm').attr('action', '/admin/roles/' + id);
                $('#editRoleModal').modal('show');
            },
            error: function() {
                alert('Failed to load role data');
            }
        });
    }
    
    function deleteRole(id, userCount) {
        if (userCount > 0) {
            $('#deleteRoleWarning').show();
            $('#userCount').text(userCount);
            $('#deleteRoleForm button[type="submit"]').prop('disabled', true);
        } else {
            $('#deleteRoleWarning').hide();
            $('#deleteRoleForm button[type="submit"]').prop('disabled', false);
        }
        
        $('#deleteRoleForm').attr('action', '/admin/roles/' + id);
        $('#deleteRoleModal').modal('show');
    }
</script>
@endpush