@extends('layouts.app')

@section('title', 'Delivery Details - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Delivery Details</h2>
        <p class="text-muted">Order #{{ $order->order_number }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-store"></i> Pickup Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Shop Name:</strong> {{ $order->supplierOutlet->shop_name }}</p>
                <p><strong>Address:</strong> {{ $order->supplierOutlet->address }}</p>
                <p><strong>Phone:</strong> {{ $order->supplierOutlet->phone }}</p>
                <p><strong>Contact Person:</strong> {{ $order->supplierOutlet->owner->name ?? 'N/A' }}</p>
                
                <a href="https://www.google.com/maps?q={{ $order->supplierOutlet->latitude }},{{ $order->supplierOutlet->longitude }}" 
                   target="_blank" class="btn btn-info btn-sm">
                    <i class="fas fa-map-marker-alt"></i> Open in Google Maps
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Delivery Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Shop Name:</strong> {{ $order->buyerOutlet->shop_name }}</p>
                <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
                <p><strong>Phone:</strong> {{ $order->buyerOutlet->phone }}</p>
                <p><strong>Contact Person:</strong> {{ $order->buyerOutlet->owner->name ?? 'N/A' }}</p>
                
                <a href="https://www.google.com/maps?q={{ $order->buyerOutlet->latitude }},{{ $order->buyerOutlet->longitude }}" 
                   target="_blank" class="btn btn-info btn-sm">
                    <i class="fas fa-map-marker-alt"></i> Open in Google Maps
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-box"></i> Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</br>
                                    <small class="text-muted">{{ $item->product->sku }}</small>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>৳{{ number_format($item->price, 2) }}</td>
                                <td>৳{{ number_format($item->quantity * $item->price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td><strong>৳{{ number_format($order->total_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-motorcycle"></i> Delivery Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="map" style="height: 300px; border-radius: 8px;"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h6>Delivery Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}</h6>
                            <p id="locationStatus">Getting your location...</p>
                        </div>
                        
                        @if($order->status == 'rider_assigned')
                            <form action="{{ route('rider.deliveries.pickup', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-box"></i> Mark as Picked Up
                                </button>
                            </form>
                        @endif
                        
                        @if($order->status == 'picked_up')
                            <form action="{{ route('rider.deliveries.deliver', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Confirm delivery completion?')">
                                    <i class="fas fa-check-circle"></i> Mark as Delivered
                                </button>
                            </form>
                        @endif
                        
                        @if($order->status == 'delivered')
                            <div class="alert alert-success text-center">
                                <i class="fas fa-check-circle"></i> Delivery Completed!
                                <br><small>Delivered on: {{ $order->delivered_at->format('d M Y, h:i A') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>
<script>
    let map;
    let riderMarker;
    let supplierMarker;
    let buyerMarker;
    
    function initMap() {
        const supplierLat = parseFloat('{{ $order->supplierOutlet->latitude }}');
        const supplierLng = parseFloat('{{ $order->supplierOutlet->longitude }}');
        const buyerLat = parseFloat('{{ $order->buyerOutlet->latitude }}');
        const buyerLng = parseFloat('{{ $order->buyerOutlet->longitude }}');
        
        // Create map centered between supplier and buyer
        const centerLat = (supplierLat + buyerLat) / 2;
        const centerLng = (supplierLng + buyerLng) / 2;
        
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: { lat: centerLat, lng: centerLng }
        });
        
        // Add supplier marker
        supplierMarker = new google.maps.Marker({
            position: { lat: supplierLat, lng: supplierLng },
            map: map,
            title: 'Pickup Location',
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
        });
        
        // Add buyer marker
        buyerMarker = new google.maps.Marker({
            position: { lat: buyerLat, lng: buyerLng },
            map: map,
            title: 'Delivery Location',
            icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
        });
        
        // Draw route
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer();
        directionsRenderer.setMap(map);
        
        const request = {
            origin: { lat: supplierLat, lng: supplierLng },
            destination: { lat: buyerLat, lng: buyerLng },
            travelMode: 'DRIVING'
        };
        
        directionsService.route(request, function(result, status) {
            if (status == 'OK') {
                directionsRenderer.setDirections(result);
            }
        });
        
        // Track rider location
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function(position) {
                const riderLat = position.coords.latitude;
                const riderLng = position.coords.longitude;
                
                if (!riderMarker) {
                    riderMarker = new google.maps.Marker({
                        position: { lat: riderLat, lng: riderLng },
                        map: map,
                        title: 'Your Location',
                        icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                    });
                } else {
                    riderMarker.setPosition({ lat: riderLat, lng: riderLng });
                }
                
                $('#locationStatus').html('Your location is being tracked.');
            }, function(error) {
                $('#locationStatus').html('Unable to get your location.');
            });
        }
    }
    
    // Fallback
    if (typeof google === 'undefined') {
        document.getElementById('map').innerHTML = '<div class="alert alert-info">Map will appear here. Enable Google Maps API.</div>';
    }
</script>
@endpush