@extends('layouts.app')

@section('title', 'My Products - MotoLink')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>My Products</h2>
        <p class="text-muted">Manage your motorcycle parts inventory</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('outlet.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>
</div>

<div class="card">
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
                            <th>Image</th>
                            <th>Product Name / SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" width="50" height="50" style="object-fit: cover; border-radius: 8px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 8px;">
                                        <i class="fas fa-image fa-2x text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong><br>
                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                            </td>
                            <td>{{ $product->category->name ?? 'N/A' }}</br>
                                <small class="text-muted">{{ $product->category->description ?? '' }}</small>
                            </td>
                            <td>৳{{ number_format($product->price, 2) }}</br>
                                <small>MRP</small>
                            </td>
                            <td>
                                @if($product->stock_quantity > 10)
                                    <span class="badge bg-success">{{ $product->stock_quantity }} in stock</span>
                                @elseif($product->stock_quantity > 0)
                                    <span class="badge bg-warning">{{ $product->stock_quantity }} in stock</span>
                                @else
                                    <span class="badge bg-danger">Out of stock</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_available && $product->stock_quantity > 0)
                                    <span class="badge bg-success">Available</span>
                                @else
                                    <span class="badge bg-secondary">Unavailable</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('outlet.products.edit', $product->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="updateStock({{ $product->id }}, {{ $product->stock_quantity }})">
                                        <i class="fas fa-warehouse"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleAvailability({{ $product->id }})">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteProduct({{ $product->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No products added yet.</p>
                <a href="{{ route('outlet.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Your First Product
                </a>
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
            <form id="stockForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" id="productName" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="number" id="currentStock" class="form-control" readonly>
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentProductId = null;
    
    function updateStock(productId, currentStock) {
        currentProductId = productId;
        $('#currentStock').val(currentStock);
        $('#newStock').val(currentStock);
        
        // Get product name via AJAX
        $.ajax({
            url: '/api/products/' + productId,
            method: 'GET',
            success: function(data) {
                $('#productName').val(data.name);
            }
        });
        
        $('#stockForm').attr('action', '/outlet/inventory/' + productId + '/stock');
        $('#stockModal').modal('show');
    }
    
    function toggleAvailability(productId) {
        if(confirm('Do you want to change the availability status?')) {
            $.ajax({
                url: '/outlet/products/' + productId + '/toggle-availability',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }
    }
    
    function deleteProduct(productId) {
        $('#deleteForm').attr('action', '/outlet/products/' + productId);
        $('#deleteModal').modal('show');
    }
</script>
@endpush