@extends('layouts.app')

@section('title', 'Available Deliveries - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Available Deliveries</h2>
        <p class="text-muted">Orders ready for delivery in your area</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        @if($orders->count() > 0)
            <div class="row">
                @foreach($orders as $order)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-store"></i> Pickup From:</h6>
                                    <p>
                                        <strong>{{ $order->supplierOutlet->shop_name }}</strong><br>
                                        {{ $order->supplierOutlet->address }}<br>
                                        Phone: {{ $order->supplierOutlet->phone }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-map-marker-alt"></i> Deliver To:</h6>
                                    <p>
                                        <strong>{{ $order->buyerOutlet->shop_name }}</strong><br>
                                        {{ $order->delivery_address }}<br>
                                        Phone: {{ $order->buyerOutlet->phone }}
                                    </p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Order Details:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Items:</strong> {{ $order->items->count() }} products</li>
                                        <li><strong>Total Amount:</strong> ৳{{ number_format($order->total_amount, 2) }}</li>
                                        <li><strong>Delivery Fee:</strong> ৳{{ number_format($order->delivery_charge, 2) }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Distance:</h6>
                                    <p>
                                        <i class="fas fa-route"></i> 
                                        <span id="distance_{{ $order->id }}">Calculating...</span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Please confirm you can deliver this order.
                            </div>
                            
                            <form action="{{ route('rider.deliveries.accept', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle"></i> Accept Delivery
                                </button>
                            </form>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Order placed: {{ $order->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-motorcycle fa-3x text-muted mb-3"></i>
                <p class="text-muted">No available deliveries at this moment.</p>
                <p class="text-muted">Check back later for new orders in your area.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Calculate distance between two points (Haversine formula)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth's radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }
    
    // Get rider's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const riderLat = position.coords.latitude;
            const riderLng = position.coords.longitude;
            
            @foreach($orders as $order)
                const supplierLat = {{ $order->supplierOutlet->latitude }};
                const supplierLng = {{ $order->supplierOutlet->longitude }};
                const distance = calculateDistance(riderLat, riderLng, supplierLat, supplierLng);
                $('#distance_{{ $order->id }}').text(distance.toFixed(2) + ' km');
            @endforeach
        }, function(error) {
            @foreach($orders as $order)
                $('#distance_{{ $order->id }}').text('Location unavailable');
            @endforeach
        });
    } else {
        @foreach($orders as $order)
            $('#distance_{{ $order->id }}').text('Geolocation not supported');
        @endforeach
    }
</script>
@endpush