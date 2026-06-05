@extends('layouts.app')

@section('title', 'Monthly Report - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Monthly Report</h2>
        <p class="text-muted">{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</p>
    </div>
    <div class="col-md-6 text-end">
        <form method="GET" action="{{ route('head-office.reports.monthly') }}" class="d-inline">
            <div class="input-group">
                <select name="month" class="form-control" style="width: auto;">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="form-control" style="width: auto;">
                    @for($i = date('Y'); $i >= date('Y')-2; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar"></i> View
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Total Orders</h6>
                <h3>{{ $monthlyStats['total_orders'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Completed Orders</h6>
                <h3>{{ $monthlyStats['completed_orders'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Total Revenue</h6>
                <h3>৳{{ number_format($monthlyStats['total_revenue'] ?? 0, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Daily Breakdown</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Summary</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Total Commission:</th>
                        <td>৳{{ number_format($monthlyStats['total_commission'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <th>New Outlets:</th>
                        <td>{{ $monthlyStats['new_outlets'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>New Riders:</th>
                        <td>{{ $monthlyStats['new_riders'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Average Daily Orders:</th>
                        <td>{{ number_format(($monthlyStats['total_orders'] ?? 0) / 30, 1) }}</td>
                    </tr>
                    <tr>
                        <th>Average Order Value:</th>
                        <td>৳{{ number_format(($monthlyStats['total_orders'] > 0 ? ($monthlyStats['total_revenue'] / $monthlyStats['total_orders']) : 0), 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyBreakdown->pluck('day')->map(function($day) { return 'Day ' . $day; })) !!},
            datasets: [{
                label: 'Revenue (৳)',
                data: {!! json_encode($dailyBreakdown->pluck('revenue')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'Orders',
                data: {!! json_encode($dailyBreakdown->pluck('orders')) !!},
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Daily Performance'
                }
            }
        }
    });
</script>
@endpush