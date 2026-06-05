@extends('layouts.app')

@section('title', 'Withdrawal Request Details - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Withdrawal Request Details</h2>
        <p class="text-muted">Reference: {{ $withdrawal->reference }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Requests
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-store"></i> Outlet Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Shop Name:</th>
                        <td><strong>{{ $withdrawal->outlet->shop_name ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Owner Name:</th>
                        <td>{{ $withdrawal->outlet->owner->name ?? 'N/A' }}</br>
                            <small>{{ $withdrawal->outlet->owner->email ?? '' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $withdrawal->outlet->phone ?? 'N/A' }}</br>
                            <small>{{ $withdrawal->outlet->owner->phone ?? '' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td>{{ $withdrawal->outlet->address ?? 'N/A' }}</br>
                            <small>Area: {{ $withdrawal->outlet->area->name ?? 'N/A' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Current Wallet:</th>
                        <td><strong class="text-success">৳{{ number_format($withdrawal->outlet->wallet_balance ?? 0, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Withdrawal Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Request Date:</th>
                        <td>{{ $withdrawal->created_at->format('d M Y, h:i A') }}<br>
                            <small>{{ $withdrawal->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Reference:</th>
                        <td><strong>{{ $withdrawal->reference }}</strong></td>
                    </tr>
                    <tr>
                        <th>Amount:</th>
                        <td><strong class="text-danger">- ৳{{ number_format($withdrawal->amount, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
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
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($withdrawal->status) }}</span>
                        </td>
                    </tr>
                    @if($withdrawal->approved_by)
                    <tr>
                        <th>Approved By:</th>
                        <td>{{ $withdrawal->approver->name ?? 'N/A' }}<br>
                            <small>{{ $withdrawal->approved_at ? $withdrawal->approved_at->format('d M Y, h:i A') : '' }}</small>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-credit-card"></i> Payment Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Payment Method:</th>
                        <td>
                            @if($withdrawal->payment_method == 'bkash')
                                <i class="fab fa-bkash"></i> bKash
                            @elseif($withdrawal->payment_method == 'nagad')
                                <i class="fas fa-mobile-alt"></i> Nagad
                            @elseif($withdrawal->payment_method == 'rocket')
                                <i class="fas fa-rocket"></i> Rocket
                            @else
                                <i class="fas fa-university"></i> Bank Transfer
                            @endif
                        </td>
                    </tr>
                    @if($withdrawal->payment_method == 'bank')
                    <tr>
                        <th>Bank Name:</th>
                        <td>{{ $withdrawal->bank_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Account Holder:</th>
                        <td>{{ $withdrawal->account_holder ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Branch Name:</th>
                        <td>{{ $withdrawal->branch_name ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Account Number:</th>
                        <td><strong>{{ $withdrawal->account_number ?? 'N/A' }}</strong></td>
                    </tr>
                    @if($withdrawal->note)
                    <tr>
                        <th>Note:</th>
                        <td>{{ $withdrawal->note }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Action Log</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon bg-success">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Request Created</h6>
                            <p class="text-muted">{{ $withdrawal->created_at->format('d M Y, h:i A') }}</p>
                            <small>By: {{ $withdrawal->outlet->shop_name ?? 'N/A' }}</small>
                        </div>
                    </div>
                    
                    @if($withdrawal->status == 'processing')
                    <div class="timeline-item">
                        <div class="timeline-icon bg-info">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Under Processing</h6>
                            <p class="text-muted">{{ $withdrawal->updated_at->format('d M Y, h:i A') }}</p>
                            <small>Admin is reviewing</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($withdrawal->status == 'completed')
                    <div class="timeline-item">
                        <div class="timeline-icon bg-success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Payment Completed</h6>
                            <p class="text-muted">{{ $withdrawal->approved_at ? $withdrawal->approved_at->format('d M Y, h:i A') : '' }}</p>
                            <small>By: {{ $withdrawal->approver->name ?? 'Admin' }}</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($withdrawal->status == 'rejected')
                    <div class="timeline-item">
                        <div class="timeline-icon bg-danger">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Request Rejected</h6>
                            <p class="text-muted">{{ $withdrawal->updated_at->format('d M Y, h:i A') }}</p>
                            <small>By: {{ $withdrawal->approver->name ?? 'Admin' }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($withdrawal->status == 'pending')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-gavel"></i> Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <form action="{{ route('admin.withdrawals.processing', $withdrawal->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fas fa-spinner"></i> Mark as Processing
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Confirm approval? Amount will be sent to outlet owner.')">
                                <i class="fas fa-check"></i> Approve & Complete
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Reject this request? Amount will be added back to outlet wallet.')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 25px;
    }
    .timeline-icon {
        position: absolute;
        left: -30px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .timeline-content {
        padding-left: 20px;
    }
    .timeline-content h6 {
        margin-bottom: 5px;
    }
    .table-borderless th {
        font-weight: 600;
        color: #6c757d;
    }
</style>
@endpush