@extends('layouts.app')

@section('title', 'Admin Dashboard - MotoLink')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Welcome, {{ auth()->user()->name }}!</h2>
        <p class="text-muted">System Overview and Statistics</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Outlets</h6>
                        <h3 class="text-white">{{ $stats['total_outlets'] ?? 0 }}</h3>
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
                        <h6>Total Riders</h6>
                        <h3>{{ $stats['total_riders'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-motorcycle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Orders</h6>
                        <h3>{{ $stats['total_orders'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Revenue</h6>
                        <h3>৳{{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Commission Earned</h6>
                <h3>৳{{ number_format($stats['total_commission'] ?? 0, 2) }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Pending Orders</h6>
                <h3>{{ $stats['pending_orders'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Delivered Today</h6>
                <h3>{{ $stats['delivered_today'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Recent Orders</h5>
            </div>
            <div class="card-body">
                @if(isset($recent_orders) && $recent_orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Buyer</th>
                                    <th>Supplier</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_orders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->buyerOutlet->shop_name ?? 'N/A' }}</td>
                                    <td>{{ $order->supplierOutlet->shop_name ?? 'Pending' }}</td>
                                    <td>৳{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No orders found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.outlets.index') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-store"></i> Manage Outlets
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.riders.index') }}" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-motorcycle"></i> Manage Riders
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-chart-line"></i> View Reports
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.settings') }}" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection