@extends('layouts.app')

@section('title', 'User Details - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>User Details</h2>
        <p class="text-muted">{{ $user->name }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit User
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Name:</th>
                        <td><strong>{{ $user->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $user->email }}<br>
                            <small>{{ $user->email_verified_at ? 'Verified' : 'Not verified' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $user->phone }}</br>
                            <small>{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Role:</th>
                        <td>{{ $user->role->display_name ?? 'N/A' }}<br>
                            <small>Role ID: {{ $user->role_id }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Area:</th>
                        <td>{{ $user->area->name ?? 'Not Assigned' }}<br>
                            <small>{{ $user->area->city ?? '' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Joined:</th>
                        <td>{{ $user->created_at->format('d M Y, h:i A') }}<br>
                            <small>{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>{{ $user->updated_at->format('d M Y, h:i A') }}</br>
                            <small>{{ $user->updated_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Statistics</h5>
            </div>
            <div class="card-body">
                @if($user->hasRole('rider'))
                    <div class="row text-center">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h3 class="text-primary">{{ $user->deliveries()->count() }}</h3>
                                <p class="text-muted mb-0">Total Deliveries</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h3 class="text-success">৳{{ number_format($user->deliveries()->sum('delivery_charge'), 2) }}</h3>
                                <p class="text-muted mb-0">Total Earnings</p>
                            </div>
                        </div>
                    </div>
                @elseif($user->hasRole('outlet-owner'))
                    <div class="row text-center">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h3 class="text-primary">{{ $user->outlet->ordersAsSupplier()->count() ?? 0 }}</h3>
                                <p class="text-muted mb-0">Orders Received</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h3 class="text-success">৳{{ number_format($user->outlet->wallet_balance ?? 0, 2) }}</h3>
                                <p class="text-muted mb-0">Wallet Balance</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-simple fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No statistics available for this role.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Permissions</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Dashboard Access
                        <span class="badge bg-success">Granted</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Profile Management
                        <span class="badge bg-success">Granted</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $user->role->display_name ?? 'Role' }} Specific Access
                        <span class="badge bg-info">Granted</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection