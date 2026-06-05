@extends('layouts.app')

@section('title', 'My Orders - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Orders Placed</h2>
        <p class="text-muted">Parts you have ordered</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('outlet.request-product') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Request New Product
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Supplier</th>
                            <th>Total Amount</th>
                            <th>Delivery Charge</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</br>
                                <small class="text-muted">{{ $order->items->count() }} items</small>
                            </td>
                            <td>
                                @if($order->supplierOutlet)
                                    {{ $order->supplierOutlet->shop_name }}<br>
                                    <small class="text-muted">{{ $order->supplierOutlet->phone }}</small>
                                @else
                                    <span class="badge bg-warning">Pending Assignment</span>
                                @endif
                            </td>
                            <td>৳{{ number_format($order->total_amount, 2) }}</td>
                            <td>৳{{ number_format($order->delivery_charge, 2) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'accepted' => 'info',
                                        'rider_assigned' => 'primary',
                                        'picked_up' => 'dark',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusText = [
                                        'pending' => 'Pending',
                                        'accepted' => 'Accepted',
                                        'rider_assigned' => 'Rider Assigned',
                                        'picked_up' => 'Picked Up',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                    {{ $statusText[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</br>
                                <small>{{ $order->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('outlet.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">You haven't placed any orders yet.</p>
                <a href="{{ route('outlet.request-product') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Request Your First Product
                </a>
            </div>
        @endif
    </div>
</div>
@endsection