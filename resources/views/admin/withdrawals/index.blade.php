@extends('layouts.app')

@section('title', 'Withdrawal Requests - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Withdrawal Requests</h2>
        <p class="text-muted">Manage outlet withdrawal requests</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['pending'] ?? 0 }}</h3>
                <p class="mb-0">Pending</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['processing'] ?? 0 }}</h3>
                <p class="mb-0">Processing</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>৳{{ number_format($stats['completed'] ?? 0, 2) }}</h3>
                <p class="mb-0">Total Paid</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>৳{{ number_format($stats['total_requested'] ?? 0, 2) }}</h3>
                <p class="mb-0">Total Requested</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($withdrawals->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Outlet</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawals as $withdrawal)
                        <tr>
                            <td>
                                {{ $withdrawal->created_at->format('d M Y, h:i A') }}<br>
                                <small class="text-muted">{{ $withdrawal->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                {{ $withdrawal->reference }}<br>
                                <small class="text-muted">ID: {{ $withdrawal->id }}</small>
                            </td>
                            <td>
                                <strong>{{ $withdrawal->outlet->shop_name ?? 'N/A' }}</strong><br>
                                <small>{{ $withdrawal->outlet->owner->name ?? '' }}<br>
                                {{ $withdrawal->outlet->phone ?? '' }}</small>
                            </td>
                            <td>
                                <strong class="text-danger">- ৳{{ number_format($withdrawal->amount, 2) }}</strong>
                            </td>
                            <td>
                                @if($withdrawal->payment_method == 'bkash')
                                    <i class="fab fa-bkash"></i> bKash
                                @elseif($withdrawal->payment_method == 'nagad')
                                    <i class="fas fa-mobile-alt"></i> Nagad
                                @elseif($withdrawal->payment_method == 'rocket')
                                    <i class="fas fa-rocket"></i> Rocket
                                @else
                                    <i class="fas fa-university"></i> Bank
                                @endif
                                <br>
                                <small>{{ $withdrawal->account_number ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'rejected' => 'danger',
                                        'cancelled' => 'secondary'
                                    ][$withdrawal->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst($withdrawal->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($withdrawal->status == 'pending')
                                        <form action="{{ route('admin.withdrawals.processing', $withdrawal->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" title="Mark as Processing">
                                                <i class="fas fa-spinner"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this withdrawal?')" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this withdrawal?')" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $withdrawals->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                <p class="text-muted">No withdrawal requests found.</p>
            </div>
        @endif
    </div>
</div>
@endsection