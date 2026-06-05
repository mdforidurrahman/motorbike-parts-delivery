@extends('layouts.app')

@section('title', 'Orders in Your Area - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Orders in Your Area</h2>
        <p class="text-muted">Managing area: <strong>{{ auth()->user()->area->name ?? 'N/A' }}</strong></p>
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
                            <th>Supplier Shop</th>
                            <th>Rider</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
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
                                <small class="text-muted">{{ $order->buyerOutlet->phone ?? '' }}</small>
                            </td>
                            <td>
                                @if($order->supplierOutlet)
                                    {{ $order->supplierOutlet->shop_name }}<br>
                                    <small class="text-muted">{{ $order->supplierOutlet->phone }}</small>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($order->rider)
                                    {{ $order->rider->name }}<br>
                                    <small class="text-muted">{{ $order->rider->phone }}</small>
                                @else
                                    <span class="badge bg-secondary">Not Assigned</span>
                                @endif
                            </td>
                            <td>৳{{ number_format($order->total_amount, 2) }}</br>
                                <small>Del: ৳{{ number_format($order->delivery_charge, 2) }}</small>
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
                                    <a href="{{ route('area-manager.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($order->status == 'pending' && !$order->supplier_outlet_id)
                                        <button type="button" class="btn btn-sm btn-success" onclick="assignSupplier({{ $order->id }})">
                                            <i class="fas fa-store"></i>
                                        </button>
                                    @endif
                                    @if($order->status == 'accepted' && !$order->rider_id)
                                        <button type="button" class="btn btn-sm btn-info" onclick="assignRider({{ $order->id }})">
                                            <i class="fas fa-motorcycle"></i>
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
                <p class="text-muted">No orders found in your area.</p>
            </div>
        @endif
    </div>
</div>

<!-- Assign Supplier Modal -->
<div class="modal fade" id="assignSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignSupplierForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Contracted Outlet</label>
                        <select class="form-control" name="supplier_outlet_id" required>
                            <option value="">Select Outlet</option>
                            @foreach($contractedOutlets ?? [] as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->shop_name }} ({{ $outlet->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Rider Modal -->
<div class="modal fade" id="assignRiderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Rider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignRiderForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Rider</label>
                        <select class="form-control" name="rider_id" required>
                            <option value="">Select Rider</option>
                            @foreach($availableRiders ?? [] as $rider)
                                <option value="{{ $rider->id }}">{{ $rider->name }} ({{ $rider->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Rider</button>
                </div>
            </form>
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
    
    function assignSupplier(orderId) {
        currentOrderId = orderId;
        $('#assignSupplierForm').attr('action', '/area-manager/orders/' + orderId + '/assign-supplier');
        $('#assignSupplierModal').modal('show');
    }
    
    function assignRider(orderId) {
        currentOrderId = orderId;
        $('#assignRiderForm').attr('action', '/area-manager/orders/' + orderId + '/assign-rider');
        $('#assignRiderModal').modal('show');
    }
    
    function exportOrders() {
        window.location.href = '/area-manager/orders/export';
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
                var orderStatus = $(this).find('td:eq(5) .badge').text().trim().toLowerCase().replace(' ', '_');
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