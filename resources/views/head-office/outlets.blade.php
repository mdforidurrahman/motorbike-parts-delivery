@extends('layouts.app')

@section('title', 'All Outlets - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>All Outlets</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Shop Name</th>
                        <th>Owner</th>
                        <th>Area</th>
                        <th>Type</th>
                        <th>Orders</th>
                        <th>Revenue</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outlets as $outlet)
                    <tr>
                        <td>{{ $outlet->id }}</td>
                        <td>
                            <strong>{{ $outlet->shop_name }}</strong><br>
                            <small>{{ $outlet->phone }}</small>
                        </td>
                        <td>{{ $outlet->owner->name ?? 'N/A' }}</td>
                        <td>{{ $outlet->area->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $outlet->type == 'contracted' ? 'success' : 'info' }}">
                                {{ ucfirst($outlet->type) }}
                            </span>
                        </td>
                        <td>{{ $outlet->ordersAsSupplier()->count() }}</td>
                        <td>৳{{ number_format($outlet->ordersAsSupplier()->where('status', 'delivered')->sum('total_amount'), 2) }}</td>
                        <td>
                            @if($outlet->is_verified)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No outlets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $outlets->links() }}
    </div>
</div>
@endsection