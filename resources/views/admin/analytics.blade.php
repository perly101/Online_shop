@extends('layouts.app')

@section('title', 'Admin Analytics')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">
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
    <a href="{{ route('admin.dashboard') }}" class="nav-tab">
        <i class="fas fa-shopping-cart"></i> Orders
    </a>
    <a href="{{ route('admin.inventory') }}" class="nav-tab">
        <i class="fas fa-clipboard-list"></i> Inventory
    </a>
    <a href="{{ route('admin.analytics') }}" class="nav-tab active">
        <i class="fas fa-chart-bar"></i> Analytics
    </a>
    <a href="{{ route('admin.management') }}" class="nav-tab">
        <i class="fas fa-users"></i> Admins
    </a>
</nav>

<!-- Main Content -->
<main class="container">
    <!-- Reports & Analytics Card -->
    <div class="analytics-card">
        <!-- Card Title -->
        <div class="analytics-header">
            <i class="fas fa-chart-bar"></i>
            <h2 class="analytics-title">Reports & Analytics</h2>
        </div>

        <div class="analytics-content">
            <!-- Left Column - Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card" onclick="window.location.href='{{ route('admin.order-history') }}'" style="cursor: pointer;">
                    <div class="stat-icon">
                        <i class="fas fa-peso-sign"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-title">Total Sales (Delivered & Complete)</div>
                        <div style="margin-top: 10px; font-size: 14px; color: #666; line-height: 1.8;">
                            <div><strong>Today:</strong> ₱{{ number_format($todayRevenue, 0) }}</div>
                            <div><strong>This Week:</strong> ₱{{ number_format($weekRevenue, 0) }}</div>
                            <div><strong>This Month:</strong> ₱{{ number_format($monthRevenue, 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card" onclick="window.location.href='{{ route('admin.order-history') }}'" style="cursor: pointer;">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-title">Total Orders</div>
                        <div class="stat-value">{{ $totalOrders }}</div>
                    </div>
                </div>

                <!-- Avg. Customer Rating positioned centered directly under the two cards -->
                <div class="stat-card" style="grid-column: span 2; justify-self: center; margin-top: 40px;">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-title">Avg. Customer Rating</div>
                        <div class="stat-value">{{ number_format($avgRating, 1) }}</div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Lists -->
            <div class="lists-column">
                <!-- Top Popular Products -->
                <div class="list-section">
                    <h3 class="list-title">
                        <i class="fas fa-trophy"></i>
                        Top Popular Products
                    </h3>
                    <div class="product-list" id="popular-products-list">
                        @forelse($topProducts as $index => $product)
                            <div class="product-item">
                                <span class="product-item-rank">#{{ $index + 1 }}</span>
                                <span class="product-item-name">{{ $product->product_name }}</span>
                                <span class="product-item-sold">{{ $product->total_sold }} sold</span>
                            </div>
                        @empty
                            <div class="no-data">No sales data yet</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="list-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 class="list-title" style="margin: 0;">
                            <i class="fas fa-clock"></i>
                            Recent Orders
                        </h3>
                        <a href="{{ route('admin.order-history') }}" class="btn-view-all" style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: 600;">
                            <i class="fas fa-list"></i> View All Orders
                        </a>
                    </div>
                    <div class="order-list" id="recent-orders-list">
                        @forelse($recentOrders as $order)
                            <div class="order-item">
                                <div class="order-item-header">
                                    <span class="order-item-id">{{ $order->order_number }}</span>
                                    <span class="order-item-status status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                </div>
                                <div class="order-item-details">
                                    <span class="order-item-customer">{{ $order->user->name }}</span>
                                    <span class="order-item-amount">₱{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <div class="order-item-date"><span class="local-time" data-ts="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->diffForHumans() }}</span></div>
                            </div>
                        @empty
                            <div class="no-data">No recent orders</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Customer Feedback -->
                <div class="list-section">
                    <h3 class="list-title">
                        <i class="fas fa-comment-dots"></i>
                        Recent Customer Feedback
                    </h3>
                    <div class="feedback-list" id="feedback-list">
                        @forelse($recentReviews as $review)
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <div class="feedback-user">{{ $review->user->email }}</div>
                                    <div class="feedback-date">Received: <span class="local-time" data-ts="{{ $review->created_at->toIso8601String() }}">{{ $review->created_at->format('n/j/Y g:i A') }}</span></div>
                                </div>
                                @if($review->comment)
                                    <div class="feedback-text">{{ $review->comment }}</div>
                                @endif
                                <div class="feedback-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star" style="color: {{ $i <= $review->rating ? '#f39c12' : '#ddd' }};"></i>
                                    @endfor
                                </div>
                            </div>
                        @empty
                            <div class="no-data">No feedback yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Analytics rendered from Laravel backend
    console.log('Admin Analytics loaded');
</script>
@endpush
