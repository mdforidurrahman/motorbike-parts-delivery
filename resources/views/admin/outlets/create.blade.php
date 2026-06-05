@extends('layouts.app')

@section('title', 'Create Outlet - MotoLink')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Create New Outlet</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.outlets.store') }}">
                    @csrf
                    
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
                                <input type="text" class="form-control @error('owner_name') is-invalid @enderror" 
                                       name="owner_name" value="{{ old('owner_name') }}">
                                @error('owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
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
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
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
                                <label class="form-label">Area <span class="text-danger">*</span></label>
                                <select class="form-control @error('area_id') is-invalid @enderror" name="area_id" required>
                                    <option value="">Select Area</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }} - {{ $area->city }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('area_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
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
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}>
                            <label class="form-check-label">Verify this outlet immediately</label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Default password for outlet owner will be: <strong>password123</strong>
                        <br>Owner can change password after first login.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.outlets.index') }}" class="btn btn-secondary">
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
                <h5 class="mb-0">Instructions</h5>
            </div>
            <div class="card-body">
                <h6>Outlet Types:</h6>
                <ul>
                    <li><strong>Small Shop:</strong> Local repair shops that order parts</li>
                    <li><strong>Large Shop:</strong> Big shops with good inventory</li>
                    <li><strong>Contracted Shop:</strong> Partner shops that supply parts</li>
                </ul>
                
                <h6>Getting Coordinates:</h6>
                <p>You can get latitude/longitude from:</p>
                <ul>
                    <li>Google Maps (right-click → What's here?)</li>
                    <li>LatLong.net</li>
                </ul>
                
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="getCurrentLocation()">
                        <i class="fas fa-map-marker-alt"></i> Use Current Location
                    </button>
                </div>
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
                document.querySelector('input[name="latitude"]').value = position.coords.latitude;
                document.querySelector('input[name="longitude"]').value = position.coords.longitude;
                
                // Show success message
                alert('Location captured successfully!');
            }, function(error) {
                alert('Error getting location: ' + error.message);
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }
    
    // Preview coordinates as user types
    document.querySelectorAll('input[name="latitude"], input[name="longitude"]').forEach(input => {
        input.addEventListener('change', function() {
            let lat = document.querySelector('input[name="latitude"]').value;
            let lng = document.querySelector('input[name="longitude"]').value;
            
            if(lat && lng) {
                console.log(`Location: ${lat}, ${lng}`);
                // You can add Google Maps preview here
            }
        });
    });
</script>
@endpush