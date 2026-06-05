@extends('layouts.app')

@section('title', 'Create Outlet - MotoLink')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Create New Outlet</h4>
                <p class="text-muted mb-0">Add a new outlet in your area: <strong>{{ auth()->user()->area->name ?? 'N/A' }}</strong></p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('area-manager.outlets.store') }}">
                    @csrf
                    
                    <!-- Hidden area_id (automatically set from logged in user) -->
                    <input type="hidden" name="area_id" value="{{ auth()->user()->area_id }}">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Shop Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('shop_name') is-invalid @enderror" 
                                       name="shop_name" value="{{ old('shop_name') }}" required>
                                @error('shop_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Owner Name</label>
                                <input type="text" class="form-control" name="owner_name" value="{{ old('owner_name') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="2" required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Latitude <span class="text-danger">*</span></label>
                                <input type="number" step="any" id="latitude" class="form-control @error('latitude') is-invalid @enderror" 
                                       name="latitude" value="{{ old('latitude') }}" required>
                                <small class="text-muted">Example: 23.8103</small>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Longitude <span class="text-danger">*</span></label>
                                <input type="number" step="any" id="longitude" class="form-control @error('longitude') is-invalid @enderror" 
                                       name="longitude" value="{{ old('longitude') }}" required>
                                <small class="text-muted">Example: 90.4125</small>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Outlet Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" name="type" required>
                                    <option value="small" {{ old('type') == 'small' ? 'selected' : '' }}>Small Shop</option>
                                    <option value="large" {{ old('type') == 'large' ? 'selected' : '' }}>Large Shop</option>
                                    <option value="contracted" {{ old('type') == 'contracted' ? 'selected' : '' }}>Contracted Shop</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Area <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" 
                                       value="{{ auth()->user()->area->name ?? 'N/A' }} (Auto-assigned)" readonly disabled>
                                <small class="text-muted">Area is automatically assigned based on your area</small>
                                <input type="hidden" name="area_id" value="{{ auth()->user()->area_id }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Verification Status</label>
                                <input type="text" class="form-control" value="Pending (Auto-assigned)" readonly disabled>
                                <small class="text-muted">New outlets are pending verification by admin</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Information:</strong><br>
                        - Area: <strong>{{ auth()->user()->area->name ?? 'N/A' }}</strong> (Auto-assigned)<br>
                        - Verification Status: <strong>Pending</strong> (Admin will verify)<br>
                        - Default password for outlet owner: <strong>password123</strong><br>
                        - Owner can change password after first login
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('area-manager.outlets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Outlet
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
                <h6>Outlet Types:</h6>
                <ul>
                    <li><strong>Small Shop:</strong> Local repair shops that order parts</li>
                    <li><strong>Large Shop:</strong> Big shops with good inventory</li>
                    <li><strong>Contracted Shop:</strong> Partner shops that supply parts</li>
                </ul>
                
                <h6 class="mt-3">Auto-assigned Fields:</h6>
                <ul>
                    <li><strong>Area:</strong> Automatically set to your area</li>
                    <li><strong>Verification Status:</strong> Always "Pending" on creation</li>
                </ul>
                
                <button type="button" class="btn btn-sm btn-outline-info w-100 mt-2" onclick="getCurrentLocation()">
                    <i class="fas fa-map-marker-alt"></i> Use Current Location
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                alert('Location captured successfully!');
            }, function(error) {
                alert('Error getting location: ' + error.message);
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }
</script>
@endpush