@extends('layouts.app')

@section('title', 'Head Office Dashboard - MotoLink')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Head Office Dashboard</h2>
        <p class="text-muted">Welcome, {{ auth()->user()->name }}!</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Monthly Revenue</h6>
                        <h3 class="text-white">৳{{ number_format($monthlyEarnings->total ?? 0, 2) }}</h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Monthly Commission</h6>
                        <h3>৳{{ number_format($monthlyEarnings->commission ?? 0, 2) }}</h3>
                    </div>
                    <i class="fas fa-percent fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Outlets</h6>
                        <h3>{{ \App\Models\Outlet::count() }}</h3>
                    </div>
                    <i class="fas fa-store fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Performing Outlets -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning">
                <h5>Top Performing Outlets</h5>
            </div>
            <div class="card-body">
                @if(isset($topOutlets) && $topOutlets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Shop Name</th>
                                    <th>Area</th>
                                    <th>Completed Deliveries</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topOutlets as $index => $outlet)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $outlet->shop_name }}</strong><br>
                                        <small class="text-muted">{{ $outlet->phone }}</small>
                                    </td>
                                    <td>{{ $outlet->area->name ?? 'N/A' }}</td>
                                    <td>{{ $outlet->completed_deliveries ?? 0 }}</td>
                                    <td>
                                        <span class="badge bg-{{ $outlet->type == 'contracted' ? 'success' : 'info' }}">
                                            {{ ucfirst($outlet->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($outlet->is_verified)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-danger">Pending</span>
                                        @endif
                                     </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No outlet data available.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Chart -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Revenue Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ route('head-office.outlets.all') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-store"></i> View All Outlets
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('head-office.financial') }}" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-chart-pie"></i> Financial Report
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('head-office.commission') }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-percent"></i> Commission Report
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('head-office.reports.daily') }}" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-calendar-day"></i> Daily Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Monthly Revenue (৳)',
                data: [15000, 22000, 18000, 25000, 30000, 35000, 32000, 40000, 38000, 45000, 42000, 50000],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        }
    });
</script>
@endpush