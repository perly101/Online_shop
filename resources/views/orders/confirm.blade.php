@extends('layouts.customer')

@section('title', 'Order Confirmed - Absolute Essential Trading')

@push('styles')
<style>
  .hero{padding:40px 20px 80px}
  .confirm-card{background:rgba(255,255,255,0.98);border-radius:12px;padding:40px 36px;border:2px solid rgba(244,180,60,0.35);box-shadow:0 20px 40px rgba(0,0,0,0.06);max-width:800px;margin:0 auto}
  
  .confirm-title{font-size:42px;font-weight:800;margin:0 0 12px;text-align:center}
  .thank-you{font-size:16px;color:#333;text-align:center;margin-bottom:24px}
  
  .checkmark-wrapper{display:flex;justify-content:center;margin:24px 0 32px}
  .checkmark-circle{width:120px;height:120px;border-radius:50%;background:#ff6b35;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 20px rgba(255,107,53,0.25)}
  .checkmark-circle img{width:70px;height:70px;object-fit:contain}
  
  .order-status{text-align:center;margin:24px 0;font-weight:700;font-size:20px}
  .status-notification{font-size:15px;color:#444;text-align:center;margin:16px 0 32px;line-height:1.6;max-width:600px;margin-left:auto;margin-right:auto}
  
  .order-details-section{margin-top:32px}
  .order-details-title{font-size:24px;font-weight:700;margin-bottom:18px}
  .detail-list{list-style:none;padding:0;margin:0}
  .detail-item{margin:12px 0;font-size:16px;line-height:1.6}
  .detail-label{font-weight:700;display:inline-block;min-width:160px}
  
  .confirmation-code-wrapper{margin:12px 0;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
  .confirmation-code-image{width:150px;height:150px;border:2px solid #ddd;border-radius:8px;padding:8px;background:#fff;display:flex;align-items:center;justify-content:center}
  .confirmation-code-image img{width:100%;height:100%;object-fit:contain}
  #qrcode{display:flex;align-items:center;justify-content:center}
  #qrcode img{border-radius:4px}
  
  .items-list{margin:12px 0;padding-left:20px}
  .items-list li{margin:6px 0}

  @media (max-width:768px){
    .confirm-card{padding:28px 20px}
    .confirm-title{font-size:32px}
    .detail-label{min-width:140px;font-size:14px}
  }
</style>
@endpush

@section('content')
<div class="hero">
  <div class="confirm-card">
    <h1 class="confirm-title">Order Confirmed!</h1>
    <p class="thank-you">Thank you for your order! Your payment has been successfully processed.</p>
    
    <div class="checkmark-wrapper">
      <div class="checkmark-circle">
        <img src="{{ asset('images/correct.png') }}" alt="Checkmark" onerror="this.style.display='none'">
      </div>
    </div>
    
    <div class="order-status">Your Order Status: {{ ucfirst($order->status) }}</div>
    <p class="status-notification">
      We will send you a separate SMS and Email notification when your order is packed and ready for pick-up. 
      Please do not proceed to the store until you receive the 'Ready for Pick-Up' notification.
    </p>
    
    <div class="order-details-section">
      <h2 class="order-details-title">Order Details</h2>
      <ul class="detail-list">
        <li class="detail-item">
          <span class="detail-label">Order Number:</span>
          <span id="orderIdDisplay">{{ $order->order_number }}</span>
        </li>
        <li class="detail-item">
          <span class="detail-label">Pick-Up Location:</span>
          <span id="pickupLocation">Alesandra Building, Rizal Corner Amat Streets, Surigao City</span>
        </li>
        <li class="detail-item">
          <span class="detail-label">Confirmation Code:</span>
          <div class="confirmation-code-wrapper">
            <div class="confirmation-code-image" id="qrcode">
              <!-- QR code will be generated here -->
            </div>
          </div>
        </li>
        <li class="detail-item">
          <span class="detail-label">Order Total:</span>
          <span id="orderTotalDisplay">₱{{ number_format($order->total_amount, 2) }}</span>
        </li>
        <li class="detail-item">
          <span class="detail-label">Items:</span>
          <ul class="items-list">
            @foreach($order->items as $item)
              <li>{{ $item->product_name }} @if($item->flavor)({{ $item->flavor }})@endif × {{ $item->quantity }} — ₱{{ number_format($item->price * $item->quantity, 2) }}</li>
            @endforeach
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
window.addEventListener('load', function() {
  const qrcodeContainer = document.getElementById('qrcode');
  const qrData = '{{ $order->pickup_qr_code ?? "" }}';
  
  if (!qrcodeContainer) {
    console.error('QR code container not found');
    return;
  }
  
  if (!qrData) {
    console.error('QR code data is empty');
    qrcodeContainer.innerHTML = '<p style="font-size:12px;color:#e74c3c;">QR code data missing</p>';
    return;
  }
  
  console.log('Generating QR code with data:', qrData.substring(0, 30) + '...');
  
  if (typeof QRCode === 'undefined') {
    console.error('QRCode library failed to load from CDN');
    qrcodeContainer.innerHTML = '<p style="font-size:12px;color:#e74c3c;">Failed to load QR library</p>';
    return;
  }
  
  try {
    qrcodeContainer.innerHTML = '';
    new QRCode(qrcodeContainer, {
      text: qrData,
      width: 128,
      height: 128,
      colorDark: "#000000",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });
    console.log('✓ QR code generated successfully');
  } catch(e) {
    console.error('QR code generation error:', e);
    qrcodeContainer.innerHTML = '<p style="font-size:12px;color:#e74c3c;">Error: ' + e.message + '</p>';
  }
});
</script>
@endpush
