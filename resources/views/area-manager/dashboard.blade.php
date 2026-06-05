@extends('layouts.app')

@section('title', 'Area Manager Dashboard - MotoLink')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Area Manager Dashboard</h2>
        <p class="text-muted">Welcome back, {{ auth()->user()->name }}!</p>
        <p class="text-muted">Managing Area: <strong>{{ auth()->user()->area->name ?? 'N/A' }}</strong></p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Outlets</h6>
                        <h3 class="text-white">{{ $stats['outlets'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-store fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Active Riders</h6>
                        <h3 class="text-white">{{ $stats['riders'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-motorcycle fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Orders</h6>
                        <h3 class="text-white">{{ $stats['orders'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Pending Orders</h6>
                        <h3 class="text-white">{{ $stats['pending_orders'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-clock fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row Stats -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Area Performance</h5>
            </div>
            <div class="card-body">
                <canvas id="areaPerformanceChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <div class="border rounded p-3 mb-2">
                            <h4 class="text-primary">{{ $stats['verified_outlets'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Verified Outlets</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 mb-2">
                            <h4 class="text-success">{{ $stats['active_riders'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Active Riders</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h4 class="text-info">৳{{ number_format($stats['total_revenue'] ?? 0, 2) }}</h4>
                            <p class="text-muted mb-0">Total Revenue</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h4 class="text-warning">{{ $stats['delivered_today'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Delivered Today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Orders in Your Area</h5>
            </div>
            <div class="card-body">
                @if(isset($recentOrders) && $recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Buyer Shop</th>
                                    <th>Supplier Shop</th>
                                    <th>Rider</th>
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
                                        <small class="text-muted">{{ $order->buyerOutlet->phone ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if($order->supplierOutlet)
                                            {{ $order->supplierOutlet->shop_name }}
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->rider)
                                            {{ $order->rider->name }}
                                        @else
                                            <span class="badge bg-secondary">Not Assigned</span>
                                        @endif
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
                                            $statusText = [
                                                'pending' => 'Pending',
                                                'accepted' => 'Accepted',
                                                'rider_assigned' => 'Rider Assigned',
                                                'picked_up' => 'Picked Up',
                                                'delivered' => 'Delivered',
                                                'cancelled' => 'Cancelled'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                            {{ $statusText[$order->status] ?? ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
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
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No orders found in your area.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('area-manager.outlets.create') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-plus"></i> Add New Outlet
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('area-manager.riders.create') }}" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-motorcycle"></i> Add New Rider
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('area-manager.outlets.index') }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-store"></i> Manage Outlets
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('area-manager.riders.index') }}" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-users"></i> Manage Riders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Area Performance Chart
    const ctx = document.getElementById('areaPerformanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Orders',
                data: [65, 59, 80, 81, 56, 55, 40, 70, 85, 90, 95, 100],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Revenue (৳)',
                data: [28000, 25000, 35000, 40000, 32000, 30000, 25000, 45000, 50000, 55000, 60000, 65000],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>
@endpush