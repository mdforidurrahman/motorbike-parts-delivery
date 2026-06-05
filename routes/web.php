<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AreaManagerController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// ==================== Public Routes ====================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// ==================== Authenticated Routes ====================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // General Dashboard (redirect based on role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notifications (All authenticated users)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });
});

// ==================== Admin Routes ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
Route::get('/users/export', [AdminController::class, 'exportUsers'])->name('users.export');

    // User Management
        Route::resource('users', AdminController::class);
    // Additional user routes
    Route::get('/users/{user}/impersonate', [AdminController::class, 'impersonate'])->name('users.impersonate');
    Route::post('/users/{user}/activate', [AdminController::class, 'activateUser'])->name('users.activate');
    Route::post('/users/{user}/deactivate', [AdminController::class, 'deactivateUser'])->name('users.deactivate');
    
    // Bulk actions
    Route::post('/users/bulk-activate', [AdminController::class, 'bulkActivate'])->name('users.bulk-activate');
    Route::post('/users/bulk-deactivate', [AdminController::class, 'bulkDeactivate'])->name('users.bulk-deactivate');
    Route::post('/users/bulk-delete', [AdminController::class, 'bulkDelete'])->name('users.bulk-delete');
    // User Export - এই লাইনটি যোগ করুন
    // Route::get('/admin/users/export', [AdminController::class, 'exportUsers'])->name('admin.users.export');
    
    
    // Role Management
    Route::get('/roles', [AdminController::class, 'roles'])->name('roles');
    Route::post('/roles', [AdminController::class, 'storeRole'])->name('roles.store');
    Route::get('/roles/{id}/edit', [AdminController::class, 'editRole'])->name('roles.edit');
    Route::put('/roles/{id}', [AdminController::class, 'updateRole'])->name('roles.update');
    Route::delete('/roles/{id}', [AdminController::class, 'destroyRole'])->name('roles.destroy');
    Route::get('/roles/{id}/users-count', [AdminController::class, 'getRoleUsersCount'])->name('roles.users-count');
    
    // Area Management
    Route::resource('areas', AreaManagerController::class);
    
    // Outlet Management
    Route::get('/outlets', [OutletController::class, 'index'])->name('outlets.index');
    Route::get('/outlets/{outlet}', [OutletController::class, 'show'])->name('outlets.show');
    Route::get('/outlets/create', [OutletController::class, 'create'])->name('outlets.create');
    Route::post('/outlets/{outlet}/verify', [OutletController::class, 'verify'])->name('outlets.verify');
    Route::post('/outlets/{outlet}/suspend', [OutletController::class, 'suspend'])->name('outlets.suspend');
    
    // Rider Management
    Route::get('/riders', [RiderController::class, 'index'])->name('riders.index');
    Route::get('/riders/{rider}', [RiderController::class, 'show'])->name('riders.show');
    Route::post('/riders/{rider}/activate', [RiderController::class, 'activate'])->name('riders.activate');
    Route::post('/riders/{rider}/deactivate', [RiderController::class, 'deactivate'])->name('riders.deactivate');
    
    // Order Management
    Route::get('/orders', [OrderController::class, 'adminOrders'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'adminCancel'])->name('orders.cancel');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/commission', [ReportController::class, 'commission'])->name('reports.commission');
    Route::get('/reports/riders', [ReportController::class, 'riders'])->name('reports.riders');
    Route::get('/reports/outlets', [ReportController::class, 'outlets'])->name('reports.outlets');
    
    // System Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');


    // Withdrawal Management
    Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals.index');
    Route::get('/withdrawals/{id}', [AdminController::class, 'showWithdrawal'])->name('withdrawals.show');
    Route::post('/withdrawals/{id}/approve', [AdminController::class, 'approveWithdrawal'])->name('withdrawals.approve');
    Route::post('/withdrawals/{id}/reject', [AdminController::class, 'rejectWithdrawal'])->name('withdrawals.reject');
    Route::post('/withdrawals/{id}/processing', [AdminController::class, 'markAsProcessing'])->name('withdrawals.processing');
});

// ==================== Head Office Routes ====================
Route::middleware(['auth', 'role:head-office'])->prefix('head-office')->name('head-office.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'headOfficeDashboard'])->name('dashboard');
    Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
    Route::get('/commission', [ReportController::class, 'commissionReport'])->name('commission');
    Route::get('/outlets/all', [OutletController::class, 'allOutlets'])->name('outlets.all');
    Route::get('/reports/daily', [ReportController::class, 'dailyReport'])->name('reports.daily');
    Route::get('/reports/monthly', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
});






// ==================== Area Manager Routes ====================
Route::middleware(['auth', 'role:area-manager'])->prefix('area-manager')->name('area-manager.')->group(function () {
    Route::get('/dashboard', [AreaManagerController::class, 'dashboard'])->name('dashboard');
    
    // Outlet Management (within area)
    Route::get('/outlets', [OutletController::class, 'areaOutlets'])->name('outlets.index');
    Route::get('/outlets/create', [OutletController::class, 'create'])->name('outlets.create');
    Route::post('/outlets', [OutletController::class, 'store'])->name('outlets.store');
    Route::get('/outlets/{outlet}/edit', [OutletController::class, 'edit'])->name('outlets.edit');
        Route::get('/outlets/{outlet}', [AreaManagerController::class, 'showOutlet'])->name('outlets.show');  // নতুন যোগ করুন
    Route::put('/outlets/{outlet}', [OutletController::class, 'update'])->name('outlets.update');
    

// Rider Management (within area)
Route::get('/riders', [AreaManagerController::class, 'riders'])->name('riders.index');
Route::get('/riders/create', [AreaManagerController::class, 'createRider'])->name('riders.create');
Route::post('/riders', [AreaManagerController::class, 'storeRider'])->name('riders.store');
Route::get('/riders/{rider}', [AreaManagerController::class, 'showRider'])->name('riders.show');
Route::get('/riders/{rider}/edit', [AreaManagerController::class, 'editRider'])->name('riders.edit');
Route::put('/riders/{rider}', [AreaManagerController::class, 'updateRider'])->name('riders.update');
Route::delete('/riders/{rider}', [AreaManagerController::class, 'deleteRider'])->name('riders.destroy');
Route::post('/riders/{rider}/activate', [AreaManagerController::class, 'activateRider'])->name('riders.activate');
Route::post('/riders/{rider}/deactivate', [AreaManagerController::class, 'deactivateRider'])->name('riders.deactivate');
    
    // Order Monitoring
// Order Monitoring
Route::get('/orders', [OrderController::class, 'areaOrders'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // Order Assignment Routes - এই রাউটগুলো যোগ করুন
    Route::post('/orders/{order}/assign-supplier', [OrderController::class, 'assignSupplier'])->name('orders.assign-supplier');
    Route::post('/orders/{order}/assign-rider', [OrderController::class, 'assignRider'])->name('orders.assign-rider');


    // Reports
    Route::get('/reports', [ReportController::class, 'areaReports'])->name('reports.index');
    Route::get('/reports/delivery', [ReportController::class, 'deliveryReport'])->name('reports.delivery');
});

// ==================== Marketing Officer Routes ====================
Route::middleware(['auth', 'role:marketing-officer'])->prefix('marketing')->name('marketing.')->group(function () {
    Route::get('/dashboard', [MarketingController::class, 'dashboard'])->name('dashboard');
    
    // Promotions
    Route::get('/promotions', [MarketingController::class, 'promotions'])->name('promotions.index');
    Route::get('/promotions/create', [MarketingController::class, 'createPromotion'])->name('promotions.create');
    Route::post('/promotions', [MarketingController::class, 'storePromotion'])->name('promotions.store');
    Route::get('/promotions/{promotion}/edit', [MarketingController::class, 'editPromotion'])->name('promotions.edit');
    Route::put('/promotions/{promotion}', [MarketingController::class, 'updatePromotion'])->name('promotions.update');
    Route::delete('/promotions/{promotion}', [MarketingController::class, 'destroyPromotion'])->name('promotions.destroy');
    
    // Customer Acquisition
    Route::get('/leads', [MarketingController::class, 'leads'])->name('leads.index');
    Route::post('/leads', [MarketingController::class, 'storeLead'])->name('leads.store');
    Route::post('/leads/{lead}/convert', [MarketingController::class, 'convertLead'])->name('leads.convert');
    
    // Campaigns
    Route::get('/campaigns', [MarketingController::class, 'campaigns'])->name('campaigns.index');
    Route::post('/campaigns', [MarketingController::class, 'storeCampaign'])->name('campaigns.store');
    Route::post('/campaigns/{campaign}/send', [MarketingController::class, 'sendCampaign'])->name('campaigns.send');
    
    // Analytics
    Route::get('/analytics', [MarketingController::class, 'analytics'])->name('analytics');
});

// ==================== Outlet Owner Routes ====================
Route::middleware(['auth', 'role:outlet-owner'])->prefix('outlet')->name('outlet.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [OutletController::class, 'dashboard'])->name('dashboard');
    
    // Product Management
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/toggle-availability', [ProductController::class, 'toggleAvailability'])->name('products.toggle');
    
    // Order Management (as Supplier/Buyer)
    Route::get('/orders/supplier', [OrderController::class, 'supplierOrders'])->name('orders.supplier');
    Route::get('/orders/buyer', [OrderController::class, 'buyerOrders'])->name('orders.buyer');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/accept', [OrderController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{order}/reject', [OrderController::class, 'rejectOrder'])->name('orders.reject');
    Route::post('/orders/{order}/ready', [OrderController::class, 'markAsReady'])->name('orders.ready');
    
    // Product Request (Create order for parts)
    Route::get('/request-product', [OrderController::class, 'requestProductForm'])->name('request-product');
    Route::post('/request-product', [OrderController::class, 'create'])->name('request-product.store');
    
    // Inventory Management
    Route::get('/inventory', [ProductController::class, 'inventory'])->name('inventory.index');
    Route::post('/inventory/{product}/stock', [ProductController::class, 'updateStock'])->name('inventory.update-stock');
    
    // Financial
    Route::get('/wallet', [OutletController::class, 'wallet'])->name('wallet');
    Route::get('/transactions', [OutletController::class, 'transactions'])->name('transactions.index');
    Route::get('/withdraw', [OutletController::class, 'withdrawForm'])->name('withdraw.form');
    Route::post('/withdraw', [OutletController::class, 'withdraw'])->name('withdraw.store');
        Route::post('/withdrawals/{transaction}/cancel', [OutletController::class, 'cancelWithdrawal'])->name('withdrawals.cancel');
    
    // QR Code for shop
    Route::get('/qr-code', [OutletController::class, 'qrCode'])->name('qr-code');
});

// ==================== Rider Routes ====================
Route::middleware(['auth', 'role:rider'])->prefix('rider')->name('rider.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [RiderController::class, 'dashboard'])->name('dashboard');
    
    // Delivery Management
    Route::get('/deliveries/available', [RiderController::class, 'availableDeliveries'])->name('deliveries.available');
    Route::get('/deliveries/my-deliveries', [RiderController::class, 'myDeliveries'])->name('deliveries.my');
    Route::get('/deliveries/{order}', [RiderController::class, 'showDelivery'])->name('deliveries.show');
    Route::post('/deliveries/{order}/accept', [RiderController::class, 'acceptDelivery'])->name('deliveries.accept');
    Route::post('/deliveries/{order}/pickup', [RiderController::class, 'markPickedUp'])->name('deliveries.pickup');
    Route::post('/deliveries/{order}/deliver', [RiderController::class, 'markDelivered'])->name('deliveries.deliver');
    Route::post('/deliveries/{order}/report-issue', [RiderController::class, 'reportIssue'])->name('deliveries.report-issue');
    
    // Earnings
    Route::get('/earnings', [RiderController::class, 'earnings'])->name('earnings');
    Route::get('/earnings/history', [RiderController::class, 'earningHistory'])->name('earnings.history');
    Route::get('/wallet', [RiderController::class, 'wallet'])->name('wallet');
    Route::post('/withdraw', [RiderController::class, 'withdraw'])->name('withdraw');
    
    // Live Location Tracking
    Route::post('/location/update', [RiderController::class, 'updateLocation'])->name('location.update');
    Route::get('/location/{order}', [RiderController::class, 'getLocation'])->name('location.get');
    
    // Availability Toggle
    Route::post('/availability/toggle', [RiderController::class, 'toggleAvailability'])->name('availability.toggle');
});

// ==================== API Routes for AJAX Calls ====================
// ==================== API Routes for AJAX Calls ====================
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    // Orders
    Route::get('/orders/nearby', [App\Http\Controllers\OrderController::class, 'nearbyOrders'])->name('orders.nearby');
    Route::post('/orders/{order}/track', [App\Http\Controllers\OrderController::class, 'trackOrder'])->name('orders.track');
    
    // Products
    Route::get('/products/search', [App\Http\Controllers\ProductController::class, 'search'])->name('products.search');
    Route::get('/products/{product}/availability', [App\Http\Controllers\ProductController::class, 'checkAvailability'])->name('products.availability');
    
    // Notifications
    Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::get('/notifications/latest', [App\Http\Controllers\NotificationController::class, 'latest'])->name('notifications.latest');
    
    // Dashboard Stats
    Route::get('/stats/daily', [App\Http\Controllers\DashboardController::class, 'dailyStats'])->name('stats.daily');
    Route::get('/stats/weekly', [App\Http\Controllers\DashboardController::class, 'weeklyStats'])->name('stats.weekly');
    
    // Location
    Route::get('/areas', [App\Http\Controllers\AreaManagerController::class, 'getAreas'])->name('areas');
    Route::get('/areas/{area}/outlets', [App\Http\Controllers\OutletController::class, 'getOutletsByArea'])->name('areas.outlets');
});

// ==================== Payment Routes ====================
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/initiate/{order}', [PaymentController::class, 'initiate'])->name('initiate');
    Route::post('/callback', [PaymentController::class, 'callback'])->name('callback');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/failed', [PaymentController::class, 'failed'])->name('failed');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
});

// ==================== Testing Routes (Only in Local) ====================
if (app()->environment('local')) {
    Route::get('/test/mail', function () {
        return view('test.mail');
    })->name('test.mail');
    
    Route::get('/test/notification', [NotificationController::class, 'testNotification']);
}

// ==================== Fallback Route ====================
Route::fallback(function () {
    return view('errors.404');
});

require __DIR__.'/auth.php';