document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in
    const loggedInStaff = localStorage.getItem('loggedInStaff');
    if (!loggedInStaff) {
        // Redirect to login page if not logged in
        window.location.href = '/admin/login';
        return;
    }
    // ============================================
    // ORDER DATA
    // ============================================
    // Load order history from localStorage if available, otherwise use default data
    let orders = [];
    loadOrderHistory();
    console.log('Initial orders loaded:', orders);
    
    /**
     * Load order history from localStorage
     */
    function loadOrderHistory() {
        console.log('Loading order history from localStorage');
        const storedHistory = localStorage.getItem('orderHistory');
        console.log('Stored history from localStorage:', storedHistory);
        if (storedHistory) {
            try {
                orders = JSON.parse(storedHistory);
                console.log('Parsed orders:', orders);
                // Log the structure of the first order if available
                if (orders.length > 0) {
                    console.log('First order structure:', orders[0]);
                    console.log('First order keys:', Object.keys(orders[0]));
                }
            } catch (e) {
                console.error('Error parsing stored order history:', e);
                // Fallback to default data if parsing fails
                orders = [
                    {
                        id: 'AET-2025-001',
                        date: '10/05/25',
                        time: '9:10 am',
                        status: 'Delivered',
                        price: 150
                    },
                    {
                        id: 'AET-2025-002',
                        date: '6/20/25',
                        time: '11:30 pm',
                        status: 'Delivered',
                        price: 300
                    },
                    {
                        id: 'AET-2025-003',
                        date: '4/16/25',
                        time: '9:00 am',
                        status: 'Delivered',
                        price: 14
                    },
                    {
                        id: 'AET-2025-004',
                        date: '2/05/25',
                        time: '3:00 pm',
                        status: 'Canceled',
                        price: 352
                    }
                ];
            }
        } else {
            console.log('No stored history found, using default data');
            // Default data if no stored data
            orders = [
                {
                    id: 'AET-2025-001',
                    date: '10/05/25',
                    time: '9:10 am',
                    status: 'Delivered',
                    price: 150
                },
                {
                    id: 'AET-2025-002',
                    date: '6/20/25',
                    time: '11:30 pm',
                    status: 'Delivered',
                    price: 300
                },
                {
                    id: 'AET-2025-003',
                    date: '4/16/25',
                    time: '9:00 am',
                    status: 'Delivered',
                    price: 14
                },
                {
                    id: 'AET-2025-004',
                    date: '2/05/25',
                    time: '3:00 pm',
                    status: 'Canceled',
                    price: 352
                }
            ];
        }
        console.log('Final orders array:', orders);
        // Log the structure of the first order if available
        if (orders.length > 0) {
            console.log('Final first order structure:', orders[0]);
            console.log('Final first order keys:', Object.keys(orders[0]));
        }
    }

    // Get userId from URL if available
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('userId');

    // ============================================
    // DOM ELEMENTS
    // ============================================
    const orderHistoryList = document.getElementById('order-history-list');
    const orderSearch = document.getElementById('order-search');
    const searchIcon = document.getElementById('search-icon');

    // ============================================
    // FUNCTIONS
    // ============================================

    /**
     * Formats currency
     */
    function formatCurrency(amount) {
        return `₱ ${amount.toLocaleString()}`;
    }

    /**
     * Renders order history list
     */
    function renderOrderHistory() {
        console.log('Rendering order history, orders count:', orders.length);
        console.log('Orders data:', orders);
        // Log the structure of the first order if available
        if (orders.length > 0) {
            console.log('Rendering first order structure:', orders[0]);
            console.log('Rendering first order keys:', Object.keys(orders[0]));
        }
        orderHistoryList.innerHTML = '';

        orders.forEach(order => {
            console.log('Rendering order:', order);
            const item = document.createElement('div');
            item.className = 'order-history-item';
            
            const statusClass = order.status === 'Delivered' ? 'status-delivered' : 'status-canceled';
            
            item.innerHTML = `
                <div class="order-history-left">
                    <div class="order-history-id">${order.id}</div>
                    <div class="order-history-date">${order.date}</div>
                    <div class="order-history-time">${order.time}</div>
                </div>
                <div class="order-history-right">
                    <span class="order-history-status ${statusClass}">${order.status}</span>
                    <span class="order-history-view" data-order-id="${order.id}">View Details</span>
                    <div class="order-history-price">${formatCurrency(order.price)}</div>
                </div>
            `;
            
            // Add click handler for "View Details" - use event delegation to avoid duplicates
            item.addEventListener('click', (event) => {
                if (event.target.classList.contains('order-history-view')) {
                    console.log('View details clicked for order:', order);
                    // Show order details modal
                    showOrderDetails(order);
                }
            });
            
            orderHistoryList.appendChild(item);
        });
    }

    /**
     * Filters orders based on search term
     */
    function filterOrders(searchTerm) {
        if (!searchTerm) {
            // If no search term, show all orders
            renderOrderHistory();
            return;
        }
        
        // Normalize the search term
        const normalizedSearch = searchTerm.trim();
        
        // Filter orders based on ID
        const filteredOrders = orders.filter(order => {
            // Convert order ID to string
            const orderIdStr = order.id.toString();
            
            // Check if the search term matches the order ID
            return orderIdStr.includes(normalizedSearch);
        });
        
        renderFilteredOrders(filteredOrders);
    }
    
    /**
     * Shows order details in a modal
     * @param {object} order - The order data object.
     */
    function showOrderDetails(order) {
        console.log('Show order details called with:', order);
        console.log('Order has items array:', !!order.items);
        
        // Get the existing modal from the HTML
        const orderModal = document.getElementById('order-modal');
        
        // Check if this is a full order object (from localStorage) or a simplified one (from orderHistory)
        if (order.items) {
            // This is a full order object with items array
            console.log('Showing full order details directly');
            showFullOrderDetails(order);
        } else {
            // This is a simplified order object from orderHistory - try to find the full order
            console.log('Looking up historical order details');
            showHistoricalOrderDetails(order);
        }
    }
    
    /**
     * Shows full order details with items
     * @param {object} order - The full order data object with items.
     */
    function showFullOrderDetails(order) {
        // Make sure event listeners are added
        addModalEventListeners();
        
        // Update customer email
        document.getElementById('modal-customer-email').textContent = order.id;
        
        // Update date and time
        document.getElementById('modal-date').textContent = order.date;
        document.getElementById('modal-time').textContent = order.time;
        
        // Clear and populate items list
        const itemsList = document.getElementById('modal-items-list');
        itemsList.innerHTML = '';

        // Load current products from localStorage to get latest product info
        let currentProducts = [];
        try {
            const storedProducts = localStorage.getItem('inventoryProducts');
            if (storedProducts) {
                currentProducts = JSON.parse(storedProducts);
            }
        } catch (e) {
            console.error('Error loading products from localStorage:', e);
        }

        // Calculate the correct total based on current product prices
        let calculatedTotal = 0;

        order.items.forEach(item => {
            // Find matching product in inventory to get current name, image, and price
            let productName = item.name;
            let productImage = item.image || 'image/placeholder.png';
            let productPrice = item.price; // Default to order price if product not found
            
            // Look for product by name (assuming product names are unique)
            const matchingProduct = currentProducts.find(product => product.name === item.name);
            if (matchingProduct) {
                productName = matchingProduct.name;
                productImage = matchingProduct.image || 'image/placeholder.png';
                productPrice = matchingProduct.price; // Use current product price
            }
            
            // Calculate line total: quantity × unit price
            const lineTotal = item.quantity * productPrice;
            calculatedTotal += lineTotal;
            
            // Format the line total for display
            const formattedLineTotal = lineTotal.toLocaleString();
            
            const itemElement = document.createElement('div');
            itemElement.className = 'item-row';
            
            const img = document.createElement('img');
            img.src = productImage;
            img.alt = productName;
            img.addEventListener('error', () => {
                img.src = 'image/placeholder.png';
            });
            
            const detailsDiv = document.createElement('div');
            detailsDiv.className = 'item-details';
            detailsDiv.textContent = productName;
            
            const priceDiv = document.createElement('div');
            priceDiv.className = 'item-quantity-price';
            priceDiv.innerHTML = `
                <span class="quantity">x${item.quantity}</span>
                <span class="price">₱ ${formattedLineTotal}</span>
            `;
            
            itemElement.appendChild(img);
            itemElement.appendChild(detailsDiv);
            itemElement.appendChild(priceDiv);
            itemsList.appendChild(itemElement);
        });

        // Update total price with the calculated total
        document.getElementById('modal-total-price').textContent = `₱ ${calculatedTotal.toLocaleString()}`;
        
        // Show modal
        const orderModal = document.getElementById('order-modal');
        orderModal.style.display = "block";
        document.body.style.overflow = 'hidden';
    }
    
    /**
     * Shows historical order details by finding the full order data
     * @param {object} order - The simplified order data object.
     */
    function showHistoricalOrderDetails(order) {
        console.log('Looking up historical order:', order);
        
        // Make sure event listeners are added
        addModalEventListeners();
        
        // Try to find the full order data from localStorage
        let fullOrders = [];
        try {
            const storedOrders = localStorage.getItem('ordersData');
            console.log('Raw ordersData from localStorage:', storedOrders);
            if (storedOrders) {
                fullOrders = JSON.parse(storedOrders);
                console.log('Parsed ordersData:', fullOrders);
            }
        } catch (e) {
            console.error('Error loading full orders from localStorage:', e);
        }
        
        // Find the matching full order
        const fullOrder = fullOrders.find(o => o.id === order.id);
        console.log('Found full order in ordersData:', fullOrder);
        
        if (fullOrder) {
            // Show the full order details
            showFullOrderDetails(fullOrder);
        } else {
            // If we can't find it in ordersData, try to find it in orderHistory with items
            // This might happen if the order was moved to history but still has full data
            let orderHistory = [];
            try {
                const storedHistory = localStorage.getItem('orderHistory');
                console.log('Raw orderHistory from localStorage:', storedHistory);
                if (storedHistory) {
                    orderHistory = JSON.parse(storedHistory);
                    console.log('Parsed orderHistory:', orderHistory);
                }
            } catch (e) {
                console.error('Error loading order history from localStorage:', e);
            }
            
            // Look for a full order in history (just in case)
            const fullHistoryOrder = orderHistory.find(o => o.id === order.id && o.items);
            console.log('Found full order in orderHistory:', fullHistoryOrder);
            
            if (fullHistoryOrder) {
                showFullOrderDetails(fullHistoryOrder);
            } else {
                // Fall back to basic information
                document.getElementById('modal-customer-email').textContent = order.id;
                document.getElementById('modal-date').textContent = order.date;
                document.getElementById('modal-time').textContent = order.time;
                document.getElementById('modal-total-price').textContent = formatCurrency(order.price);
                
                // Populate items list with a message indicating limited information
                const itemsList = document.getElementById('modal-items-list');
                itemsList.innerHTML = '<p>Item details are not available for this historical order.</p><p>This order was completed on ' + order.date + ' at ' + order.time + '.</p>';
                
                // Show modal
                const orderModal = document.getElementById('order-modal');
                orderModal.style.display = "block";
                document.body.style.overflow = 'hidden';
            }
        }
    }
    
    /**
     * Renders filtered orders
     */
    function renderFilteredOrders(filteredOrders) {
        orderHistoryList.innerHTML = '';
        
        if (filteredOrders.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.textContent = 'No orders found matching your search.';
            noResults.style.textAlign = 'center';
            noResults.style.padding = '20px';
            noResults.style.color = '#666';
            orderHistoryList.appendChild(noResults);
            return;
        }
        
        filteredOrders.forEach(order => {
            const item = document.createElement('div');
            item.className = 'order-history-item';
            
            const statusClass = order.status === 'Delivered' ? 'status-delivered' : 'status-canceled';
            
            item.innerHTML = `
                <div class="order-history-left">
                    <div class="order-history-id">${order.id}</div>
                    <div class="order-history-date">${order.date}</div>
                    <div class="order-history-time">${order.time}</div>
                </div>
                <div class="order-history-right">
                    <span class="order-history-status ${statusClass}">${order.status}</span>
                    <span class="order-history-view" data-order-id="${order.id}">View Details</span>
                    <div class="order-history-price">${formatCurrency(order.price)}</div>
                </div>
            `;
            
            // Add click handler for "View Details" - use event delegation to avoid duplicates
            item.addEventListener('click', (event) => {
                if (event.target.classList.contains('order-history-view')) {
                    // Show order details modal
                    showOrderDetails(order);
                }
            });
            
            orderHistoryList.appendChild(item);
        });
    }

    // ============================================
    // INITIALIZATION
    // ============================================
    
    // Flag to track if event listeners have been added
    let modalEventListenersAdded = false;
    
    /**
     * Updates the order count badge in the navigation
     */
    function updateOrderBadge() {
        const badgeElement = document.getElementById('orders-badge');
        if (badgeElement) {
            // For order history, we want to show all orders (both delivered and canceled)
            const orderCount = orders.length;
            badgeElement.textContent = orderCount;
        }
    }
    
    // Function to add event listeners for the modal
    function addModalEventListeners() {
        if (modalEventListenersAdded) return;
        
        const orderModal = document.getElementById('order-modal');
        if (orderModal) {
            const closeBtn = orderModal.querySelector('.close-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    orderModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                });
            }
            
            // Close modal when clicking outside
            orderModal.addEventListener('click', (event) => {
                if (event.target === orderModal) {
                    orderModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
            
            // Close modal with Escape key
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && orderModal.style.display === 'block') {
                    orderModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
            
            modalEventListenersAdded = true;
        }
    }
    
    // Add event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        addModalEventListeners();
    });
    
    renderOrderHistory();
    updateOrderBadge(); // Update the order count badge
    
    // Refresh order history periodically to catch updates
    setInterval(() => {
        const oldOrdersStr = JSON.stringify(orders);
        loadOrderHistory();
        const newOrdersStr = JSON.stringify(orders);
        // Only update if the data has changed
        if (oldOrdersStr !== newOrdersStr) {
            console.log('Order history changed, re-rendering');
            renderOrderHistory();
            updateOrderBadge(); // Update the order count badge
        }
    }, 5000); // Check every 5 seconds
    
    // Search functionality - attach after DOM is fully loaded
    setTimeout(() => {
        if (orderSearch && searchIcon) {
            orderSearch.addEventListener('input', (e) => {
                filterOrders(e.target.value);
            });
            
            searchIcon.addEventListener('click', () => {
                filterOrders(orderSearch.value);
            });
            
            // Allow Enter key to trigger search
            orderSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    filterOrders(orderSearch.value);
                }
            });
        }
    }, 100);
});