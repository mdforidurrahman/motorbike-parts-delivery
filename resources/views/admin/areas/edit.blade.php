@extends('layouts.app')

@section('title', 'Edit Area - MotoLink')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Area: {{ $area->name }}</h4>
                <p class="text-muted mb-0">Update delivery zone information</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.areas.update', $area->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Area Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $area->name) }}" required>
                        <small class="text-muted">Example: Gulshan, Banani, Uttara</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                               name="city" value="{{ old('city', $area->city) }}" required>
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
                                   name="delivery_charge" value="{{ old('delivery_charge', $area->delivery_charge) }}" required>
                        </div>
                        <small class="text-muted">Default delivery charge for this area</small>
                        @error('delivery_charge')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Note:</strong> Changing area name or delivery charge will affect existing outlets and riders.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.areas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Area
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection