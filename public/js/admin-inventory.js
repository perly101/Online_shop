document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in
    const loggedInStaff = localStorage.getItem('loggedInStaff');
    if (!loggedInStaff) {
        // Redirect to login page if not logged in
        window.location.href = '../admin-login.html';
        return;
    }
    
    // Make logout function globally accessible
    window.logout = function() {
        showConfirm('Are you sure you want to logout?', function() {
            // Remove staff info from localStorage
            localStorage.removeItem('loggedInStaff');
            
            // Redirect to login page
            window.location.href = '/admin/login';
        }, null, false); // false means don't show close button
    };

    // ============================================
    // PRODUCT DATA STORAGE
    // ============================================
    let products = [
        {
            id: 1,
            name: "Nescafe Original Twin pack",
            price: 150,
            stock: 130,
            image: "https://via.placeholder.com/60x80/DC143C/FFFFFF?text=Nescafe"
        },
        {
            id: 2,
            name: "Coke mismo 290ml",
            price: 310,
            stock: 60,
            image: "https://via.placeholder.com/60x80/DC143C/FFFFFF?text=Coke"
        },
        {
            id: 3,
            name: "Bear brand 320g",
            price: 300,
            stock: 33,
            image: "https://via.placeholder.com/60x80/4169E1/FFFFFF?text=Bear"
        },
        {
            id: 4,
            name: "Mega Sardines",
            price: 20,
            stock: 189,
            image: "https://via.placeholder.com/60x80/FFD700/000000?text=Sardines"
        },
        {
            id: 5,
            name: "Surf 65g",
            price: 7,
            stock: 140,
            image: "https://via.placeholder.com/60x80/FF69B4/FFFFFF?text=Surf"
        },
        {
            id: 6,
            name: "Pancit Canton",
            price: 230,
            stock: 60,
            image: "https://via.placeholder.com/60x80/FF8C00/FFFFFF?text=Canton"
        },
        {
            id: 7,
            name: "Safeguard 85g",
            price: 37,
            stock: 30,
            image: "https://via.placeholder.com/60x80/00BFFF/FFFFFF?text=Safe"
        },
        {
            id: 8,
            name: "Piatos 5 Flavors",
            price: 30,
            stock: 15,
            image: "https://via.placeholder.com/60x80/8B4513/FFFFFF?text=Piatos"
        },
        {
            id: 9,
            name: "Tanduay Select",
            price: 160,
            stock: 22,
            image: "https://via.placeholder.com/60x80/FFD700/000000?text=Tanduay"
        },
        {
            id: 10,
            name: "Closeup Toothpaste",
            price: 10,
            stock: 45,
            image: "https://via.placeholder.com/60x80/DC143C/FFFFFF?text=Closeup"
        }
    ];

    // ============================================
    // ORDER DATA FOR BADGE COUNT
    // ============================================
    // Load orders from localStorage if available, otherwise use default data
    let orders = [];
    const storedOrders = localStorage.getItem('ordersData');
    if (storedOrders) {
        try {
            orders = JSON.parse(storedOrders);
            
            // Fix any orders that might have NaN values or missing data
            orders = orders.map(order => {
                // Fix items with NaN prices
                if (order.items) {
                    order.items = order.items.map(item => {
                        // Ensure price is a valid number
                        if (isNaN(item.price) || item.price === null || item.price === undefined) {
                            item.price = 0;
                        }
                        
                        // Ensure quantity is a valid number
                        if (isNaN(item.quantity) || item.quantity === null || item.quantity === undefined) {
                            item.quantity = 1;
                        }
                        
                        // Ensure image has a fallback
                        if (!item.image) {
                            item.image = 'image/placeholder.png';
                        }
                        
                        return item;
                    });
                }
                
                // Recalculate total based on fixed item prices
                if (order.items) {
                    order.total = order.items.reduce((sum, item) => {
                        return sum + (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 1);
                    }, 0);
                }
                
                return order;
            });
        } catch (e) {
            console.error('Error parsing stored orders data:', e);
            // Fallback to default data if parsing fails
            orders = [
                {
                    id: "AET-2025-001",
                    customerEmail: "altheacatubig@gmail.com",
                    total: 150,
                    date: "10/8/2025",
                    time: "8:15:05 AM",
                    status: "Processing",
                    items: [
                        { name: "Nescafe Original Twin pack", quantity: 1, price: 150, image: null }
                    ]
                },
                {
                    id: "AET-2025-002",
                    customerEmail: "sally45@gmail.com",
                    total: 1060,
                    date: "10/8/2025",
                    time: "8:55:00 AM",
                    status: "Pending",
                    items: [
                        { name: "Nescafe Original Twin pack", quantity: 2, price: 150, image: null },
                        { name: "Coke mismo 290ml", quantity: 1, price: 310, image: null }
                    ]
                },
                {
                    id: "AET-2025-003",
                    customerEmail: "marites D.",
                    total: 290,
                    date: "10/8/2025",
                    time: "10:15:11 AM",
                    status: "Pending",
                    items: [
                        { name: "Bear brand 320g", quantity: 1, price: 290, image: null }
                    ]
                },
                {
                    id: "AET-2025-004",
                    customerEmail: "Juan Gorve",
                    total: 123,
                    date: "10/8/2025",
                    time: "11:11:19 AM",
                    status: "Pending",
                    items: [
                        { name: "Mega Sardines", quantity: 1, price: 123, image: null }
                    ]
                },
                {
                    id: "AET-2025-005",
                    customerEmail: "ally34@gmail.com",
                    total: 233,
                    date: "10/8/2025",
                    time: "3:30:10 PM",
                    status: "Pending",
                    items: [
                        { name: "Surf 65g", quantity: 1, price: 233, image: null }
                    ]
                },
                {
                    id: "AET-2025-006",
                    customerEmail: "davied2@gmail.com",
                    total: 300,
                    date: "10/8/2025",
                    time: "4:25:15 AM",
                    status: "Pending",
                    items: [
                        { name: "Nescafe Original Twin pack", quantity: 1, price: 150, image: 'image/nescafe.png' },
                        { name: "Piatos 5 Flavors", quantity: 5, price: 30, image: 'image/piatos.png' }
                    ]
                }
            ];
        }
    } else {
        // Default data if no stored data
        orders = [
            {
                id: "AET-2025-001",
                customerEmail: "altheacatubig@gmail.com",
                total: 150,
                date: "10/8/2025",
                time: "8:15:05 AM",
                status: "Processing",
                items: [
                    { name: "Nescafe Original Twin pack", quantity: 1, price: 150, image: null }
                ]
            },
            {
                id: "AET-2025-002",
                customerEmail: "sally45@gmail.com",
                total: 1060,
                date: "10/8/2025",
                time: "8:55:00 AM",
                status: "Pending",
                items: [
                    { name: "Nescafe Original Twin pack", quantity: 2, price: 150, image: null },
                    { name: "Coke mismo 290ml", quantity: 1, price: 310, image: null }
                ]
            },
            {
                id: "AET-2025-003",
                customerEmail: "marites D.",
                total: 290,
                date: "10/8/2025",
                time: "10:15:11 AM",
                status: "Pending",
                items: [
                    { name: "Bear brand 320g", quantity: 1, price: 290, image: null }
                ]
            },
            {
                id: "AET-2025-004",
                customerEmail: "Juan Gorve",
                total: 123,
                date: "10/8/2025",
                time: "11:11:19 AM",
                status: "Pending",
                items: [
                    { name: "Mega Sardines", quantity: 1, price: 123, image: null }
                ]
            },
            {
                id: "AET-2025-005",
                customerEmail: "ally34@gmail.com",
                total: 233,
                date: "10/8/2025",
                time: "3:30:10 PM",
                status: "Pending",
                items: [
                    { name: "Surf 65g", quantity: 1, price: 233, image: null }
                ]
            },
            {
                id: "AET-2025-006",
                customerEmail: "davied2@gmail.com",
                total: 300,
                date: "10/8/2025",
                time: "4:25:15 AM",
                status: "Pending",
                items: [
                    { name: "Nescafe Original Twin pack", quantity: 1, price: 150, image: 'image/nescafe.png' },
                    { name: "Piatos 5 Flavors", quantity: 5, price: 30, image: 'image/piatos.png' }
                ]
            }
        ];
    }

    let nextProductId = 11;
    let editingProductId = null;
    let displayedProducts = 10; // Number of products to display initially
    let imagePreviewUrl = null;
    let editImagePreviewUrl = null;
    // let isEditImagePreviewListenerAdded = false; // Flag to track if event listener is added (removed as we're using a different approach)
    
    // ============================================
    // DOM ELEMENTS
    // ============================================
    const productTableBody = document.getElementById('product-table-body');
    const productCount = document.getElementById('product-count');
    const addProductSection = document.getElementById('add-product-section');
    const editProductSection = document.getElementById('edit-product-section');
    
    // Add product elements
    const productNameInput = document.getElementById('product-name');
    const productPriceInput = document.getElementById('product-price');
    const productStockInput = document.getElementById('product-stock');
    const productDescriptionInput = document.getElementById('product-description');
    const productImageInput = document.getElementById('product-image');
    const imagePreview = document.getElementById('image-preview');
    const addProductBtn = document.getElementById('add-product-btn');
    
    // Flavors elements
    const flavorsContainer = document.getElementById('flavors-container');
    const addFlavorBtn = document.getElementById('add-flavor-btn');
    
    // Edit product elements
    const editProductNameInput = document.getElementById('edit-product-name');
    const editProductPriceInput = document.getElementById('edit-product-price');
    const editProductStockInput = document.getElementById('edit-product-stock');
    const editProductDescriptionInput = document.getElementById('edit-product-description');
    const editProductImageInput = document.getElementById('edit-product-image');
    const editImagePreview = document.getElementById('edit-image-preview');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const saveChangesBtn = document.getElementById('save-changes-btn');
    
    // Edit flavors elements
    const editFlavorsContainer = document.getElementById('edit-flavors-container');
    const editAddFlavorBtn = document.getElementById('edit-add-flavor-btn');
    
    const seeMoreBtn = document.getElementById('see-more-btn');
    
    // Search elements
    const productSearch = document.getElementById('product-search');
    const searchIcon = document.getElementById('search-icon');

    // ============================================
    // FLAVOR FUNCTIONALITY
    // ============================================

    /**
     * Creates a new flavor entry element
     */
    function createFlavorEntry(name = '', price = '', stock = '', imageUrl = '') {
        const flavorEntry = document.createElement('div');
        flavorEntry.className = 'flavor-entry';
        
        flavorEntry.innerHTML = `
            <input type="text" class="flavor-name form-input" placeholder="Flavor Name" value="${name}">
            <input type="number" class="flavor-price form-input" placeholder="Flavor Price" min="0" step="0.01" value="${price}">
            <input type="number" class="flavor-stock form-input" placeholder="Stock Quantity" min="0" value="${stock}">
            <div class="image-upload-container">
                <label class="image-upload-label">
                    <i class="fas fa-image"></i>
                    <span>Flavor Image</span>
                </label>
                <input type="file" class="flavor-image" accept="image/*" style="display: none;">
                <div class="flavor-image-preview image-preview" ${imageUrl ? `data-image-url="${imageUrl}"` : ''}></div>
            </div>
            <button type="button" class="remove-flavor-btn">Remove</button>
        `;
        
        // Set up image preview if imageUrl is provided
        if (imageUrl) {
            const previewDiv = flavorEntry.querySelector('.flavor-image-preview');
            const label = flavorEntry.querySelector('.image-upload-label');
            label.style.display = 'none';
            previewDiv.innerHTML = `<div class="preview-wrapper"><img src="${imageUrl}" alt="Preview" class="preview-image"></div>`;
            previewDiv.style.display = 'flex';
        }
        
        return flavorEntry;
    }

    /**
     * Adds a new flavor entry to the container
     */
    function addFlavorEntry(container) {
        const flavorEntry = createFlavorEntry();
        container.appendChild(flavorEntry);
        
        // Show remove button if there's more than one flavor
        const removeButtons = container.querySelectorAll('.remove-flavor-btn');
        if (removeButtons.length > 1) {
            removeButtons.forEach(btn => btn.style.display = 'block');
        }
        
        // Add event listener to the new remove button
        const removeBtn = flavorEntry.querySelector('.remove-flavor-btn');
        removeBtn.addEventListener('click', function() {
            if (container.querySelectorAll('.flavor-entry').length > 1) {
                flavorEntry.remove();
                
                // Hide remove buttons if only one flavor left
                const remainingEntries = container.querySelectorAll('.flavor-entry');
                if (remainingEntries.length === 1) {
                    remainingEntries[0].querySelector('.remove-flavor-btn').style.display = 'none';
                }
            }
        });
        
        // Add event listener to the new image input
        const imageInput = flavorEntry.querySelector('.flavor-image');
        const imagePreview = flavorEntry.querySelector('.flavor-image-preview');
        
        imageInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (file) {
                try {
                    const imageDataUrl = await fileToDataURL(file);
                    
                    // Hide the label and show the preview
                    const container = imagePreview.parentElement; // image-upload-container
                    const label = container.querySelector('.image-upload-label');
                    if (label) {
                        label.style.display = 'none';
                    }
                    imagePreview.innerHTML = `<div class="preview-wrapper"><img src="${imageDataUrl}" alt="Preview" class="preview-image"></div>`;
                    imagePreview.style.display = 'flex';
                    imagePreview.dataset.imageUrl = imageDataUrl;
                } catch (error) {
                    console.error('Error reading image:', error);
                    showPopup(error.message || 'Error loading image. Please try again.');
                }
            }
        });
        
        // Add click event to the image preview to trigger the file input
        imagePreview.addEventListener('click', function() {
            imageInput.click();
        });
        
        // Also add click event to the container for better UX
        const imageContainer = imagePreview.parentElement;
        imageContainer.addEventListener('click', function(e) {
            // Only trigger if clicked on the container itself or label, not the preview image
            if (e.target === imageContainer || e.target.classList.contains('image-upload-label') || e.target.tagName === 'I' || e.target.tagName === 'SPAN') {
                imageInput.click();
            }
        });
    }

    /**
     * Initializes flavor entries with remove button visibility
     */
    function initializeFlavorEntries(container) {
        const entries = container.querySelectorAll('.flavor-entry');
        if (entries.length > 1) {
            entries.forEach(entry => {
                const removeBtn = entry.querySelector('.remove-flavor-btn');
                if (removeBtn) {
                    removeBtn.style.display = 'block';
                }
            });
        }
    }

    // ============================================
    // EVENT LISTENERS FOR FLAVORS
    // ============================================

    // Add flavor button event listener
    addFlavorBtn.addEventListener('click', function() {
        addFlavorEntry(flavorsContainer);
    });

    // Edit add flavor button event listener
    editAddFlavorBtn.addEventListener('click', function() {
        addFlavorEntry(editFlavorsContainer);
    });

    // Initialize existing flavor entries
    initializeFlavorEntries(flavorsContainer);
    initializeFlavorEntries(editFlavorsContainer);

    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-flavor-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.closest('.flavors-section').querySelector('.flavor-entry').parentElement;
            if (container.querySelectorAll('.flavor-entry').length > 1) {
                this.closest('.flavor-entry').remove();
                
                // Hide remove buttons if only one flavor left
                const remainingEntries = container.querySelectorAll('.flavor-entry');
                if (remainingEntries.length === 1) {
                    remainingEntries[0].querySelector('.remove-flavor-btn').style.display = 'none';
                }
            }
        });
    });

    // Add event listeners to existing image inputs
    document.querySelectorAll('.flavor-image').forEach(input => {
        const flavorEntry = input.closest('.flavor-entry');
        const imagePreview = flavorEntry.querySelector('.flavor-image-preview');
        
        input.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (file) {
                try {
                    const imageDataUrl = await fileToDataURL(file);
                    
                    // Hide the label and show the preview
                    const container = imagePreview.parentElement; // image-upload-container
                    const label = container.querySelector('.image-upload-label');
                    if (label) {
                        label.style.display = 'none';
                    }
                    imagePreview.innerHTML = `<div class="preview-wrapper"><img src="${imageDataUrl}" alt="Preview" class="preview-image"></div>`;
                    imagePreview.style.display = 'flex';
                    imagePreview.dataset.imageUrl = imageDataUrl;
                } catch (error) {
                    console.error('Error reading image:', error);
                    showPopup(error.message || 'Error loading image. Please try again.');
                }
            }
        });
        
        // Add click event to the image preview to trigger the file input
        imagePreview.addEventListener('click', function() {
            input.click();
        });
        
        // Also add click event to the container for better UX
        const imageContainer = imagePreview.parentElement;
        imageContainer.addEventListener('click', function(e) {
            // Only trigger if clicked on the container itself or label, not the preview image
            if (e.target === imageContainer || e.target.classList.contains('image-upload-label') || e.target.tagName === 'I' || e.target.tagName === 'SPAN') {
                input.click();
            }
        });
    });

    // ============================================
    // FUNCTIONS
    // ============================================

    /**
     * Updates the product count display
     */
    function updateProductCount() {
        productCount.textContent = `(${products.length})`;
    }

    /**
     * Converts file to base64 data URL with size limitation
     */
    function fileToDataURL(file) {
        // Limit file size to 2MB to prevent performance issues
        if (file.size > 2 * 1024 * 1024) {
            throw new Error('Image size too large. Please select an image smaller than 2MB.');
        }
        
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    /**
     * Handles image preview for add product
     */
    productImageInput.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (file) {
            try {
                imagePreviewUrl = await fileToDataURL(file);
                // Hide the label and show the preview
                const container = imagePreview.parentElement; // image-upload-container
                const label = container.querySelector('.image-upload-label');
                if (label) {
                    label.style.display = 'none';
                }
                imagePreview.innerHTML = `<div class="preview-wrapper"><img src="${imagePreviewUrl}" alt="Preview" class="preview-image" onload="console.log('Image loaded successfully')"></div>`;
                imagePreview.style.display = 'flex';
            } catch (error) {
                console.error('Error reading image:', error);
                showPopup(error.message || 'Error loading image. Please try again.');
            }
        } else {
            // Clear preview if no file is selected
            imagePreview.innerHTML = '';
            imagePreview.style.display = 'none';
            // Show the label again
            const container = imagePreview.parentElement; // image-upload-container
            const label = container.querySelector('.image-upload-label');
            if (label) {
                label.style.display = 'flex';
            }
            imagePreviewUrl = null;
        }
    });

    /**
     * Handles image preview for edit product
     */
    editProductImageInput.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (file) {
            try {
                const imageDataUrl = await fileToDataURL(file);
                editImagePreviewUrl = imageDataUrl; // Update the global variable
                // Hide the label and show the preview
                // Make sure we're working with the current DOM element
                const currentEditImagePreview = document.getElementById('edit-image-preview');
                const container = currentEditImagePreview.parentElement; // image-upload-container
                const label = container.querySelector('.image-upload-label');
                if (label) {
                    label.style.display = 'none';
                }
                currentEditImagePreview.innerHTML = `<div class="preview-wrapper"><img src="${imageDataUrl}" alt="Preview" class="preview-image" onload="console.log('Edit image loaded successfully')"></div>`;
                currentEditImagePreview.style.display = 'flex';
                console.log('New image selected for editing:', imageDataUrl);
            } catch (error) {
                console.error('Error reading image:', error);
                showPopup(error.message || 'Error loading image. Please try again.');
            }
        }
        // Note: When no file is selected (e.g., dialog cancelled), we don't change anything
        // The existing preview and editImagePreviewUrl remain unchanged
    });

    /**
     * Renders the product table
     */
    function renderProducts() {
        // Use DocumentFragment for better performance
        const fragment = document.createDocumentFragment();
        const productsToShow = products.slice(0, displayedProducts);
        
        productsToShow.forEach(product => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="product-name">${product.name}</td>
                <td class="product-price">₱ ${product.price.toLocaleString()}</td>
                <td class="product-stock">${product.stock}</td>
                <td class="product-image-cell">
                    <img src="${product.image}" alt="${product.name}" onerror="this.src='https://via.placeholder.com/50x70/CCCCCC/666666?text=No+Image'" style="width:50px; height:70px; object-fit:contain; display:block; margin:0 auto; box-sizing:border-box;">
                </td>
                <td>
                    <div class="product-actions">
                        <button class="action-btn edit-btn" data-product-id="${product.id}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-btn" data-product-id="${product.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            fragment.appendChild(row);
        });
        
        // Clear and populate table body in one operation
        productTableBody.innerHTML = '';
        productTableBody.appendChild(fragment);

        // Show/hide "see more" button
        if (products.length > displayedProducts) {
            seeMoreBtn.style.display = 'block';
        } else {
            seeMoreBtn.style.display = 'none';
        }

        // Attach event listeners
        attachProductActionListeners();
    }

    /**
     * Attaches event listeners to edit and delete buttons
     */
    function attachProductActionListeners() {
        // Remove existing listeners to prevent duplicates
        document.querySelectorAll('.edit-btn').forEach(btn => {
            const clone = btn.cloneNode(true);
            btn.parentNode.replaceChild(clone, btn);
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            const clone = btn.cloneNode(true);
            btn.parentNode.replaceChild(clone, btn);
        });
        
        // Edit buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const productId = parseInt(e.currentTarget.getAttribute('data-product-id'));
                editProduct(productId);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const productId = parseInt(e.currentTarget.getAttribute('data-product-id'));
                deleteProduct(productId);
            });
        });
    }

    /**
     * Resets the add product form
     */
    function resetAddProductForm() {
        productNameInput.value = '';
        productPriceInput.value = '';
        productStockInput.value = '';
        productDescriptionInput.value = '';
        productImageInput.value = '';
        imagePreview.innerHTML = '';
        imagePreview.style.display = 'none';
        // Show the label again
        const container = imagePreview.parentElement; // image-upload-container
        const label = container.querySelector('.image-upload-label');
        if (label) {
            label.style.display = 'flex';
        }
        imagePreviewUrl = null;
        
        // Reset flavors section
        const flavorEntries = flavorsContainer.querySelectorAll('.flavor-entry');
        // Keep only the first entry
        for (let i = 1; i < flavorEntries.length; i++) {
            flavorEntries[i].remove();
        }
        // Reset the first entry
        const firstEntry = flavorEntries[0];
        firstEntry.querySelector('.flavor-name').value = '';
        firstEntry.querySelector('.flavor-stock').value = '';
        firstEntry.querySelector('.flavor-image').value = '';
        const firstPreview = firstEntry.querySelector('.flavor-image-preview');
        firstPreview.innerHTML = '';
        firstPreview.style.display = 'none';
        firstPreview.removeAttribute('data-image-url');
        const firstLabel = firstEntry.querySelector('.image-upload-label');
        firstLabel.style.display = 'flex';
        firstEntry.querySelector('.remove-flavor-btn').style.display = 'none';
    }

    /**
     * Resets the edit product form
     */
    function resetEditProductForm() {
        editProductNameInput.value = '';
        editProductPriceInput.value = '';
        editProductStockInput.value = '';
        editProductDescriptionInput.value = '';
        editProductImageInput.value = '';
        
        // Make sure we're working with the current DOM element
        const currentEditImagePreview = document.getElementById('edit-image-preview');
        currentEditImagePreview.innerHTML = '';
        currentEditImagePreview.style.display = 'none';
        
        // Show the label again
        const container = currentEditImagePreview.parentElement; // image-upload-container
        const label = container.querySelector('.image-upload-label');
        if (label) {
            label.style.display = 'flex';
        }
        editImagePreviewUrl = null; // Reset to null to indicate no new image selected
        editingProductId = null;
        // isEditImagePreviewListenerAdded = false; // Reset the flag (removed as we're using a different approach)
        console.log('Edit form reset, editImagePreviewUrl set to null');
        
        // Reset flavors section
        const flavorEntries = editFlavorsContainer.querySelectorAll('.flavor-entry');
        // Keep only the first entry
        for (let i = 1; i < flavorEntries.length; i++) {
            flavorEntries[i].remove();
        }
        // Reset the first entry
        const firstEntry = flavorEntries[0];
        firstEntry.querySelector('.flavor-name').value = '';
        firstEntry.querySelector('.flavor-stock').value = '';
        firstEntry.querySelector('.flavor-image').value = '';
        const firstPreview = firstEntry.querySelector('.flavor-image-preview');
        firstPreview.innerHTML = '';
        firstPreview.style.display = 'none';
        firstPreview.removeAttribute('data-image-url');
        const firstLabel = firstEntry.querySelector('.image-upload-label');
        firstLabel.style.display = 'flex';
        firstEntry.querySelector('.remove-flavor-btn').style.display = 'none';
    }

    /**
     * Adds a new product
     */
    function addProduct() {
        const name = productNameInput.value.trim();
        const price = parseFloat(productPriceInput.value);
        const stock = parseInt(productStockInput.value);
        const description = productDescriptionInput.value.trim();
        const image = imagePreviewUrl || 'https://via.placeholder.com/50x70/CCCCCC/666666?text=No+Image';

        // Collect flavor variants
        const variants = [];
        const flavorEntries = flavorsContainer.querySelectorAll('.flavor-entry');
        flavorEntries.forEach(entry => {
            const flavorName = entry.querySelector('.flavor-name').value.trim();
            const flavorPrice = parseFloat(entry.querySelector('.flavor-price').value) || 0;
            const flavorStock = parseInt(entry.querySelector('.flavor-stock').value) || 0;
            const flavorImagePreview = entry.querySelector('.flavor-image-preview');
            const flavorImageUrl = flavorImagePreview.dataset.imageUrl;
            
            if (flavorName && flavorImageUrl) {
                variants.push({
                    flavor: flavorName,
                    price: flavorPrice,
                    stock: flavorStock,
                    image: flavorImageUrl
                });
            }
        });

        // Validation
        if (!name) {
            showPopup('Please enter a product name.');
            return;
        }
        if (isNaN(price) || price <= 0) {
            showPopup('Please enter a valid price.');
            return;
        }
        if (isNaN(stock) || stock < 0) {
            showPopup('Please enter a valid stock quantity.');
            return;
        }

        // Add product
        const newProduct = {
            id: nextProductId++,
            name: name,
            price: price,
            stock: stock,
            description: description,
            image: image,
            variants: variants
        };

        products.unshift(newProduct); // Add to beginning
        saveProductsToLocalStorage(); // Save to localStorage
        updateProductCount();
        renderProducts();
        resetAddProductForm();
    }

    /**
     * Edits a product
     */
    function editProduct(productId) {
        const product = products.find(p => p.id === productId);
        if (!product) return;

        editingProductId = productId;
        editProductNameInput.value = product.name;
        editProductPriceInput.value = product.price;
        editProductStockInput.value = product.stock;
        editProductDescriptionInput.value = product.description || '';
        
        // Initialize editImagePreviewUrl to null to indicate no new image has been selected yet
        // The current image is shown in the preview, but editImagePreviewUrl will only be set if user selects a new image
        editImagePreviewUrl = null;
        // Hide the label and show the preview of the current image
        const container = editImagePreview.parentElement; // image-upload-container
        const label = container.querySelector('.image-upload-label');
        if (label) {
            label.style.display = 'none';
        }
        editImagePreview.innerHTML = `<div class="preview-wrapper"><img src="${product.image}" alt="${product.name}" class="preview-image" onload="console.log('Edit product image loaded successfully')"></div>`;
        editImagePreview.style.display = 'flex';
        console.log('Editing product with current image:', product.image);
        
        // Add click event to the image preview to trigger the file input
        // We need to make sure we're always working with the current DOM element
        editImagePreview.onclick = function() {
            editProductImageInput.click();
        };

        // Populate flavor variants
        const editFlavorsContainer = document.getElementById('edit-flavors-container');
        // Clear existing entries except the first one
        const existingEntries = editFlavorsContainer.querySelectorAll('.flavor-entry');
        for (let i = 1; i < existingEntries.length; i++) {
            existingEntries[i].remove();
        }
        
        // Reset the first entry
        const firstEntry = existingEntries[0];
        firstEntry.querySelector('.flavor-name').value = '';
        firstEntry.querySelector('.flavor-stock').value = '';
        firstEntry.querySelector('.flavor-image').value = '';
        const firstPreview = firstEntry.querySelector('.flavor-image-preview');
        firstPreview.innerHTML = '';
        firstPreview.style.display = 'none';
        firstPreview.removeAttribute('data-image-url');
        const firstLabel = firstEntry.querySelector('.image-upload-label');
        firstLabel.style.display = 'flex';
        firstEntry.querySelector('.remove-flavor-btn').style.display = 'none';
        
        // Add variants if they exist
        if (product.variants && product.variants.length > 0) {
            // Clear the first entry since we'll be adding all variants
            firstEntry.remove();
            
            // Add all variants
            product.variants.forEach((variant, index) => {
                const flavorEntry = createFlavorEntry(variant.flavor, variant.price || 0, variant.stock || 0, variant.image);
                editFlavorsContainer.appendChild(flavorEntry);
                
                // Add event listeners for remove button
                const removeBtn = flavorEntry.querySelector('.remove-flavor-btn');
                removeBtn.addEventListener('click', function() {
                    if (editFlavorsContainer.querySelectorAll('.flavor-entry').length > 1) {
                        flavorEntry.remove();
                        
                        // Hide remove buttons if only one flavor left
                        const remainingEntries = editFlavorsContainer.querySelectorAll('.flavor-entry');
                        if (remainingEntries.length === 1) {
                            remainingEntries[0].querySelector('.remove-flavor-btn').style.display = 'none';
                        }
                    }
                });
                
                // Add event listeners for image input
                const imageInput = flavorEntry.querySelector('.flavor-image');
                const imagePreview = flavorEntry.querySelector('.flavor-image-preview');
                imageInput.addEventListener('change', async function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        try {
                            const imageDataUrl = await fileToDataURL(file);
                            
                            // Hide the label and show the preview
                            const container = imagePreview.parentElement; // image-upload-container
                            const label = container.querySelector('.image-upload-label');
                            if (label) {
                                label.style.display = 'none';
                            }
                            imagePreview.innerHTML = `<div class="preview-wrapper"><img src="${imageDataUrl}" alt="Preview" class="preview-image"></div>`;
                            imagePreview.style.display = 'flex';
                            imagePreview.dataset.imageUrl = imageDataUrl;
                        } catch (error) {
                            console.error('Error reading image:', error);
                            showPopup(error.message || 'Error loading image. Please try again.');
                        }
                    }
                });
                
                // Add click event to the image preview to trigger the file input
                imagePreview.addEventListener('click', function() {
                    imageInput.click();
                });
                
                // Also add click event to the container for better UX
                const imageContainer = imagePreview.parentElement;
                imageContainer.addEventListener('click', function(e) {
                    // Only trigger if clicked on the container itself or label, not the preview image
                    if (e.target === imageContainer || e.target.classList.contains('image-upload-label') || e.target.tagName === 'I' || e.target.tagName === 'SPAN') {
                        imageInput.click();
                    }
                });
            });
        }
        
        // Show remove button if there's more than one flavor
        const entries = editFlavorsContainer.querySelectorAll('.flavor-entry');
        if (entries.length > 1) {
            entries.forEach(entry => {
                entry.querySelector('.remove-flavor-btn').style.display = 'block';
            });
        }

        // Show edit section, hide add section
        addProductSection.style.display = 'none';
        editProductSection.style.display = 'block';

        // Scroll to edit section
        editProductSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Saves changes to an edited product
     */
    function saveProductChanges() {
        if (editingProductId === null) return;

        const product = products.find(p => p.id === editingProductId);
        if (!product) return;

        const name = editProductNameInput.value.trim();
        const price = parseFloat(editProductPriceInput.value);
        const stock = parseInt(editProductStockInput.value);
        const description = editProductDescriptionInput.value.trim();
        // Use the new image if one was selected, otherwise keep the existing image
        const image = editImagePreviewUrl !== null ? editImagePreviewUrl : product.image;
        
        // Collect flavor variants
        const variants = [];
        const flavorEntries = editFlavorsContainer.querySelectorAll('.flavor-entry');
        flavorEntries.forEach(entry => {
            const flavorName = entry.querySelector('.flavor-name').value.trim();
            const flavorPrice = parseFloat(entry.querySelector('.flavor-price').value) || 0;
            const flavorStock = parseInt(entry.querySelector('.flavor-stock').value) || 0;
            const flavorImagePreview = entry.querySelector('.flavor-image-preview');
            const flavorImageUrl = flavorImagePreview.dataset.imageUrl;
            
            if (flavorName && flavorImageUrl) {
                variants.push({
                    flavor: flavorName,
                    price: flavorPrice,
                    stock: flavorStock,
                    image: flavorImageUrl
                });
            }
        });
        
        console.log('Saving product with image - new image selected:', editImagePreviewUrl !== null, 'Image URL:', image);

        // Validation
        if (!name) {
            showPopup('Please enter a product name.');
            return;
        }
        if (isNaN(price) || price <= 0) {
            showPopup('Please enter a valid price.');
            return;
        }
        if (isNaN(stock) || stock < 0) {
            showPopup('Please enter a valid stock quantity.');
            return;
        }

        // Update product
        product.name = name;
        product.price = price;
        product.stock = stock;
        product.description = description;
        product.image = image;
        product.variants = variants;
        console.log('Product updated with image:', image);

        saveProductsToLocalStorage(); // Save to localStorage
        updateProductCount();
        renderProducts();
        
        // Reset and show add section again
        resetEditProductForm();
        addProductSection.style.display = 'block';
        editProductSection.style.display = 'none';
        // Hide image preview after saving
        // Make sure we're working with the current DOM element
        const currentEditImagePreview = document.getElementById('edit-image-preview');
        currentEditImagePreview.style.display = 'none';
    }

    /**
     * Deletes a product
     */
    function deleteProduct(productId) {
        showConfirm('Are you sure you want to delete this product?', function() {
            products = products.filter(p => p.id !== productId);
            saveProductsToLocalStorage(); // Save to localStorage
            updateProductCount();
            renderProducts();
        });
    }

    /**
     * Shows more products
     */
    function showMoreProducts() {
        displayedProducts += 10;
        renderProducts();
    }

    /**
     * Cancels editing
     */
    function cancelEdit() {
        resetEditProductForm();
        addProductSection.style.display = 'block';
        editProductSection.style.display = 'none';
        // Hide image preview when cancelling edit
        // Make sure we're working with the current DOM element
        const currentEditImagePreview = document.getElementById('edit-image-preview');
        currentEditImagePreview.style.display = 'none';
    }

    /**
     * Filters products based on search term
     */
    function filterProducts(searchTerm) {
        if (!searchTerm) {
            // If no search term, show all products
            renderProducts();
            return;
        }
        
        // Filter products based on name
        const filteredProducts = products.filter(product => 
            product.name.toLowerCase().includes(searchTerm.toLowerCase())
        );
        
        // Render filtered products
        renderFilteredProducts(filteredProducts);
    }
    
    /**
     * Renders filtered products
     */
    function renderFilteredProducts(filteredProducts) {
        // Use DocumentFragment for better performance
        const fragment = document.createDocumentFragment();
        
        if (filteredProducts.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="5" style="text-align: center; padding: 20px;">
                    No products found matching your search.
                </td>
            `;
            fragment.appendChild(row);
        } else {
            // Display all filtered products (no pagination for search results)
            filteredProducts.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="product-name">${product.name}</div>
                    </td>
                    <td>
                        <div class="product-price">₱${product.price.toLocaleString()}</div>
                    </td>
                    <td>
                        <div class="product-stock">${product.stock}</div>
                    </td>
                    <td>
                        <div class="product-image-cell">
                            <img src="${product.image}" alt="${product.name}" onerror="this.src='https://via.placeholder.com/50x70/CCCCCC/FFFFFF?text=No+Image'">
                        </div>
                    </td>
                    <td>
                        <div class="product-actions">
                            <button class="action-btn edit-btn" data-product-id="${product.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" data-product-id="${product.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                fragment.appendChild(row);
            });
        }
        
        // Clear and populate table body in one operation
        productTableBody.innerHTML = '';
        productTableBody.appendChild(fragment);
        
        // Reattach event listeners for edit and delete buttons
        attachProductActionListeners();
    }

    /**
     * Saves products data to localStorage with error handling
     */
    function saveProductsToLocalStorage() {
        try {
            // Use a more efficient serialization method
            const serializedData = JSON.stringify(products);
            
            // Check if data size is reasonable before saving
            if (serializedData.length > 5 * 1024 * 1024) { // 5MB limit
                console.warn('Product data is very large, consider optimizing');
                showPopup('Warning: Product data is very large, which may affect performance.');
            }
            
            localStorage.setItem('inventoryProducts', serializedData);
        } catch (e) {
            console.error('Error saving products to localStorage:', e);
            showPopup('Error saving data. Please try again.');
        }
    }

    /**
     * Loads products data from localStorage
     */
    function loadProductsFromLocalStorage() {
        try {
            const storedProducts = localStorage.getItem('inventoryProducts');
            if (storedProducts) {
                const parsedData = JSON.parse(storedProducts);
                
                // Validate data structure
                if (Array.isArray(parsedData)) {
                    products = parsedData;
                    // Update nextProductId to be higher than the highest existing ID
                    const maxId = Math.max(...products.map(p => p.id), 0);
                    nextProductId = maxId + 1;
                } else {
                    console.warn('Invalid data structure in localStorage');
                }
            }
        } catch (e) {
            console.error('Error loading products from localStorage:', e);
            // Keep default data
        }
    }

    /**
     * Updates the order count badge in the navigation
     */
    function updateOrderBadge() {
        const badgeElements = document.querySelectorAll('.badge');
        // Count only recent orders (not completed)
        const recentOrders = orders.filter(order => order.status !== 'Complete');
        const orderCount = recentOrders.length;
        
        badgeElements.forEach(badge => {
            badge.textContent = orderCount;
        });
    }

    // ============================================
    // EVENT LISTENERS
    // ============================================
    
    addProductBtn.addEventListener('click', addProduct);
    saveChangesBtn.addEventListener('click', saveProductChanges);
    cancelEditBtn.addEventListener('click', cancelEdit);
    seeMoreBtn.addEventListener('click', showMoreProducts);

    // Allow Enter key to submit forms
    productNameInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') addProduct();
    });
    productPriceInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') addProduct();
    });
    productStockInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') addProduct();
    });
    
    // Search functionality with debounce
    let searchTimeout;
    productSearch.addEventListener('input', (e) => {
        // Debounce search to prevent excessive filtering while typing
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterProducts(e.target.value);
        }, 300); // Wait 300ms after user stops typing
    });
    
    searchIcon.addEventListener('click', () => {
        filterProducts(productSearch.value);
    });
    
    // Allow Enter key to trigger search
    productSearch.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') filterProducts(productSearch.value);
    });

    editProductNameInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') saveProductChanges();
    });
    editProductPriceInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') saveProductChanges();
    });
    editProductStockInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') saveProductChanges();
    });

    // ============================================
    // INITIALIZATION
    // ============================================
    
    loadProductsFromLocalStorage();
    updateProductCount();
    renderProducts();
    updateOrderBadge();
});

