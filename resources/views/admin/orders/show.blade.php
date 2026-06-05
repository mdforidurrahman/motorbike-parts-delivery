@extends('layouts.app')

@section('title', 'Order Details - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Order Details</h2>
        <p class="text-muted">Order #: {{ $order->order_number }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Order Status Timeline -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Order Status Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="step {{ $order->status == 'pending' ? 'active' : ($order->status != 'pending' ? 'completed' : '') }}">
                        <div class="step-icon"><i class="fas fa-clock"></i></div>
                        <div class="step-text">Order Placed</div>
                        <div class="step-date">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="step {{ $order->status == 'accepted' ? 'active' : ($order->status == 'rider_assigned' || $order->status == 'picked_up' || $order->status == 'delivered' ? 'completed' : '') }}">
                        <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="step-text">Order Accepted</div>
                        <div class="step-date">{{ $order->supplier_outlet_id ? $order->updated_at->format('d M Y, h:i A') : 'Pending' }}</div>
                    </div>
                    <div class="step {{ $order->status == 'rider_assigned' ? 'active' : ($order->status == 'picked_up' || $order->status == 'delivered' ? 'completed' : '') }}">
                        <div class="step-icon"><i class="fas fa-motorcycle"></i></div>
                        <div class="step-text">Rider Assigned</div>
                        <div class="step-date">{{ $order->rider_id ? ($order->status != 'accepted' ? $order->updated_at->format('d M Y, h:i A') : 'Pending') : 'Not Assigned' }}</div>
                    </div>
                    <div class="step {{ $order->status == 'picked_up' ? 'active' : ($order->status == 'delivered' ? 'completed' : '') }}">
                        <div class="step-icon"><i class="fas fa-box"></i></div>
                        <div class="step-text">Picked Up</div>
                        <div class="step-date">{{ $order->status == 'picked_up' || $order->status == 'delivered' ? $order->updated_at->format('d M Y, h:i A') : 'Pending' }}</div>
                    </div>
                    <div class="step {{ $order->status == 'delivered' ? 'active completed' : '' }}">
                        <div class="step-icon"><i class="fas fa-flag-checkered"></i></div>
                        <div class="step-text">Delivered</div>
                        <div class="step-date">{{ $order->delivered_at ? $order->delivered_at->format('d M Y, h:i A') : 'Pending' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-store"></i> Buyer Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Shop Name:</strong> {{ $order->buyerOutlet->shop_name ?? 'N/A' }}</p>
                        <p><strong>Owner:</strong> {{ $order->buyerOutlet->name ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $order->buyerOutlet->phone ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $order->buyerOutlet->email ?? 'N/A' }}</p>
                        <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-truck"></i> Supplier Information</h5>
                    </div>
                    <div class="card-body">
                        @if($order->supplierOutlet)
                            <p><strong>Shop Name:</strong> {{ $order->supplierOutlet->shop_name }}</p>
                            <p><strong>Phone:</strong> {{ $order->supplierOutlet->phone }}</p>
                            <p><strong>Address:</strong> {{ $order->supplierOutlet->address }}</p>
                        @else
                            <p class="text-muted">No supplier assigned yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-motorcycle"></i> Rider Information</h5>
                    </div>
                    <div class="card-body">
                        @if($order->rider)
                            <p><strong>Name:</strong> {{ $order->rider->name }}</p>
                            <p><strong>Phone:</strong> {{ $order->rider->phone }}</p>
                            <p><strong>Email:</strong> {{ $order->rider->email }}</p>
                        @else
                            <p class="text-muted">No rider assigned yet.</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Subtotal:</strong> ৳{{ number_format($order->total_amount - $order->delivery_charge, 2) }}</p>
                        <p><strong>Delivery Charge:</strong> ৳{{ number_format($order->delivery_charge, 2) }}</p>
                        <p><strong>Total Amount:</strong> ৳{{ number_format($order->total_amount, 2) }}</p>
                        <p><strong>Commission (1%):</strong> ৳{{ number_format($order->commission_amount, 2) }}</p>
                        <p><strong>Payment Status:</strong> 
                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-boxes"></i> Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->product->sku }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>৳{{ number_format($item->price, 2) }}</td>
                                <td>৳{{ number_format($item->quantity * $item->price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>৳{{ number_format($order->total_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
    }
    .timeline:before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #dee2e6;
        z-index: 1;
    }
    .step {
        text-align: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }
    .step-icon {
        width: 40px;
        height: 40px;
        background: #dee2e6;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    .step.completed .step-icon {
        background: #28a745;
        color: white;
    }
    .step.active .step-icon {
        background: #007bff;
        color: white;
    }
    .step-text {
        font-weight: 500;
        font-size: 14px;
    }
    .step-date {
        font-size: 11px;
        color: #6c757d;
    }
</style>
@endpush