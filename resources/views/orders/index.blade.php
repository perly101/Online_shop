@extends('layouts.customer')

@section('title', 'Order History - Absolute Essential Trading')

@push('styles')
<style>
  .content{padding:20px;max-width:1000px;margin:0 auto;padding-bottom:60px}
  .page-title{font-size:28px;font-weight:700;margin-bottom:25px;color:#111}
  
  .order-card{background:rgba(255,255,255,0.98);border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 4px 12px rgba(0,0,0,0.08);border:2px solid rgba(244,180,60,0.35)}
  .order-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:15px;padding-bottom:15px;border-bottom:2px solid #f0f0f0}
  .order-info{flex:1}
  .order-id{font-size:16px;font-weight:700;margin-bottom:5px}
  .order-date{font-size:14px;color:#666}
  .order-status{padding:6px 14px;border-radius:20px;font-size:14px;font-weight:600;text-align:center;min-width:100px}
  .status-pending{background:#fff3cd;color:#856404}
  .status-confirmed{background:#d1ecf1;color:#0c5460}
  .status-completed{background:#d4edda;color:#155724}
  .status-cancelled{background:#f8d7da;color:#721c24}
  
  .order-items{margin:15px 0}
  .order-item{display:flex;align-items:center;gap:15px;padding:10px 0;border-bottom:1px solid #f5f5f5}
  .order-item:last-child{border-bottom:none}
  .item-image{width:60px;height:60px;border-radius:6px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;overflow:hidden}
  .item-image img{width:100%;height:100%;object-fit:contain}
  .item-details{flex:1}
  .item-name{font-size:16px;font-weight:600;margin-bottom:3px}
  .item-qty{font-size:14px;color:#666}
  .item-price{font-size:16px;font-weight:700;color:#111}
  
  .order-total{display:flex;justify-content:space-between;align-items:center;padding-top:15px;margin-top:15px;border-top:2px solid #f0f0f0}
  .total-label{font-size:18px;font-weight:700}
  .total-amount{font-size:20px;font-weight:700;color:#111}
  
  .order-actions{display:flex;gap:10px;margin-top:15px}
  .btn{padding:10px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;border:none;transition:all 0.2s;text-decoration:none;display:inline-block}
  .btn-view{background:linear-gradient(90deg,var(--gold1),var(--gold2));color:#111}
  .btn-view:hover{transform:translateY(-2px);box-shadow:0 4px 10px rgba(0,0,0,0.15)}
  
  .empty-state{text-align:center;padding:80px 20px;background:rgba(255,255,255,0.98);border-radius:12px;border:2px solid rgba(244,180,60,0.35)}
  .empty-state i{font-size:64px;margin-bottom:20px;color:#ddd}
  .empty-state p{font-size:18px;margin-bottom:15px;color:#666}
  .empty-state a{color:#111;font-weight:600;text-decoration:none;background:linear-gradient(90deg,var(--gold1),var(--gold2));padding:12px 28px;border-radius:8px;display:inline-block;margin-top:10px}
  .empty-state a:hover{transform:translateY(-2px);box-shadow:0 4px 10px rgba(0,0,0,0.15)}
</style>
@endpush

@section('content')
<div class="content">
  <h1 class="page-title">Order History</h1>
  
  @forelse($orders as $order)
    <div class="order-card">
      <div class="order-header">
        <div class="order-info">
          <div class="order-id">Order #{{ $order->order_number ?? $order->id }}</div>
          <div class="order-date" data-ts="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->format('M d, Y h:i A') }}</div>
        </div>
        <div class="order-status status-{{ strtolower($order->status) }}">
          {{ ucfirst($order->status) }}
        </div>
      </div>
      
      <div class="order-items">
        @foreach($order->items as $item)
          <div class="order-item">
            <div class="item-image">
              @if($item->product)
                <img src="{{ asset('images/' . $item->product->image) }}" alt="{{ $item->product_name }}">
              @else
                <img src="{{ asset('images/pick up.jpg') }}" alt="{{ $item->product_name }}">
              @endif
            </div>
            <div class="item-details">
              <div class="item-name">{{ $item->product_name }}</div>
              @if($item->flavor)
              <div class="item-qty">Flavor: {{ $item->flavor }} | Qty: {{ $item->quantity }}</div>
              @else
              <div class="item-qty">Qty: {{ $item->quantity }}</div>
              @endif
            </div>
            <div class="item-price">₱{{ number_format($item->price * $item->quantity, 2) }}</div>
          </div>
        @endforeach
      </div>
      
      <div class="order-total">
        <div class="total-label">Total</div>
        <div class="total-amount">₱{{ number_format($order->total_amount, 2) }}</div>
      </div>
      
      <div class="order-actions">
        <a href="{{ route('orders.show', $order) }}" class="btn btn-view">View Details</a>
        <button class="btn btn-delete" onclick="deleteOrder({{ $order->id }}, this)">Delete</button>
      </div>
    </div>
  @empty
    <div class="empty-state">
      <i class="fa-solid fa-box-open"></i>
      <p>You haven't placed any orders yet</p>
      <a href="{{ route('shop.index') }}">Start Shopping</a>
    </div>
  @endforelse
</div>
@push('scripts')
<script>
function deleteOrder(orderId, btn) {
  if (!confirm('Are you sure you want to delete this order?')) return;
  fetch(`/orders/${orderId}/delete`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Remove the order card from the DOM
      btn.closest('.order-card').remove();
    } else {
      alert(data.message || 'Failed to delete order.');
    }
  })
  .catch(() => alert('Failed to delete order.'));
}

// Format server timestamps to the user's local timezone
function formatLocalTimes() {
  document.querySelectorAll('.order-date[data-ts]').forEach(el => {
    const ts = el.getAttribute('data-ts');
    if (!ts) return;
    const d = new Date(ts);
    if (isNaN(d)) return;
    const datePart = d.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' });
    const timePart = d.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit', hour12: true });
    el.textContent = `${datePart} ${timePart}`;
  });
}

document.addEventListener('DOMContentLoaded', function(){ formatLocalTimes(); });

</script>
@endpush
@endsection
