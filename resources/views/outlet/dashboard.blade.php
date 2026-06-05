@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Welcome, {{ auth()->user()->name }}</h1>
            <p>Shop: {{ auth()->user()->outlet->shop_name }}</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Wallet Balance</h5>
                    <h2>{{ number_format(auth()->user()->outlet->wallet_balance, 2) }} TK</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Total Orders</h5>
                    <h2>{{ auth()->user()->outlet->orders()->count() }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Pending Deliveries</h5>
                    <h2>{{ auth()->user()->outlet->orders()->where('status', 'pending')->count() }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Real-time Notifications</h4>
                </div>
                <div class="card-body" id="notifications-list">
                    @foreach(auth()->user()->getNotifications()->limit(10)->get() as $notification)
                        <div class="alert alert-info">
                            <strong>{{ $notification->title }}</strong>
                            <p>{{ $notification->message }}</p>
                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;
    
    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
    });
    
    var channel = pusher.subscribe('outlet.{{ auth()->user()->outlet->id }}');
    channel.bind('new-notification', function(data) {
        var notificationHtml = `
            <div class="alert alert-success alert-dismissible fade show">
                <strong>${data.title}</strong>
                <p>${data.message}</p>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#notifications-list').prepend(notificationHtml);
        
        // Play notification sound
        var audio = new Audio('/notification.mp3');
        audio.play();
    });
</script>
@endpush
@endsection