@extends('layouts.app')

@section('title', 'Create Rider - MotoLink')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Add New Delivery Rider</h4>
                <p class="text-muted mb-0">Add a new rider in your area: <strong>{{ auth()->user()->area->name ?? 'N/A' }}</strong></p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('area-manager.riders.store') }}">
                    @csrf
                    
                    <!-- Hidden area_id -->
                    <input type="hidden" name="area_id" value="{{ auth()->user()->area_id }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               name="phone" value="{{ old('phone') }}" required>
                        <small class="text-muted">Example: 017xxxxxxxx</small>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" 
                                       value="{{ auth()->user()->area->name ?? 'N/A' }} (Auto-assigned)" readonly disabled>
                                <input type="hidden" name="area_id" value="{{ auth()->user()->area_id }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" value="Active (Auto-assigned)" readonly disabled>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Information:</strong><br>
                        - Area: <strong>{{ auth()->user()->area->name ?? 'N/A' }}</strong> (Auto-assigned)<br>
                        - Default password for rider: <strong>password123</strong><br>
                        - Rider can change password after first login<br>
                        - Initial wallet balance: <strong>0 TK</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('area-manager.riders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Rider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Instructions</h5>
            </div>
            <div class="card-body">
                <h6>Rider Responsibilities:</h6>
                <ul>
                    <li>Deliver parts to small shops</li>
                    <li>Collect payments</li>
                    <li>Update delivery status</li>
                    <li>Maintain delivery records</li>
                </ul>
                
                <h6 class="mt-3">Requirements:</h6>
                <ul>
                    <li>Valid driving license</li>
                    <li>Own motorcycle</li>
                    <li>Smartphone with internet</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection