@extends('layouts.app')

@section('title', 'Order History')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">
<style>
    .page-header {
        display: flex;
        align-items: center;
        padding: 20px 40px;
        background: white;
        border-bottom: 1px solid #e0e0e0;
        gap: 15px;
    }
    
    .back-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #333;
        cursor: pointer;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }
    
    .back-btn:hover {
        color: #FFA500;
    }
    
    .page-title {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
        color: #333;
    }
    
    .search-container {
        padding: 20px 40px;
        background: white;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .search-input-container {
        position: relative;
        max-width: 600px;
    }
    
    .search-input {
        width: 100%;
        padding: 12px 40px 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #FFA500;
    }
    
    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        font-size: 16px;
        cursor: pointer;
    }
    
    .order-history-list {
        padding: 20px 40px;
    }
    
    .history-order-item {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .history-order-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .history-order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .history-order-id {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
    
    .history-order-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-processing {
        background: #cfe2ff;
        color: #084298;
    }
    
    .status-ready {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .status-completed {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .status-cancelled {
        background: #f8d7da;
        color: #842029;
    }
    
    .history-order-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .history-detail-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .history-detail-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        font-weight: 600;
    }
    
    .history-detail-value {
        font-size: 14px;
        color: #333;
    }
    
    .history-order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #e0e0e0;
    }
    
    .history-order-date {
        font-size: 13px;
        color: #666;
    }
    
    .history-order-total {
        font-size: 18px;
        font-weight: 600;
        color: #FFA500;
    }
    
    .no-orders {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    
    .no-orders i {
        font-size: 64px;
        margin-bottom: 20px;
        display: block;
        color: #ccc;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
        padding: 30px 0;
    }
    
    .pagination {
        display: flex;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pagination li {
        display: inline-block;
    }
    
    .pagination a,
    .pagination span {
        display: block;
        padding: 8px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .pagination a:hover {
        background: #FFA500;
        color: white;
        border-color: #FFA500;
    }
    
    .pagination .active span {
        background: #FFA500;
        color: white;
        border-color: #FFA500;
    }
    
    .pagination .disabled span {
        color: #ccc;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<!-- Header Bar -->
<header class="header-bar">
    <div class="logo-area">
        <div class="logo-icon"><i class="fas fa-building"></i></div>
        <div class="logo-text">Absolute Essential Trading</div>
    </div>
    <div class="id-tag">{{ session('admin_user_id', 'ADMIN') }}</div>
    <div class="header-actions">
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="logout-icon" title="Logout" style="background:none;border:none;cursor:pointer">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>
    </div>
</header>

<!-- Navigation Tabs -->
<nav class="nav-tabs">
    <a href="{{ route('admin.dashboard') }}" class="nav-tab active">
        <i class="fas fa-shopping-cart"></i> Orders
    </a>
    <a href="{{ route('admin.inventory') }}" class="nav-tab">
        <i class="fas fa-box"></i> Inventory
    </a>
    <a href="{{ route('admin.analytics') }}" class="nav-tab">
        <i class="fas fa-chart-line"></i> Analytics
    </a>
    <a href="{{ route('admin.management') }}" class="nav-tab">
        <i class="fas fa-users"></i> Admins
    </a>
</nav>

<!-- Page Header -->
<div class="page-header">
    <button class="back-btn" onclick="history.back()">
        <i class="fas fa-arrow-left"></i>
    </button>
    <h2 class="page-title">Order History</h2>
</div>

<!-- Main Content -->
<main class="container">
    <!-- Search Bar -->
    <div class="search-container">
        <form method="GET" action="{{ route('admin.order-history') }}" class="search-input-container">
            <input type="text" name="search" id="order-search" class="search-input" 
                   placeholder="Search by Order ID (e.g., AET-2025-001)..." 
                   value="{{ request('search') }}">
            <button type="submit" style="background:none;border:none;position:absolute;right:15px;top:50%;transform:translateY(-50%);cursor:pointer;">
                <i class="fas fa-search search-icon"></i>
            </button>
        </form>
    </div>
    
    <div class="order-history-list" id="order-history-list">
        @forelse($orders as $order)
            <div class="history-order-item" onclick="viewOrderDetail({{ $order->id }})">
                <div class="history-order-header">
                    <div class="history-order-id">{{ $order->order_number }}</div>
                    <div class="history-order-status status-{{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </div>
                </div>
                
                <div class="history-order-details">
                    <div class="history-detail-item">
                        <div class="history-detail-label">Customer Name</div>
                        <div class="history-detail-value">{{ $order->customer_name ?? $order->user->name }}</div>
                    </div>
                    <div class="history-detail-item">
                        <div class="history-detail-label">Phone</div>
                        <div class="history-detail-value">{{ $order->customer_mobile ?? 'N/A' }}</div>
                    </div>
                    <div class="history-detail-item">
                        <div class="history-detail-label">Email</div>
                        <div class="history-detail-value">{{ $order->customer_email ?? $order->user->email }}</div>
                    </div>
                    <div class="history-detail-item">
                        <div class="history-detail-label">Items</div>
                        <div class="history-detail-value">{{ $order->items->count() }} item(s)</div>
                    </div>
                </div>
                
                <div class="history-order-footer">
                    <div class="history-order-date">
                        <i class="fas fa-calendar"></i> <span class="local-time" data-ts="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                    <div class="history-order-total">
                        ₱{{ number_format($order->total_amount, 2) }}
                    </div>
                </div>
            </div>
        @empty
            <div class="no-orders">
                <i class="fas fa-inbox"></i>
                <p style="font-size: 18px; margin: 0;">No orders found</p>
                @if(request('search'))
                    <p style="margin-top: 10px;">Try adjusting your search terms</p>
                @endif
            </div>
        @endforelse
    </div>
    
    @if($orders->hasPages())
        <div class="pagination-container">
            {{ $orders->appends(request()->except('page'))->links() }}
        </div>
    @endif
</main>

<!-- Order Detail Modal -->
<div id="order-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <button class="close-btn" onclick="closeOrderModal()"><i class="fas fa-arrow-left"></i></button>
            <div class="customer-info" id="modal-customer-email"></div>
        </div>
        <div class="modal-body">
            <div class="order-summary">
                <div class="order-date-time-container">
                    <div id="modal-date" class="order-date"></div>
                    <div id="modal-time" class="order-time"></div>
                </div>
                <div class="order-total-container">
                    <div class="order-total-label">Total:</div>
                    <div id="modal-total-price" class="order-total-amount"></div>
                </div>
            </div>
            <h3>Items in this Order</h3>
            <div id="modal-items-list" class="items-list">
                <!-- Items will be dynamically inserted here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewOrderDetail(orderId) {
        fetch(`/admin/orders/${orderId}`)
            .then(response => response.json())
            .then(order => {
                document.getElementById('modal-customer-email').textContent = order.user.email;
                document.getElementById('modal-date').textContent = new Date(order.created_at).toLocaleDateString();
                document.getElementById('modal-time').textContent = new Date(order.created_at).toLocaleTimeString();
                document.getElementById('modal-total-price').textContent = `₱${parseFloat(order.total_amount).toFixed(2)}`;
                
                const itemsList = document.getElementById('modal-items-list');
                itemsList.innerHTML = order.items.map(item => `
                    <div class="order-item">
                        <div class="item-name">${item.product_name} ${item.flavor ? '(' + item.flavor + ')' : ''}</div>
                        <div class="item-details">
                            <span class="item-quantity">Qty: ${item.quantity}</span>
                            <span class="item-price">₱${parseFloat(item.price).toFixed(2)}</span>
                        </div>
                    </div>
                `).join('');
                
                const modal = document.getElementById('order-modal');
                modal.style.display = 'flex';
                modal.classList.add('active');
            })
            .catch(error => {
                console.error('Error loading order:', error);
                alert('Error loading order details. Please try again.');
            });
    }
    
    function closeOrderModal() {
        const modal = document.getElementById('order-modal');
        modal.style.display = 'none';
        modal.classList.remove('active');
    }
    
    // Close modal when clicking outside
    document.getElementById('order-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeOrderModal();
        }
    });
</script>
@endpush
