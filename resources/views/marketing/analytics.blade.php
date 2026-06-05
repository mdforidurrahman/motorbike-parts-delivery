@extends('layouts.app')

@section('title', 'Analytics - MotoLink')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Marketing Analytics</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <canvas id="analyticsChart" height="300"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'New Signups',
                data: [12, 19, 15, 17],
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        }
    });
</script>
@endpush