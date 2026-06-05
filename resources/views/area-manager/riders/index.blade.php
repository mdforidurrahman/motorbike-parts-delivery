@extends('layouts.app')

@section('title', 'Manage Riders - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Delivery Riders in Your Area</h2>
        <p class="text-muted">Managing area: <strong>{{ auth()->user()->area->name ?? 'N/A' }}</strong></p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('area-manager.riders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Rider
        </a>
    </div>
</div>

<div class="card">
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
        
        @if($riders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Area</th>
                            <th>Total Deliveries</th>
                            <th>Total Earnings</th>
                            <th>Wallet</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riders as $rider)
                        <tr>
                            <td>{{ $rider->id }}</td>
                            <td>
                                <strong>{{ $rider->name }}</strong><br>
                                <small class="text-muted">Joined: {{ $rider->created_at->format('d M Y') }}</small>
                            </td>
                            <td>{{ $rider->phone }}</td>
                            <td>{{ $rider->email }}</td>
                            <td>
                                <span class="badge bg-info">{{ $rider->area->name ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $rider->deliveries()->count() }}</td>
                            <td>৳{{ number_format($rider->deliveries()->sum('delivery_charge'), 2) }}</td>
                            <td>৳{{ number_format($rider->wallet_balance, 2) }}</td>
                            <td>
                                @if($rider->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('area-manager.riders.show', $rider->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('area-manager.riders.edit', $rider->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($rider->is_active)
                                        <button type="button" class="btn btn-sm btn-warning" onclick="toggleStatus({{ $rider->id }}, 'deactivate')">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-success" onclick="toggleStatus({{ $rider->id }}, 'activate')">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteRider({{ $rider->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $riders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-motorcycle fa-3x text-muted mb-3"></i>
                <p class="text-muted">No riders found in your area.</p>
                <a href="{{ route('area-manager.riders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Rider
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Rider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this rider?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
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
                <p id="statusModalMessage">Are you sure you want to update this rider's status?</p>
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
@endsection

@push('scripts')
<script>
    function deleteRider(id) {
        $('#deleteForm').attr('action', '/area-manager/riders/' + id);
        $('#deleteModal').modal('show');
    }
    
    function toggleStatus(id, action) {
        if(action === 'activate') {
            $('#statusModalTitle').text('Activate Rider');
            $('#statusModalMessage').text('Are you sure you want to activate this rider?');
            $('#statusBtn').removeClass('btn-warning').addClass('btn-success').text('Activate');
            $('#statusForm').attr('action', '/area-manager/riders/' + id + '/activate');
        } else {
            $('#statusModalTitle').text('Deactivate Rider');
            $('#statusModalMessage').text('Are you sure you want to deactivate this rider?');
            $('#statusBtn').removeClass('btn-success').addClass('btn-warning').text('Deactivate');
            $('#statusForm').attr('action', '/area-manager/riders/' + id + '/deactivate');
        }
        $('#statusModal').modal('show');
    }
</script>
@endpush