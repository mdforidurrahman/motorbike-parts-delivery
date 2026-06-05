@extends('layouts.app')

@section('title', 'Manage Outlets - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Outlets in Your Area</h2>
        <p class="text-muted">Managing area: {{ auth()->user()->area->name ?? 'N/A' }}</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('area-manager.outlets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Outlet
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($outlets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Shop Name</th>
                            <th>Owner</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>Verified</th>
                            <th>Orders</th>
                            <th>Wallet</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outlets as $outlet)
                        <tr>
                            <td>{{ $outlet->id }}</td>
                            <td>
                                <strong>{{ $outlet->shop_name }}</strong><br>
                                <small class="text-muted">{{ $outlet->address }}</small>
                            </td>
                            <td>{{ $outlet->owner->name ?? 'N/A' }}</br>
                                <small>{{ $outlet->owner->phone ?? '' }}</small>
                            </td>
                            <td>{{ $outlet->phone }}</td>
                            <td>
                                @if($outlet->type == 'contracted')
                                    <span class="badge bg-success">Contracted</span>
                                @elseif($outlet->type == 'large')
                                    <span class="badge bg-info">Large</span>
                                @else
                                    <span class="badge bg-secondary">Small</span>
                                @endif
                            </td>
                            <td>
                                @if($outlet->is_verified)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $outlet->ordersAsSupplier()->count() }}</td>
                            <td>৳{{ number_format($outlet->wallet_balance, 2) }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <!-- Show/View Button -->
                                    <a href="{{ route('area-manager.outlets.show', $outlet->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- Edit Button -->
                                    <a href="{{ route('area-manager.outlets.edit', $outlet->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Verify Button -->
                                    @if(!$outlet->is_verified)
                                        <button type="button" class="btn btn-sm btn-success" onclick="verifyOutlet({{ $outlet->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteOutlet({{ $outlet->id }})">
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
                {{ $outlets->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-store fa-3x text-muted mb-3"></i>
                <p class="text-muted">No outlets found in your area.</p>
                <a href="{{ route('area-manager.outlets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Outlet
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
                <h5 class="modal-title">Delete Outlet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this outlet?</p>
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
@endsection

@push('scripts')
<script>
    function deleteOutlet(id) {
        $('#deleteForm').attr('action', '/area-manager/outlets/' + id);
        $('#deleteModal').modal('show');
    }
    
    function verifyOutlet(id) {
        if(confirm('Verify this outlet?')) {
            $.ajax({
                url: '/admin/outlets/' + id + '/verify',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    location.reload();
                }
            });
        }
    }
</script>
@endpush