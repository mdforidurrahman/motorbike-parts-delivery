@extends('layouts.app')

@section('title', 'Manage Areas - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Manage Areas</h2>
        <p class="text-muted">Manage delivery zones and areas</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.areas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Area
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
        
        @if($areas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Area Name</th>
                            <th>City</th>
                            <th>Delivery Charge</th>
                            <th>Outlets</th>
                            <th>Riders</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($areas as $area)
                        <tr>
                            <td>{{ $area->id }}</td>
                            <td>
                                <strong>{{ $area->name }}</strong>
                            </td>
                            <td>{{ $area->city }}</td>
                            <td>৳{{ number_format($area->delivery_charge, 2) }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $area->outlets_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $area->users_count ?? 0 }}</span>
                            </td>
                            <td>{{ $area->created_at->format('d M Y') }}</br>
                                <small>{{ $area->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.areas.show', $area->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.areas.edit', $area->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($area->outlets_count == 0 && $area->users_count == 0)
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteArea({{ $area->id }})">
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
                {{ $areas->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                <p class="text-muted">No areas found.</p>
                <a href="{{ route('admin.areas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Area
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
                <h5 class="modal-title">Delete Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this area?</p>
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

<script>
    function deleteArea(id) {
        $('#deleteForm').attr('action', '/admin/areas/' + id);
        $('#deleteModal').modal('show');
    }
</script>
@endsection