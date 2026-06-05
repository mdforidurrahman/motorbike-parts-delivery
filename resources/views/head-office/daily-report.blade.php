@extends('layouts.app')

@section('title', 'Daily Report - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Daily Report</h2>
        <p class="text-muted">Overview of daily business performance</p>
    </div>
    <div class="col-md-6 text-end">
        <form method="GET" action="{{ route('head-office.reports.daily') }}" class="d-inline">
            <div class="input-group">
                <input type="date" name="date" class="form-control" value="{{ $date }}" style="width: auto;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar"></i> View Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Orders</h6>
                        <h3 class="text-white">{{ $dailyStats['total_orders'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Completed Orders</h6>
                        <h3>{{ $dailyStats['completed_orders'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Revenue</h6>
                        <h3>৳{{ number_format($dailyStats['total_revenue'] ?? 0, 2) }}</h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Commission Earned</h6>
                        <h3>৳{{ number_format($dailyStats['total_commission'] ?? 0, 2) }}</h3>
                    </div>
                    <i class="fas fa-percent fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-store"></i> New Registrations</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <h3>{{ $dailyStats['new_outlets'] ?? 0 }}</h3>
                        <p class="text-muted">New Outlets</p>
                    </div>
                    <div class="col-md-6">
                        <h3>{{ $dailyStats['new_riders'] ?? 0 }}</h3>
                        <p class="text-muted">New Riders</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Order Statistics</h5>
            </div>
            <div class="card-body">
                <canvas id="orderChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Orders ({{ date('d M Y', strtotime($date)) }})</h5>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Buyer</th>
                                    <th>Supplier</th>
                                    <th>Amount</th>
                                    <th>Commission</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->buyerOutlet->shop_name ?? 'N/A' }}<br>
                                        <small>{{ $order->buyerOutlet->phone ?? '' }}</small>
                                    </td>
                                    <td>{{ $order->supplierOutlet->shop_name ?? 'Pending' }}<br>
                                        <small>{{ $order->supplierOutlet->phone ?? '' }}</small>
                                    </td>
                                    <td>৳{{ number_format($order->total_amount, 2) }}</br>
                                        <small>Delivery: ৳{{ number_format($order->delivery_charge, 2) }}</small>
                                    </td>
                                    <td>৳{{ number_format($order->commission_amount, 2) }}</br>
                                        <small>1%</small>
                                    </td>
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
                                    <td>
                                        {{ $order->created_at->format('h:i A') }}<br>
                                        <small>{{ $order->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No orders found for this date.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Summary Footer -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Daily Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6>Average Order Value</h6>
                            <h4>
                                ৳{{ number_format(($dailyStats['total_orders'] > 0 ? $dailyStats['total_revenue'] / $dailyStats['total_orders'] : 0), 2) }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6>Completion Rate</h6>
                            <h4>
                                {{ number_format(($dailyStats['total_orders'] > 0 ? ($dailyStats['completed_orders'] / $dailyStats['total_orders']) * 100 : 0), 1) }}%
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6>Average Commission</h6>
                            <h4>
                                ৳{{ number_format(($dailyStats['total_orders'] > 0 ? $dailyStats['total_commission'] / $dailyStats['total_orders'] : 0), 2) }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6>Report Date</h6>
                            <h4>{{ date('d M, Y', strtotime($date)) }}</h4>
                        </div>
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
    // Order Statistics Chart
    const ctx = document.getElementById('orderChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'In Progress', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $dailyStats['completed_orders'] ?? 0 }},
                    {{ $dailyStats['total_orders'] - ($dailyStats['completed_orders'] ?? 0) - ($dailyStats['cancelled_orders'] ?? 0) }},
                    {{ $dailyStats['in_progress_orders'] ?? 0 }},
                    {{ $dailyStats['cancelled_orders'] ?? 0 }}
                ],
                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush