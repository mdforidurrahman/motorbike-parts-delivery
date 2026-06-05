@extends('layouts.app')

@section('title', 'Edit Rider - MotoLink')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Rider: {{ $rider->name }}</h4>
                <p class="text-muted mb-0">Update rider information</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('area-manager.riders.update', $rider->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $rider->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               name="phone" value="{{ old('phone', $rider->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email', $rider->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" 
                                       value="{{ $rider->area->name ?? 'N/A' }}" readonly disabled>
                                <small class="text-muted">Area cannot be changed</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" 
                                       value="{{ $rider->is_active ? 'Active' : 'Inactive' }}" readonly disabled>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Wallet Balance:</strong> {{ number_format($rider->wallet_balance, 2) }} TK
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('area-manager.riders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Rider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection