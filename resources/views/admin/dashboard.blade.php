@extends('layouts.app')

@section('title', 'Admin Dashboard - Orders')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">
<style>
/* Admin-only: hide native scrollbars while preserving scrolling */
html, body { -ms-overflow-style: none; scrollbar-width: none; }
html::-webkit-scrollbar, body::-webkit-scrollbar { display: none; width: 0; height: 0; }
.container, .customer-list-panel, .user-detail-panel, .order-history-list, .analytics-content { -ms-overflow-style: none; scrollbar-width: none; }
.container::-webkit-scrollbar, .customer-list-panel::-webkit-scrollbar, .user-detail-panel::-webkit-scrollbar, .order-history-list::-webkit-scrollbar, .analytics-content::-webkit-scrollbar { display: none; width: 0; height: 0; }
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
        <i class="fas fa-shopping-cart"></i> Orders <span class="badge">{{ $orders->count() }}</span>
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

<!-- Main Content -->
<main class="container">
    <!-- Order Pick-up Verification Section -->
    <div class="pickup-verification-container">
        <h2>Order Pick-up Verification</h2>
        
        <div class="verification-content">
            <!-- Left side - Order ID Search -->
            <div class="order-search-container">
                <div class="search-box">
                    <input type="text" id="order-id-verification" placeholder="Search by Order ID (e.g., AET-2025-001)...">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <!-- Right side - QR Code Scanner -->
            <div class="qr-scanner-container">
                <div class="scanner-placeholder">
                    <i class="fas fa-qrcode scanner-icon"></i>
                    <p>QR Code Scanner</p>
                    <button class="scan-btn">Scan QR Code</button>
                </div>
            </div>
        </div>
    </div>
    
    <h2>Recent Orders</h2>
    
    <div id="recent-orders-list">
        @forelse($orders->where('status', '!=', 'completed') as $order)
            <div class="order-item">
                <div class="order-detail">
                    <strong>Order ID:</strong> {{ $order->order_number }}
                </div>
                <div class="order-detail">
                    <strong>Customer Name:</strong> {{ $order->customer_name ?? $order->user->name }}
                </div>
                <div class="order-detail">
                    <strong>Phone:</strong> {{ $order->customer_mobile ?? 'N/A' }}
                </div>
                <div class="order-detail">
                    <strong>Email:</strong> {{ $order->customer_email ?? $order->user->email }}
                </div>
                <div class="order-detail">
                    <strong>Placed At:</strong> <span class="local-time" data-ts="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->format('m/d/Y g:i:s A') }}</span>
                </div>
                <div class="order-actions">
                    <a href="#" class="view-orders-btn" onclick="viewOrder({{ $order->id }}); return false;">View Orders</a>
                    <div class="update-status">
                        <span>Update Status:</span>
                        <select class="status-dropdown" data-order-id="{{ $order->id }}" onchange="updateOrderStatus({{ $order->id }}, this.value)">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready for Pickup</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Complete</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancel</option>
                        </select>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-orders" style="text-align:center;padding:40px;color:#666;">
                <i class="fas fa-inbox" style="font-size:48px;margin-bottom:16px;display:block;"></i>
                <p>No orders yet</p>
            </div>
        @endforelse
    </div>
</main>

<!-- Order Detail Modal -->
<div id="order-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <button class="close-btn" id="order-close-btn" type="button">
                <i class="fas fa-arrow-left"></i>
            </button>
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
            <div class="modal-actions">
                <button class="btn-verify" onclick="verifyOrder()">Mark as Completed</button>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div id="qr-scanner-modal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <button class="close-btn" id="qr-close-btn" type="button">
                <i class="fas fa-times"></i>
            </button>
            <h2 style="margin: 0; font-size: 20px;"><i class="fas fa-qrcode"></i> Scan Order QR Code</h2>
        </div>
        <div class="modal-body">
            <div id="qr-reader" style="width: 100%; border-radius: 8px; overflow: hidden;"></div>
            <div id="scan-status" style="text-align: center; margin-top: 15px; font-size: 14px; color: #666;">
                Position QR code within the frame
            </div>
            <div id="scanned-order-info" style="display: none;">
                <!-- Order details will be displayed here after successful scan -->
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div id="notification-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Send Notification to Customer</h3>
        </div>
        <div class="modal-body">
            <p id="notification-message" style="margin-bottom: 20px; font-size: 16px;"></p>
            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn-cancel-edit" id="cancel-notification-send">Cancel</button>
                <button class="btn-add-product" id="send-notification-to-customer">Send</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let currentOrderId = null;
    let html5QrCode = null;
    
    function viewOrder(orderId) {
        currentOrderId = orderId;
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
    
    function closeModal() {
        const modal = document.getElementById('order-modal');
        modal.style.display = 'none';
        modal.classList.remove('active');
    }
    
    function verifyOrder() {
        if (!currentOrderId) return;
        
        if (confirm('Mark this order as completed?')) {
            fetch(`/admin/orders/${currentOrderId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order marked as completed!');
                    location.reload();
                }
            });
        }
    }

    // QR Scanner functionality
    const scanBtn = document.querySelector('.scan-btn');
    if (scanBtn) {
        scanBtn.addEventListener('click', function() {
            const modal = document.getElementById('qr-scanner-modal');
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.add('active');
                setTimeout(() => startQrScanner(), 300);
            }
        });
    }

    function startQrScanner() {
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        html5QrCode = new Html5Qrcode("qr-reader");
        
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error("Error starting scanner:", err);
            document.getElementById('scan-status').textContent = 'Error accessing camera. Please check permissions.';
            document.getElementById('scan-status').style.color = '#e74c3c';
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning
        html5QrCode.stop().then(() => {
            document.getElementById('scan-status').textContent = 'Processing...';
            document.getElementById('scan-status').style.color = '#3498db';
            
            // Send QR code to server for verification
            fetch('/admin/scan-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_code: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('scan-status').textContent = data.message;
                    document.getElementById('scan-status').style.color = '#27ae60';
                    displayScannedOrder(data.order);
                } else {
                    document.getElementById('scan-status').textContent = data.message;
                    document.getElementById('scan-status').style.color = '#e74c3c';
                    setTimeout(() => {
                        closeQrScanner();
                    }, 3000);
                }
            })
            .catch(error => {
                document.getElementById('scan-status').textContent = 'Error verifying QR code';
                document.getElementById('scan-status').style.color = '#e74c3c';
                console.error('Error:', error);
            });
        });
    }

    function onScanFailure(error) {
        // Silent - scanning in progress
    }

    function displayScannedOrder(order) {
        const orderInfo = document.getElementById('scanned-order-info');
        orderInfo.innerHTML = `
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;">
                <h3 style="margin: 0 0 15px; color: #27ae60;"><i class="fas fa-check-circle"></i> Order Found!</h3>
                <div style="margin-bottom: 10px;"><strong>Order Number:</strong> ${order.order_number}</div>
                <div style="margin-bottom: 10px;"><strong>Customer:</strong> ${order.customer_name}</div>
                <div style="margin-bottom: 10px;"><strong>Mobile:</strong> ${order.customer_mobile}</div>
                <div style="margin-bottom: 10px;"><strong>Total:</strong> ₱${parseFloat(order.total_amount).toFixed(2)}</div>
                <div style="margin-bottom: 10px;"><strong>Status:</strong> <span class="status-${order.status}">${order.status.toUpperCase()}</span></div>
                <div style="margin-top: 15px;"><strong>Items:</strong></div>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    ${order.items.map(item => `
                        <li>${item.product_name} ${item.flavor ? '(' + item.flavor + ')' : ''} × ${item.quantity} - ₱${parseFloat(item.price * item.quantity).toFixed(2)}</li>
                    `).join('')}
                </ul>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button id="complete-pickup-btn" data-order-id="${order.id}" class="btn-complete-pickup" style="flex: 1; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-check"></i> Complete Pickup
                    </button>
                    <button id="cancel-pickup-btn" type="button" style="flex: 1; padding: 12px; background: #95a5a6; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </div>
        `;
        orderInfo.style.display = 'block';
        
        // Attach event listeners to the new buttons
        setTimeout(() => {
            const completeBtn = document.getElementById('complete-pickup-btn');
            if (completeBtn) {
                completeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const orderId = this.getAttribute('data-order-id');
                    console.log('Complete pickup clicked for order:', orderId);
                    completePickup(orderId);
                });
            }
            
            const cancelBtn = document.getElementById('cancel-pickup-btn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Cancel button clicked');
                    closeQrScanner();
                });
            }
        }, 100);
    }

    function completePickup(orderId) {
        if (!orderId) {
            alert('Invalid order ID');
            return;
        }
        
        if (confirm('Confirm order pickup and mark as completed?')) {
            fetch(`/admin/orders/${orderId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert('Order completed successfully!');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Order completed successfully!');
                location.reload();
            });
        } else {
            // User cancelled the confirmation
            closeQrScanner();
        }
    }

    function closeQrScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
                resetQrModal();
            }).catch(err => {
                console.error("Error stopping scanner:", err);
                resetQrModal();
            });
        } else {
            resetQrModal();
        }
    }
    
    function resetQrModal() {
        const modal = document.getElementById('qr-scanner-modal');
        if (modal) {
            modal.classList.remove('active');
            modal.style.display = 'none';
        }
        
        const qrReader = document.getElementById('qr-reader');
        if (qrReader) {
            qrReader.innerHTML = ''; // Clear the QR reader
        }
        
        const scanStatus = document.getElementById('scan-status');
        if (scanStatus) {
            scanStatus.textContent = 'Position QR code within the frame';
            scanStatus.style.color = '#666';
        }
        
        const orderInfo = document.getElementById('scanned-order-info');
        if (orderInfo) {
            orderInfo.style.display = 'none';
            orderInfo.innerHTML = '';
        }
    }
    
    // Event listeners for QR modal close
    document.addEventListener('DOMContentLoaded', function() {
        // Order modal close button
        const orderCloseBtn = document.getElementById('order-close-btn');
        if (orderCloseBtn) {
            orderCloseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeModal();
            });
        }
        
        // Order modal backdrop click
        const orderModal = document.getElementById('order-modal');
        if (orderModal) {
            orderModal.addEventListener('click', function(e) {
                if (e.target.id === 'order-modal') {
                    closeModal();
                }
            });
            
            // Prevent clicks inside modal content from closing
            const modalContentOrder = orderModal.querySelector('.modal-content');
            if (modalContentOrder) {
                modalContentOrder.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        }
        
        // QR Close button
        const qrCloseBtn = document.getElementById('qr-close-btn');
        if (qrCloseBtn) {
            qrCloseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('QR Close button clicked');
                closeQrScanner();
            });
        }
        
        // QR Backdrop click
        const qrModal = document.getElementById('qr-scanner-modal');
        if (qrModal) {
            qrModal.addEventListener('click', function(e) {
                if (e.target.id === 'qr-scanner-modal') {
                    e.preventDefault();
                    e.stopPropagation();
                    closeQrScanner();
                }
            });
            
            // Prevent clicks inside QR modal content from closing
            const modalContentQr = qrModal.querySelector('.modal-content');
            if (modalContentQr) {
                modalContentQr.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        }
    });
    
    function handleModalBackdropClick(event) {
        if (event.target.id === 'qr-scanner-modal') {
            closeQrScanner();
        }
    }
    
    // Update order status function
    function updateOrderStatus(orderId, newStatus) {
        // If status is ready (Ready for Pickup), show notification modal
        if (newStatus === 'ready') {
            showNotificationModal(orderId, newStatus);
        } else {
            // For other status changes, just update directly
            if (confirm('Update order status to ' + newStatus + '?')) {
                performStatusUpdate(orderId, newStatus);
            } else {
                // Reset select to original value
                location.reload();
            }
        }
    }

    // Show notification modal
    function showNotificationModal(orderId, newStatus) {
        const modal = document.getElementById('notification-modal');
        const messageElement = document.getElementById('notification-message');
        const cancelBtn = document.getElementById('cancel-notification-send');
        const sendBtn = document.getElementById('send-notification-to-customer');
        
        // Find order to get order number
        const orderElement = document.querySelector(`select[data-order-id="${orderId}"]`);
        const orderRow = orderElement ? orderElement.closest('.order-item') : null;
        const orderNumberElement = orderRow ? orderRow.querySelector('.order-id') : null;
        const orderNumber = orderNumberElement ? orderNumberElement.textContent : orderId;
        
        // Set message
        messageElement.textContent = `Your order ${orderNumber} is ready for pick-up`;
        
        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Remove existing event listeners
        const newCancelBtn = cancelBtn.cloneNode(true);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
        const newSendBtn = sendBtn.cloneNode(true);
        sendBtn.parentNode.replaceChild(newSendBtn, sendBtn);
        
        // Cancel button
        document.getElementById('cancel-notification-send').addEventListener('click', function() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            location.reload(); // Reset dropdown
        });
        
        // Send button
        document.getElementById('send-notification-to-customer').addEventListener('click', function() {
            performStatusUpdate(orderId, newStatus, messageElement.textContent);
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    }

    // Perform status update
    function performStatusUpdate(orderId, newStatus, notificationMessage = null) {
        console.log('Updating order:', orderId, 'to status:', newStatus);
        console.log('URL:', `/admin/orders/${orderId}/status`);
        
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                _method: 'PUT',
                status: newStatus 
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response OK:', response.ok);
            return response.text(); // Get text first to see what we're receiving
        })
        .then(text => {
            console.log('Response text:', text);
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alert('Order status updated and notification sent!');
                    location.reload();
                } else {
                    alert('Failed to update status: ' + (data.message || 'Unknown error'));
                    console.error('Update failed:', data);
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response was:', text);
                alert('Server returned non-JSON response. Check console for details.');
            }
        })
        .catch(error => {
            console.error('Error details:', error);
            alert('Error updating status: ' + error.message);
        });
    }
</script>
@endpush
