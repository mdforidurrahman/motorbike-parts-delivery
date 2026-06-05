@extends('layouts.app')

@section('title', 'Area Details - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Area Details</h2>
        <p class="text-muted">{{ $area->name }} - {{ $area->city }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.areas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Areas
        </a>
        <a href="{{ route('admin.areas.edit', $area->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Area
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Area Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Area Name:</th>
                        <td><strong>{{ $area->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>City:</th>
                        <td>{{ $area->city }}</td>
                    </tr>
                    <tr>
                        <th>Delivery Charge:</th>
                        <td><strong class="text-success">৳{{ number_format($area->delivery_charge, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Created At:</th>
                        <td>{{ $area->created_at->format('d M Y, h:i A') }}<br>
                            <small>{{ $area->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>{{ $area->updated_at->format('d M Y, h:i A') }}<br>
                            <small>{{ $area->updated_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-store"></i> Outlets in this Area</h5>
            </div>
            <div class="card-body">
                @if($area->outlets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Shop Name</th>
                                    <th>Owner</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($area->outlets as $outlet)
                                <tr>
                                    <td>{{ $outlet->shop_name }}<br>
                                        <small class="text-muted">{{ $outlet->address }}</small>
                                    </td>
                                    <td>{{ $outlet->owner->name ?? 'N/A' }}</br>
                                        <small>{{ $outlet->owner->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $outlet->phone }}</br>
                                        <small>{{ $outlet->owner->phone ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $outlet->type == 'contracted' ? 'success' : 'info' }}">
                                            {{ ucfirst($outlet->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($outlet->is_verified)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No outlets in this area.</p>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-users"></i> Riders in this Area</h5>
            </div>
            <div class="card-body">
                @if($area->users->where('role.name', 'rider')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($area->users->where('role.name', 'rider') as $rider)
                                <tr>
                                    <td>{{ $rider->name }}<br>
                                        <small>Joined: {{ $rider->created_at->format('d M Y') }}</small>
                                    </td>
                                    <td>{{ $rider->phone }}</br>
                                        <small>{{ $rider->deliveries()->count() }} deliveries</small>
                                    </td>
                                    <td>{{ $rider->email }}<br>
                                        <small>{{ $rider->wallet_balance }} TK</small>
                                    </td>
                                    <td>
                                        @if($rider->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No riders in this area.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection