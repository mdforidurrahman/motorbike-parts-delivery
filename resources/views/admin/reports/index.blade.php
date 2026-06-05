@extends('layouts.app')

@section('title', 'Reports - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Reports Dashboard</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Sales Report</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="300"></canvas>
                <div class="mt-3">
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-primary">View Detailed Report</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Commission Report</h5>
            </div>
            <div class="card-body">
                <canvas id="commissionChart" height="300"></canvas>
                <div class="mt-3">
                    <a href="{{ route('admin.reports.commission') }}" class="btn btn-primary">View Detailed Report</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Quick Reports</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.riders') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-motorcycle"></i> Rider Performance
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.outlets') }}" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-store"></i> Outlet Performance
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-chart-line"></i> Daily Sales
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.commission') }}" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-dollar-sign"></i> Commission Report
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
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales (৳)',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
    });
    
    // Commission Chart
    const commissionCtx = document.getElementById('commissionChart').getContext('2d');
    new Chart(commissionCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Commission (৳)',
                data: [120, 190, 150, 250, 220, 300],
                backgroundColor: 'rgb(255, 99, 132)'
            }]
        }
    });
</script>
@endpush