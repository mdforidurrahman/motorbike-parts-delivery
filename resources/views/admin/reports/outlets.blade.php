@extends('layouts.app')

@section('title', 'Outlet Performance Report - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Outlet Performance Report</h2>
        <p class="text-muted">View outlet statistics and revenue</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($outlets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Shop Name</th>
                            <th>Area</th>
                            <th>Type</th>
                            <th>Total Orders</th>
                            <th>Total Revenue</th>
                            <th>Wallet Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outlets as $index => $outlet)
                        <tr>
                            <td>
                                @if($index == 0)
                                    <span class="badge bg-warning">🥇 #1</span>
                                @elseif($index == 1)
                                    <span class="badge bg-secondary">🥈 #2</span>
                                @elseif($index == 2)
                                    <span class="badge bg-danger">🥉 #3</span>
                                @else
                                    <span class="badge bg-light text-dark">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $outlet->shop_name }}</strong><br>
                                <small class="text-muted">{{ $outlet->phone }}</small>
                            </td>
                            <td>{{ $outlet->area->name ?? 'N/A' }}<br>
                                <small>{{ $outlet->area->city ?? '' }}</small>
                            </td>
                            <td>
                                @if($outlet->type == 'contracted')
                                    <span class="badge bg-success">Contracted</span>
                                @elseif($outlet->type == 'large')
                                    <span class="badge bg-info">Large</span>
                                @else
                                    <span class="badge bg-secondary">Small</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $outlet->total_orders ?? 0 }}</span>
                            </td>
                            <td>
                                <strong class="text-success">৳{{ number_format($outlet->total_revenue ?? 0, 2) }}</strong>
                            </td>
                            <td>
                                ৳{{ number_format($outlet->wallet_balance, 2) }}
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
            
            <div class="mt-4">
                {{ $outlets->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-store fa-3x text-muted mb-3"></i>
                <p class="text-muted">No outlet data found.</p>
            </div>
        @endif
    </div>
</div>
@endsection