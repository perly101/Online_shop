@extends('layouts.app')

@section('title', 'Admin Inventory Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">
<style>
/* Admin-only: hide native scrollbars while preserving scrolling */
html, body { -ms-overflow-style: none; scrollbar-width: none; }
html::-webkit-scrollbar, body::-webkit-scrollbar { display: none; width: 0; height: 0; }
.container, .customer-list-panel, .user-detail-panel, .order-history-list, .analytics-content { -ms-overflow-style: none; scrollbar-width: none; }
.container::-webkit-scrollbar, .customer-list-panel::-webkit-scrollbar, .user-detail-panel::-webkit-scrollbar, .order-history-list::-webkit-scrollbar, .analytics-content::-webkit-scrollbar { display: none; width: 0; height: 0; }

/* Image preview styling (compact) */
.image-preview { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; color: #888; border: 1px dashed #e0e0e0; padding: 4px; background: #fafafa; box-sizing: border-box; overflow: hidden; }
.image-preview img { width: 100%; height: 100%; display: block; object-fit: cover; }
.image-preview i { font-size: 20px; margin: 0; }
.image-preview p { display: none; }

/* Image label layout so previews appear inside the clickable label */
.image-upload-label { display: inline-flex; flex-direction: column; align-items: center; gap: 6px; cursor: pointer; }
.image-upload-container { display: inline-block; }

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
    <a href="{{ route('admin.dashboard') }}" class="nav-tab">
        <i class="fas fa-shopping-cart"></i> Orders
    </a>
    <a href="{{ route('admin.inventory') }}" class="nav-tab active">
        <i class="fas fa-clipboard-list"></i> Inventory
    </a>
    <a href="{{ route('admin.analytics') }}" class="nav-tab">
        <i class="fas fa-chart-bar"></i> Analytics
    </a>
    <a href="{{ route('admin.management') }}" class="nav-tab">
        <i class="fas fa-users"></i> Admins
    </a>
</nav>

<!-- Main Content -->
<main class="container">
    <!-- Product Inventory Card -->
    <div class="inventory-card">
        <!-- Card Title -->
        <div class="inventory-header">
            <i class="fas fa-box"></i>
            <h2 class="inventory-title">Product Inventory <span id="product-count" class="product-count">({{ $products->count() }})</span></h2>
        </div>
                    
        <!-- Search Bar -->
        <div class="search-container">
            <div class="search-input-container">
                <input type="text" id="product-search" class="search-input" placeholder="Search products...">
                <i class="fas fa-search search-icon" id="search-icon"></i>
            </div>
        </div>

        <!-- Add New Product Section -->
        <div class="add-product-section" id="add-product-section">
            <h3 class="section-title">Add new product</h3>
            <form id="add-product-form" method="POST" action="{{ route('admin.inventory.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <input type="text" id="product-name" name="name" class="form-input" placeholder="Product Name" required>
                    <input type="number" id="product-price" name="price" class="form-input" placeholder="Price" step="0.01" required>
                    <input type="number" id="product-stock" name="stock" class="form-input" placeholder="Stock Quantity" required>
                    <div class="image-upload-container">
                        <label for="product-image" class="image-upload-label">
                            <i class="fas fa-image"></i>
                            <span>Image</span>
                            <div id="image-preview" class="image-preview"><i class="fas fa-image"></i><p style="margin:0;">No image selected</p></div>
                        </label>
                        <input type="file" id="product-image" name="image" accept="image/*" style="display: none;">
                    </div>
                    <textarea id="product-description" name="description" class="form-input" placeholder="Product Description" rows="3"></textarea>
                </div>
                <!-- Flavors/Variants Section -->
                <div class="flavors-section">
                    <h4 class="section-subtitle">Flavor Options (Variants)</h4>
                    <div id="flavors-container">
                        <div class="flavor-entry">
                            <input type="text" class="flavor-name form-input" name="variants[0][name]" placeholder="Flavor Name">
                            <input type="number" class="flavor-price form-input" name="variants[0][price]" placeholder="Flavor Price" min="0" step="0.01">
                            <input type="number" class="flavor-stock form-input" name="variants[0][stock]" placeholder="Stock" min="0">
                            <div class="image-upload-container">
                                <label class="image-upload-label">
                                    <i class="fas fa-image"></i>
                                    <span>Flavor Image</span>
                                    <div class="flavor-image-preview image-preview"></div>
                                </label>
                                <input type="file" class="flavor-image" name="variants[0][image]" accept="image/*" style="display: none;">
                            </div>
                            <button type="button" class="btn-remove-flavor" style="display: none;">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="btn-add-flavor" id="add-flavor-btn">
                        <i class="fas fa-plus"></i> Add Flavor
                    </button>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-add-product" id="add-product-btn">
                        <i class="fas fa-plus"></i> ADD PRODUCT
                    </button>
                </div>
            </form>
        </div>

        <!-- Edit Product Section (Hidden by default) -->
        <div class="edit-product-section" id="edit-product-section" style="display: none;">
            <h3 class="section-title">Edit product</h3>
            <form id="edit-product-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-product-id">
                <div class="form-row">
                    <input type="text" id="edit-product-name" name="name" class="form-input" placeholder="Product Name" required>
                    <input type="number" id="edit-product-price" name="price" class="form-input" placeholder="Price" step="0.01" required>
                    <input type="number" id="edit-product-stock" name="stock" class="form-input" placeholder="Stock Quantity" required>
                    <div class="image-upload-container">
                        <label for="edit-product-image" class="image-upload-label">
                            <i class="fas fa-image"></i>
                            <span>Image</span>
                            <div id="edit-image-preview" class="image-preview"><i class="fas fa-image"></i><p style="margin:0;">No image selected</p></div>
                        </label>
                        <input type="file" id="edit-product-image" name="image" accept="image/*" style="display: none;">
                    </div>
                    <textarea id="edit-product-description" name="description" class="form-input" placeholder="Product Description" rows="3"></textarea>
                </div>
                <!-- Flavors Section -->
                <div class="flavors-section">
                    <h4 class="section-subtitle">Flavor Options (Variants)</h4>
                    <div id="edit-flavors-container">
                        <div class="flavor-entry">
                            <input type="text" class="flavor-name form-input" placeholder="Flavor Name">
                            <input type="number" class="flavor-price form-input" placeholder="Flavor Price" min="0" step="0.01">
                            <input type="number" class="flavor-stock form-input" placeholder="Stock Quantity" min="0">
                            <div class="image-upload-container">
                                <label class="image-upload-label">
                                    <i class="fas fa-image"></i>
                                    <span>Flavor Image</span>
                                    <div class="flavor-image-preview image-preview"></div>
                                </label>
                                <input type="file" class="flavor-image" accept="image/*" style="display: none;">
                            </div>
                            <button type="button" class="btn-remove-flavor" style="display: none;">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="btn-add-flavor" id="edit-add-flavor-btn">
                        <i class="fas fa-plus"></i> Add Flavor
                    </button>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel-edit" id="cancel-edit-btn">
                        Cancel Edit
                    </button>
                    <button type="submit" class="btn-save-changes" id="save-changes-btn">
                        Save changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Product Table -->
        <div class="table-container">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="product-table-body">
                    @forelse($products as $product)
                        <tr data-product-id="{{ $product->id }}">
                            <td>{{ $product->name }}</td>
                            <td>₱ {{ number_format($product->price, 0) }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                @if($product->image)
                                    <div style="width: 50px; height: 50px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                        <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @else
                                    <div style="width: 50px; height: 50px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px;"></div>
                                @endif
                            </td>
                            <td>
                                <button class="btn-action-edit" onclick="editProduct({{ $product->id }})" style="background: none; border: none; color: #666; cursor: pointer; font-size: 18px; margin-right: 10px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.inventory.destroy', $product->id) }}" style="display:inline" onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete" style="background: none; border: none; color: #666; cursor: pointer; font-size: 18px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px;color:#666;">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($products->count() > 10)
                <div class="table-footer">
                    <button class="btn-see-more" id="see-more-btn">see more</button>
                </div>
            @endif
        </div>
    </div>
</main>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Counter for flavor entries
    let flavorCounter = 1;
    let editFlavorCounter = 0;

    // Get elements
    const addFlavorBtn = document.getElementById('add-flavor-btn');
    const flavorsContainer = document.getElementById('flavors-container');
    const productImageInput = document.getElementById('product-image');
    const imagePreview = document.getElementById('image-preview');
    const editSection = document.getElementById('edit-product-section');
    const editFlavorsContainer = document.getElementById('edit-flavors-container');
    const editProductImageInput = document.getElementById('edit-product-image');
    const editImagePreview = document.getElementById('edit-image-preview');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');

    // Ensure previews show a placeholder initially
    if (imagePreview && imagePreview.innerHTML.trim() === '') {
        imagePreview.innerHTML = '<i class="fas fa-image"></i><p style="margin:0;">No image selected</p>';
    }
    if (editImagePreview && editImagePreview.innerHTML.trim() === '') {
        editImagePreview.innerHTML = '<i class="fas fa-image"></i><p style="margin:0;">No image selected</p>';
    }

    // Label click fallback (for browsers where label[for] might be unreliable)
    const productImageLabel = document.querySelector('label[for="product-image"]');
    if (productImageLabel && productImageInput) {
        productImageLabel.addEventListener('click', function(e){ e.preventDefault(); productImageInput.click(); });
    }
    const editImageLabel = document.querySelector('label[for="edit-product-image"]');
    if (editImageLabel && editProductImageInput) {
        editImageLabel.addEventListener('click', function(e){ e.preventDefault(); editProductImageInput.click(); });
    }

    // Create a flavor entry element
    function createFlavorEntry(index, data = {}) {
        const entry = document.createElement('div');
        entry.className = 'flavor-entry';
        entry.innerHTML = `
            <input type="text" name="variants[${index}][name]" class="flavor-name form-input" 
                   placeholder="Flavor Name" value="${data.name || ''}">
            <input type="number" name="variants[${index}][price]" class="flavor-price form-input" 
                   placeholder="Flavor Price" step="0.01" min="0" value="${data.price || ''}">
            <input type="number" name="variants[${index}][stock]" class="flavor-stock form-input" 
                   placeholder="Stock" min="0" value="${data.stock || ''}">
            <div class="image-upload-container">
                <label class="image-upload-label">
                    <i class="fas fa-image"></i>
                    <span>Flavor Image</span>
                    <div class="flavor-image-preview image-preview">${data.image ? `<img src="/images/${data.image}" alt="Flavor">` : ''}</div>
                </label>
                <input type="file" class="flavor-image" name="variants[${index}][image]" accept="image/*" style="display: none;">
            </div>
            <button type="button" class="btn-remove-flavor">
                Remove
            </button>
        `;

        // Add image preview functionality
        const imageInput = entry.querySelector('.flavor-image');
        const imagePreview = entry.querySelector('.flavor-image-preview');
        const imageLabel = entry.querySelector('.image-upload-label');
        
        imageLabel.addEventListener('click', function() {
            imageInput.click();
        });
        
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Add remove button functionality
        const removeBtn = entry.querySelector('.btn-remove-flavor');
        removeBtn.addEventListener('click', function() {
            entry.remove();
            updateRemoveButtons();
        });

        return entry;
    }

    // Update remove button visibility
    function updateRemoveButtons() {
        const addFlavors = flavorsContainer.querySelectorAll('.flavor-entry');
        addFlavors.forEach(entry => {
            const removeBtn = entry.querySelector('.btn-remove-flavor');
            if (removeBtn) {
                if (addFlavors.length === 1) {
                    removeBtn.style.display = 'none';
                } else {
                    removeBtn.style.display = 'block';
                }
            }
        });

        const editFlavors = editFlavorsContainer.querySelectorAll('.flavor-entry');
        editFlavors.forEach(entry => {
            const removeBtn = entry.querySelector('.btn-remove-flavor');
            if (removeBtn) {
                if (editFlavors.length === 1) {
                    removeBtn.style.display = 'none';
                } else {
                    removeBtn.style.display = 'block';
                }
            }
        });
    }

    // Add flavor button click
    if (addFlavorBtn) {
        addFlavorBtn.addEventListener('click', function() {
            const newEntry = createFlavorEntry(flavorCounter);
            flavorsContainer.appendChild(newEntry);
            flavorCounter++;
            updateRemoveButtons();
        });
    }

    // Initialize remove buttons
    updateRemoveButtons();

    // Setup image preview for initial flavor entries
    function setupFlavorImagePreview() {
        document.querySelectorAll('.flavor-entry').forEach(entry => {
            const imageInput = entry.querySelector('.flavor-image');
            const imagePreview = entry.querySelector('.flavor-image-preview');
            const imageLabel = entry.querySelector('.image-upload-label');
            
            if (imageLabel && imageInput && imagePreview) {
                imageLabel.addEventListener('click', function(e) {
                    e.preventDefault();
                    imageInput.click();
                });
                
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            imagePreview.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    }
    
    setupFlavorImagePreview();

    // Image preview for add product
    if (productImageInput && imagePreview) {
        productImageInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = '<i class="fas fa-image"></i><p>No image selected</p>';
            }
        });
    }

    // Image preview for edit product
    if (editProductImageInput && editImagePreview) {
        editProductImageInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    editImagePreview.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                editImagePreview.innerHTML = '<i class="fas fa-image"></i><p>No image selected</p>';
            }
        });
    }

    // Edit product function
    window.editProduct = async function(productId) {
        try {
            // Fetch product data
            const response = await fetch(`/admin/inventory/products/${productId}`);
            if (!response.ok) {
                throw new Error('Failed to fetch product');
            }

            const product = await response.json();

            // Show edit section
            editSection.style.display = 'block';
            editSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Set form action
            const editForm = document.getElementById('edit-product-form');
            editForm.action = `/admin/inventory/products/${productId}`;

            // Populate form fields
            document.getElementById('edit-product-name').value = product.name;
            document.getElementById('edit-product-price').value = product.price;
            document.getElementById('edit-product-stock').value = product.stock;
            document.getElementById('edit-product-description').value = product.description || '';

            // Show current image
            if (product.image) {
                editImagePreview.innerHTML = `<img src="/images/${product.image}" alt="${product.name}">`;
            } else {
                editImagePreview.innerHTML = '<i class="fas fa-image"></i><p>No image</p>';
            }

            // Clear and populate variants/flavors
            editFlavorsContainer.innerHTML = '';
            editFlavorCounter = 0;

            if (product.variants && product.variants.length > 0) {
                product.variants.forEach(variant => {
                    const entry = createFlavorEntry(editFlavorCounter, {
                        name: variant.flavor,
                        price: variant.price,
                        stock: variant.stock,
                        image: variant.image
                    });
                    editFlavorsContainer.appendChild(entry);
                    editFlavorCounter++;
                });
            } else {
                // Add at least one empty entry if no variants
                const entry = createFlavorEntry(editFlavorCounter);
                editFlavorsContainer.appendChild(entry);
                editFlavorCounter++;
            }

            updateRemoveButtons();

            // Add flavor button for edit section
            const editAddFlavorBtn = document.getElementById('edit-add-flavor-btn');
            if (editAddFlavorBtn) {
                editAddFlavorBtn.onclick = function() {
                    const newEntry = createFlavorEntry(editFlavorCounter);
                    editFlavorsContainer.appendChild(newEntry);
                    editFlavorCounter++;
                    updateRemoveButtons();
                };
            }

        } catch (error) {
            console.error('Error loading product:', error);
            alert('Error loading product data');
        }
    };

    // Cancel edit button
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            editSection.style.display = 'none';
            editFlavorsContainer.innerHTML = '';
        });
    }

    // Save changes button
    const saveChangesBtn = document.getElementById('save-changes-btn');
    if (saveChangesBtn) {
        saveChangesBtn.addEventListener('click', function() {
            const editForm = document.getElementById('edit-product-form');
            editForm.submit();
        });
    }

    // Search functionality
    const searchInput = document.getElementById('product-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#product-table-body tr');
            
            rows.forEach(row => {
                if (row.getAttribute('data-product-id')) {
                    const productName = row.querySelector('.product-name-cell')?.textContent.toLowerCase() || '';
                    const productDesc = row.querySelector('.product-description-cell')?.textContent.toLowerCase() || '';
                    
                    if (productName.includes(searchTerm) || productDesc.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }
});
</script>
@endpush

@push('scripts')
<script>
    // Display selected file name
    function displayFileName(input) {
        const fileName = input.files[0]?.name || '';
        document.getElementById('file-name').textContent = fileName ? `Selected: ${fileName}` : '';
    }
    
    // Search functionality
    document.getElementById('product-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#product-table-body tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Duplicate editProduct / closeEditModal removed — single implementation lives in the DOMContentLoaded block above to ensure image preview behavior works correctly.
</script>
@endpush
