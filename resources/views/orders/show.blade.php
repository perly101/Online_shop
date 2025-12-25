@extends('layouts.customer')

@section('title', 'Order Details - Absolute Essential Trading')

@push('styles')
<style>
  .content{padding:20px;max-width:1000px;margin:0 auto;padding-bottom:60px}
  .page-title{font-size:28px;font-weight:700;margin-bottom:25px;color:#111;display:flex;align-items:center;gap:15px}
  .back-link{color:#111;text-decoration:none;font-size:20px;transition:transform 0.2s}
  .back-link:hover{transform:translateX(-5px)}
  
  .order-detail-card{background:rgba(255,255,255,0.98);border-radius:12px;padding:30px;box-shadow:0 4px 12px rgba(0,0,0,0.08);border:2px solid rgba(244,180,60,0.35)}
  
  .order-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:25px;padding-bottom:20px;border-bottom:2px solid #f0f0f0;flex-wrap:wrap;gap:15px}
  .order-main-info h2{font-size:24px;font-weight:700;margin:0 0 8px;color:#111}
  .order-date{font-size:15px;color:#666;margin-bottom:5px}
  .order-status-badge{padding:8px 18px;border-radius:25px;font-size:15px;font-weight:600;text-align:center;display:inline-block}
  .status-pending{background:#fff3cd;color:#856404}
  .status-processing{background:#d1ecf1;color:#0c5460}
  .status-ready{background:#cfe2ff;color:#084298}
  .status-completed{background:#d4edda;color:#155724}
  .status-cancelled{background:#f8d7da;color:#721c24}
  
  .details-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:30px}
  .detail-box{background:#f9f9f9;padding:18px;border-radius:8px}
  .detail-label{font-size:13px;color:#666;font-weight:600;text-transform:uppercase;margin-bottom:6px;letter-spacing:0.5px}
  .detail-value{font-size:16px;color:#111;font-weight:600}
  
  .section-title{font-size:20px;font-weight:700;margin:30px 0 15px;color:#111;padding-bottom:10px;border-bottom:2px solid #f0f0f0}
  
  .items-table{width:100%;border-collapse:collapse;margin-bottom:20px}
  .items-table thead{background:#f5f5f5}
  .items-table th{padding:12px;text-align:left;font-weight:600;font-size:14px;color:#666;text-transform:uppercase;letter-spacing:0.5px}
  .items-table td{padding:15px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle}
  .items-table tbody tr:last-child td{border-bottom:none}
  
  .item-image-cell{width:80px}
  .item-image{width:70px;height:70px;border-radius:8px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;overflow:hidden}
  .item-image img{width:100%;height:100%;object-fit:contain}
  
  .item-name{font-size:16px;font-weight:600;color:#111;margin-bottom:4px}
  .item-flavor{font-size:14px;color:#666}
  
  .item-qty{font-size:15px;color:#666;text-align:center}
  .item-price{font-size:16px;font-weight:700;color:#111;text-align:right}
  
  .order-summary{background:#f9f9f9;padding:20px;border-radius:8px;margin-top:20px}
  .summary-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;font-size:16px}
  .summary-row.total{font-size:22px;font-weight:700;color:#111;margin-top:15px;padding-top:15px;border-top:2px solid #ddd}
  
  .qr-code-section{margin-top:30px;padding:25px;background:#f9f9f9;border-radius:8px;text-align:center}
  .qr-code-title{font-size:18px;font-weight:700;margin-bottom:15px;color:#111}
  .qr-code-wrapper{display:inline-block;padding:15px;background:#fff;border-radius:8px;border:2px solid #ddd}
  .qr-code-note{font-size:14px;color:#666;margin-top:15px;line-height:1.6}
  
  .action-buttons{display:flex;gap:15px;margin-top:30px;flex-wrap:wrap}
  .btn{padding:12px 28px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;transition:all 0.2s;text-decoration:none;display:inline-block}
  .btn-primary{background:linear-gradient(90deg,var(--gold1),var(--gold2));color:#111}
  .btn-primary:hover{transform:translateY(-2px);box-shadow:0 4px 10px rgba(0,0,0,0.15)}
  .btn-outline{background:#fff;border:2px solid #111;color:#111}
  .btn-outline:hover{background:#111;color:#fff}
  .btn-danger{background:#dc3545;color:#fff}
  .btn-danger:hover{background:#c82333}
  .btn:disabled{opacity:0.5;cursor:not-allowed}
  
  @media (max-width:768px){
    .details-grid{grid-template-columns:1fr}
    .items-table{font-size:14px}
    .items-table th,.items-table td{padding:10px 8px}
    .item-image{width:50px;height:50px}
    .page-title{font-size:24px}
  }
</style>
@endpush

@section('content')
<div class="content">
  <h1 class="page-title">
    <a href="{{ route('orders.index') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i></a>
    Order Details
  </h1>
  
  <div class="order-detail-card">
    <!-- Order Header -->
    <div class="order-header">
      <div class="order-main-info">
        <h2>Order #{{ $order->order_number }}</h2>
        <div class="order-date">Placed on <span class="local-time" data-ts="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->format('M d, Y \a\t h:i A') }}</span></div>
      </div>
      <div class="order-status-badge status-{{ strtolower($order->status) }}">
        {{ ucfirst($order->status) }}
      </div>
    </div>
    
    <!-- Customer & Order Info -->
    <div class="details-grid">
      <div class="detail-box">
        <div class="detail-label">Customer Name</div>
        <div class="detail-value">{{ $order->customer_name }}</div>
      </div>
      <div class="detail-box">
        <div class="detail-label">Mobile Number</div>
        <div class="detail-value">{{ $order->customer_mobile }}</div>
      </div>
      <div class="detail-box">
        <div class="detail-label">Email Address</div>
        <div class="detail-value">{{ $order->customer_email ?? 'N/A' }}</div>
      </div>
      <div class="detail-box">
        <div class="detail-label">Payment Method</div>
        <div class="detail-value">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</div>
      </div>
    </div>
    
    <!-- Order Items -->
    <h3 class="section-title">Order Items</h3>
    <table class="items-table">
      <thead>
        <tr>
          <th class="item-image-cell"></th>
          <th>Product</th>
          <th style="text-align:center">Quantity</th>
          <th style="text-align:right">Price</th>
          <th style="text-align:right">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
        <tr>
          <td class="item-image-cell">
            <div class="item-image">
              @if($item->product)
                <img src="{{ asset('images/' . $item->product->image) }}" alt="{{ $item->product_name }}">
              @else
                <img src="{{ asset('images/pick up.jpg') }}" alt="{{ $item->product_name }}">
              @endif
            </div>
          </td>
          <td>
            <div class="item-name">{{ $item->product_name }}</div>
            @if($item->flavor)
              <div class="item-flavor">Flavor: {{ $item->flavor }}</div>
            @endif
          </td>
          <td class="item-qty">{{ $item->quantity }}</td>
          <td class="item-price">₱{{ number_format($item->price, 2) }}</td>
          <td class="item-price">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    
    <!-- Order Summary -->
    <div class="order-summary">
      <div class="summary-row">
        <span>Subtotal</span>
        <span>₱{{ number_format($order->total_amount, 2) }}</span>
      </div>
      <div class="summary-row total">
        <span>Total</span>
        <span>₱{{ number_format($order->total_amount, 2) }}</span>
      </div>
    </div>
    
    <!-- QR Code for Pickup -->
    @if($order->pickup_qr_code && in_array($order->status, ['ready', 'processing', 'pending']))
    <div class="qr-code-section">
      <h3 class="qr-code-title">Pickup QR Code</h3>
      <div class="qr-code-wrapper" id="qrcode"></div>
      <p class="qr-code-note">
        Present this QR code when picking up your order. Make sure to save or screenshot this code for easy access.
      </p>
    </div>
    @endif
    
    <!-- Pickup Location -->
    <div class="detail-box" style="margin-top:20px">
      <div class="detail-label">Pickup Location</div>
      <div class="detail-value">Alesandra Building, Rizal Corner Amat Streets, Surigao City</div>
    </div>
    
    <!-- Action Buttons -->
    <div class="action-buttons">
      <a href="{{ route('orders.index') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Orders
      </a>
      
      @if($order->status === 'completed')
      <button class="btn btn-primary" onclick="reorder()">
        <i class="fa-solid fa-refresh"></i> Reorder
      </button>
      @endif
      
      @if(in_array($order->status, ['pending', 'processing']))
      <button class="btn btn-danger" onclick="cancelOrder()">
        <i class="fa-solid fa-times"></i> Cancel Order
      </button>
      @endif
    </div>
  </div>
</div>

<!-- Custom Popup Modal -->
<div id="global-popup" class="popup-modal" style="display: none;">
  <div class="popup-content">
    <span class="popup-close" id="popup-close-btn" style="display: none;">&times;</span>
    <p id="popup-message"></p>
    <div class="popup-buttons" style="margin-top: 20px; display: none;" id="popup-buttons-container">
      <button id="popup-cancel-btn" class="btn-cancel" style="margin-right: 10px;">Cancel</button>
      <button id="popup-ok-btn" class="btn-save">Yes</button>
    </div>
  </div>
</div>

<style>
:root{--gold1:#ffd54a;--gold2:#f0ad06}
.popup-modal{position:fixed;inset:0;background:rgba(0,0,0,0.30);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;z-index:10000}
.popup-content{background:#fff;border-radius:12px;box-shadow:0 30px 60px rgba(0,0,0,0.25);width:90%;max-width:500px;position:relative;border:2px solid rgba(240,173,6,0.3);padding:20px}
.popup-close{background:transparent;border:none;font-size:24px;cursor:pointer;padding:4px 8px;color:#666}
.popup-buttons{display:flex;justify-content:flex-end;gap:10px}
.btn-cancel{background:#6c757d;color:#fff;border:none;padding:10px 20px;border-radius:6px;cursor:pointer}
.btn-save{background:#ffd54a;color:#111;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;font-weight:600}
</style>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// Generate QR Code
@if($order->pickup_qr_code)
document.addEventListener('DOMContentLoaded', function() {
  const qrcodeEl = document.getElementById('qrcode');
  if (qrcodeEl && typeof QRCode !== 'undefined') {
    new QRCode(qrcodeEl, {
      text: '{{ $order->pickup_qr_code }}',
      width: 200,
      height: 200,
      colorDark: '#000000',
      colorLight: '#ffffff',
      correctLevel: QRCode.CorrectLevel.H
    });
  }
});
@endif

// Cancel Order
function cancelOrder() {
  showConfirmPopup('Are you sure you want to cancel this order?', async () => {
    try {
      const response = await fetch('{{ route('orders.cancel', $order->id) }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      });
      
      const data = await response.json();
      
      if (data.success) {
        showPopup(data.message);
        setTimeout(() => {
          window.location.reload();
        }, 2000);
      } else {
        showPopup(data.message);
      }
    } catch (error) {
      console.error('Error:', error);
      showPopup('Failed to cancel order. Please try again.');
    }
  });
}

// Reorder
function reorder() {
  showConfirmPopup('Add all items from this order to your cart?', async () => {
    try {
      const response = await fetch('{{ route('orders.reorder', $order->id) }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      });
      const data = await response.json();
      if (data.success) {
        showPopup(data.message || 'Items added to cart');
        setTimeout(() => {
          window.location.href = '{{ route('cart.index') }}';
        }, 900);
      } else {
        showPopup(data.message || 'Failed to add items to cart');
      }
    } catch (err) {
      console.error('Reorder failed', err);
      showPopup('Failed to add items to cart, please try again');
    }
  });
}

// Popup Functions
function showPopup(message) {
  document.getElementById('popup-message').textContent = message;
  document.getElementById('popup-buttons-container').style.display = 'none';
  document.getElementById('global-popup').style.display = 'flex';
  
  setTimeout(() => {
    document.getElementById('global-popup').style.display = 'none';
  }, 2000);
}

function showConfirmPopup(message, callback) {
  document.getElementById('popup-message').textContent = message;
  document.getElementById('popup-buttons-container').style.display = 'flex';
  document.getElementById('global-popup').style.display = 'flex';
  
  document.getElementById('popup-ok-btn').onclick = function() {
    document.getElementById('global-popup').style.display = 'none';
    callback();
  };
  
  document.getElementById('popup-cancel-btn').onclick = function() {
    document.getElementById('global-popup').style.display = 'none';
  };
}

// Close modal on outside click
document.getElementById('global-popup').addEventListener('click', function(e) {
  if (e.target === this) {
    this.style.display = 'none';
  }
});
</script>
@endpush
