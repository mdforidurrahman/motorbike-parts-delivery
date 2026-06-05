@extends('layouts.app')

@section('title', 'My Deliveries - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>My Deliveries</h2>
        <p class="text-muted">Current and completed deliveries</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @if($deliveries->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Pickup From</th>
                            <th>Deliver To</th>
                            <th>Delivery Fee</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>
                            <td>
                                <strong>{{ $delivery->order_number }}</strong><br>
                                <small class="text-muted">{{ $delivery->created_at->format('d M Y') }}</small>
                            </td>
                            <td>
                                {{ $delivery->supplierOutlet->shop_name }}<br>
                                <small class="text-muted">{{ $delivery->supplierOutlet->address }}</small>
                            </td>
                            <td>
                                {{ $delivery->buyerOutlet->shop_name }}<br>
                                <small class="text-muted">{{ $delivery->delivery_address }}</small>
                            </td>
                            <td>৳{{ number_format($delivery->delivery_charge, 2) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'rider_assigned' => 'primary',
                                        'picked_up' => 'warning',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$delivery->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('rider.deliveries.show', $delivery->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($delivery->status == 'rider_assigned')
                                        <form action="{{ route('rider.deliveries.pickup', $delivery->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="fas fa-box"></i> Pickup
                                            </button>
                                        </form>
                                    @endif
                                    @if($delivery->status == 'picked_up')
                                        <form action="{{ route('rider.deliveries.deliver', $delivery->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark as delivered?')">
                                                <i class="fas fa-check"></i> Deliver
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
                {{ $deliveries->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <p class="text-muted">No deliveries assigned yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection