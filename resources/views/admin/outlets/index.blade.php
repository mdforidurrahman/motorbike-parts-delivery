@extends('layouts.app')

@section('title', 'All Outlets - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>All Outlets</h2>
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
                            <th>Area</th>
                            <th>Type</th>
                            <th>Verified</th>
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
                            <td>{{ $outlet->owner->name ?? 'N/A' }}</td>
                            <td>{{ $outlet->phone }}</td>
                            <td>{{ $outlet->area->name ?? 'N/A' }}</td>
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
                                    <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                            <td>৳{{ number_format($outlet->wallet_balance, 2) }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.outlets.show', $outlet->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!$outlet->is_verified)
                                        <form action="{{ route('admin.outlets.verify', $outlet->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.outlets.suspend', $outlet->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to suspend this outlet?')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
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
                <p class="text-muted">No outlets found.</p>
                <a href="{{ route('admin.outlets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Outlet
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
</style>
@endpush