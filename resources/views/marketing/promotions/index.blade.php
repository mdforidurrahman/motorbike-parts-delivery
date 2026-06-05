@extends('layouts.app')

@section('title', 'Promotions - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Promotions</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('marketing.promotions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Promotion
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <p class="text-muted text-center">No promotions created yet.</p>
    </div>
</div>
@endsection