@extends('layouts.app')

@section('title', 'Orders Received - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Orders Received</h2>
        <p class="text-muted">Orders placed by small shops</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button type="button" class="btn btn-outline-success" onclick="exportOrders()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="orderTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-status="all" href="#">All Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="pending" href="#">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="accepted" href="#">Accepted</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="rider_assigned" href="#">Rider Assigned</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="picked_up" href="#">Picked Up</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="delivered" href="#">Delivered</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="cancelled" href="#">Cancelled</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Buyer Shop</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Delivery Fee</th>
                            <th>Rider</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong>{{ $order->order_number }}</strong>
                            </td>
                            <td>
                                {{ $order->buyerOutlet->shop_name ?? 'N/A' }}<br>
                                <small class="text-muted">{{ $order->delivery_address }}</small>
                            </td>
                            <td>{{ $order->buyerOutlet->phone ?? 'N/A' }}</br>
                                <small>{{ $order->buyerOutlet->email ?? '' }}</small>
                            </td>
                            <td>৳{{ number_format($order->total_amount, 2) }}<br>
                                <small class="text-muted">Items: {{ $order->items->count() }}</small>
                            </td>
                            <td>৳{{ number_format($order->delivery_charge, 2) }}</br>
                                <small>Commission: ৳{{ number_format($order->commission_amount, 2) }}</small>
                            </td>
                            <td>
                                @if($order->rider)
                                    {{ $order->rider->name }}<br>
                                    <small class="text-muted">{{ $order->rider->phone }}</small>
                                @else
                                    <span class="badge bg-secondary">Not Assigned</span>
                                @endif
                            </td>
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
                                {{ $order->created_at->format('d M Y') }}<br>
                                <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('outlet.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($order->status == 'pending')
                                        <button type="button" class="btn btn-sm btn-success" onclick="acceptOrder({{ $order->id }})">
                                            <i class="fas fa-check"></i> Accept
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="rejectOrder({{ $order->id }})">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    @endif
                                    @if($order->status == 'accepted')
                                        <button type="button" class="btn btn-sm btn-info" onclick="markAsReady({{ $order->id }})">
                                            <i class="fas fa-box"></i> Ready for Pickup
                                        </button>
                                    @endif
                                </div>
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
                <p class="text-muted">No orders received yet.</p>
                <p class="text-muted">When small shops order parts, they will appear here.</p>
            </div>
        @endif
    </div>
</div>

<!-- Accept Order Modal -->
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Accept Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to accept this order?</p>
                <p class="text-info">You will need to prepare the products for delivery.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="acceptForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Accept Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Order Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject this order?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="rejectForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Reject Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ready for Pickup Modal -->
<div class="modal fade" id="readyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Ready for Pickup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Have you prepared all the products for this order?</p>
                <p class="text-info">A rider will be assigned for delivery.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="readyForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-info">Mark as Ready</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
    .nav-tabs .nav-link {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
    let currentOrderId = null;
    
    function acceptOrder(orderId) {
        currentOrderId = orderId;
        $('#acceptForm').attr('action', '/outlet/orders/' + orderId + '/accept');
        $('#acceptModal').modal('show');
    }
    
    function rejectOrder(orderId) {
        currentOrderId = orderId;
        $('#rejectForm').attr('action', '/outlet/orders/' + orderId + '/reject');
        $('#rejectModal').modal('show');
    }
    
    function markAsReady(orderId) {
        currentOrderId = orderId;
        $('#readyForm').attr('action', '/outlet/orders/' + orderId + '/ready');
        $('#readyModal').modal('show');
    }
    
    function exportOrders() {
        window.location.href = '/outlet/orders/export';
    }
    
    // Filter by status
    $('.nav-link').click(function() {
        var status = $(this).data('status');
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        
        if (status === 'all') {
            $('tbody tr').show();
        } else {
            $('tbody tr').each(function() {
                var orderStatus = $(this).find('td:eq(6) .badge').text().trim().toLowerCase().replace(' ', '_');
                if (orderStatus === status) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });
</script>
@endpush