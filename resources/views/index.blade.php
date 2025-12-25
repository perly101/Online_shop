@extends('layouts.app')

@section('title', 'Absolute Essential Trading - Online Ordering and Pick-up System')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/index-styles.css') }}">
@endpush

@section('content')
<div class="main-container">
    <div class="marketing-section">
        <!-- LEFT: White section with building and overlay text -->
        <div class="left-section exact-bg">
            <!-- Logo background image -->
            <div class="logo-background">
                <img src="{{ asset('images/logo of abs.jpg') }}" alt="Absolute Essential Trading Logo" class="logo-bg-img">
            </div>
            <div class="centered-overlay-text">
                <h1 class="company-name">ABSOLUTE ESSENTIAL TRADING</h1>
                <span class="for-text">FOR</span>
                <h2 class="service-title">ONLINE ORDERING AND PICK-UP SYSTEM</h2>
                <div class="sub-description">Absolute Essentials Trading is located in Surigao. Absolute Essentials Trading is working in Shopping, All food and beverage, Grocery store activities.</div>
                <div class="central-link"><a href="https://www.cybo.com/PH-biz/absolute-essentials-trading" target="_blank">https://www.cybo.com/PH-biz/absolute-essentials-trading</a></div>
            </div>
        </div>
        <!-- RIGHT: Yellow with cart, image, button, info -->
        <div class="right-section">
            <div class="cart-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="banner-image">
                <img src="{{ asset('images/pick up.jpg') }}" alt="Pick up in store" class="store-image" />
            </div>
            <a href="{{ route('user-type') }}" class="shop-now-btn">Shop Now</a>
            <div class="store-details">
                <h3>Absolute Essentials Trading</h3>
                <p>3.7(47) &nbsp;· Supermarket</p>
                <p>Surigao, Surigao del Norte</p>
                <p>0929 222 4308</p>
                <p class="hours">Open · Closes 6:30 PM</p>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal (copied and adapted from shop-home.html) -->
<div id="productModal" class="product-modal" aria-hidden="true" style="display:none">
        <div class="backdrop" id="productModalBackdrop"></div>
        <div class="modal-card" role="dialog" aria-modal="true">
            <div class="modal-image-col" id="modalImages"></div>
            <div class="modal-details">
                <div class="title" id="modalTitle">Product name</div>
                <div class="price" id="modalPrice">₱ 0.00</div>
                <!-- Flavors section (only shown for products with variants) -->
                <div id="flavorsSection" class="flavors-section" style="display:none">
                    <span class="flavors-label">Flavors :</span>
                    <div id="flavorsGrid" class="flavors-grid"></div>
                </div>
                <div style="margin-top:8px">Quantity <div class="qty-inline"><button id="modalQtyMinus">-</button><input id="modalQty" type="number" value="1" min="1"><button id="modalQtyPlus">+</button></div></div>
                <div class="modal-actions">
                    <button id="modalAddToCart" class="btn">Add To Cart</button>
                    <button id="modalBuyNow" class="btn secondary">Buy Now</button>
                </div>
                <div class="description" id="modalDesc" style="margin-top:12px;text-align:left">Product description</div>
                <a href="#" id="modalFindSimilar" class="find-similar-link">Find similar ▾</a>
            </div>
            <div id="modalSimilar" class="similar-section" style="display:none;margin-top:18px">
                <div id="modalSimilarGrid" class="similar-grid"></div>
            </div>
        </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('data/products.js') }}"></script>
<script>
// Product modal logic (adapted from shop-home.html)
function openProductModal(id, selectedVariant = null){
    const products = window.PRODUCTS || [];
    const p = products.find(x => Number(x.id) === Number(id)) || products[0];
    if(!p) return;
    let currentVariant = selectedVariant;
    let currentImage = p.image;
    const imagesCol = document.getElementById('modalImages');
    imagesCol.innerHTML = '';
    function updateMainImage(imageSrc) {
        currentImage = imageSrc;
        imagesCol.innerHTML = '';
        const wrapper = document.createElement('div'); wrapper.className='thumb-box';
        const t = document.createElement('img');
        let productImageUrl = imageSrc || 'pick up.jpg';
        if (productImageUrl.startsWith('data:')) {
                t.src = productImageUrl;
        } else {
                t.src = 'images/' + encodeURIComponent(productImageUrl);
        }
        t.alt = p.name;
        t.onerror = ()=> { t.src = 'images/pick%20up.jpg'; };
        wrapper.appendChild(t);
        imagesCol.appendChild(wrapper);
    }
    // Always show the flavor selector if variants exist
    const flavorsSection = document.getElementById('flavorsSection');
    const flavorsGrid = document.getElementById('flavorsGrid');
    if(Array.isArray(p.variants) && p.variants.length > 0) {
            flavorsSection.style.display = 'block';
            flavorsGrid.innerHTML = '';
            p.variants.forEach((variant, index) => {
                const flavorItem = document.createElement('div');
                flavorItem.className = 'flavor-item';
                if(index === 0 && !currentVariant) {
                    flavorItem.classList.add('selected');
                    currentVariant = variant;
                    updateMainImage(variant.image || p.image);
                } else if(currentVariant && currentVariant.flavor === variant.flavor) {
                    flavorItem.classList.add('selected');
                }
                const flavorImg = document.createElement('img');
                let flavorImageUrl = variant.image || p.image || 'pick up.jpg';
                if (flavorImageUrl.startsWith('data:')) {
                    flavorImg.src = flavorImageUrl;
                } else {
                    flavorImg.src = 'images/' + encodeURIComponent(flavorImageUrl);
                }
                flavorImg.alt = variant.flavor || 'Flavor';
                flavorImg.onerror = function() { flavorImg.src = 'images/pick%20up.jpg'; };
                flavorItem.appendChild(flavorImg);
                // Style for proper layout and highlight
                flavorItem.style.display = 'flex';
                flavorItem.style.alignItems = 'center';
                flavorItem.style.justifyContent = 'center';
                flavorItem.style.boxSizing = 'border-box';
                flavorImg.style.width = '100%';
                flavorImg.style.height = '100%';
                flavorImg.style.objectFit = 'contain';
                flavorItem.addEventListener('click', function(){
                    document.querySelectorAll('.flavor-item').forEach(item => item.classList.remove('selected'));
                    flavorItem.classList.add('selected');
                    currentVariant = variant;
                    updateMainImage(variant.image || p.image);
                    document.getElementById('modalPrice').textContent = `₱ ${Number(variant.price || p.price).toFixed(2)}`;
                });
                flavorsGrid.appendChild(flavorItem);
            });
            // Set price for first variant or selected
            const priceToShow = currentVariant ? (currentVariant.price || p.price) : p.price;
            document.getElementById('modalPrice').textContent = `₱ ${Number(priceToShow).toFixed(2)}`;
            updateMainImage(currentVariant && currentVariant.image ? currentVariant.image : p.variants[0].image || p.image);
    } else {
            flavorsSection.style.display = 'none';
            updateMainImage(p.image);
            document.getElementById('modalPrice').textContent = `₱ ${Number(p.price).toFixed(2)}`;
    }
    document.getElementById('modalTitle').textContent = p.name;
    document.getElementById('modalDesc').textContent = p.description || '';
    document.getElementById('modalQty').value = 1;
    document.getElementById('modalAddToCart').onclick = function(){
        const qty = Math.max(1, Number(document.getElementById('modalQty').value)||1);
        const cartRaw = localStorage.getItem('cart'); const cart = cartRaw ? JSON.parse(cartRaw) : [];
        const cartItem = {
            id: p.id, 
            name: p.name, 
            price: currentVariant ? (currentVariant.price || p.price) : p.price, 
            image: currentVariant && currentVariant.image ? currentVariant.image : currentImage, 
            qty: qty
        };
        if(currentVariant) {
            cartItem.flavor = currentVariant.flavor;
            cartItem.name = `${p.name} (${currentVariant.flavor})`;
        }
        const existing = cart.find(i=> i.id===p.id && (!currentVariant || i.flavor === currentVariant.flavor));
        if(existing){ 
            existing.qty = Number(existing.qty) + qty;
        } else { 
            cart.push(cartItem);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        closeProductModal();
        window.location.href = 'cart.html';
    };
    document.getElementById('modalBuyNow').onclick = function(e){
        try{ e.stopPropagation(); e.preventDefault(); }catch(_){}
        const qty = Math.max(1, Number(document.getElementById('modalQty').value)||1);
        const cartItem = {
            id: p.id,
            name: p.name,
            price: currentVariant ? (currentVariant.price || p.price) : p.price,
            image: currentVariant && currentVariant.image ? currentVariant.image : currentImage,
            qty: qty
        };
        if(currentVariant) {
            cartItem.flavor = currentVariant.flavor;
            cartItem.name = `${p.name} (${currentVariant.flavor})`;
        }
        const cart = [cartItem];
        localStorage.setItem('cart', JSON.stringify(cart));
        closeProductModal();
        window.location.href = 'cart.html';
    };
    document.getElementById('modalQtyPlus').onclick = ()=>{ const el = document.getElementById('modalQty'); el.value = Number(el.value||1)+1 };
    document.getElementById('modalQtyMinus').onclick = ()=>{ const el = document.getElementById('modalQty'); el.value = Math.max(1, Number(el.value||1)-1) };
    document.getElementById('modalFindSimilar').onclick = function(ev){
        ev.preventDefault();
        const s = document.getElementById('modalSimilar');
        if (s && s.style.display && s.style.display !== 'none'){
            s.style.display = 'none';
        } else {
            s.style.display = 'block';
            document.getElementById('modalSimilarGrid').innerHTML = '<div style="padding:20px">Similar products coming soon...</div>';
        }
    };
    // Show modal
    const modal = document.getElementById('productModal');
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
    document.getElementById('productModalBackdrop').onclick = closeProductModal;
    document.addEventListener('keydown', modalEscHandler);
}
function closeProductModal(){
    const modal = document.getElementById('productModal'); if(!modal) return;
    modal.style.display = 'none'; modal.setAttribute('aria-hidden','true'); document.body.style.overflow = '';
    document.removeEventListener('keydown', modalEscHandler);
    const s = document.getElementById('modalSimilar'); if(s) s.style.display='none';
}
function modalEscHandler(e){ if(e.key==='Escape'){ closeProductModal(); } }
// Example: openProductModal(3) to open Piattos modal
</script>
<script>
        // make the cart icon clickable on index page
        (function(){
                document.querySelectorAll('.cart-icon, .fa-cart-shopping').forEach(function(el){
                        const node = el.classList && el.classList.contains('fa-cart-shopping') ? (el.closest('button') || el.closest('.cart-icon') || el) : el;
                        if (node){ node.style.cursor = 'pointer'; node.addEventListener('click', function(){ window.location.href = '{{ route('cart.index') }}'; }); }
                });
        })();
</script>
@endpush
