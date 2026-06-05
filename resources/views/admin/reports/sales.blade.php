@extends('layouts.app')

@section('title', 'Sales Report - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Sales Report</h2>
        <p class="text-muted">View all sales transactions</p>
    </div>
    <div class="col-md-6 text-end">
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="d-inline">
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
                <h6 class="text-white-50">Total Sales</h6>
                <h3 class="text-white">৳{{ number_format($totalSales, 2) }}</h3>
                <small>{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50">Total Orders</h6>
                <h3 class="text-white">{{ $totalOrders }}</h3>
                <small>During this period</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50">Average Order Value</h6>
                <h3 class="text-white">৳{{ number_format($totalOrders > 0 ? $totalSales / $totalOrders : 0, 2) }}</h3>
                <small>Per order average</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> Daily Sales Details</h5>
    </div>
    <div class="card-body">
        @if($sales->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Orders</th>
                            <th>Total Sales</th>
                            <th>Average per Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>
                                <strong>{{ date('d M Y', strtotime($sale->date)) }}</strong><br>
                                <small>{{ \Carbon\Carbon::parse($sale->date)->diffForHumans() }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $sale->orders }}</span>
                            </td>
                            <td>
                                <strong class="text-success">৳{{ number_format($sale->total, 2) }}</strong>
                            </td>
                            <td>
                                ৳{{ number_format($sale->total / $sale->orders, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <th>Total</th>
                            <th>{{ $sales->sum('orders') }}</th>
                            <th>৳{{ number_format($sales->sum('total'), 2) }}</th>
                            <th>৳{{ number_format($sales->sum('total') / max($sales->sum('orders'), 1), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $sales->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <p class="text-muted">No sales data found for this period.</p>
            </div>
        @endif
    </div>
</div>
@endsection