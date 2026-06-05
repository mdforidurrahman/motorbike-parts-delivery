@extends('layouts.app')

@section('title', 'Inventory Management - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Inventory Management</h2>
        <p class="text-muted">Low stock alerts and inventory updates</p>
    </div>
</div>

<div class="card">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Stock Products</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name }}</strong><br>
                                <small class="text-muted">{{ $product->description }}</small>
                            </td>
                            <td>{{ $product->sku }}</td>
                            <td>
                                @if($product->stock_quantity <= 0)
                                    <span class="badge bg-danger">Out of stock</span>
                                @elseif($product->stock_quantity <= 3)
                                    <span class="badge bg-danger">Very Low ({{ $product->stock_quantity }})</span>
                                @else
                                    <span class="badge bg-warning">Low ({{ $product->stock_quantity }})</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_available)
                                    <span class="badge bg-success">Available</span>
                                @else
                                    <span class="badge bg-secondary">Unavailable</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="openStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock_quantity }})">
                                    <i class="fas fa-edit"></i> Update Stock
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p class="text-muted">All products have sufficient stock!</p>
            </div>
        @endif
    </div>
</div>

<!-- Update Stock Modal -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock Quantity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" id="productName" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" id="currentStock" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Stock Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="stock_quantity" id="newStock" class="form-control" required min="0">
                        <small class="text-muted">Enter the total stock quantity</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openStockModal(productId, productName, currentStock) {
        // Set form action
        $('#stockForm').attr('action', '/outlet/inventory/' + productId + '/stock');
        
        // Set values
        $('#productName').val(productName);
        $('#currentStock').val(currentStock);
        $('#newStock').val(currentStock);
        
        // Show modal
        $('#stockModal').modal('show');
    }
    
    // Optional: AJAX submission without page reload (if you prefer)
    $(document).ready(function() {
        $('#stockForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#stockModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error updating stock');
                }
            });
        });
    });
</script>
@endpush