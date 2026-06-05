@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Rider Dashboard</h1>
            <p>Welcome back, {{ auth()->user()->name }}</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5>Available Deliveries</h5>
                </div>
                <div class="card-body" id="available-deliveries">
                    @foreach($availableOrders as $order)
                        <div class="delivery-card mb-3 p-3 border rounded">
                            <h6>Order #{{ $order->order_number }}</h6>
                            <p>From: {{ $order->supplierOutlet->shop_name }}</p>
                            <p>To: {{ $order->buyerOutlet->shop_name }}</p>
                            <p>Delivery Fee: 50 TK</p>
                            <button class="btn btn-primary accept-delivery" data-order-id="{{ $order->id }}">
                                Accept Delivery
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>My Active Deliveries</h5>
                </div>
                <div class="card-body" id="active-deliveries">
                    @foreach($myDeliveries as $delivery)
                        <div class="delivery-card mb-3 p-3 border rounded">
                            <h6>Order #{{ $delivery->order_number }}</h6>
                            <span class="badge bg-info">{{ $delivery->status }}</span>
                            
                            @if($delivery->status == 'rider_assigned')
                                <button class="btn btn-success mark-picked mt-2" data-order-id="{{ $delivery->id }}">
                                    Mark as Picked Up
                                </button>
                            @endif
                            
                            @if($delivery->status == 'picked_up')
                                <button class="btn btn-primary mark-delivered mt-2" data-order-id="{{ $delivery->id }}">
                                    Mark as Delivered
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.accept-delivery').click(function() {
        var orderId = $(this).data('order-id');
        
        $.ajax({
            url: '/rider/accept-delivery/' + orderId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            }
        });
    });
    
    $('.mark-picked').click(function() {
        var orderId = $(this).data('order-id');
        
        $.ajax({
            url: '/rider/update-status/' + orderId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: 'picked_up'
            },
            success: function(response) {
                location.reload();
            }
        });
    });
    
    $('.mark-delivered').click(function() {
        var orderId = $(this).data('order-id');
        
        $.ajax({
            url: '/rider/update-status/' + orderId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: 'delivered'
            },
            success: function(response) {
                location.reload();
            }
        });
    });
});
</script>
@endsection