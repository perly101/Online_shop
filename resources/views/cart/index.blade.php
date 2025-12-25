@extends('layouts.customer')

@section('title', 'Your Cart - Absolute Essential Trading')

@push('styles')
<style>
  :root{--gold1:#ffd54a;--gold2:#f0ad06}
  html,body{height:100%;margin:0;font-family:Georgia, 'Times New Roman', serif;color:#111;overflow:hidden}
  body{background:#fff;background-image:url('{{ asset('images/Backgound.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed}

  /* header */
  .topbar{display:flex;align-items:center;justify-content:space-between;padding:20px 26px;background:linear-gradient(90deg,var(--gold1),var(--gold2));box-shadow:0 2px 6px rgba(0,0,0,0.08);position:fixed;top:0;left:0;right:0;width:100%;z-index:1000}
  .brand-section{display:flex;align-items:center;gap:12px}
  .brand{font-size:20px;font-weight:700}
  .icons{display:flex;gap:12px;align-items:center}
  .icon-btn{width:40px;height:40px;border-radius:6px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.15);color:#000;cursor:pointer;border:none;padding:0;transition:background 0.2s}
  .icon-btn:hover{background:rgba(0,0,0,0.25)}
  .icon-btn i{font-size:18px}
  #backBtn{background:transparent;width:auto;height:auto;padding:0}
  #backBtn:hover{background:transparent}

  /* hero and overlay - same as shop-home */
  .hero{position:fixed;top:72px;left:0;right:0;bottom:72px;padding:0;background:none;display:flex;flex-direction:column;overflow-y:auto;overflow-x:hidden}
  .hero::-webkit-scrollbar{display:none}
  .hero{-ms-overflow-style:none;scrollbar-width:none}
  .overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:linear-gradient(rgba(255,244,200,0.60), rgba(255,244,200,0.60));mix-blend-mode:normal;pointer-events:none;z-index:1}
  .hero-content{padding:40px 18px 40px;z-index:2;width:100%;max-width:1100px;margin:0 auto}

  /* cart card */
  .cart-card{background:rgba(255,255,255,0.98);border-radius:10px;padding:26px;border:2px solid rgba(244,180,60,0.35);box-shadow:0 20px 40px rgba(0,0,0,0.06)}
  .cart-header{font-family:Georgia, 'Times New Roman', serif;font-size:26px;font-weight:700;margin-bottom:18px}

  .cart-list{display:flex;flex-direction:column;gap:12px}
  .cart-row{display:flex;align-items:center;gap:18px;padding:18px;border-top:1px solid rgba(0,0,0,0.03)}
  .cart-row:first-of-type{border-top:0}
  .col-check{width:36px;text-align:center}
  .thumb img{width:68px;height:68px;object-fit:contain;border-radius:6px}
  .col-name{flex:1;font-weight:700}
  .col-unit{width:120px;text-align:right}
  .col-qty{width:170px;text-align:center}
  .col-sub{width:120px;text-align:right}
  .qty-control{display:inline-flex;align-items:center;border:1px solid #eee;border-radius:6px;overflow:hidden;background:#fff}
  .qty-control button{background:transparent;border:0;padding:6px 12px;cursor:pointer}
  .qty-control input{width:48px;text-align:center;border:0}
  .remove{background:transparent;border:1px solid rgba(244,180,60,0.6);color:#333;padding:8px 10px;border-radius:8px;cursor:pointer}

  .cart-footer{display:flex;align-items:center;justify-content:space-between;margin-top:18px}
  .checkout{background:var(--gold1);border:none;padding:12px 18px;border-radius:10px;font-weight:700;cursor:pointer}
  .empty{padding:40px;text-align:center;color:#666}

  /* Checkout overlay (styled to match design sample) */
  .checkout-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.30);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;z-index:9999}
  .checkout-panel{width:88%;max-width:1000px;background:#fff;border:6px solid rgba(240,173,6,0.95);box-shadow:0 30px 60px rgba(0,0,0,0.25);padding:28px;border-radius:12px;display:grid;grid-template-columns:1fr 380px;gap:28px;position:relative}
  .checkout-left{padding:8px 8px 8px 24px}
  .checkout-left h2{font-size:36px;margin:0 0 18px;font-weight:800;font-family:Georgia, serif}
  .checkout-left label{display:block;font-size:14px;color:#222;margin:12px 0 6px}
  .checkout-left input[type=text], .checkout-left input[type=email], .checkout-left input[type=tel]{width:90%;box-sizing:border-box;padding:12px;border:1px solid #e7e7e7;background:#fafafa;border-radius:6px}
  .payment-method{margin-top:20px;font-size:18px;font-weight:700}
  .place-order-btn{display:inline-block;margin-top:18px;background:var(--gold2);color:#111;padding:12px 22px;font-weight:800;border-radius:8px;border:none;cursor:pointer;box-shadow:0 6px 18px rgba(240,140,0,0.12)}
  .checkout-right{width:360px;padding:22px 28px;background:#f6f6f6;border-radius:8px;transform:translateX(-10px)}
  .checkout-right h3{font-size:30px;text-align:center;margin:6px 0 18px;font-weight:700}
  .order-list{background:#fff;padding:12px;border-radius:6px;box-shadow:inset 0 1px 0 rgba(255,255,255,0.6)}
  .order-item{display:flex;justify-content:space-between;padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.04)}
  .order-total{display:flex;justify-content:space-between;font-weight:700;padding-top:18px;margin-top:8px}
  .checkout-close{position:absolute;right:18px;top:12px;font-size:22px;background:transparent;border:none;cursor:pointer}

  @media (max-width:900px){ .col-unit,.col-sub{display:none} .col-qty{width:120px} .cart-row{flex-wrap:wrap} }
  
  /* footer - fixed at bottom */
  .footer{background:linear-gradient(90deg,var(--gold1),var(--gold2));padding:4px 26px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 -2px 6px rgba(0,0,0,0.08);position:fixed;bottom:0;left:0;right:0;z-index:10}
  .footer-left{display:flex;align-items:center;gap:12px;font-size:14px}
  .footer-right{display:flex;gap:12px;align-items:center}
  .footer-icon{position:relative;width:40px;height:40px;border-radius:6px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.15);color:#000;cursor:pointer;transition:background 0.2s}
  .footer-icon:hover{background:rgba(0,0,0,0.25)}
  .footer-icon i{font-size:18px}
  .notification-dot{position:absolute;top:4px;right:4px;width:8px;height:8px;background:#e74c3c;border-radius:50%;border:2px solid var(--gold1)}
  .notification-dot.hidden{display:none}
  
  /* Sidebar */
  .sidebar-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.30);backdrop-filter:blur(6px);display:none;z-index:9999;transition:opacity 0.3s}
  .sidebar-overlay.active{display:block;opacity:1}
  .sidebar{position:fixed;right:-300px;top:0;bottom:0;width:280px;background:#fff;box-shadow:-2px 0 20px rgba(0,0,0,0.15);transition:right 0.3s ease;z-index:10000;display:flex;flex-direction:column}
  .sidebar.active{right:0}
  .sidebar-header{background:linear-gradient(90deg,var(--gold1),var(--gold2));padding:20px;display:flex;align-items:center;justify-content:space-between;border-bottom:2px solid rgba(0,0,0,0.1)}
  .sidebar-title{font-size:20px;font-weight:700;color:#111}
  .sidebar-close{background:rgba(0,0,0,0.15);border:none;width:36px;height:36px;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s}
  .sidebar-close:hover{background:rgba(0,0,0,0.25)}
  .sidebar-close i{font-size:18px}
  .sidebar-content{flex:1;overflow-y:auto;padding:0}
  .sidebar-menu{list-style:none;padding:0;margin:0}
  .sidebar-menu li{border-bottom:1px solid #f0f0f0}
  .sidebar-menu a{display:flex;align-items:center;gap:12px;padding:18px 20px;color:#333;text-decoration:none;font-size:16px;transition:background 0.2s}
  .sidebar-menu a:hover{background:#f9f9f9}
  .sidebar-menu i{width:24px;text-align:center;color:#666;font-size:18px}
  
  /* Notifications Modal */
  .notifications-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.30);backdrop-filter:blur(6px);display:none;align-items:flex-start;justify-content:center;z-index:10000;padding:20px}
  .notifications-overlay.active{display:flex}
  .notifications-panel{background:#fff;border-radius:12px;box-shadow:0 30px 60px rgba(0,0,0,0.25);width:90%;max-width:600px;max-height:80vh;display:flex;flex-direction:column;margin-top:60px;position:relative;border:2px solid rgba(240,173,6,0.3)}
  .notifications-header{display:flex;align-items:center;justify-content:space-between;padding:20px;border-bottom:1px solid #eee}
  .notifications-header h2{margin:0;font-size:24px;font-weight:700;color:#111}
  .notifications-close{background:transparent;border:none;font-size:24px;cursor:pointer;padding:4px 8px;color:#666}
  .notifications-content{flex:1;overflow-y:auto;padding:0}
  .notifications-list{list-style:none;padding:0;margin:0}
  .notification-item{display:flex;align-items:flex-start;gap:12px;padding:16px 20px;border-bottom:1px solid #f0f0f0;cursor:pointer;transition:background 0.2s}
  .notification-item:hover{background:#f9f9f9}
  .notification-item.unread{background:#fffef0;border-left:4px solid var(--gold2)}
  .notification-icon{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px}
  .notification-icon.success{background:#d4edda;color:#155724}
  .notification-icon.info{background:#d1ecf1;color:#0c5460}
  .notification-content{flex:1}
  .notification-message{font-size:15px;color:#333;margin-bottom:4px;line-height:1.4}
  .notification-time{font-size:12px;color:#999}
  .notification-empty{text-align:center;padding:60px 20px;color:#999}
  .notification-empty i{font-size:48px;margin-bottom:12px;color:#ddd}
  .mark-all-read{background:var(--gold1);border:none;padding:8px 16px;border-radius:6px;font-weight:600;cursor:pointer;font-size:14px;margin:10px 20px}
  .mark-all-read:disabled{opacity:0.5;cursor:not-allowed}
  
  /* Custom Popup Styles */
  .popup-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.30);
    backdrop-filter: blur(6px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
  }
  
  .popup-content {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 30px 60px rgba(0,0,0,0.25);
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    position: relative;
    border: 2px solid rgba(240,173,6,0.3);
  }
  
  .popup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border-bottom: 1px solid #eee;
  }
  
  .popup-header h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #111;
  }
  
  .popup-close {
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
    padding: 4px 8px;
    color: #666;
  }
  
  .popup-message {
    font-size: 16px;
    color: #333;
    margin-bottom: 20px;
    line-height: 1.4;
  }
  
  .popup-button {
    background: #f0f0f0;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    font-size: 14px;
  }
  
  .popup-button:hover {
    background: #e0e0e0;
  }
  
  .popup-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }
</style>
@endpush

@section('content')
<section class="hero" role="main">
  <div class="overlay" aria-hidden="true"></div>
  <div class="hero-content">
    <div class="cart-card">
      <div class="cart-header">Your Shopping Cart</div>

      @if(count($cart) > 0)
      <div class="cart-list">
        @foreach($cart as $key => $item)
        <div class="cart-row">
          <div class="col-check"><input type="checkbox" class="item-check" data-key="{{ $key }}"></div>
          <div class="thumb"><img src="{{ asset('images/' . $item['image']) }}" alt="{{ $item['name'] }}"></div>
          <div class="col-name">
            {{ $item['name'] }}
            @if($item['flavor'])
            <div style="font-size:12px;color:#666">Flavor: {{ $item['flavor'] }}</div>
            @endif
          </div>
          <div class="col-unit">₱{{ number_format($item['price'], 2) }}</div>
          <div class="col-qty">
            <div class="qty-control">
              <button onclick="updateQty('{{ $key }}', -1)">−</button>
              <input type="number" value="{{ $item['quantity'] }}" min="1" readonly>
              <button onclick="updateQty('{{ $key }}', 1)">+</button>
            </div>
          </div>
          <div class="col-sub">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
          <button class="remove" onclick="removeItem('{{ $key }}')">Remove</button>
        </div>
        @endforeach
      </div>

      <div class="cart-footer">
        <div><label><input type="checkbox" id="selectAll"> All</label></div>
        <div><button id="checkoutBtn" class="checkout">Check Out ({{ count($cart) }})</button></div>
      </div>
      @else
      <div class="empty">Your cart is empty. <a href="{{ route('shop.index') }}" style="color:var(--gold2)">Start shopping</a></div>
      @endif
    </div>
  </div>
</section>

<!-- Checkout overlay (hidden by default) -->
<div id="checkoutOverlay" class="checkout-overlay" style="display:none">
  <div class="checkout-panel" role="dialog" aria-modal="true" aria-labelledby="checkoutTitle">
    <button class="checkout-close" id="checkoutClose" title="Close">✕</button>
    <div class="checkout-left">
      <h2 id="checkoutTitle">Contact &amp; Payment Information</h2>
      <form id="checkoutForm" method="POST" action="{{ route('orders.store') }}">
        @csrf
        <input type="hidden" name="selected_items" id="checkout-selected-items" value="">
        <label for="contactName">Full Name</label>
        <input id="contactName" name="name" type="text" placeholder="Full Name" value="{{ auth()->user()->name }}" required>
        <label for="contactMobile">Mobile Number</label>
        <input id="contactMobile" name="mobile" type="tel" placeholder="Mobile Number" value="{{ auth()->user()->phone ?? '' }}" required>
        <label for="contactEmail">Email Address</label>
        <input id="contactEmail" name="email" type="email" placeholder="Email Address" value="{{ auth()->user()->email ?? '' }}">

        <div class="payment-method">Payment Method</div>
        <div style="margin-top:8px">
          <label style="font-weight:400"><input type="radio" name="payment_method" value="cod" checked> Cash Upon Pickup</label>
        </div>

        <button type="submit" class="place-order-btn">Place Order</button>
      </form>
    </div>
    <div class="checkout-right">
      <h3>Order Summary</h3>
      <div class="order-list" id="orderList"></div>
      <div class="order-total" id="orderTotal"><div>Total</div><div id="orderTotalPrice">₱ 0.00</div></div>
    </div>
  </div>
</div>

<!-- Custom Popup Modal -->
<div id="customPopup" class="popup-overlay">
  <div class="popup-content">
    <div class="popup-header">
      <h3 id="popupTitle"></h3>
      <button class="popup-close" id="popupClose">&times;</button>
    </div>
    <div class="popup-message" id="popupMessage"></div>
    <button class="popup-button" id="popupButton">OK</button>
  </div>
</div>

<!-- Logout Confirmation Popup -->
<div id="logoutPopup" class="popup-overlay">
  <div class="popup-content">
    <div class="popup-header">
      <h3 id="logoutTitle">Confirm Logout</h3>
      <button class="popup-close" id="logoutClose">&times;</button>
    </div>
    <div class="popup-message" id="logoutMessage">Are you sure you want to log out?</div>
    <div class="popup-buttons">
      <button class="popup-button" id="logoutNoBtn">No</button>
      <button class="popup-button" id="logoutYesBtn" style="background: #ffd54a;">Yes</button>
    </div>
  </div>
</div>

<!-- Notifications Modal -->
<div id="notificationsOverlay" class="notifications-overlay">
  <div class="notifications-panel">
    <div class="notifications-header">
      <h2>Notifications</h2>
      <button class="notifications-close" id="notificationsClose">✕</button>
    </div>
    <div class="notifications-content">
      <ul class="notifications-list" id="notificationsList"></ul>
      <div class="notification-empty" id="notificationsEmpty" style="display:none">
        <i class="fa-solid fa-inbox"></i>
        <p>No messages yet</p>
        <p style="font-size:14px;color:#999;margin-top:8px;">Your notifications will appear here</p>
      </div>
    </div>
    <div style="padding:10px 20px;border-top:1px solid #eee">
      <button class="mark-all-read" id="markAllReadBtn">Mark All as Read</button>
    </div>
  </div>
</div>

<footer class="footer">
  <div class="footer-left">
    <span>0929 222 4309</span>
    <span>Open - Closes 8:30 PM</span>
  </div>
  <div class="footer-right">
    <div class="footer-icon" id="messagesIcon" title="Messages">
      <i class="fa-solid fa-envelope"></i>
      <span class="notification-dot" id="notificationDot"></span>
    </div>
    <div class="footer-icon" title="Profile" onclick="window.location='{{ route('profile.index') }}'">
      <i class="fa-solid fa-user"></i>
    </div>
  </div>
</footer>

<!-- Sidebar -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-title">Menu</div>
    <button class="sidebar-close" id="sidebarClose">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>
  <div class="sidebar-content">
    <ul class="sidebar-menu">
      <li><a href="#"><i class="fa-solid fa-phone"></i> Contact Us</a></li>
      <li><a href="#"><i class="fa-solid fa-star"></i> Review Service</a></li>
      <li><a href="#" id="logoutLink" style="color:#e74c3c"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a></li>
    </ul>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Cart data from server
const cartData = @json($cart);

// Cart interaction functions
function updateQty(itemKey, change) {
  const row = event.target.closest('.cart-row');
  const input = row.querySelector('input[type="number"]');
  const currentQty = parseInt(input.value);
  const newQty = Math.max(1, currentQty + change);
  
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("cart.update") }}';
  
  const csrf = document.createElement('input');
  csrf.type = 'hidden';
  csrf.name = '_token';
  csrf.value = '{{ csrf_token() }}';
  
  const key = document.createElement('input');
  key.type = 'hidden';
  key.name = 'item_key';
  key.value = itemKey;
  
  const qty = document.createElement('input');
  qty.type = 'hidden';
  qty.name = 'quantity';
  qty.value = newQty;
  
  form.appendChild(csrf);
  form.appendChild(key);
  form.appendChild(qty);
  document.body.appendChild(form);
  form.submit();
}

function removeItem(itemKey) {
  if(confirm('Remove this item from cart?')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("cart.remove") }}';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    
    const key = document.createElement('input');
    key.type = 'hidden';
    key.name = 'item_key';
    key.value = itemKey;
    
    form.appendChild(csrf);
    form.appendChild(key);
    document.body.appendChild(form);
    form.submit();
  }
}

// Checkout functionality
const checkoutBtn = document.getElementById('checkoutBtn');
if (checkoutBtn) {
  checkoutBtn.addEventListener('click', function() {
    if (Object.keys(cartData).length === 0) {
      showAlert('Your cart is empty');
      return;
    }
    // Collect selected items (by cart DB id)
    const selectedCheckboxes = Array.from(document.querySelectorAll('.item-check:checked'));
    let selectedIds = null; // null means all
    if (selectedCheckboxes.length > 0) {
      selectedIds = selectedCheckboxes.map(cb => {
        const key = cb.dataset.key;
        const item = cartData[key];
        return item ? item.id : null;
      }).filter(Boolean);
    } else {
      // No selection: ask user whether to proceed with all
      if (!confirm('No items selected — do you want to checkout ALL items in your cart?')) return;
    }
    openCheckoutOverlay(selectedIds);
  });
}

function formatPrice(v) { 
  return `₱${Number(v).toFixed(2)}`; 
}

function openCheckoutOverlay(selectedIds = null) {
  try {
    const overlay = document.getElementById('checkoutOverlay');
    const orderList = document.getElementById('orderList');
    const orderTotalPrice = document.getElementById('orderTotalPrice');
    const selectedInput = document.getElementById('checkout-selected-items');

    orderList.innerHTML = '';
    let total = 0;

    // Determine which items to show
    const items = [];
    if (selectedIds && Array.isArray(selectedIds)) {
      Object.values(cartData).forEach(item => {
        if (selectedIds.indexOf(item.id) !== -1) items.push(item);
      });
    } else {
      // all items
      Object.values(cartData).forEach(item => items.push(item));
    }

    if (items.length === 0) {
      orderList.innerHTML = '<div style="padding:12px;color:#666">No items selected</div>';
    } else {
      items.forEach(item => {
        const row = document.createElement('div');
        row.className = 'order-item';

        const name = document.createElement('div');
        name.textContent = item.name + (item.flavor ? ` (${item.flavor})` : '') + ` x${item.quantity}`;

        const price = document.createElement('div');
        const itemTotal = Number(item.price) * Number(item.quantity);
        price.textContent = formatPrice(itemTotal);

        row.appendChild(name);
        row.appendChild(price);
        orderList.appendChild(row);

        total += itemTotal;
      });
    }

    orderTotalPrice.textContent = formatPrice(total);

    // Set hidden input to selected ids (CSV) or empty for all
    if (selectedIds && Array.isArray(selectedIds)) {
      selectedInput.value = selectedIds.join(',');
    } else {
      selectedInput.value = '';
    }

    // Enable/disable place order button depending on items
    const placeOrderBtn = document.querySelector('.place-order-btn');
    if (placeOrderBtn) {
      placeOrderBtn.disabled = items.length === 0;
      placeOrderBtn.style.opacity = items.length === 0 ? '0.6' : '1';
      placeOrderBtn.style.cursor = items.length === 0 ? 'not-allowed' : 'pointer';
    }

    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    try {
      const nameField = document.getElementById('contactName');
      if (nameField) nameField.focus();
    } catch(e) {}
  } catch(e) {
    console.error('Error in openCheckoutOverlay:', e);
  }
}

function closeCheckoutOverlay() {
  const overlay = document.getElementById('checkoutOverlay');
  if (overlay) {
    overlay.style.display = 'none';
    document.body.style.overflow = 'auto';
  }
}

// Custom Popup Functions
function showCustomPopup(message, title = "") {
  const popup = document.getElementById('customPopup');
  const popupMessage = document.getElementById('popupMessage');
  const popupTitle = document.getElementById('popupTitle');
  
  popupMessage.textContent = message;
  popupTitle.textContent = title;
  
  popup.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function showAlert(message, title = "") {
  showCustomPopup(message, title);
}

document.getElementById('popupClose').addEventListener('click', function() {
  document.getElementById('customPopup').style.display = 'none';
  document.body.style.overflow = 'auto';
});

document.getElementById('popupButton').addEventListener('click', function() {
  document.getElementById('customPopup').style.display = 'none';
  document.body.style.overflow = 'auto';
});

document.getElementById('customPopup').addEventListener('click', function(e) {
  if (e.target === this) {
    this.style.display = 'none';
    document.body.style.overflow = 'auto';
  }
});

// Logout Confirmation Functions
function showLogoutConfirmation() {
  document.getElementById('logoutPopup').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function hideLogoutConfirmation() {
  document.getElementById('logoutPopup').style.display = 'none';
  document.body.style.overflow = 'auto';
}

function handleLogout() {
  window.location.href = '{{ route('logout') }}';
}

document.getElementById('logoutLink').addEventListener('click', function(e) {
  e.preventDefault();
  showLogoutConfirmation();
});

document.getElementById('logoutYesBtn').addEventListener('click', handleLogout);
document.getElementById('logoutNoBtn').addEventListener('click', hideLogoutConfirmation);
document.getElementById('logoutClose').addEventListener('click', hideLogoutConfirmation);

// Checkout overlay controls
document.getElementById('checkoutClose').addEventListener('click', function(e) {
  e.stopPropagation();
  closeCheckoutOverlay();
});

document.getElementById('checkoutOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeCheckoutOverlay();
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const overlay = document.getElementById('checkoutOverlay');
    if (overlay && overlay.style.display !== 'none') {
      closeCheckoutOverlay();
    }
  }
});

// Select all checkbox
const selectAll = document.getElementById('selectAll');
if (selectAll) {
  selectAll.addEventListener('change', function() {
    document.querySelectorAll('.item-check').forEach(cb => {
      cb.checked = selectAll.checked;
    });
  });
}

// If URL contains ?checkout=1&product_id=..., open checkout overlay automatically
(function() {
  try {
    const params = new URLSearchParams(window.location.search);
    if (params.get('checkout') === '1') {
      const productId = params.get('product_id');
      const flavor = params.get('flavor') || '';

      const findSelectedIds = () => {
        const ids = [];
        Object.values(cartData).forEach(item => {
          if (String(item.product_id) === String(productId) && ((item.flavor || '') === flavor)) {
            ids.push(item.id);
          }
        });
        return ids;
      };

      // retry a few times in case cart data hasn't been refreshed yet
      let attempts = 6;
      const tryOpen = () => {
        const selectedIds = findSelectedIds();
        if (selectedIds.length > 0) {
          openCheckoutOverlay(selectedIds);
          // remove query params to avoid reopening on navigation
          if (window.history && window.history.replaceState) {
            const u = new URL(window.location.href);
            u.searchParams.delete('checkout');
            u.searchParams.delete('product_id');
            u.searchParams.delete('flavor');
            window.history.replaceState({}, '', u.toString());
          }
        } else if (attempts-- > 0) {
          setTimeout(tryOpen, 250);
        } else {
          // fallback: open overlay with all items
          openCheckoutOverlay();
        }
      };

      tryOpen();
    }
  } catch (e) { console.warn('Auto-checkout init failed', e); }
})();
</script>
@endpush
