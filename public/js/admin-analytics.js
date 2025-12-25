document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in
    const loggedInStaff = localStorage.getItem('loggedInStaff');
    if (!loggedInStaff) {
        // Redirect to login page if not logged in
        window.location.href = '/admin/login';
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

    // Sample order data for analytics
    // Load orders from localStorage if available, otherwise use default data
    let orders = [];
    const storedOrders = localStorage.getItem('ordersData');
    console.log('Stored orders in localStorage:', storedOrders);
    if (storedOrders) {
        try {
            orders = JSON.parse(storedOrders);
            console.log('Parsed orders data:', orders);
        } catch (e) {
            console.error('Error parsing stored orders data:', e);
            // Fallback to default data if parsing fails
            orders = [
                {
                    id: "AET-2025-001",
                    date: "2025-11-01",
                    status: "Delivered",
                    total: 150,
                    items: [
                        { name: "Nescafe Original Twin pack 7 units", quantity: 1, price: 150 }
                    ]
                },
                {
                    id: "AET-2025-002",
                    date: "2025-11-02",
                    status: "Delivered",
                    total: 300,
                    items: [
                        { name: "Coke 290ml 15 units", quantity: 1, price: 310 }
                    ]
                },
                {
                    id: "AET-2025-003",
                    date: "2025-11-03",
                    status: "Delivered",
                    total: 14,
                    items: [
                        { name: "Mega Sardines", quantity: 1, price: 20 }
                    ]
                },
                {
                    id: "AET-2025-004",
                    date: "2025-11-04",
                    status: "Processing",
                    total: 352,
                    items: [
                        { name: "Surf 65g 10 units", quantity: 2, price: 7 },
                        { name: "Pancit Canton", quantity: 1, price: 230 }
                    ]
                },
                {
                    id: "AET-2025-005",
                    date: "2025-10-25",
                    status: "Delivered",
                    total: 250,
                    items: [
                        { name: "Nescafe Original Twin pack 7 units", quantity: 1, price: 150 },
                        { name: "Coke 290ml 15 units", quantity: 1, price: 100 }
                    ]
                },
                {
                    id: "AET-2025-006",
                    date: "2025-10-15",
                    status: "Delivered",
                    total: 180,
                    items: [
                        { name: "Surf 65g 10 units", quantity: 3, price: 7 },
                        { name: "Bear brand 320g", quantity: 1, price: 150 }
                    ]
                }
            ];
        }
    } else {
        // Default data if no stored data
        orders = [
            {
                id: "AET-2025-001",
                date: "2025-11-01",
                status: "Delivered",
                total: 150,
                items: [
                    { name: "Nescafe Original Twin pack 7 units", quantity: 1, price: 150 }
                ]
            },
            {
                id: "AET-2025-002",
                date: "2025-11-02",
                status: "Delivered",
                total: 300,
                items: [
                    { name: "Coke 290ml 15 units", quantity: 1, price: 310 }
                ]
            },
            {
                id: "AET-2025-003",
                date: "2025-11-03",
                status: "Delivered",
                total: 14,
                items: [
                    { name: "Mega Sardines", quantity: 1, price: 20 }
                ]
            },
            {
                id: "AET-2025-004",
                date: "2025-11-04",
                status: "Processing",
                total: 352,
                items: [
                    { name: "Surf 65g 10 units", quantity: 2, price: 7 },
                    { name: "Pancit Canton", quantity: 1, price: 230 }
                ]
            },
            {
                id: "AET-2025-005",
                date: "2025-10-25",
                status: "Delivered",
                total: 250,
                items: [
                    { name: "Nescafe Original Twin pack 7 units", quantity: 1, price: 150 },
                    { name: "Coke 290ml 15 units", quantity: 1, price: 100 }
                ]
            },
            {
                id: "AET-2025-006",
                date: "2025-10-15",
                status: "Delivered",
                total: 180,
                items: [
                    { name: "Surf 65g 10 units", quantity: 3, price: 7 },
                    { name: "Bear brand 320g", quantity: 1, price: 150 }
                ]
            }
        ];
    }

    // Top Popular Products Data - sorted by sold count (highest first)
    let popularProducts = [
        { name: "Coke 290ml 15 units", sold: 15 },
        { name: "Surf 65g 10 units", sold: 10 },
        { name: "Nescafe Original Twin pack 7 units", sold: 7 }
    ].sort((a, b) => b.sold - a.sold); // Ensure descending order by sold count

    // Recent Customer Feedback Data
    let customerFeedback = [
        {
            user: "altheacatubig@gmail.com",
            feedback: "Great service, easy pickup process!",
            date: "10/8/2025",
            rating: 5
        },
        {
            user: "eusan A.",
            feedback: "all goods akoa gipang order complete",
            date: "10/29/2025",
            rating: 5
        },
        {
            user: "Anjie b.",
            feedback: "Fast delivery and good quality products!",
            date: "11/01/2025",
            rating: 4
        },
        {
            user: "JALLY BENORES",
            feedback: "Excellent customer service!",
            date: "11/03/2025",
            rating: 5
        }
    ];
    
    // Store previous service reviews to detect changes
    let previousServiceReviews = '';
    
    // Load service reviews from localStorage
    function loadServiceReviews() {
        try {
            const serviceReviewsRaw = localStorage.getItem('serviceReviews');
            // Only process if there are changes
            if (serviceReviewsRaw !== previousServiceReviews) {
                previousServiceReviews = serviceReviewsRaw;
                if (serviceReviewsRaw) {
                    const serviceReviews = JSON.parse(serviceReviewsRaw);
                    // Convert service reviews to customer feedback format
                    const convertedReviews = serviceReviews.map(review => ({
                        user: "Customer", // In a real app, this would come from user data
                        feedback: review.comment,
                        date: new Date(review.date).toLocaleDateString('en-US'),
                        rating: review.rating
                    }));
                    
                    // Combine with existing feedback and sort by date (newest first)
                    customerFeedback = [...convertedReviews, ...customerFeedback];
                    
                    // Keep only the most recent 10 feedback items
                    customerFeedback = customerFeedback.slice(0, 10);
                }
                return true; // Indicates data was updated
            }
            return false; // Indicates no changes
        } catch (error) {
            console.error('Error loading service reviews:', error);
            return false;
        }
    }
    
    // Load service reviews initially
    loadServiceReviews();

    // Function to calculate total sales
    function calculateTotalSales() {
        const today = new Date();
        console.log('Today\'s date:', today.toDateString());
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay());
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        
        const todaySales = orders
            .filter(order => {
                // For Complete orders, use completionDate; for Delivered orders, use original date
                let orderDateStr;
                if (order.status === "Complete" && order.completionDate) {
                    orderDateStr = order.completionDate;
                } else {
                    orderDateStr = order.date;
                }
                
                // Parse the order date
                const orderDate = new Date(orderDateStr);
                
                // Check if the status matches
                const isMatchingStatus = (order.status === "Delivered" || order.status === "Complete");
                
                // Compare dates by checking if they're the same day
                const isToday = orderDate.getDate() === today.getDate() && 
                               orderDate.getMonth() === today.getMonth() && 
                               orderDate.getFullYear() === today.getFullYear();
                
                console.log('Order:', order.id, 'Status:', order.status, 'Date:', orderDateStr, 
                           'Parsed Date:', orderDate, 'Is matching status:', isMatchingStatus, 'Is today:', isToday);
                
                return isMatchingStatus && isToday;
            })
            .reduce((sum, order) => sum + order.total, 0);
            
        const weekSales = orders
            .filter(order => {
                // For Complete orders, use completionDate; for Delivered orders, use original date
                let orderDateStr;
                if (order.status === "Complete" && order.completionDate) {
                    orderDateStr = order.completionDate;
                } else {
                    orderDateStr = order.date;
                }
                
                const orderDate = new Date(orderDateStr);
                const isMatchingStatus = (order.status === "Delivered" || order.status === "Complete");
                const isInWeek = orderDate >= startOfWeek && orderDate <= today;
                console.log('Order:', order.id, 'Status:', order.status, 'Date:', orderDateStr, 
                           'Parsed Date:', orderDate, 'Is matching status:', isMatchingStatus, 'Is in week:', isInWeek);
                return isMatchingStatus && isInWeek;
            })
            .reduce((sum, order) => sum + order.total, 0);
            
        const monthSales = orders
            .filter(order => {
                // For Complete orders, use completionDate; for Delivered orders, use original date
                let orderDateStr;
                if (order.status === "Complete" && order.completionDate) {
                    orderDateStr = order.completionDate;
                } else {
                    orderDateStr = order.date;
                }
                
                const orderDate = new Date(orderDateStr);
                const isMatchingStatus = (order.status === "Delivered" || order.status === "Complete");
                const isInMonth = orderDate >= startOfMonth && orderDate <= today;
                console.log('Order:', order.id, 'Status:', order.status, 'Date:', orderDateStr, 
                           'Parsed Date:', orderDate, 'Is matching status:', isMatchingStatus, 'Is in month:', isInMonth);
                return isMatchingStatus && isInMonth;
            })
            .reduce((sum, order) => sum + order.total, 0);
            
        return {
            today: todaySales,
            week: weekSales,
            month: monthSales
        };
    }

    // Function to calculate total orders
    function calculateTotalOrders() {
        // Only count completed orders (Delivered or Complete status)
        return orders.filter(order => order.status === "Delivered" || order.status === "Complete").length;
    }

    // Function to calculate pending orders
    function calculatePendingOrders() {
        return orders.filter(order => order.status === "Processing" || order.status === "Pending").length;
    }

    // Function to calculate average rating
    function calculateAverageRating() {
        if (customerFeedback.length === 0) return 0;
        const totalRating = customerFeedback.reduce((sum, feedback) => sum + feedback.rating, 0);
        return (totalRating / customerFeedback.length).toFixed(1);
    }

    // Function to render analytics data
    function renderAnalyticsData() {
        // Update total sales
        const salesData = calculateTotalSales();
        console.log('Calculated sales data:', salesData);
        const totalSalesElement = document.getElementById('total-sales');
        if (totalSalesElement) {
            totalSalesElement.innerHTML = `
                <div class="sales-breakdown">
                    <div class="sales-item">Today: ₱${salesData.today.toLocaleString()}</div>
                    <div class="sales-item">This Week: ₱${salesData.week.toLocaleString()}</div>
                    <div class="sales-item">This Month: ₱${salesData.month.toLocaleString()}</div>
                </div>
            `;
        }

        // Update total orders
        const totalOrdersElement = document.getElementById('total-orders');
        if (totalOrdersElement) {
            totalOrdersElement.textContent = calculateTotalOrders();
        }

        // Update pending orders
        const pendingOrdersElement = document.getElementById('pending-orders');
        if (pendingOrdersElement) {
            pendingOrdersElement.textContent = calculatePendingOrders();
        }

        // Update average rating
        const avgRatingElement = document.getElementById('avg-rating');
        if (avgRatingElement) {
            avgRatingElement.textContent = calculateAverageRating();
        }
    }

    // Function to render popular products
    function renderPopularProducts() {
        const productList = document.getElementById('popular-products-list');
        const popularCount = document.getElementById('popular-count');
        
        if (productList && popularCount) {
            // Sort products by sold count (highest first) before rendering
            const sortedProducts = [...popularProducts].sort((a, b) => b.sold - a.sold);
            
            productList.innerHTML = '';
            popularCount.textContent = sortedProducts.length;
            
            sortedProducts.forEach((product, index) => {
                const productItem = document.createElement('div');
                productItem.className = 'product-item';
                productItem.innerHTML = `
                    <span class="product-item-rank">#${index + 1}</span>
                    <span class="product-item-name">${product.name}</span>
                    <span class="product-item-sold">${product.sold} sold</span>
                `;
                productList.appendChild(productItem);
            });
        }
    }

    // Function to render customer feedback
    function renderCustomerFeedback() {
        const feedbackList = document.getElementById('feedback-list');
        
        if (feedbackList) {
            feedbackList.innerHTML = '';
            
            customerFeedback.forEach(feedback => {
                const feedbackItem = document.createElement('div');
                feedbackItem.className = 'feedback-item';
                
                // Generate star icons based on rating
                let starsHTML = '';
                for (let i = 0; i < 5; i++) {
                    if (i < feedback.rating) {
                        starsHTML += '<i class="fas fa-star"></i>';
                    } else {
                        starsHTML += '<i class="far fa-star"></i>';
                    }
                }
                
                feedbackItem.innerHTML = `
                    <div class="feedback-header">
                        <div class="feedback-user">${feedback.user}</div>
                        <div class="feedback-date">Received: ${feedback.date}</div>
                    </div>
                    <div class="feedback-text">${feedback.feedback}</div>
                    <div class="feedback-stars">${starsHTML}</div>
                `;
                feedbackList.appendChild(feedbackItem);
            });
        }
    }

    // Initial render
    renderAnalyticsData();
    renderPopularProducts();
    renderCustomerFeedback();
    
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

    // Add click handler for total sales card
    const totalSalesCard = document.getElementById('total-sales-card');
    if (totalSalesCard) {
        totalSalesCard.addEventListener('click', function() {
            showSalesBreakdownPopup();
        });
    }

    /**
     * Shows a popup with detailed sales breakdown
     */
    function showSalesBreakdownPopup() {
        // Create modal if it doesn't exist
        let salesModal = document.getElementById('sales-breakdown-modal');
        if (!salesModal) {
            salesModal = document.createElement('div');
            salesModal.id = 'sales-breakdown-modal';
            salesModal.className = 'modal';
            salesModal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Total Sales Breakdown</h3>
                        <span class="close" id="close-sales-modal">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="sales-breakdown-details">
                            <p><strong>Today:</strong> Sales for current day</p>
                            <p><strong>This Week:</strong> Sales from Monday to today</p>
                            <p><strong>This Month:</strong> Sales from 1st of month to today</p>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(salesModal);
            
            // Add close functionality
            document.getElementById('close-sales-modal').addEventListener('click', function() {
                salesModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
            
            // Close when clicking outside the modal
            salesModal.addEventListener('click', function(e) {
                if (e.target === salesModal) {
                    salesModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }
        
        // Show the modal
        salesModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    // Render initial analytics data
    console.log('Initial orders data:', orders);
    renderAnalyticsData();
    
    // Update order badge
    updateOrderBadge();
    
    // Periodically check for updates to orders data and service reviews
    setInterval(() => {
        // Check for order updates
        const storedOrders = localStorage.getItem('ordersData');
        console.log('Checking localStorage for updates...');
        if (storedOrders) {
            try {
                const parsedOrders = JSON.parse(storedOrders);
                console.log('Current orders in memory:', orders);
                console.log('Orders in localStorage:', parsedOrders);
                if (JSON.stringify(parsedOrders) !== JSON.stringify(orders)) {
                    console.log('Detected orders data change, updating analytics');
                    orders = parsedOrders;
                    renderAnalyticsData();
                    updateOrderBadge();
                    console.log('Analytics updated with new data');
                } else {
                    console.log('No changes detected in orders data');
                }
            } catch (e) {
                console.error('Error parsing stored orders data:', e);
            }
        } else {
            console.log('No orders data found in localStorage');
        }
        
        // Check for service review updates
        const wasUpdated = loadServiceReviews();
        if (wasUpdated) {
            renderCustomerFeedback();
            console.log('Customer feedback updated with new reviews');
        }
    }, 500); // Check every 500ms for faster updates
});