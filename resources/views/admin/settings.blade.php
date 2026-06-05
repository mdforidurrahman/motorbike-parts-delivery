@extends('layouts.app')

@section('title', 'Settings - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>System Settings</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Commission Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Commission Rate (%)</label>
                        <input type="number" class="form-control" name="commission_rate" value="1" step="0.1">
                        <small class="text-muted">Current: 1% of each order</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Delivery Charge (৳)</label>
                        <input type="number" class="form-control" name="delivery_charge" value="50">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>System Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Laravel Version:</strong> {{ app()->version() }}</p>
                <p><strong>PHP Version:</strong> {{ phpversion() }}</p>
                <p><strong>Environment:</strong> {{ app()->environment() }}</p>
                <p><strong>Timezone:</strong> {{ config('app.timezone') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection