@extends('layouts.app')

@section('title', 'All Orders - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>All Orders</h2>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary" onclick="window.location.href='{{ route('admin.orders.index') }}'">
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
        <div class="table-responsive">
            <table class="table table-hover" id="ordersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order #</th>
                        <th>Buyer</th>
                        <th>Supplier</th>
                        <th>Rider</th>
                        <th>Amount</th>
                        <th>Commission</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                        </td>
                        <td>
                            {{ $order->buyerOutlet->shop_name ?? 'N/A' }}<br>
                            <small class="text-muted">{{ $order->buyerOutlet->phone ?? '' }}</small>
                        </td>
                        <td>
                            {{ $order->supplierOutlet->shop_name ?? 'Pending' }}<br>
                            <small class="text-muted">{{ $order->supplierOutlet->phone ?? '' }}</small>
                        </td>
                        <td>
                            @if($order->rider)
                                {{ $order->rider->name }}<br>
                                <small class="text-muted">{{ $order->rider->phone }}</small>
                            @else
                                <span class="badge bg-secondary">Not Assigned</span>
                            @endif
                        </td>
                        <td>৳{{ number_format($order->total_amount, 2) }}</td>
                        <td>৳{{ number_format($order->commission_amount, 2) }}</td>
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
                        <td>
                            {{ $order->created_at->format('d M Y') }}<br>
                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($order->status != 'delivered' && $order->status != 'cancelled')
                                    <button type="button" class="btn btn-sm btn-danger" onclick="cancelOrder({{ $order->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>No orders found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form id="cancelOrderForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
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
    function cancelOrder(orderId) {
        $('#cancelOrderForm').attr('action', '/admin/orders/' + orderId + '/cancel');
        $('#cancelOrderModal').modal('show');
    }
    
    function exportOrders() {
        window.location.href = '/admin/orders/export';
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
                var orderStatus = $(this).find('td:eq(7) .badge').text().trim().toLowerCase().replace(' ', '_');
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