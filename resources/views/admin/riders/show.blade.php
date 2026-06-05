@extends('layouts.app')

@section('title', 'Rider Details - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>{{ $rider->name }}</h2>
        <p class="text-muted">Delivery Rider • {{ $rider->area->name ?? 'No Area' }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.riders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Personal Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $rider->name }}</p>
                <p><strong>Phone:</strong> {{ $rider->phone }}</p>
                <p><strong>Email:</strong> {{ $rider->email }}</p>
                <p><strong>Area:</strong> {{ $rider->area->name ?? 'N/A' }}</p>
                <p><strong>Status:</strong>
                    @if($rider->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </p>
                <p><strong>Wallet Balance:</strong> ৳{{ number_format($rider->wallet_balance, 2) }}</p>
                <p><strong>Joined:</strong> {{ $rider->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Delivery Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3>{{ $stats['total_deliveries'] ?? 0 }}</h3>
                            <p class="text-muted">Total Deliveries</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3>{{ $stats['completed_deliveries'] ?? 0 }}</h3>
                            <p class="text-muted">Completed</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3>৳{{ number_format($stats['total_earnings'] ?? 0, 2) }}</h3>
                            <p class="text-muted">Total Earnings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Recent Deliveries</h5>
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
                                    <td>{{ ucfirst($delivery->status) }}</td>
                                    <td>{{ $delivery->delivered_at ? $delivery->delivered_at->format('d M Y') : 'Pending' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No deliveries found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection