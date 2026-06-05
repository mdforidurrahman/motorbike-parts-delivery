@extends('layouts.app')

@section('title', 'Edit Outlet - MotoLink')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Outlet: {{ $outlet->shop_name }}</h4>
                <p class="text-muted mb-0">Update outlet information</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('area-manager.outlets.update', $outlet->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Shop Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('shop_name') is-invalid @enderror" 
                                       name="shop_name" value="{{ old('shop_name', $outlet->shop_name) }}" required>
                                @error('shop_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Owner Name</label>
                                <input type="text" class="form-control" name="owner_name" value="{{ old('owner_name', $outlet->owner->name ?? '') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone', $outlet->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $outlet->email) }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="2" required>{{ old('address', $outlet->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Latitude <span class="text-danger">*</span></label>
                                <input type="number" step="any" id="latitude" class="form-control @error('latitude') is-invalid @enderror" 
                                       name="latitude" value="{{ old('latitude', $outlet->latitude) }}" required>
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
                                       name="longitude" value="{{ old('longitude', $outlet->longitude) }}" required>
                                <small class="text-muted">Example: 90.4125</small>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Outlet Type <span class="text-danger">*</span></label>
                        <select class="form-control @error('type') is-invalid @enderror" name="type" required>
                            <option value="small" {{ old('type', $outlet->type) == 'small' ? 'selected' : '' }}>Small Shop</option>
                            <option value="large" {{ old('type', $outlet->type) == 'large' ? 'selected' : '' }}>Large Shop</option>
                            <option value="contracted" {{ old('type', $outlet->type) == 'contracted' ? 'selected' : '' }}>Contracted Shop</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('area-manager.outlets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Outlet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Location Helper</h5>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-outline-info w-100 mb-2" onclick="getCurrentLocation()">
                    <i class="fas fa-location-dot"></i> Use Current Location
                </button>
                
                <button type="button" class="btn btn-outline-success w-100 mb-2" onclick="openGoogleMaps()">
                    <i class="fab fa-google"></i> View on Google Maps
                </button>
                
                <hr>
                
                <div class="form-group">
                    <label class="form-label">Quick Coordinates</label>
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1" onclick="setDhakaLocation()">
                                Dhaka
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1" onclick="setChittagongLocation()">
                                Chittagong
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="setSylhetLocation()">
                                Sylhet
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="setKhulnaLocation()">
                                Khulna
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Instructions</h5>
            </div>
            <div class="card-body">
                <h6>Getting Location:</h6>
                <ul>
                    <li>Click "Use Current Location" for automatic capture</li>
                    <li>Click city buttons for preset coordinates</li>
                    <li>Manually enter latitude/longitude from Google Maps</li>
                </ul>
                
                <div class="alert alert-info mt-2">
                    <small>To get coordinates from Google Maps:<br>
                    1. Open maps.google.com<br>
                    2. Right-click on location<br>
                    3. Select "What's here?"<br>
                    4. Copy coordinates</small>
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
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                updateAddressFromCoords();
                alert('Location captured successfully!');
            }, function(error) {
                alert('Error getting location: ' + error.message);
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }
    
    function setDhakaLocation() {
        document.getElementById('latitude').value = 23.8103;
        document.getElementById('longitude').value = 90.4125;
        updateAddressFromCoords();
        alert('Dhaka location set!');
    }
    
    function setChittagongLocation() {
        document.getElementById('latitude').value = 22.3569;
        document.getElementById('longitude').value = 91.7832;
        updateAddressFromCoords();
        alert('Chittagong location set!');
    }
    
    function setSylhetLocation() {
        document.getElementById('latitude').value = 24.8949;
        document.getElementById('longitude').value = 91.8687;
        updateAddressFromCoords();
        alert('Sylhet location set!');
    }
    
    function setKhulnaLocation() {
        document.getElementById('latitude').value = 22.8456;
        document.getElementById('longitude').value = 89.5403;
        updateAddressFromCoords();
        alert('Khulna location set!');
    }
    
    function openGoogleMaps() {
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        if (lat && lng) {
            window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
        } else {
            alert('Please set latitude and longitude first');
        }
    }
    
    function updateAddressFromCoords() {
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        if (lat && lng) {
            // Using OpenStreetMap Nominatim API
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        const addressField = document.querySelector('textarea[name="address"]');
                        if (addressField && !addressField.value) {
                            addressField.value = data.display_name;
                        }
                    }
                })
                .catch(error => console.log('Error getting address:', error));
        }
    }
    
    // Update address when coordinates change
    document.getElementById('latitude').addEventListener('change', updateAddressFromCoords);
    document.getElementById('longitude').addEventListener('change', updateAddressFromCoords);
</script>
@endpush