@extends('layouts.app')

@section('title', 'Request Product - MotoLink')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Request Motorcycle Part</h4>
                <p class="mb-0 text-white-50">Search and order the parts you need</p>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <div class="mb-4">
                    <label class="form-label">Search Products</label>
                    <div class="input-group">
                        <input type="text" id="searchProduct" class="form-control" placeholder="Search by product name or SKU...">
                        <button class="btn btn-primary" onclick="searchProducts()">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <small class="text-muted">Type at least 2 characters to search</small>
                </div>
                
                <div id="searchResults"></div>
                
                <form method="POST" action="{{ route('outlet.request-product.store') }}" id="orderForm" style="display: none;">
                    @csrf
                    <input type="hidden" name="product_id" id="product_id">
                    <input type="hidden" name="price" id="price">
                    
                    <div class="alert alert-info">
                        <h6>Order Summary</h6>
                        <p id="productInfo"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="productName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="supplierName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Unit Price</label>
                        <input type="text" class="form-control" id="unitPrice" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" required onchange="calculateTotal()">
                        <small class="text-muted">Available stock: <span id="availableStock">0</span></small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Subtotal</label>
                        <input type="text" id="subtotal" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Delivery Charge</label>
                        <input type="text" id="deliveryCharge" class="form-control" value="50 TK" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Total Amount</label>
                        <input type="text" id="totalAmount" class="form-control bg-warning" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Delivery Address <span class="text-danger">*</span></label>
                        <textarea name="delivery_address" class="form-control" rows="3" required>{{ auth()->user()->outlet->address }}</textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Payment:</strong> You can pay online or cash on delivery.<br>
                        <strong>Delivery Time:</strong> 2-4 hours within your area.
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-shopping-cart"></i> Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedProduct = null;
    
    function searchProducts() {
        let query = $('#searchProduct').val();
        
        if(query.length < 2) {
            alert('Please enter at least 2 characters');
            return;
        }
        
        $('#searchResults').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>');
        
        $.ajax({
            url: '{{ route("api.products.search") }}',
            method: 'GET',
            data: {q: query},
            success: function(products) {
                if(products.length > 0) {
                    let html = '<div class="list-group mb-4">';
                    products.forEach(function(product) {
                        html += `
                            <a href="#" class="list-group-item list-group-item-action" onclick="selectProduct(${product.id}, '${product.name}', ${product.price}, ${product.stock_quantity}, '${product.outlet.shop_name}')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">${product.name}</h6>
                                        <small class="text-muted">SKU: ${product.sku}</small><br>
                                        <small class="text-muted">Supplier: ${product.outlet.shop_name}</small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-primary">৳${product.price}</strong><br>
                                        <small class="text-muted">Stock: ${product.stock_quantity}</small>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                    html += '</div>';
                    $('#searchResults').html(html);
                } else {
                    $('#searchResults').html('<div class="alert alert-info">No products found. Try different keywords.</div>');
                }
            },
            error: function() {
                $('#searchResults').html('<div class="alert alert-danger">Error searching products. Please try again.</div>');
            }
        });
    }
    
    function selectProduct(id, name, price, stock, supplier) {
        selectedProduct = {id, name, price, stock, supplier};
        
        $('#product_id').val(id);
        $('#productName').val(name);
        $('#supplierName').val(supplier);
        $('#unitPrice').val('৳' + price);
        $('#price').val(price);
        $('#availableStock').text(stock);
        $('#quantity').val(1);
        $('#quantity').attr('max', stock);
        
        calculateTotal();
        
        $('#searchResults').hide();
        $('#orderForm').show();
        
        // Update product info
        $('#productInfo').html(`
            <strong>${name}</strong><br>
            Supplier: ${supplier}<br>
            Price: ৳${price} per unit<br>
            Available: ${stock} units
        `);
    }
    
    function calculateTotal() {
        let quantity = parseInt($('#quantity').val()) || 0;
        let price = parseFloat($('#price').val()) || 0;
        let maxStock = parseInt($('#availableStock').text()) || 0;
        
        if(quantity > maxStock) {
            alert('Not enough stock! Available: ' + maxStock);
            $('#quantity').val(maxStock);
            quantity = maxStock;
        }
        
        let subtotal = quantity * price;
        let deliveryCharge = 50;
        let total = subtotal + deliveryCharge;
        
        $('#subtotal').val('৳' + subtotal.toFixed(2));
        $('#totalAmount').val('৳' + total.toFixed(2));
    }
    
    $('#quantity').on('input', function() {
        calculateTotal();
    });
    
    // Refresh search
    $('#searchProduct').on('input', function() {
        if($(this).val().length < 2) {
            $('#searchResults').html('');
            $('#orderForm').hide();
        }
    });
</script>
@endpush