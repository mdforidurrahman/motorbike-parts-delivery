@extends('layouts.app')

@section('title', 'Add New Area - MotoLink')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Add New Area</h4>
                <p class="text-muted mb-0">Create a new delivery zone</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.areas.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Area Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required>
                        <small class="text-muted">Example: Gulshan, Banani, Uttara</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                               name="city" value="{{ old('city') }}" required>
                        <small class="text-muted">Example: Dhaka, Chittagong, Sylhet</small>
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Delivery Charge <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" step="0.01" class="form-control @error('delivery_charge') is-invalid @enderror" 
                                   name="delivery_charge" value="{{ old('delivery_charge', 50) }}" required>
                        </div>
                        <small class="text-muted">Default delivery charge for this area</small>
                        @error('delivery_charge')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Note:</strong> Areas can have multiple outlets and riders.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.areas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Area
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-tips"></i> Tips</h5>
            </div>
            <div class="card-body">
                <h6>Area Guidelines:</h6>
                <ul>
                    <li>Use unique area names</li>
                    <li>Set appropriate delivery charges</li>
                    <li>Areas help organize deliveries</li>
                    <li>Riders work within their area</li>
                </ul>
                
                <h6 class="mt-3">Common Areas:</h6>
                <ul>
                    <li>Gulshan, Banani, Uttara</li>
                    <li>Dhanmondi, Mohammadpur</li>
                    <li>Motijheel, Paltan</li>
                    <li>Chittagong, Sylhet, Khulna</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection