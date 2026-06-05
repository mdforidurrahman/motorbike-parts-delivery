@extends('layouts.app')

@section('title', 'All Riders - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Delivery Riders</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('area-manager.riders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Rider
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
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
                            <th>Deliveries</th>
                            <th>Earnings</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riders as $rider)
                        <tr>
                            <td>{{ $rider->id }}</td>
                            <td><strong>{{ $rider->name }}</strong></td>
                            <td>{{ $rider->phone }}</td>
                            <td>{{ $rider->email }}</td>
                            <td>{{ $rider->area->name ?? 'N/A' }}</td>
                            <td>{{ $rider->deliveries()->count() }}</td>
                            <td>৳{{ number_format($rider->deliveries()->sum('delivery_charge'), 2) }}</td>
                            <td>
                                @if($rider->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.riders.show', $rider->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($rider->is_active)
                                        <form action="{{ route('admin.riders.deactivate', $rider->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.riders.activate', $rider->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif
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
                <p class="text-muted">No riders found.</p>
                <a href="{{ route('area-manager.riders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Rider
                </a>
            </div>
        @endif
    </div>
</div>
@endsection