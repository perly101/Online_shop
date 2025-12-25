document.addEventListener('DOMContentLoaded', () => {
    // ============================================
    // DUMMY DATA FOR ORDERS
    // ============================================
    // Load orders from localStorage if available, otherwise use default data
    let ordersData = [];
    const storedOrders = localStorage.getItem('ordersData');
    console.log('Loading orders from localStorage:', storedOrders);
    if (storedOrders) {
        try {
            ordersData = JSON.parse(storedOrders);
            console.log('Loaded orders data:', ordersData);
        } catch (e) {
            console.error('Error parsing stored orders data:', e);
            // Fallback to default data if parsing fails
            ordersData = [
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
        ordersData = [
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

    // ============================================
    // DOM ELEMENTS
    // ============================================
    const ordersList = document.getElementById('recent-orders-list');
    const modal = document.getElementById('order-modal');
    const closeBtn = document.querySelector('.close-btn');
    const orderIdInput = document.getElementById('order-id'); // Changed from 'order-search'
    const searchIcon = document.getElementById('search-icon');
    const searchBtn = document.querySelector('.search-btn');
    const verificationOrderIdInput = document.getElementById('order-id-verification');
    const scanBtn = document.querySelector('.scan-btn');
    
    // Check if required elements exist
    if (!ordersList || !modal || !closeBtn) {
        console.error('Required DOM elements not found. Make sure the HTML structure is correct.');
        return;
    }

    // ============================================
    // FUNCTIONS
    // ============================================

    /**
     * Formats date from "10/8/2025" to "10/08/25"
     */
    function formatDate(dateString) {
        const parts = dateString.split('/');
        if (parts.length === 3) {
            const month = parts[0].padStart(2, '0');
            const day = parts[1].padStart(2, '0');
            const year = parts[2].slice(-2);
            return `${month}/${day}/${year}`;
        }
        return dateString;
    }

    /**
     * Formats time to lowercase am/pm
     */
    function formatTime(timeString) {
        return timeString.toLowerCase();
    }

    /**
     * Renders the list of recent orders based on the data array.
     */
    function renderOrders() {
        ordersList.innerHTML = ''; // Clear existing list

        ordersData.forEach((order, index) => {
            const orderItem = document.createElement('div');
            orderItem.className = 'order-item';
            
            // Generate list item HTML
            orderItem.innerHTML = `
                <div class="order-detail">
                    <strong>Order ID:</strong> ${order.id}
                </div>
                <div class="order-detail">
                    <strong>Customer:</strong> ${order.customerEmail}
                </div>
                <div class="order-detail">
                    <strong>Total:</strong> ₱${order.total.toLocaleString()}
                </div>
                <div class="order-detail">
                    <strong>Placed At:</strong> ${order.date}, ${order.time}
                </div>
                <div class="order-actions">
                    <a href="#" class="view-orders-btn" data-order-id="${order.id}">View Orders</a>
                    <div class="update-status">
                        <span>Update Status:</span>
                        <select class="status-dropdown" data-order-id="${order.id}">
                            <option value="Processing" ${order.status === 'Processing' ? 'selected' : ''}>Processing</option>
                            <option value="Pending" ${order.status === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Ready for Pickup" ${order.status === 'Ready for Pickup' ? 'selected' : ''}>Ready for Pickup</option>
                            <option value="Complete" ${order.status === 'Complete' ? 'selected' : ''}>Complete</option>
                            <option value="Cancel" ${order.status === 'Cancel' ? 'selected' : ''}>Cancel</option>
                        </select>
                    </div>
                </div>
            `;
            ordersList.appendChild(orderItem);
        });

        // Attach event listeners for the "View Orders" buttons
        document.querySelectorAll('.view-orders-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const orderId = e.currentTarget.getAttribute('data-order-id');
                if (orderId) {
                    const order = ordersData.find(o => o.id === orderId);
                    if (order) {
                        showOrderDetails(order);
                    } else {
                        console.error('Order not found:', orderId);
                    }
                } else {
                    console.error('Invalid order ID');
                }
            });
        });

        // Attach event listeners for status dropdowns
        document.querySelectorAll('.status-dropdown').forEach(dropdown => {
            dropdown.addEventListener('change', (e) => {
                const orderId = e.target.getAttribute('data-order-id');
                if (orderId) {
                    const order = ordersData.find(o => o.id === orderId);
                    if (order) {
                        const orderIndex = ordersData.findIndex(o => o.id === orderId);
                        const newStatus = e.target.value;
                        const oldStatus = order.status;
                        
                        // Show confirmation modal for all status changes
                        showStatusChangeConfirmation(order, orderIndex, newStatus);
                    } else {
                        console.error('Order not found:', orderId);
                    }
                } else {
                    console.error('Invalid order ID');
                }
            });
        });
    }

    /**
     * Filters orders based on search term and reorders the list to show matches first
     * Exact matches are prioritized at the top
     */
    function filterOrders(searchTerm) {
        if (!searchTerm) {
            // If no search term, show all orders in original order
            renderOrders();
            return;
        }
        
        // Normalize the search term
        const normalizedSearch = searchTerm.trim().toUpperCase();
        
        // Create a copy of ordersData to avoid modifying the original
        const ordersCopy = [...ordersData];
        
        // Sort orders to prioritize matches
        ordersCopy.sort((a, b) => {
            const idA = a.id.toUpperCase();
            const idB = b.id.toUpperCase();
            
            // Check for exact matches
            const isExactMatchA = idA === normalizedSearch;
            const isExactMatchB = idB === normalizedSearch;
            
            // If one is exact match and other isn't, prioritize exact match
            if (isExactMatchA && !isExactMatchB) return -1;
            if (!isExactMatchA && isExactMatchB) return 1;
            
            // Check for partial matches
            const isPartialMatchA = idA.includes(normalizedSearch);
            const isPartialMatchB = idB.includes(normalizedSearch);
            
            // If one is partial match and other isn't, prioritize partial match
            if (isPartialMatchA && !isPartialMatchB) return -1;
            if (!isPartialMatchA && isPartialMatchB) return 1;
            
            // If both are matches or both are not matches, maintain original order
            return 0;
        });
        
        // Filter to only show matching orders
        const filteredOrders = ordersCopy.filter(order => {
            const orderIdStr = order.id.toString().toUpperCase();
            return orderIdStr.includes(normalizedSearch);
        });
        
        renderFilteredOrders(filteredOrders);
    }
    
    /**
     * Renders filtered orders
     */
    function renderFilteredOrders(filteredOrders) {
        ordersList.innerHTML = '';
        
        if (filteredOrders.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.textContent = 'No orders found matching your search.';
            noResults.style.textAlign = 'center';
            noResults.style.padding = '20px';
            noResults.style.color = '#666';
            ordersList.appendChild(noResults);
            return;
        }
        
        filteredOrders.forEach((order, index) => {
            const orderItem = document.createElement('div');
            orderItem.className = 'order-item';
            
            // Generate list item HTML
            orderItem.innerHTML = `
                <div class="order-detail">
                    <strong>Order ID:</strong> ${order.id}
                </div>
                <div class="order-detail">
                    <strong>Customer:</strong> ${order.customerEmail}
                </div>
                <div class="order-detail">
                    <strong>Total:</strong> ₱${order.total.toLocaleString()}
                </div>
                <div class="order-detail">
                    <strong>Placed At:</strong> ${order.date}, ${order.time}
                </div>
                <div class="order-actions">
                    <a href="#" class="view-orders-btn" data-order-id="${order.id}">View Orders</a>
                    <div class="update-status">
                        <span>Update Status:</span>
                        <select class="status-dropdown" data-order-id="${order.id}">
                            <option value="Processing" ${order.status === 'Processing' ? 'selected' : ''}>Processing</option>
                            <option value="Pending" ${order.status === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Ready for Pickup" ${order.status === 'Ready for Pickup' ? 'selected' : ''}>Ready for Pickup</option>
                            <option value="Complete" ${order.status === 'Complete' ? 'selected' : ''}>Complete</option>
                            <option value="Cancel" ${order.status === 'Cancel' ? 'selected' : ''}>Cancel</option>
                        </select>
                    </div>
                </div>
            `;
            ordersList.appendChild(orderItem);
        });
        
        // Attach event listeners for the "View Orders" buttons
        document.querySelectorAll('.view-orders-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const orderId = e.currentTarget.getAttribute('data-order-id');
                if (orderId) {
                    const order = ordersData.find(o => o.id === orderId);
                    if (order) {
                        showOrderDetails(order);
                    } else {
                        console.error('Order not found:', orderId);
                    }
                } else {
                    console.error('Invalid order ID');
                }
            });
        });
        
        // Attach event listeners for status dropdowns
        document.querySelectorAll('.status-dropdown').forEach(dropdown => {
            dropdown.addEventListener('change', (e) => {
                const orderId = e.target.getAttribute('data-order-id');
                if (orderId) {
                    const order = ordersData.find(o => o.id === orderId);
                    if (order) {
                        const orderIndex = ordersData.findIndex(o => o.id === orderId);
                        const newStatus = e.target.value;
                        const oldStatus = order.status;
                        
                        // Show confirmation modal for all status changes
                        showStatusChangeConfirmation(order, orderIndex, newStatus);
                    } else {
                        console.error('Order not found:', orderId);
                    }
                } else {
                    console.error('Invalid order ID');
                }
            });
        });
    }
    
    /**
     * Shows confirmation modal for sending notification when changing status to Ready for Pickup
     * @param {object} order - The order data object.
     * @param {number} index - The index of the order in the ordersData array.
     */
    function showNotificationConfirmation(order, index) {
        // This function is kept for backward compatibility
        showStatusChangeConfirmation(order, index, 'Ready for Pickup');
    }
    
    /**
     * Shows confirmation modal for status change
     * @param {object} order - The order data object.
     * @param {number} index - The index of the order in the ordersData array.
     * @param {string} newStatus - The new status to be set.
     */
    function showStatusChangeConfirmation(order, index, newStatus) {
        // Create modal if it doesn't exist
        let notificationModal = document.getElementById('notification-modal');
        if (!notificationModal) {
            notificationModal = document.createElement('div');
            notificationModal.id = 'notification-modal';
            notificationModal.className = 'modal';
            notificationModal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Send Notification to Customer</h3>
                    </div>
                    <div class="modal-body">
                        <p id="status-message"></p>
                        <div class="form-actions">
                            <button class="btn-cancel-edit" id="cancel-notification-send">Cancel</button>
                            <button class="btn-add-product" id="send-notification-to-customer">Send</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(notificationModal);
        }
        
        // Set the appropriate message based on the new status
        const messageElement = notificationModal.querySelector('#status-message');
        switch(newStatus) {
            case 'Ready for Pickup':
                messageElement.textContent = `Your order #${order.id} is ready for pick-up`;
                break;
            case 'Cancel':
                messageElement.textContent = `Your order #${order.id} is cancelled`;
                break;
            case 'Processing':
                messageElement.textContent = `Your order #${order.id} is now on processing`;
                break;
            case 'Pending':
                messageElement.textContent = `Your order #${order.id} is in pending`;
                break;
            case 'Complete':
                messageElement.textContent = `Your order #${order.id} is successfully received!`;
                break;
            default:
                messageElement.textContent = `Your order #${order.id} status changed to ${newStatus}`;
        }
        
        // Remove existing event listeners to avoid duplicates
        const cancelBtn = document.getElementById('cancel-notification-send');
        const sendBtn = document.getElementById('send-notification-to-customer');
        
        // Create new event listeners
        const cancelHandler = () => {
            notificationModal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
            // Reset the dropdown to the previous value
            const dropdown = document.querySelector(`.status-dropdown[data-order-id="${order.id}"]`);
            if (dropdown) {
                dropdown.value = ordersData[index].status; // Reset to previous status
            }
            // Remove event listeners
            cancelBtn.removeEventListener('click', cancelHandler);
            sendBtn.removeEventListener('click', sendHandler);
        };
        
        const sendHandler = () => {
            // Update the order status
            ordersData[index].status = newStatus;
            
            // Add completion date if status is Complete
            if (newStatus === 'Complete') {
                ordersData[index].completionDate = new Date().toLocaleDateString('en-US'); // Format: MM/DD/YYYY
            }
            
            // Save updated orders to localStorage
            console.log('Saving orders to localStorage:', ordersData);
            localStorage.setItem('ordersData', JSON.stringify(ordersData));
            console.log('Order status updated and saved to localStorage:', ordersData[index]);
            
            // Update the dropdown to reflect the new status
            const dropdown = document.querySelector(`.status-dropdown[data-order-id="${order.id}"]`);
            if (dropdown) {
                dropdown.value = newStatus; // Set to new status
            }
            
            // Send notification (simulated)
            sendNotificationForStatusChange(order, newStatus);
            
            // Close modal
            notificationModal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
            
            // Remove event listeners
            cancelBtn.removeEventListener('click', cancelHandler);
            sendBtn.removeEventListener('click', sendHandler);
        };
        
        // Add event listeners
        cancelBtn.addEventListener('click', cancelHandler);
        sendBtn.addEventListener('click', sendHandler);
        
        // Show the modal
        notificationModal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
    
    /**
     * Simulates sending notification to customer for status change
     * @param {object} order - The order data object.
     * @param {string} newStatus - The new status of the order.
     */
    function sendNotificationForStatusChange(order, newStatus) {
        // Create a customer-facing notification and store it for Messages overlay
        try {
            // Build message based on status
            let message = '';
            switch(newStatus) {
                case 'Ready for Pickup':
                    message = `Your order ${order.id} is ready for pickup! Please proceed to the store.`;
                    break;
                case 'Cancel':
                    message = `Your order ${order.id} has been cancelled.`;
                    break;
                case 'Processing':
                    message = `Your order ${order.id} is now being processed.`;
                    break;
                case 'Pending':
                    message = `Your order ${order.id} is now pending.`;
                    break;
                case 'Complete':
                    message = `Your order ${order.id} has been successfully completed!`;
                    break;
                default:
                    message = `Your order ${order.id} status has been updated to: ${newStatus}`;
            }

            const raw = localStorage.getItem('notifications') || '[]';
            let notifications = [];
            try { notifications = JSON.parse(raw); } catch { notifications = []; }
            const type = (newStatus === 'Complete' || newStatus === 'Ready for Pickup') ? 'success'
                        : (newStatus === 'Cancel' ? 'warning' : 'info');
            const notif = {
                id: Date.now(),
                orderId: order.id,
                message: message,
                type: type,
                read: false,
                createdAt: new Date().toISOString()
            };
            notifications.unshift(notif);
            if (notifications.length > 100) notifications.splice(100);
            localStorage.setItem('notifications', JSON.stringify(notifications));
        } catch (e) {
            console.warn('Error creating customer notification:', e);
        }
        // Log for debugging
        console.log(`Notification stored for customer ${order.customerEmail} for order status change to: ${newStatus}`);
    }
    
    /**
     * Simulates sending notification to customer
     * @param {object} order - The order data object.
     */
    function sendNotificationToCustomer(order) {
        // In a real application, this would make an API call to send the notification
        // For now, we'll just log it and not show any alert
        console.log(`Notification sent to customer for order: ${order.customerEmail}`);
        // No alert is shown as per requirements
    }
    
    /**
     * Populates and displays the modal with specific order details.
     * @param {object} order - The order data object.
     */
    function showOrderDetails(order) {
        // Update customer email
        document.getElementById('modal-customer-email').textContent = order.customerEmail;
        
        // Update date and time (formatted)
        const formattedDate = formatDate(order.date);
        const formattedTime = formatTime(order.time);
        document.getElementById('modal-date').textContent = formattedDate;
        document.getElementById('modal-time').textContent = formattedTime;
        
        // Update total price
        document.getElementById('modal-total-price').textContent = `Php. ${order.total.toLocaleString()}`;
        
        // Clear and populate items list
        const itemsList = document.getElementById('modal-items-list');
        itemsList.innerHTML = '';

        order.items.forEach(item => {
            // Use placeholder if no image provided
            const imgSrc = item.image || 'image/placeholder.png';
            const itemPrice = (item.price * item.quantity).toLocaleString();
            
            const itemElement = document.createElement('div');
            itemElement.className = 'item-row';
            
            const img = document.createElement('img');
            img.src = imgSrc;
            img.alt = item.name;
            img.addEventListener('error', () => {
                img.src = 'image/placeholder.png';
            });
            
            const detailsDiv = document.createElement('div');
            detailsDiv.className = 'item-details';
            detailsDiv.textContent = item.name;
            
            const priceDiv = document.createElement('div');
            priceDiv.className = 'item-quantity-price';
            priceDiv.innerHTML = `
                <span class="quantity">x${item.quantity}</span>
                <span class="price">Php.${itemPrice}</span>
            `;
            
            itemElement.appendChild(img);
            itemElement.appendChild(detailsDiv);
            itemElement.appendChild(priceDiv);
            itemsList.appendChild(itemElement);
        });

        // Show modal
        modal.style.display = "block";
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    /**
     * Closes the modal
     */
    function closeModal() {
        modal.style.display = "none";
        document.body.style.overflow = 'auto'; // Restore scrolling
    }

    /**
     * Updates the order count badge in the navigation
     */
    function updateOrderBadge() {
        const badgeElements = document.querySelectorAll('.badge');
        const orderCount = ordersData.length;
        
        badgeElements.forEach(badge => {
            badge.textContent = orderCount;
        });
    }

    // ============================================
    // EVENT LISTENERS
    // ============================================
    
    // Close modal when the back arrow button is clicked
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    // Close modal when user clicks outside of the modal
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
    
    // Search functionality - attach after DOM is fully loaded
    setTimeout(() => {
        if (orderIdInput) {
            // Search when typing in the input
            orderIdInput.addEventListener('input', (e) => {
                filterOrders(e.target.value);
            });
            
            // Search when pressing Enter key
            orderIdInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    filterOrders(orderIdInput.value);
                    // Prevent form submission if it's inside a form
                    e.preventDefault();
                }
            });
        }
        
        // Search when clicking search icon
        if (searchIcon && orderIdInput) {
            searchIcon.addEventListener('click', () => {
                filterOrders(orderIdInput.value);
            });
        }
        
        // Search when clicking search button
        if (searchBtn && orderIdInput) {
            searchBtn.addEventListener('click', () => {
                filterOrders(orderIdInput.value);
            });
        }
        
        // Search functionality for verification search bar
        if (verificationOrderIdInput) {
            // Search when typing in the verification input
            verificationOrderIdInput.addEventListener('input', (e) => {
                filterOrders(e.target.value);
            });
            
            // Search when pressing Enter key in verification input
            verificationOrderIdInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    filterOrders(verificationOrderIdInput.value);
                    // Prevent form submission if it's inside a form
                    e.preventDefault();
                }
            });
        }
        
        // Scan button functionality
        if (scanBtn) {
            scanBtn.addEventListener('click', () => {
                showPopup('QR Code scanning functionality would be implemented here.');
            });
        }
    }, 200);
    // ============================================
    // INITIALIZATION
    // ============================================
    
    // Initial render
    renderOrders();
    updateOrderBadge();
});