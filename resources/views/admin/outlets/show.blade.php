@extends('layouts.app')

@section('title', 'Outlet Details - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>{{ $outlet->shop_name }}</h2>
        <p class="text-muted">{{ $outlet->type }} Outlet • {{ $outlet->area->name ?? 'No Area' }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.outlets.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Outlet Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Shop Name:</th>
                        <td>{{ $outlet->shop_name }}</td>
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
                        <td>{{ $outlet->area->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Type:</th>
                        <td>
                            @if($outlet->type == 'contracted')
                                <span class="badge bg-success">Contracted</span>
                            @elseif($outlet->type == 'large')
                                <span class="badge bg-info">Large</span>
                            @else
                                <span class="badge bg-secondary">Small</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Verified:</th>
                        <td>
                            @if($outlet->is_verified)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Wallet Balance:</th>
                        <td><strong>৳{{ number_format($outlet->wallet_balance, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Joined:</th>
                        <td>{{ $outlet->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-primary">{{ $stats['total_orders'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-success">{{ $stats['completed_orders'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Completed</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-info">৳{{ number_format($stats['total_earnings'] ?? 0, 2) }}</h3>
                            <p class="text-muted mb-0">Total Earnings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Recent Orders</h5>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->buyerOutlet->shop_name ?? 'N/A' }}</td>
                                    <td>৳{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
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
@endsection