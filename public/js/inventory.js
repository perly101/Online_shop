document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in
    const loggedInStaff = localStorage.getItem('loggedInStaff');
    if (!loggedInStaff) {
        // Redirect to login page if not logged in
        window.location.href = 'login.html';
        return;
    }
    
    // Make logout function globally accessible
    window.logout = function() {
        showConfirm('Are you sure you want to logout?', function() {
            // Remove staff info from localStorage
            localStorage.removeItem('loggedInStaff');
            
            // Redirect to login page
            window.location.href = 'login.html';
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
    const productImageInput = document.getElementById('product-image');
    const imagePreview = document.getElementById('image-preview');
    const addProductBtn = document.getElementById('add-product-btn');
    
    // Edit product elements
    const editProductNameInput = document.getElementById('edit-product-name');
    const editProductPriceInput = document.getElementById('edit-product-price');
    const editProductStockInput = document.getElementById('edit-product-stock');
    const editProductImageInput = document.getElementById('edit-product-image');
    const editImagePreview = document.getElementById('edit-image-preview');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const saveChangesBtn = document.getElementById('save-changes-btn');
    
    const seeMoreBtn = document.getElementById('see-more-btn');
    
    // Search elements
    const productSearch = document.getElementById('product-search');
    const searchIcon = document.getElementById('search-icon');

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
     * Converts file to base64 data URL
     */
    function fileToDataURL(file) {
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
                imagePreview.innerHTML = `<div class="preview-wrapper"><img src="${imagePreviewUrl}" alt="Preview" class="preview-image"></div>`;
                imagePreview.style.display = 'flex';
            } catch (error) {
                console.error('Error reading image:', error);
                showPopup('Error loading image. Please try again.');
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
                editImagePreviewUrl = await fileToDataURL(file);
                // Hide the label and show the preview
                const container = editImagePreview.parentElement; // image-upload-container
                const label = container.querySelector('.image-upload-label');
                if (label) {
                    label.style.display = 'none';
                }
                editImagePreview.innerHTML = `<div class="preview-wrapper"><img src="${editImagePreviewUrl}" alt="Preview" class="preview-image"></div>`;
                editImagePreview.style.display = 'flex';
            } catch (error) {
                console.error('Error reading image:', error);
                showPopup('Error loading image. Please try again.');
            }
        } else {
            // Clear preview if no file is selected
            editImagePreview.innerHTML = '';
            editImagePreview.style.display = 'none';
            // Show the label again
            const container = editImagePreview.parentElement; // image-upload-container
            const label = container.querySelector('.image-upload-label');
            if (label) {
                label.style.display = 'flex';
            }
            editImagePreviewUrl = null;
        }
    });

    /**
     * Renders the product table
     */
    function renderProducts() {
        productTableBody.innerHTML = '';
        
        const productsToShow = products.slice(0, displayedProducts);
        
        productsToShow.forEach(product => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="product-name">${product.name}</td>
                <td class="product-price">${product.price}</td>
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
            productTableBody.appendChild(row);
        });

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
    }

    /**
     * Resets the edit product form
     */
    function resetEditProductForm() {
        editProductNameInput.value = '';
        editProductPriceInput.value = '';
        editProductStockInput.value = '';
        editProductImageInput.value = '';
        editImagePreview.innerHTML = '';
        editImagePreview.style.display = 'none';
        // Show the label again
        const container = editImagePreview.parentElement; // image-upload-container
        const label = container.querySelector('.image-upload-label');
        if (label) {
            label.style.display = 'flex';
        }
        editImagePreviewUrl = null;
        editingProductId = null;
    }

    /**
     * Adds a new product
     */
    function addProduct() {
        const name = productNameInput.value.trim();
        const price = parseFloat(productPriceInput.value);
        const stock = parseInt(productStockInput.value);
        const image = imagePreviewUrl || 'https://via.placeholder.com/50x70/CCCCCC/666666?text=No+Image';

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
            image: image
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
        
        // Set image preview
        editImagePreviewUrl = product.image;
        // Hide the label and show the preview
        const container = editImagePreview.parentElement; // image-upload-container
        const label = container.querySelector('.image-upload-label');
        if (label) {
            label.style.display = 'none';
        }
        editImagePreview.innerHTML = `<div class="preview-wrapper"><img src="${product.image}" alt="${product.name}" class="preview-image"></div>`;
        editImagePreview.style.display = 'flex';

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
        const image = editImagePreviewUrl || product.image;

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
        product.image = image;

        saveProductsToLocalStorage(); // Save to localStorage
        updateProductCount();
        renderProducts();
        
        // Reset and show add section again
        resetEditProductForm();
        addProductSection.style.display = 'block';
        editProductSection.style.display = 'none';
        // Hide image preview after saving
        editImagePreview.style.display = 'none';
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
        editImagePreview.style.display = 'none';
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
        productTableBody.innerHTML = '';
        
        if (filteredProducts.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="5" style="text-align: center; padding: 20px;">
                    No products found matching your search.
                </td>
            `;
            productTableBody.appendChild(row);
            return;
        }
        
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
            productTableBody.appendChild(row);
        });
        
        // Reattach event listeners for edit and delete buttons
        attachActionListeners();
    }

    /**
     * Saves products data to localStorage
     */
    function saveProductsToLocalStorage() {
        localStorage.setItem('inventoryProducts', JSON.stringify(products));
    }

    /**
     * Loads products data from localStorage
     */
    function loadProductsFromLocalStorage() {
        const storedProducts = localStorage.getItem('inventoryProducts');
        if (storedProducts) {
            try {
                products = JSON.parse(storedProducts);
                // Update nextProductId to be higher than the highest existing ID
                const maxId = Math.max(...products.map(p => p.id), 0);
                nextProductId = maxId + 1;
            } catch (e) {
                console.error('Error parsing stored products data:', e);
            }
        }
    }

    /**
     * Updates the order count badge in the navigation
     */
    function updateOrderBadge() {
        const badgeElements = document.querySelectorAll('.badge');
        const orderCount = orders.length;
        
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
    
    // Search functionality
    productSearch.addEventListener('input', (e) => {
        filterProducts(e.target.value);
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

