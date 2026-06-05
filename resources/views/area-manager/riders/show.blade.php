@extends('layouts.app')

@section('title', 'Rider Details - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Rider Details</h2>
        <p class="text-muted">{{ $rider->name }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('area-manager.riders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Riders
        </a>
        <a href="{{ route('area-manager.riders.edit', $rider->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Rider
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Name:</th>
                        <td><strong>{{ $rider->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $rider->phone }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $rider->email }}</br>
                        <small class="text-muted">{{ $rider->email_verified_at ? 'Verified' : 'Not verified' }}</small>
                    </td>
                    </tr>
                    <tr>
                        <th>Area:</th>
                        <td>{{ $rider->area->name ?? 'N/A' }} - {{ $rider->area->city ?? '' }}</br>
                        <small>Delivery Zone</small>
                    </td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($rider->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Joined:</th>
                        <td>{{ $rider->created_at->format('d M Y, h:i A') }}</br>
                        <small>{{ $rider->created_at->diffForHumans() }}</small>
                    </td>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-wallet"></i> Financial Info</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Wallet Balance:</th>
                        <td><strong class="text-success">৳{{ number_format($rider->wallet_balance, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Total Earnings:</th>
                        <td>৳{{ number_format($stats['total_earnings'] ?? 0, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Delivery Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-primary">{{ $stats['total_deliveries'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Total Deliveries</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-success">{{ $stats['completed_deliveries'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Completed</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h3 class="text-warning">{{ $stats['pending_deliveries'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Deliveries</h5>
            </div>
            <div class="card-body">
                @if(isset($recentDeliveries) && $recentDeliveries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Delivery Fee</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDeliveries as $delivery)
                                <tr>
                                    <td>{{ $delivery->order_number }}</td>
                                    <td>{{ $delivery->supplierOutlet->shop_name ?? 'N/A' }}</td>
                                    <td>{{ $delivery->buyerOutlet->shop_name ?? 'N/A' }}</td>
                                    <td>৳{{ number_format($delivery->delivery_charge, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $delivery->status == 'delivered' ? 'success' : 'warning' }}">
                                            {{ ucfirst($delivery->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $delivery->delivered_at ? $delivery->delivered_at->format('d M Y') : $delivery->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No deliveries found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection