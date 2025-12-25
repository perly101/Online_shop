document.addEventListener('DOMContentLoaded', () => {
    // ============================================
    // ORDER DATA
    // ============================================
    const orders = [
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
        return `Php.${amount.toLocaleString()}`;
    }

    /**
     * Renders order history list
     */
    function renderOrderHistory() {
        orderHistoryList.innerHTML = '';

        orders.forEach(order => {
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
            
            // Add click handler for "View Details"
            const viewDetailsBtn = item.querySelector('.order-history-view');
            viewDetailsBtn.addEventListener('click', () => {
                // Navigate to order detail or show modal
                showPopup(`Viewing details for Order ${order.id}`);
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
            
            // Add click handler for "View Details"
            const viewDetailsBtn = item.querySelector('.order-history-view');
            viewDetailsBtn.addEventListener('click', () => {
                // Navigate to order detail or show modal
                showPopup(`Viewing details for Order ${order.id}`);
            });
            
            orderHistoryList.appendChild(item);
        });
    }

    // ============================================
    // INITIALIZATION
    // ============================================
    
    renderOrderHistory();
    
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