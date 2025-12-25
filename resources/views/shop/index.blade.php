@extends('layouts.customer')

@section('title', 'Shop - Absolute Essential Trading')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/index-styles.css') }}">
<style>
    :root{--gold1:#ffd54a;--gold2:#f0ad06}
    html,body{height:100%;margin:0;font-family:Georgia, 'Times New Roman', serif;color:#111;overflow:hidden}
    body{background:#fff;background-image:url('{{ asset("images/Backgound.png") }}');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed}
    
    .topbar{display:flex;align-items:center;justify-content:space-between;padding:20px 26px;padding-right:30px;background:linear-gradient(90deg,var(--gold1),var(--gold2));box-shadow:0 2px 6px rgba(0,0,0,0.08);position:fixed;top:0;left:0;right:0;width:100%;z-index:1000;box-sizing:border-box}
    .brand{font-size:20px;font-weight:700}
    .icons{display:flex;gap:12px;align-items:center}
    .icon-btn{width:40px;height:40px;border-radius:6px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.15);color:#000;cursor:pointer;border:none;padding:0;transition:background 0.2s}
    .icon-btn:hover{background:rgba(0,0,0,0.25)}
    .icon-btn i{font-size:18px}
    
    .hero{position:fixed;top:72px;left:0;right:0;bottom:0;padding:0;background:none;display:flex;flex-direction:column;overflow-y:auto;overflow-x:hidden;}
    .hero::-webkit-scrollbar{display:none}
    .hero{-ms-overflow-style:none;scrollbar-width:none}
    
    .search-wrap{position:sticky;top:0;z-index:3;width:100%;max-width:820px;margin:0 auto;padding:20px 28px;box-sizing:border-box}
    .search{display:flex;align-items:center;background:#fff;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,0.12);padding:12px 16px}
    .search input{flex:1;border:0;outline:none;font-size:18px;padding:8px}
    .search button{background:transparent;border:0;font-size:20px;padding:6px;cursor:pointer}
    
    .hero-content{padding:0 18px 80px;z-index:2;color:#111;max-width:1100px;margin:0 auto;width:100%;padding-top:20px}
    .section-title{font-family: Georgia, serif;font-size:20px;margin-bottom:14px}
    .grid{display:grid;grid-template-columns:repeat(6,1fr);gap:18px}
    .card{background:linear-gradient(180deg,#fff,#fff);border-radius:14px;padding:14px;text-align:center;border:1px solid rgba(0,0,0,0.04);box-shadow:0 12px 30px rgba(0,0,0,0.12);cursor:pointer}
    .card img{width:100%;height:88px;object-fit:contain;border-radius:8px}
    .card .title{font-size:13px;margin-top:8px}
    .price{font-weight:700;margin-top:8px}
    
    /* Product Modal */
    .product-modal{position:fixed;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(8px);display:none;align-items:center;justify-content:center;z-index:10000;padding:20px}
    .product-modal.active{display:flex}
    .modal-backdrop{position:absolute;inset:0}
    .modal-card{position:relative;background:#fff;border-radius:16px;box-shadow:0 30px 60px rgba(0,0,0,0.3);max-width:900px;width:100%;max-height:90vh;overflow-y:auto;z-index:1;border:4px solid rgba(240,173,6,0.5);display:grid;grid-template-columns:280px 1fr;gap:30px;padding:40px}
    .modal-close{position:absolute;top:16px;right:16px;background:#f5f5f5;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:20px;display:flex;align-items:center;justify-content:center;transition:background 0.2s;z-index:10}
    .modal-close:hover{background:#e0e0e0}
    .modal-image{grid-column:1}
    .modal-image img{width:100%;height:280px;object-fit:contain;border-radius:12px}
    .modal-details{grid-column:2;position:relative}
    /* hide old inline link; use the button in modal actions instead */
    .find-similar-link{display:none}
    .btn-similar{background:#fff;border:2px solid #333;color:#333;padding:12px 18px;border-radius:30px;cursor:pointer;font-weight:700;font-size:15px;transition:all 0.2s;margin-left:8px}
    .btn-similar:hover{background:#f5f5f5;transform:translateY(-1px)}
    .modal-title{font-size:32px;font-weight:700;margin:0 0 12px;line-height:1.2}
    .modal-price{font-size:24px;font-weight:400;margin-bottom:20px}
    .modal-price::before{content:'₱ '}
    .modal-description{color:#666;line-height:1.6;margin-bottom:20px;font-size:15px}
    .flavors-section{margin:20px 0}
    .flavors-label{font-size:14px;font-weight:700;margin-bottom:8px;display:block}
    .flavors-grid{display:flex;gap:10px;flex-wrap:wrap}
    .flavor-item{width:70px;height:70px;border:2px solid #ddd;border-radius:8px;overflow:hidden;cursor:pointer;transition:all 0.2s;background:#fff}
    .flavor-item:hover{border-color:var(--gold2);transform:scale(1.05)}
    .flavor-item.selected{border-color:var(--gold2);border-width:3px;box-shadow:0 4px 12px rgba(240,173,6,0.3)}
    .flavor-item img{width:100%;height:100%;object-fit:cover}
    .qty-section{margin:20px 0}
    .qty-label{font-size:14px;color:#666;margin-bottom:8px;display:block}
    .qty-inline{display:flex;align-items:center;gap:8px}
    .qty-inline button{width:32px;height:32px;padding:0;border-radius:8px;border:1px solid #ddd;background:#fff;cursor:pointer;font-size:16px;transition:background 0.2s}
    .qty-inline button:hover{background:#f5f5f5}
    .qty-inline input{width:60px;text-align:center;border-radius:8px;border:1px solid #e6e6e6;padding:8px;font-size:16px;outline:none}
    .modal-actions{display:flex;gap:12px;margin-top:24px}
    .btn{border:none;padding:14px 28px;border-radius:30px;cursor:pointer;font-weight:700;font-size:15px;transition:all 0.2s}
    .btn-add{background:#FFD700;color:#000}
    .btn-add:hover{background:#FFC107;transform:translateY(-1px)}
    .btn-buy{background:#fff;border:2px solid #333;color:#333}
    .btn-buy:hover{background:#f5f5f5}
    .similar-section{grid-column:1/-1;display:none;padding-top:20px;border-top:1px solid #eee;margin-top:20px}
    .similar-title{font-size:18px;font-weight:700;margin-bottom:15px}
    .similar-grid{display:flex;gap:14px;align-items:flex-start;overflow-x:auto}
    .similar-card{background:#fff;border-radius:8px;padding:10px 12px;text-align:center;border:1px solid rgba(0,0,0,0.04);min-width:110px;box-shadow:0 6px 18px rgba(0,0,0,0.04);cursor:pointer;transition:all 0.2s}
    .similar-card:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,0.12)}
    .similar-card img{width:64px;height:64px;object-fit:contain;margin:0 auto 8px}
    .similar-card .s-title{font-size:13px;margin-bottom:6px}
    .similar-card .s-price{font-size:12px;font-weight:700}
</style>
@endpush

@section('content')

<div class="hero">
    <div class="search-wrap">
        <div class="search">
            <input type="text" id="search-input" placeholder="Search products...">
            <button><i class="fas fa-search"></i></button>
        </div>
    </div>
    
    <div class="hero-content">
        <h2 class="section-title">All Products</h2>
        <div class="grid" id="products-grid">
            @foreach($products as $product)
                <div class="card" onclick="openProductModal({{ $product->id }})">
                    <img data-image="{{ $product->image }}" src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}">
                    <div class="title">{{ $product->name }}</div>
                    <div class="price">₱{{ number_format($product->price, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Product Modal -->
<div id="productModal" class="product-modal">
    <div class="modal-backdrop" onclick="closeProductModal()"></div>
    <div class="modal-card">
        <button class="modal-close" onclick="closeProductModal()">&times;</button>
        <div class="modal-image">
            <img id="modal-product-image" src="" alt="">
        </div>
        <div class="modal-details">
            <h2 class="modal-title" id="modal-product-name"></h2>
            <div class="modal-price" id="modal-product-price"></div>
            <div class="modal-description" id="modal-product-description"></div>

            <!-- Flavors / Variants -->
            <div id="modal-flavors-section" class="flavors-section" style="display:none">
                <span class="flavors-label">Flavors :</span>
                <div id="modal-flavors-grid" class="flavors-grid"></div>
            </div>
            
            <form method="POST" action="{{ route('cart.add') }}" id="modal-cart-form">
                @csrf
                <input type="hidden" name="product_id" id="modal-product-id">
                <input type="hidden" name="product_name" id="modal-product-name-input">
                <input type="hidden" name="price" id="modal-product-price-input">
                <input type="hidden" name="image" id="modal-product-image-input">
                <input type="hidden" name="flavor" id="modal-selected-flavor">
                
                <div class="qty-section">
                    <label class="qty-label">Quantity</label>
                    <div class="qty-inline">
                        <button type="button" onclick="changeModalQty(-1)">-</button>
                        <input type="number" name="quantity" id="modal-qty-input" value="1" min="1" max="999" readonly>
                        <button type="button" onclick="changeModalQty(1)">+</button>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button type="submit" class="btn btn-add">Add To Cart</button>
                    <button type="button" class="btn btn-buy" onclick="buyNowModal()">Buy Now</button>
                    <button type="button" class="btn btn-similar" onclick="toggleSimilar(event)">Find similar</button>
                </div>
            </form>
        </div>
        
        <div class="similar-section" id="modal-similar-section">
            <div class="similar-title">Similar Products</div>
            <div class="similar-grid" id="modal-similar-grid"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentProductStock = 999;
    const productsData = @json($products);
    
    // Search functionality
    document.getElementById('search-input').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.card');
        
        cards.forEach(card => {
            const title = card.querySelector('.title').textContent.toLowerCase();
            card.style.display = title.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Helper to resolve image paths correctly (handles data:, absolute URLs, 'images/' paths, and filenames)
    function resolveImagePath(imgVal, fallback) {
        const defaultFallback = "{{ asset('images/pick up.jpg') }}";
        let base = "{{ asset('images') }}"; // may or may not include trailing slash
        let siteBase = "{{ url('/') }}"; // site origin
        if (!base.endsWith('/')) base = base + '/';
        if (siteBase.endsWith('/')) siteBase = siteBase.slice(0, -1);
        if (!imgVal) return fallback ? resolveImagePath(fallback) : defaultFallback;
        imgVal = String(imgVal).trim();
        // If data URL or absolute URL or starts with slash, return as-is
        if (imgVal.startsWith('data:') || imgVal.startsWith('http://') || imgVal.startsWith('https://') || imgVal.startsWith('/')) {
            return imgVal;
        }
        // If the value already contains 'images/', make it absolute relative to site root
        if (imgVal.indexOf('images/') !== -1) {
            const normalized = imgVal.replace(/^\/+/, '');
            return siteBase + '/' + normalized;
        }
        // Otherwise it's a filename or relative path; use base + encodeURI to preserve slashes
        return base + encodeURI(imgVal);
    }

    // Open product modal
    function openProductModal(productId) {
        const product = productsData.find(p => p.id === productId);
        if (!product) return;

        currentProductStock = product.stock || 999;

        document.getElementById('modal-product-image').src = resolveImagePath(product.image);
        document.getElementById('modal-product-name').textContent = product.name;
        document.getElementById('modal-product-price').textContent = parseFloat(product.price).toFixed(2);
        document.getElementById('modal-product-description').textContent = product.description || '';

        document.getElementById('modal-product-id').value = product.id;
        document.getElementById('modal-product-name-input').value = product.name;
        document.getElementById('modal-product-price-input').value = product.price;
        document.getElementById('modal-product-image-input').value = product.image;
        document.getElementById('modal-selected-flavor').value = '';
        document.getElementById('modal-qty-input').value = 1;
        document.getElementById('modal-qty-input').max = currentProductStock;

        // Render flavors/variants if available
        const flavorsSection = document.getElementById('modal-flavors-section');
        const flavorsGrid = document.getElementById('modal-flavors-grid');
        if (product.variants && Array.isArray(product.variants) && product.variants.length > 0) {
            console.debug('product has variants', product.id, product.variants);
            flavorsSection.style.display = 'block';
            flavorsGrid.innerHTML = '';
            product.variants.forEach((v, idx) => {
                const item = document.createElement('div');
                item.className = 'flavor-item' + (idx === 0 ? ' selected' : '');
                const img = document.createElement('img');
                const imgSrc = v.image ? resolveImagePath(v.image, product.image) : resolveImagePath(product.image);
                console.debug('variant', idx, 'image raw=', v.image, 'resolved=', imgSrc);
                img.src = imgSrc;
                img.alt = v.flavor || 'Flavor';
                img.onerror = function() { console.warn('Flavor image failed for', v.image, 'using fallback'); this.src = resolveImagePath(null); };
                item.appendChild(img);
                item.addEventListener('click', function(){
                    // select
                    document.querySelectorAll('#modal-flavors-grid .flavor-item').forEach(el => el.classList.remove('selected'));
                    item.classList.add('selected');
                    document.getElementById('modal-selected-flavor').value = v.flavor || '';
                    // update price and image
                    const price = v.price || product.price;
                    document.getElementById('modal-product-price').textContent = Number(price).toFixed(2);
                    document.getElementById('modal-product-price-input').value = price;
                    const imgPath = resolveImagePath(v.image, product.image);
                    console.debug('setting main image to', imgPath);
                    document.getElementById('modal-product-image').src = imgPath;
                    document.getElementById('modal-product-image-input').value = v.image || product.image;
                });
                flavorsGrid.appendChild(item);
            });
            // select default first
            const first = product.variants[0];
            document.getElementById('modal-selected-flavor').value = first.flavor || '';
            if (first.price) {
                document.getElementById('modal-product-price').textContent = Number(first.price).toFixed(2);
                document.getElementById('modal-product-price-input').value = first.price;
            }
            if (first.image) {
                const firstImg = resolveImagePath(first.image, product.image);
                console.debug('default variant image resolved:', first.image, '->', firstImg);
                document.getElementById('modal-product-image').src = firstImg;
                document.getElementById('modal-product-image-input').value = first.image;
            }
        } else {
            flavorsSection.style.display = 'none';
            document.getElementById('modal-selected-flavor').value = '';
        }

        document.getElementById('modal-similar-section').style.display = 'none';
        document.getElementById('productModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    // Close product modal
    function closeProductModal() {
        document.getElementById('productModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    
    // Change quantity in modal
    function changeModalQty(delta) {
        const input = document.getElementById('modal-qty-input');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > currentProductStock) val = currentProductStock;
        input.value = val;
    }
    
    // Buy now from modal — add item via AJAX then go to cart and open checkout
    function buyNowModal() {
        const form = document.getElementById('modal-cart-form');
        const formData = new FormData(form);
        // Send AJAX POST to add to cart
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        }).then(response => {
            // Redirect to cart and request checkout overlay for this product
            const pid = formData.get('product_id');
            const flavor = formData.get('flavor') || '';
            const param = `?checkout=1&product_id=${encodeURIComponent(pid)}&flavor=${encodeURIComponent(flavor)}`;
            window.location.href = '{{ route('cart.index') }}' + param;
        }).catch(err => {
            console.error('Buy now failed', err);
            // fallback: submit normally and redirect to cart
            form.submit();
            setTimeout(() => {
                window.location.href = '{{ route('cart.index') }}?checkout=1';
            }, 200);
        });
    }
    
    // Toggle similar products
    function toggleSimilar(event) {
        event.preventDefault();
        const section = document.getElementById('modal-similar-section');
        if (section.style.display === 'none') {
            section.style.display = 'block';
            loadModalSimilarProducts();
        } else {
            section.style.display = 'none';
        }
    }
    
    // Load similar products
    function loadModalSimilarProducts() {
        const productId = document.getElementById('modal-product-id').value;
        const grid = document.getElementById('modal-similar-grid');
        
        fetch('/api/products/similar/' + productId)
            .then(response => response.json())
            .then(products => {
                grid.innerHTML = products.map(product => {
                    const img = resolveImagePath(product.image);
                    return `
                    <div class="similar-card" onclick="openProductModal(${product.id})">
                        <img src="${img}" alt="${product.name}">
                        <div class="s-title">${product.name}</div>
                        <div class="s-price">₱${parseFloat(product.price).toFixed(2)}</div>
                    </div>
                `;
                }).join('');
            })
            .catch(error => {
                console.error('Error loading similar products:', error);
                grid.innerHTML = '<p style="color:#666;text-align:center">No similar products found</p>';
            });
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProductModal();
        }
    });

    // Ensure product grid images are resolved correctly (fix filenames / paths coming from DB)
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.card img[data-image]').forEach(function(img){
            try{
                const raw = img.getAttribute('data-image');
                const resolved = resolveImagePath(raw);
                console.debug('grid image raw=', raw, 'resolved=', resolved);
                img.src = resolved;
            }catch(e){ console.warn('resolve grid image failed', e); }
        });
    });
</script>
@endpush
