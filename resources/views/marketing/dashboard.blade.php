@extends('layouts.app')

@section('title', 'Marketing Dashboard - MotoLink')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Marketing Dashboard</h2>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>New Outlets (7 days)</h6>
                <h3>{{ $newOutlets ?? 0 }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Active Campaigns</h6>
                <h3>{{ $activeCampaigns ?? 0 }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Conversion Rate</h6>
                <h3>{{ $conversionRate ?? 0 }}%</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Leads</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">No leads yet.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('marketing.promotions.index') }}" class="btn btn-primary w-100 mb-2">
                    Create Promotion
                </a>
                <a href="{{ route('marketing.campaigns.index') }}" class="btn btn-success w-100 mb-2">
                    Start Campaign
                </a>
            </div>
        </div>
    </div>
</div>
@endsection