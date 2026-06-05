@extends('layouts.app')

@section('title', 'Outlet Details - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Outlet Details</h2>
        <p class="text-muted">{{ $outlet->shop_name }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('area-manager.outlets.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Outlets
        </a>
        <a href="{{ route('area-manager.outlets.edit', $outlet->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Outlet
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <!-- Outlet Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-store"></i> Outlet Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Shop Name:</th>
                        <td><strong>{{ $outlet->shop_name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Owner Name:</th>
                        <td>{{ $outlet->owner->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $outlet->phone }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $outlet->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td>{{ $outlet->address }}</td>
                    </tr>
                    <tr>
                        <th>Area:</th>
                        <td>{{ $outlet->area->name ?? 'N/A' }} - {{ $outlet->area->city ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Outlet Type:</th>
                        <td>
                            @if($outlet->type == 'contracted')
                                <span class="badge bg-success">Contracted Shop</span>
                            @elseif($outlet->type == 'large')
                                <span class="badge bg-info">Large Shop</span>
                            @else
                                <span class="badge bg-secondary">Small Shop</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Verification Status:</th>
                        <td>
                            @if($outlet->is_verified)
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-warning">Pending Verification</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Wallet Balance:</th>
                        <td><strong class="text-success">৳{{ number_format($outlet->wallet_balance, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Joined Date:</th>
                        <td>{{ $outlet->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Location Card -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Location</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label class="text-muted">Latitude:</label>
                        <p><strong>{{ $outlet->latitude }}</strong></p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted">Longitude:</label>
                        <p><strong>{{ $outlet->longitude }}</strong></p>
                    </div>
                </div>
                <a href="https://www.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}" target="_blank" class="btn btn-outline-info w-100">
                    <i class="fas fa-external-link-alt"></i> View on Google Maps
                </a>
                <div id="map" style="height: 250px; margin-top: 15px; border-radius: 8px;"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ $stats['total_orders'] ?? 0 }}</h3>
                        <p class="mb-0">Total Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ $stats['completed_orders'] ?? 0 }}</h3>
                        <p class="mb-0">Completed Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>৳{{ number_format($stats['total_earnings'] ?? 0, 2) }}</h3>
                        <p class="mb-0">Total Earnings</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Card -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-box"></i> Products</h5>
                <span class="badge bg-light text-dark">{{ $outlet->products()->count() }} Products</span>
            </div>
            <div class="card-body">
                @if($outlet->products()->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outlet->products()->limit(5)->get() as $product)
                                <tr>
                                    <td>{{ $product->name }}</br>
                                        <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                    </td>
                                    <td>{{ $product->sku }}</td>
                                    <td>৳{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock_quantity > 10 ? 'success' : ($product->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->is_available)
                                            <span class="badge bg-success">Available</span>
                                        @else
                                            <span class="badge bg-secondary">Unavailable</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($outlet->products()->count() > 5)
                        <div class="text-center mt-2">
                            <small class="text-muted">+ {{ $outlet->products()->count() - 5 }} more products</small>
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center">No products added yet.</p>
                @endif
            </div>
        </div>
        
        <!-- Recent Orders Card -->
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Orders</h5>
            </div>
            <div class="card-body">
                @if(isset($recentOrders) && $recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Buyer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>{{ $order->buyerOutlet->shop_name ?? 'N/A' }}</br>
                                        <small>{{ $order->buyerOutlet->phone ?? '' }}</small>
                                    </td>
                                    <td>৳{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'accepted' => 'info',
                                                'rider_assigned' => 'primary',
                                                'picked_up' => 'dark',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y') }}</br>
                                        <small>{{ $order->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('area-manager.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No orders found for this outlet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-borderless th {
        font-weight: 600;
        color: #6c757d;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>
<script>
    let map;
    let marker;
    
    function initMap() {
        const lat = parseFloat('{{ $outlet->latitude }}');
        const lng = parseFloat('{{ $outlet->longitude }}');
        const center = { lat: lat, lng: lng };
        
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: center,
            mapTypeId: 'roadmap'
        });
        
        marker = new google.maps.Marker({
            position: center,
            map: map,
            title: '{{ $outlet->shop_name }}',
            animation: google.maps.Animation.DROP
        });
        
        // Add info window
        const infoWindow = new google.maps.InfoWindow({
            content: '<strong>{{ $outlet->shop_name }}</strong><br>{{ $outlet->address }}'
        });
        
        marker.addListener('click', function() {
            infoWindow.open(map, marker);
        });
    }
    
    // Fallback if Google Maps is not loaded
    if (typeof google === 'undefined') {
        document.getElementById('map').innerHTML = `
            <div class="alert alert-info text-center" style="height: 250px; display: flex; align-items: center; justify-content: center;">
                <div>
                    <i class="fas fa-map-marker-alt fa-3x mb-2"></i>
                    <p>Latitude: {{ $outlet->latitude }}<br>Longitude: {{ $outlet->longitude }}</p>
                    <a href="https://www.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}" target="_blank" class="btn btn-sm btn-primary">
                        Open in Google Maps
                    </a>
                </div>
            </div>
        `;
    }
</script>
@endpush