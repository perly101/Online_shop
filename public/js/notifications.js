// Notifications system - reusable script for all pages
(function(){
  if(!window.OrderUtils) {
    console.warn('OrderUtils not loaded');
    return;
  }
  
  // Update notification dot
  function updateNotificationDot(){
    const dot = document.getElementById('notificationDot');
    if(!dot) return;
    const unreadCount = OrderUtils.getUnreadCount();
    if(unreadCount > 0){
      dot.classList.remove('hidden');
      dot.setAttribute('data-count', unreadCount);
    } else {
      dot.classList.add('hidden');
    }
  }
  
  // Format time
  function formatTime(dateString){
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    
    if(minutes < 1) return 'Just now';
    if(minutes < 60) return `${minutes}m ago`;
    if(hours < 24) return `${hours}h ago`;
    if(days < 7) return `${days}d ago`;
    return date.toLocaleDateString();
  }
  
  // Render notifications
  function renderNotifications(){
    console.log('Rendering notifications...');
    const list = document.getElementById('notificationsList');
    const empty = document.getElementById('notificationsEmpty');
    console.log('List element:', list, 'Empty element:', empty);
    if(!list || !empty) {
      console.log('Missing required elements for notifications');
      return;
    }
    
    const notifications = OrderUtils.getNotifications();
    console.log('Retrieved notifications:', notifications);
    list.innerHTML = '';
    
    if(notifications.length === 0){
      console.log('No notifications, showing empty state');
      empty.style.display = 'block';
      const markAllBtn = document.getElementById('markAllReadBtn');
      if(markAllBtn) markAllBtn.disabled = true;
      return;
    }
    
    console.log('Notifications found, hiding empty state');
    empty.style.display = 'none';
    const markAllBtn = document.getElementById('markAllReadBtn');
    if(markAllBtn) markAllBtn.disabled = false;
    
    notifications.forEach(notif => {
      const li = document.createElement('li');
      li.className = 'notification-item' + (notif.read ? '' : ' unread');
      li.setAttribute('data-id', notif.id);
      
      const iconClass = notif.type === 'success' ? 'success' : 'info';
      const iconSymbol = notif.type === 'success' ? '✓' : 'ℹ';
      
      li.innerHTML = `
        <div class="notification-icon ${iconClass}">${iconSymbol}</div>
        <div class="notification-content">
          <div class="notification-message">${notif.message}</div>
          <div class="notification-time">${formatTime(notif.createdAt)}</div>
        </div>
        <button class="notification-delete-btn" data-id="${notif.id}" title="Delete notification">
          <i class="fas fa-times"></i>
        </button>
      `;
      
      // Handle click on the notification (excluding delete button)
      li.addEventListener('click', function(event){
        // If the click was on the delete button, don't process the notification click
        if (event.target.closest('.notification-delete-btn')) {
          return;
        }
        
        OrderUtils.markAsRead(notif.id);
        renderNotifications();
        updateNotificationDot();
        
        // Close notifications overlay first
        closeNotifications();
        
        // If this notification is about an order, show the QR code
        if(notif.orderId) {
          showOrderQRCode(notif.orderId);
        } else if(notif.message && notif.message.includes('Your order')) {
          // Extract order ID from message for example notifications
          const orderIdMatch = notif.message.match(/Your order ([^ ]+)/);
          if(orderIdMatch && orderIdMatch[1]) {
            showOrderQRCode(orderIdMatch[1]);
          }
        }
      });
      
      list.appendChild(li);
      
      // Add event listener for the delete button after a short delay to ensure DOM is ready
      setTimeout(function() {
        const deleteBtn = li.querySelector('.notification-delete-btn');
        if (deleteBtn) {
          deleteBtn.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent the notification click event
            const notificationId = parseInt(deleteBtn.getAttribute('data-id'));
            if (notificationId && window.OrderUtils && typeof window.OrderUtils.deleteNotification === 'function') {
              window.OrderUtils.deleteNotification(notificationId);
              renderNotifications();
              updateNotificationDot();
            }
          });
        }
      }, 0);
    });
  }
  
  // Open notifications
  function openNotifications(){
    console.log('openNotifications called');
    if(!window.OrderUtils) {
      console.error('OrderUtils not available');
      return;
    }
    OrderUtils.checkOrderStatusChanges();
    renderNotifications();
    updateNotificationDot();
    const overlay = document.getElementById('notificationsOverlay');
    console.log('Notifications overlay:', overlay);
    if(overlay) {
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      console.log('Notifications opened');
    } else {
      console.error('Notifications overlay not found!');
    }
  }
  
  // Close notifications
  function closeNotifications(){
    const overlay = document.getElementById('notificationsOverlay');
    if(overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
  }
  
  // Show QR code for an order
  function showOrderQRCode(orderId) {
    // Get the order details
    const orders = JSON.parse(localStorage.getItem('orders') || '[]');
    
    // Debug: log what we're looking for and what orders exist
    console.log('Looking for order ID:', orderId);
    console.log('Available orders:', orders);
    
    // Try multiple matching strategies
    let order = orders.find(o => String(o.id) === String(orderId));
    
    if (!order) {
      // Try without # prefix
      order = orders.find(o => String(o.id) === String(orderId).replace('#', ''));
    }
    
    if (!order) {
      // Try with # prefix
      order = orders.find(o => String(o.id) === '#' + String(orderId));
    }
    
    if (!order) {
      // Try partial matching (last part of the ID)
      const orderIdPart = String(orderId).split('-').pop();
      order = orders.find(o => {
        const orderIdToCheck = String(o.id);
        return orderIdToCheck.includes(orderIdPart);
      });
    }
    
    // If we found an order in localStorage, use it
    if (order) {
      showQRCodeModal(orderId, order);
    } else {
      // For example notifications, just generate QR code from the order ID
      showQRCodeModal(orderId, { id: orderId });
    }
  }
  
  // Show QR code modal
  function showQRCodeModal(orderId, order) {
    // Create a modal to display the QR code
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.zIndex = '10001';
    
    const modalContent = document.createElement('div');
    modalContent.style.backgroundColor = 'white';
    modalContent.style.padding = '30px';
    modalContent.style.borderRadius = '10px';
    modalContent.style.textAlign = 'center';
    modalContent.style.maxWidth = '90%';
    modalContent.style.width = '400px';
    
    const title = document.createElement('h2');
    title.textContent = 'Order QR Code';
    title.style.marginTop = '0';
    
    const orderIdElement = document.createElement('p');
    orderIdElement.textContent = `Order ID: ${orderId}`;
    orderIdElement.style.fontWeight = 'bold';
    orderIdElement.style.marginBottom = '20px';
    
    const qrContainer = document.createElement('div');
    qrContainer.style.margin = '20px 0';
    qrContainer.style.display = 'flex';
    qrContainer.style.justifyContent = 'center';
    
    const closeButton = document.createElement('button');
    closeButton.textContent = 'Close';
    closeButton.style.padding = '10px 20px';
    closeButton.style.backgroundColor = '#f0ad06';
    closeButton.style.border = 'none';
    closeButton.style.borderRadius = '5px';
    closeButton.style.cursor = 'pointer';
    closeButton.style.fontWeight = 'bold';
    
    closeButton.addEventListener('click', function() {
      document.body.removeChild(modal);
    });
    
    modalContent.appendChild(title);
    modalContent.appendChild(orderIdElement);
    modalContent.appendChild(qrContainer);
    modalContent.appendChild(closeButton);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Generate QR code - dynamically load library if needed
    if (typeof QRCode !== 'undefined') {
      generateQRCode(qrContainer, orderId);
    } else {
      // Dynamically load QRCode library
      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
      script.onload = function() {
        generateQRCode(qrContainer, orderId);
      };
      script.onerror = function() {
        qrContainer.innerHTML = '<p style="font-size:12px;color:#999;">Failed to load QR code generator</p>';
      };
      document.head.appendChild(script);
    }
  }
  
  // Helper function to generate QR code
  function generateQRCode(container, text) {
    try {
      container.innerHTML = ''; // Clear any existing content
      new QRCode(container, {
        text: text,
        width: 128,
        height: 128,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });
    } catch (e) {
      console.error('QR code generation failed:', e);
      container.innerHTML = '<p style="font-size:12px;color:#999;">QR code unavailable</p>';
    }
  }
  
  // Initialize when DOM is ready
  function initNotifications(){
    console.log('Initializing notifications...');
    const messagesIcon = document.getElementById('messagesIcon');
    const closeBtn = document.getElementById('notificationsClose');
    const markAllBtn = document.getElementById('markAllReadBtn');
    const overlay = document.getElementById('notificationsOverlay');
    
    console.log('Messages icon found:', messagesIcon);
    if(messagesIcon){
      messagesIcon.style.cursor = 'pointer';
      messagesIcon.addEventListener('click', openNotifications);
      console.log('Added click listener to messages icon');
    } else {
      console.error('Messages icon not found!');
    }
    
    if(closeBtn) closeBtn.addEventListener('click', closeNotifications);
    
    if(overlay){
      overlay.addEventListener('click', function(e){
        if(e.target === overlay) closeNotifications();
      });
    }
    
    if(markAllBtn){
      markAllBtn.addEventListener('click', function(){
        OrderUtils.markAllAsRead();
        renderNotifications();
        updateNotificationDot();
      });
    }
    
    // Check for new notifications on load
    if(window.OrderUtils){
      OrderUtils.checkOrderStatusChanges();
      updateNotificationDot();
    }
    
    // Listen for Escape key
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape'){
        const overlay = document.getElementById('notificationsOverlay');
        if(overlay && overlay.classList.contains('active')){
          closeNotifications();
        }
      }
    });
  }
  
  // Run when DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    // Add a small delay to ensure all elements are loaded
    setTimeout(initNotifications, 100);
  });
  
})();