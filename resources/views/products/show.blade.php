@extends('layouts.customer')


@section('title', $product->name . ' - Absolute Essential Trading')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/index-styles.css') }}">
<style>
:root{--gold1:#ffd54a;--gold2:#f0ad06}
body{font-family:Georgia, 'Times New Roman', serif;margin:0;color:#111;background-image:url('{{ asset("images/Backgound.png") }}');background-size:cover;background-position:center;background-repeat:no-repeat;overflow-x:hidden}
/* (keep the rest of your CSS as before) */
</style>
@endpush


@section('content')


<div class="container">
<div class="card">
<div class="product-image">
<img src="{{ asset('images/' . ($product->image ?? 'pick%20up.jpg')) }}" alt="{{ $product->name }}" id="main-image">
</div>
<div class="details">
<a href="#" class="find-similar" onclick="showSimilar(event)">Find similar ▼</a>


<div class="title">{{ $product->name }}</div>
<div class="price" id="product-price">{{ number_format($product->price, 2) }}</div>


@if($product->variants->count())
<div class="flavors-section" style="margin-bottom:18px;">
<span class="flavors-label">Flavors :</span>
<div class="flavors-grid">
@foreach($product->variants as $i => $variant)
<div class="flavor-item{{ $i === 0 ? ' selected' : '' }}" tabindex="0"
data-flavor="{{ $variant->flavor }}"
data-price="{{ $variant->price ?? $product->price }}"
data-stock="{{ $variant->stock ?? $product->stock }}"
data-image="{{ $variant->image ?? $product->image }}">
<img src="{{ asset('images/' . ($variant->image ?? $product->image)) }}" alt="{{ $variant->flavor }}">
</div>
@endforeach
</div>
</div>
@endif


<form method="POST" action="{{ route('cart.add') }}" id="add-to-cart-form">
@csrf
<input type="hidden" name="product_id" value="{{ $product->id }}">
<input type="hidden" name="product_name" value="{{ $product->name }}">
<input type="hidden" name="price" id="input-price" value="{{ $product->price }}">
<input type="hidden" name="image" id="input-image" value="{{ $product->image }}">
<input type="hidden" name="flavor" id="selected-flavor" value="{{ $product->variants->first()->flavor ?? '' }}">


<div class="qty-section">
<label class="qty-label">Quantity</label>
<div class="qty">
<button type="button" onclick="changeQty(-1)">-</button>
<input type="number" name="quantity" id="qty-input" value="1" min="1" max="{{ $product->stock }}" readonly>
<button type="button" onclick="changeQty(1)">+</button>
</div>
</div>


<div class="actions">
<button type="submit" class="btn btn-add">Add To Cart</button>
<button type="button" class="btn btn-buy" onclick="buyNow()">Buy Now</button>
</div>
</form>


<div class="description">{{ $product->description }}</div>
</div>
</div>
<div class="similar-section" id="similar-section" style="display:none">
<div class="similar-title">Similar Products</div>
<div class="similar-grid" id="similar-grid">
<!-- Similar products will be loaded here -->
</div>
</div>
</div>
@endsection


@push('scripts')
@endpush