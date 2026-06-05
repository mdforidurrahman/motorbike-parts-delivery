@extends('layouts.app')

@section('title', 'Commission Report - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Commission Report</h2>
        <p class="text-muted">View all commission earnings (1% of each order)</p>
    </div>
    <div class="col-md-6 text-end">
        <form method="GET" action="{{ route('admin.reports.commission') }}" class="d-inline">
            <div class="input-group">
                <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}" style="width: auto;">
                <span class="input-group-text">to</span>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}" style="width: auto;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50">Total Commission</h6>
                <h3 class="text-white">৳{{ number_format($totalCommission, 2) }}</h3>
                <small>{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50">Total Orders</h6>
                <h3 class="text-white">{{ $commissions->sum('orders') }}</h3>
                <small>During this period</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50">Average Commission</h6>
                <h3 class="text-white">৳{{ number_format($commissions->avg('total') ?? 0, 2) }}</h3>
                <small>Per day average</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Daily Commission Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="commissionChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Commission Details</h5>
            </div>
            <div class="card-body">
                @if($commissions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Orders</th>
                                    <th>Total Commission</th>
                                    <th>Average per Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commissions as $commission)
                                <tr>
                                    <td>
                                        <strong>{{ date('d M Y', strtotime($commission->date)) }}</strong><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($commission->date)->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $commission->orders }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">৳{{ number_format($commission->total, 2) }}</strong>
                                    </td>
                                    <td>
                                        ৳{{ number_format($commission->total / $commission->orders, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-active">
                                <tr>
                                    <th>Total</th>
                                    <th>{{ $commissions->sum('orders') }}</th>
                                    <th>৳{{ number_format($commissions->sum('total'), 2) }}</th>
                                    <th>৳{{ number_format($commissions->sum('total') / max($commissions->sum('orders'), 1), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $commissions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No commission data found for this period.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare chart data
    const dates = {!! json_encode($commissions->pluck('date')->map(function($date) { 
        return \Carbon\Carbon::parse($date)->format('d M'); 
    })) !!};
    
    const amounts = {!! json_encode($commissions->pluck('total')) !!};
    
    const ctx = document.getElementById('commissionChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Commission (৳)',
                data: amounts,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Commission: ৳' + context.raw.toFixed(2);
                        }
                    }
                }
            }
        }
    });
</script>
@endpush