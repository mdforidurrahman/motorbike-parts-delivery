@php
    $user = auth()->user();
    $role = $user->role->name ?? '';
@endphp

@if($role == 'admin')
    {{-- Dashboard --}}
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    {{-- User Management --}}
    <a href="#userMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.users*') || request()->routeIs('admin.roles*') ? 'true' : 'false' }}">
        <i class="fas fa-users"></i> User Management
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('admin.users*') || request()->routeIs('admin.roles*') ? 'show' : '' }}" id="userMenu">
        <div class="ps-3">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fas fa-user"></i> All Users
            </a>
            <a href="{{ route('admin.roles') }}" class="nav-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                <i class="fas fa-tag"></i> Roles
            </a>
        </div>
    </div>

    {{-- Area Management --}}
    <a href="{{ route('admin.areas.index') }}" class="nav-link {{ request()->routeIs('admin.areas*') ? 'active' : '' }}">
        <i class="fas fa-map-marker-alt"></i> Areas
    </a>

    {{-- Outlet Management --}}
    <a href="#outletMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.outlets*') ? 'true' : 'false' }}">
        <i class="fas fa-store"></i> Outlet Management
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('admin.outlets*') ? 'show' : '' }}" id="outletMenu">
        <div class="ps-3">
            <a href="{{ route('admin.outlets.index') }}" class="nav-link {{ request()->routeIs('admin.outlets.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i> All Outlets
            </a>
            @if(Route::has('admin.outlets.create'))
            <a href="{{ route('admin.outlets.create') }}" class="nav-link {{ request()->routeIs('admin.outlets.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i> Add New Outlet
            </a>
            @endif
        </div>
    </div>

    {{-- Rider Management --}}
    <a href="#riderMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.riders*') ? 'true' : 'false' }}">
        <i class="fas fa-motorcycle"></i> Rider Management
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('admin.riders*') ? 'show' : '' }}" id="riderMenu">
        <div class="ps-3">
            <a href="{{ route('admin.riders.index') }}" class="nav-link {{ request()->routeIs('admin.riders.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i> All Riders
            </a>
        </div>
    </div>

    {{-- Order Management --}}
    <a href="#orderMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.orders*') ? 'true' : 'false' }}">
        <i class="fas fa-shopping-cart"></i> Order Management
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('admin.orders*') ? 'show' : '' }}" id="orderMenu">
        <div class="ps-3">
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i> All Orders
            </a>
        </div>
    </div>

    {{-- Withdrawal Management --}}
    <a href="#withdrawalMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.withdrawals*') ? 'true' : 'false' }}">
        <i class="fas fa-money-bill-wave"></i> Withdrawal Management
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('admin.withdrawals*') ? 'show' : '' }}" id="withdrawalMenu">
        <div class="ps-3">
            <a href="{{ route('admin.withdrawals.index') }}" class="nav-link {{ request()->routeIs('admin.withdrawals.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Withdrawal Requests
            </a>
        </div>
    </div>

    {{-- Reports --}}
    <a href="#reportMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.reports*') ? 'true' : 'false' }}">
        <i class="fas fa-chart-line"></i> Reports
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('admin.reports*') ? 'show' : '' }}" id="reportMenu">
        <div class="ps-3">
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            @if(Route::has('admin.reports.sales'))
            <a href="{{ route('admin.reports.sales') }}" class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Sales Report
            </a>
            @endif
            @if(Route::has('admin.reports.commission'))
            <a href="{{ route('admin.reports.commission') }}" class="nav-link {{ request()->routeIs('admin.reports.commission') ? 'active' : '' }}">
                <i class="fas fa-percent"></i> Commission Report
            </a>
            @endif
            @if(Route::has('admin.reports.riders'))
            <a href="{{ route('admin.reports.riders') }}" class="nav-link {{ request()->routeIs('admin.reports.riders') ? 'active' : '' }}">
                <i class="fas fa-motorcycle"></i> Rider Performance
            </a>
            @endif
            @if(Route::has('admin.reports.outlets'))
            <a href="{{ route('admin.reports.outlets') }}" class="nav-link {{ request()->routeIs('admin.reports.outlets') ? 'active' : '' }}">
                <i class="fas fa-store"></i> Outlet Performance
            </a>
            @endif
        </div>
    </div>

    {{-- System Settings --}}
    @if(Route::has('admin.settings'))
    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
        <i class="fas fa-cog"></i> System Settings
    </a>
    @endif

    {{-- Profile --}}
    <hr class="mx-3 my-2 text-white-50">
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="fas fa-user-circle"></i> My Profile
    </a>

@elseif($role == 'head-office')
    {{-- Head Office Sidebar --}}
    <a href="{{ route('head-office.dashboard') }}" class="nav-link {{ request()->routeIs('head-office.dashboard*') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    
    @if(Route::has('head-office.financial'))
    <a href="{{ route('head-office.financial') }}" class="nav-link {{ request()->routeIs('head-office.financial*') ? 'active' : '' }}">
        <i class="fas fa-chart-pie"></i> Financial Report
    </a>
    @endif
    
    @if(Route::has('head-office.commission'))
    <a href="{{ route('head-office.commission') }}" class="nav-link {{ request()->routeIs('head-office.commission*') ? 'active' : '' }}">
        <i class="fas fa-percent"></i> Commission Report
    </a>
    @endif
    
    @if(Route::has('head-office.outlets.all'))
    <a href="{{ route('head-office.outlets.all') }}" class="nav-link {{ request()->routeIs('head-office.outlets.all*') ? 'active' : '' }}">
        <i class="fas fa-store"></i> All Outlets
    </a>
    @endif
    
    @if(Route::has('head-office.reports.daily') || Route::has('head-office.reports.monthly'))
    <a href="#reportMenu" class="nav-link" data-bs-toggle="collapse" role="button">
        <i class="fas fa-chart-line"></i> Reports
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse" id="reportMenu">
        <div class="ps-3">
            @if(Route::has('head-office.reports.daily'))
            <a href="{{ route('head-office.reports.daily') }}" class="nav-link">Daily Report</a>
            @endif
            @if(Route::has('head-office.reports.monthly'))
            <a href="{{ route('head-office.reports.monthly') }}" class="nav-link">Monthly Report</a>
            @endif
        </div>
    </div>
    @endif
    
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="fas fa-user-circle"></i> My Profile
    </a>

@elseif($role == 'area-manager')
    {{-- Area Manager Sidebar --}}
    <a href="{{ route('area-manager.dashboard') }}" class="nav-link {{ request()->routeIs('area-manager.dashboard*') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    
    <a href="#outletMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('area-manager.outlets*') ? 'true' : 'false' }}">
        <i class="fas fa-store"></i> Outlets
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('area-manager.outlets*') ? 'show' : '' }}" id="outletMenu">
        <div class="ps-3">
            <a href="{{ route('area-manager.outlets.index') }}" class="nav-link {{ request()->routeIs('area-manager.outlets.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i> All Outlets
            </a>
            <!-- Create route removed - Add button inside index page instead -->
        </div>
    </div>
    
    <a href="#riderMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('area-manager.riders*') ? 'true' : 'false' }}">
        <i class="fas fa-motorcycle"></i> Riders
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('area-manager.riders*') ? 'show' : '' }}" id="riderMenu">
        <div class="ps-3">
            <a href="{{ route('area-manager.riders.index') }}" class="nav-link {{ request()->routeIs('area-manager.riders.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i> All Riders
            </a>
            <!-- Create route removed - Add button inside index page instead -->
        </div>
    </div>
    
    @if(Route::has('area-manager.orders.index'))
    <a href="{{ route('area-manager.orders.index') }}" class="nav-link {{ request()->routeIs('area-manager.orders*') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart"></i> Orders
    </a>
    @endif
    
    @if(Route::has('area-manager.reports.index'))
    <a href="{{ route('area-manager.reports.index') }}" class="nav-link {{ request()->routeIs('area-manager.reports*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> Reports
    </a>
    @endif
    
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="fas fa-user-circle"></i> My Profile
    </a>

@elseif($role == 'outlet-owner')
    {{-- Outlet Owner Sidebar --}}
    <a href="{{ route('outlet.dashboard') }}" class="nav-link {{ request()->routeIs('outlet.dashboard*') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    
    <a href="#productMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('outlet.products*') ? 'true' : 'false' }}">
        <i class="fas fa-box"></i> Products
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('outlet.products*') ? 'show' : '' }}" id="productMenu">
        <div class="ps-3">
            @if(Route::has('outlet.products.index'))
            <a href="{{ route('outlet.products.index') }}" class="nav-link {{ request()->routeIs('outlet.products.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i> All Products
            </a>
            @endif
            @if(Route::has('outlet.products.create'))
            <a href="{{ route('outlet.products.create') }}" class="nav-link {{ request()->routeIs('outlet.products.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i> Add Product
            </a>
            @endif
            @if(Route::has('outlet.inventory.index'))
            <a href="{{ route('outlet.inventory.index') }}" class="nav-link {{ request()->routeIs('outlet.inventory*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Inventory
            </a>
            @endif
        </div>
    </div>
    
    <a href="#orderMenu" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('outlet.orders*') ? 'true' : 'false' }}">
        <i class="fas fa-shopping-cart"></i> Orders
        <i class="fas fa-chevron-down float-end"></i>
    </a>
    <div class="collapse {{ request()->routeIs('outlet.orders*') ? 'show' : '' }}" id="orderMenu">
        <div class="ps-3">
            @if(Route::has('outlet.orders.supplier'))
            <a href="{{ route('outlet.orders.supplier') }}" class="nav-link {{ request()->routeIs('outlet.orders.supplier') ? 'active' : '' }}">
                <i class="fas fa-download"></i> Orders Received
            </a>
            @endif
            @if(Route::has('outlet.orders.buyer'))
            <a href="{{ route('outlet.orders.buyer') }}" class="nav-link {{ request()->routeIs('outlet.orders.buyer') ? 'active' : '' }}">
                <i class="fas fa-upload"></i> Orders Placed
            </a>
            @endif
            @if(Route::has('outlet.request-product'))
            <a href="{{ route('outlet.request-product') }}" class="nav-link {{ request()->routeIs('outlet.request-product') ? 'active' : '' }}">
                <i class="fas fa-cart-plus"></i> Request Product
            </a>
            @endif
        </div>
    </div>
    
    @if(Route::has('outlet.wallet'))
    <a href="{{ route('outlet.wallet') }}" class="nav-link {{ request()->routeIs('outlet.wallet*') ? 'active' : '' }}">
        <i class="fas fa-wallet"></i> Wallet
    </a>
    @endif
    
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="fas fa-user-circle"></i> My Profile
    </a>

@elseif($role == 'rider')
    {{-- Rider Sidebar --}}
    <a href="{{ route('rider.dashboard') }}" class="nav-link {{ request()->routeIs('rider.dashboard*') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    
    @if(Route::has('rider.deliveries.available'))
    <a href="{{ route('rider.deliveries.available') }}" class="nav-link {{ request()->routeIs('rider.deliveries.available*') ? 'active' : '' }}">
        <i class="fas fa-clipboard-list"></i> Available Deliveries
    </a>
    @endif
    
    @if(Route::has('rider.deliveries.my'))
    <a href="{{ route('rider.deliveries.my') }}" class="nav-link {{ request()->routeIs('rider.deliveries.my*') ? 'active' : '' }}">
        <i class="fas fa-truck"></i> My Deliveries
    </a>
    @endif
    
    @if(Route::has('rider.earnings'))
    <a href="{{ route('rider.earnings') }}" class="nav-link {{ request()->routeIs('rider.earnings*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> Earnings
    </a>
    @endif
    
    @if(Route::has('rider.wallet'))
    <a href="{{ route('rider.wallet') }}" class="nav-link {{ request()->routeIs('rider.wallet*') ? 'active' : '' }}">
        <i class="fas fa-wallet"></i> Wallet
    </a>
    @endif
    
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="fas fa-user-circle"></i> My Profile
    </a>

@elseif($role == 'marketing-officer')
    {{-- Marketing Officer Sidebar --}}
    <a href="{{ route('marketing.dashboard') }}" class="nav-link {{ request()->routeIs('marketing.dashboard*') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    
    @if(Route::has('marketing.promotions.index'))
    <a href="{{ route('marketing.promotions.index') }}" class="nav-link {{ request()->routeIs('marketing.promotions*') ? 'active' : '' }}">
        <i class="fas fa-tags"></i> Promotions
    </a>
    @endif
    
    @if(Route::has('marketing.leads.index'))
    <a href="{{ route('marketing.leads.index') }}" class="nav-link {{ request()->routeIs('marketing.leads*') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Leads
    </a>
    @endif
    
    @if(Route::has('marketing.campaigns.index'))
    <a href="{{ route('marketing.campaigns.index') }}" class="nav-link {{ request()->routeIs('marketing.campaigns*') ? 'active' : '' }}">
        <i class="fas fa-bullhorn"></i> Campaigns
    </a>
    @endif
    
    @if(Route::has('marketing.analytics'))
    <a href="{{ route('marketing.analytics') }}" class="nav-link {{ request()->routeIs('marketing.analytics*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> Analytics
    </a>
    @endif
    
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="fas fa-user-circle"></i> My Profile
    </a>

@endif

{{-- Common Logout --}}
<hr class="mx-3 my-2 text-white-50">
<form method="POST" action="{{ route('logout') }}" class="d-inline">
    @csrf
    <button type="submit" class="nav-link text-white bg-transparent border-0 w-100 text-start" style="cursor: pointer;">
        <i class="fas fa-sign-out-alt"></i> Logout
    </button>
</form>