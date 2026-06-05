@extends('layouts.app')

@section('title', 'My Earnings - MotoLink')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">My Earnings</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Total Earnings</h6>
                <h3>৳{{ number_format($dailyEarnings->sum('total'), 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Total Deliveries</h6>
                <h3>{{ $dailyEarnings->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6>Wallet Balance</h6>
                <h3>৳{{ number_format(auth()->user()->wallet_balance, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Daily Earnings</h5>
            </div>
            <div class="card-body">
                <canvas id="earningsChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyEarnings->pluck('date')->map(function($date) { 
                return \Carbon\Carbon::parse($date)->format('d M'); 
            })) !!},
            datasets: [{
                label: 'Daily Earnings (৳)',
                data: {!! json_encode($dailyEarnings->pluck('total')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        }
    });
</script>
@endpush