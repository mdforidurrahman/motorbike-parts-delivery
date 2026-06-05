@extends('layouts.app')

@section('title', 'Financial Report - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Financial Report</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Revenue Summary</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h6>Total Revenue</h6>
                            <h3 class="text-primary">৳{{ number_format($totalRevenue ?? 0, 2) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h6>Total Commission</h6>
                            <h3 class="text-success">৳{{ number_format($totalCommission ?? 0, 2) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h6>Total Orders</h6>
                            <h3 class="text-info">{{ $totalOrders ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h6>Active Outlets</h6>
                            <h3 class="text-warning">{{ $activeOutlets ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection