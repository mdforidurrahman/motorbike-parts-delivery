@extends('layouts.app')

@section('title', 'Wallet - MotoLink')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">My Wallet</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Current Balance</h5>
                <h2 class="card-text">৳{{ number_format($outlet->wallet_balance, 2) }}</h2>
                <p class="card-text mt-3">
                    <small>Available for withdrawal</small>
                </p>
                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <i class="fas fa-money-bill-wave"></i> Withdraw
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Transaction History</h5>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Account Info</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        {{ $transaction->created_at->format('d M Y, h:i A') }}<br>
                                        <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        {{ $transaction->reference }}<br>
                                        <small class="text-muted">Order #{{ $transaction->order_id ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($transaction->type == 'deposit')
                                            <span class="badge bg-success">Deposit</span>
                                        @elseif($transaction->type == 'withdrawal')
                                            <span class="badge bg-danger">Withdrawal</span>
                                        @elseif($transaction->type == 'commission')
                                            <span class="badge bg-info">Commission</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->type) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->type == 'deposit' || $transaction->type == 'commission')
                                            <span class="text-success">+ ৳{{ number_format($transaction->amount, 2) }}</span>
                                        @else
                                            <span class="text-danger">- ৳{{ number_format($transaction->amount, 2) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'completed' => 'success',
                                                'failed' => 'danger',
                                                'cancelled' => 'secondary'
                                            ][$transaction->status] ?? 'secondary';
                                            
                                            $statusText = [
                                                'pending' => 'Pending',
                                                'processing' => 'Processing',
                                                'completed' => 'Completed',
                                                'failed' => 'Failed',
                                                'cancelled' => 'Cancelled'
                                            ][$transaction->status] ?? ucfirst($transaction->status);
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($transaction->payment_method)
                                            <strong>{{ ucfirst($transaction->payment_method) }}</strong><br>
                                            <small class="text-muted">Account: {{ $transaction->account_number ?? 'N/A' }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->note ?? '-' }}</br>
                                        <small>{{ $transaction->payment_method ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($transaction->type == 'withdrawal' && $transaction->status == 'pending')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="cancelWithdrawal({{ $transaction->id }})">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        @elseif($transaction->type == 'withdrawal' && $transaction->status == 'processing')
                                            <span class="badge bg-info">Processing</span>
                                        @elseif($transaction->type == 'withdrawal' && $transaction->status == 'completed')
                                            <span class="badge bg-success">Paid</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No transaction history found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Quick Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6>Total Earned</h6>
                            <h4 class="text-success">
                                ৳{{ number_format($transactions->where('type', 'deposit')->sum('amount'), 2) }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6>Total Withdrawn</h6>
                            <h4 class="text-danger">
                                ৳{{ number_format($transactions->where('type', 'withdrawal')->where('status', 'completed')->sum('amount'), 2) }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6>Pending Withdrawals</h6>
                            <h4 class="text-warning">
                                ৳{{ number_format($transactions->where('type', 'withdrawal')->where('status', 'pending')->sum('amount'), 2) }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6>Commission Earned</h6>
                            <h4 class="text-info">
                                ৳{{ number_format($transactions->where('type', 'commission')->sum('amount'), 2) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Withdraw Money</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('outlet.withdraw.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Current Balance:</strong> ৳{{ number_format($outlet->wallet_balance, 2) }}
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Withdrawal Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" step="0.01" name="amount" class="form-control" 
                                   required min="100" max="{{ $outlet->wallet_balance }}" id="withdrawAmount">
                        </div>
                        <small class="text-muted">Minimum withdrawal: ৳100</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="paymentMethod" class="form-control" required>
                            <option value="">Select Method</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="accountNumberField">
                        <label class="form-label">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control" required>
                        <small class="text-muted" id="accountHint">Enter your bKash/Nagad/Rocket account number</small>
                    </div>
                    
                    <div class="mb-3" id="bankFields" style="display: none;">
                        <div class="mb-2">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Account Holder Name</label>
                            <input type="text" name="account_holder" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Branch Name</label>
                            <input type="text" name="branch_name" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Note (Optional)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Any additional information"></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> 
                        Withdrawal requests will be processed within 24-48 hours.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Request Withdrawal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Withdrawal Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Withdrawal Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this withdrawal request?</p>
                <p class="text-danger">The amount will be added back to your wallet.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form id="cancelForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cancel Request</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show/hide bank fields based on payment method
    $('#paymentMethod').change(function() {
        var method = $(this).val();
        if (method === 'bank') {
            $('#bankFields').show();
            $('#accountNumberField').hide();
            $('#accountHint').text('Enter your bank account number');
        } else {
            $('#bankFields').hide();
            $('#accountNumberField').show();
            if (method === 'bkash') {
                $('#accountHint').text('Enter your bKash account number (01XXXXXXXXX)');
            } else if (method === 'nagad') {
                $('#accountHint').text('Enter your Nagad account number');
            } else if (method === 'rocket') {
                $('#accountHint').text('Enter your Rocket account number');
            } else {
                $('#accountHint').text('Enter your account number');
            }
        }
    });
    
    // Validate amount
    $('#withdrawAmount').on('input', function() {
        var amount = parseFloat($(this).val());
        var maxAmount = parseFloat('{{ $outlet->wallet_balance }}');
        var minAmount = 100;
        
        if (amount > maxAmount) {
            $(this).val(maxAmount);
            alert('Amount cannot exceed your wallet balance!');
        }
        if (amount < minAmount && amount > 0) {
            $(this).val(minAmount);
            alert('Minimum withdrawal amount is ৳100');
        }
    });
    
    function cancelWithdrawal(transactionId) {
        $('#cancelForm').attr('action', '/outlet/withdrawals/' + transactionId + '/cancel');
        $('#cancelModal').modal('show');
    }
</script>
@endpush