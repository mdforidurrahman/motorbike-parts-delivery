@extends('layouts.app')

@section('title', 'Withdraw - MotoLink')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Withdraw Money</h4>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="alert alert-info">
                    <strong>Current Balance:</strong> ৳{{ number_format($outlet->wallet_balance, 2) }}
                </div>

                <form method="POST" action="{{ route('outlet.withdraw.store') }}" id="withdrawForm">
                    @csrf

                    {{-- AMOUNT --}}
                    <div class="mb-3">
                        <label class="form-label">Withdrawal Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number"
                                   step="100"
                                   name="amount"
                                   id="withdrawAmount"
                                   class="form-control"
                                   value="{{ old('amount') }}"
                                   min="500"
                                   max="{{ $outlet->wallet_balance }}"
                                   required>
                        </div>
                        <small class="text-muted">Minimum withdrawal: ৳500</small>
                        @error('amount')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PAYMENT METHOD --}}
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method"
                                id="paymentMethod"
                                class="form-select"
                                required>
                            <option value="">Select Method</option>
                            <option value="bkash" {{ old('payment_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                            <option value="nagad" {{ old('payment_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                            <option value="rocket" {{ old('payment_method') == 'rocket' ? 'selected' : '' }}>Rocket</option>
                            <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                        @error('payment_method')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ACCOUNT FIELD --}}
                    <div class="mb-3" id="accountField">
                        <label class="form-label">Account Number <span class="text-danger">*</span></label>
                        <input type="text"
                               name="account_number"
                               class="form-control"
                               value="{{ old('account_number') }}"
                               required>
                        <small class="text-muted" id="accountHint">Enter your account number</small>
                        @error('account_number')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- BANK FIELDS (hidden by default) --}}
                    <div id="bankFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Holder Name</label>
                            <input type="text" name="account_holder" class="form-control" value="{{ old('account_holder') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Branch Name</label>
                            <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name') }}">
                        </div>
                    </div>

                    {{-- NOTE --}}
                    <div class="mb-3">
                        <label class="form-label">Note (Optional)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Any additional information">{{ old('note') }}</textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> 
                        Withdrawal requests will be processed within 24-48 hours.
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-money-bill-wave"></i> Request Withdrawal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Show/hide bank fields based on payment method
        function toggleBankFields() {
            let method = $('#paymentMethod').val();
            
            if (method === 'bank') {
                $('#bankFields').show();
                $('#accountField').hide();
                $('#accountField input').prop('required', false);
            } else {
                $('#bankFields').hide();
                $('#accountField').show();
                $('#accountField input').prop('required', true);
                
                // Update hint text based on method
                let hintText = 'Enter your account number';
                if (method === 'bkash') hintText = 'Enter bKash number (01XXXXXXXXX)';
                else if (method === 'nagad') hintText = 'Enter Nagad number';
                else if (method === 'rocket') hintText = 'Enter Rocket number';
                
                $('#accountHint').text(hintText);
            }
        }
        
        // Initialize on page load
        $('#paymentMethod').change(toggleBankFields);
        toggleBankFields();
    });
</script>
@endpush