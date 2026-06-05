@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Commission Report</h3>
                    <div class="card-tools">
                        <form method="GET" action="{{ route('head-office.commission') }}" class="form-inline">
                            <div class="input-group">
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Commission</span>
                                    <span class="info-box-number">BDT {{ number_format($totalCommission, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order ID</th>
                                <th>Outlet</th>
                                <th>Amount</th>
                                <th>Commission Rate</th>
                                <th>Commission Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commissions as $commission)
                            <tr>
                                <td>{{ $commission->delivered_at->format('Y-m-d') }}</td>
                                <td>#{{ $commission->id }}</td>
                                <td>{{ $commission->outlet->name ?? 'N/A' }}</td>
                                <td>BDT {{ number_format($commission->total_amount, 2) }}</td>
                                <td>{{ $commission->commission_rate ?? '0' }}%</td>
                                <td>BDT {{ number_format($commission->commission_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No commission records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Total:</th>
                                <th>BDT {{ number_format($totalCommission, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection