@extends('layouts.app')

@section('title', 'Earning History - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Earning History</h2>
        <p class="text-muted">All completed deliveries and earnings</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($deliveries->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Pickup From</th>
                            <th>Deliver To</th>
                            <th>Delivery Fee</th>
                            <th>Delivered At</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>
                            <td>
                                <strong>{{ $delivery->order_number }}</strong>
                            </td>
                            <td>
                                {{ $delivery->supplierOutlet->shop_name }}<br>
                                <small class="text-muted">{{ $delivery->supplierOutlet->address }}</small>
                            </td>
                            <td>
                                {{ $delivery->buyerOutlet->shop_name }}<br>
                                <small class="text-muted">{{ $delivery->buyerOutlet->address }}</small>
                            </td>
                            <td>
                                <strong class="text-success">+ ৳{{ number_format($delivery->delivery_charge, 2) }}</strong>
                            </td>
                            <td>
                                {{ $delivery->delivered_at->format('d M Y, h:i A') }}<br>
                                <small>{{ $delivery->delivered_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success">Completed</span>
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
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <p class="text-muted">No earning history found.</p>
            </div>
        @endif
    </div>
</div>
@endsection