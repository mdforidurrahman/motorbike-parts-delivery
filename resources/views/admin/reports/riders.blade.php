@extends('layouts.app')

@section('title', 'Rider Performance Report - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Rider Performance Report</h2>
        <p class="text-muted">View delivery rider statistics and earnings</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($riders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Rider Name</th>
                            <th>Area</th>
                            <th>Total Deliveries</th>
                            <th>Total Earnings</th>
                            <th>Avg per Delivery</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riders as $index => $rider)
                        <tr>
                            <td>
                                @if($index == 0)
                                    <span class="badge bg-warning">🥇 #1</span>
                                @elseif($index == 1)
                                    <span class="badge bg-secondary">🥈 #2</span>
                                @elseif($index == 2)
                                    <span class="badge bg-danger">🥉 #3</span>
                                @else
                                    <span class="badge bg-light text-dark">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $rider->name }}</strong><br>
                                <small class="text-muted">{{ $rider->phone }}</small>
                            </td>
                            <td>{{ $rider->area->name ?? 'N/A' }}<br>
                                <small>{{ $rider->area->city ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $rider->total_deliveries ?? 0 }}</span>
                            </td>
                            <td>
                                <strong class="text-success">৳{{ number_format($rider->total_earnings ?? 0, 2) }}</strong>
                            </td>
                            <td>
                                ৳{{ number_format(($rider->total_earnings ?? 0) / max(($rider->total_deliveries ?? 1), 1), 2) }}
                            </td>
                            <td>
                                @if($rider->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $rider->created_at->format('d M Y') }}</br>
                                <small>{{ $rider->created_at->diffForHumans() }}</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $riders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-motorcycle fa-3x text-muted mb-3"></i>
                <p class="text-muted">No rider data found.</p>
            </div>
        @endif
    </div>
</div>
@endsection