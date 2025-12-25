// Order utility functions
(function(window) {
  'use strict';
  
  // Get current user identifier
  function getCurrentUserId() {
    try {
      const currentUserRaw = localStorage.getItem('currentUser');
      if (currentUserRaw) {
        const currentUser = JSON.parse(currentUserRaw);
        return currentUser.email || currentUser.id || 'anonymous';
      }
    } catch (e) {
      console.warn('Error getting current user ID:', e);
    }
    return 'anonymous';
  }
  
  // Generate a unique order ID in format: #AET-YYYY-XXX
  function generateOrderId() {
    const now = new Date();
    const year = now.getFullYear();
    
    // Get all existing orders to find the highest sequence number for this year
    let maxSequence = 0;
    try {
      const ordersRaw = localStorage.getItem('orders');
      if (ordersRaw) {
        const orders = JSON.parse(ordersRaw);
        const yearPrefix = `AET-${year}-`;
        orders.forEach(order => {
          const orderId = String(order.id || '');
          if (orderId.includes(yearPrefix)) {
            // Extract sequence number from order ID like "#AET-2024-008" or "AET-2024-008"
            const match = orderId.match(new RegExp(yearPrefix + '(\\d+)'));
            if (match) {
              const seq = parseInt(match[1], 10);
              if (seq > maxSequence) {
                maxSequence = seq;
              }
            }
          }
        });
      }
    } catch (e) {
      console.warn('Error reading orders for ID generation:', e);
    }
    
    // Increment sequence for new order
    const sequence = maxSequence + 1;
    const sequenceStr = String(sequence).padStart(3, '0');
    
    return `#AET-${year}-${sequenceStr}`;
  }
  
  // Save the last placed order to localStorage for confirmation page access
  function saveLastPlacedOrder(order) {
    try {
      localStorage.setItem('lastPlacedOrder', JSON.stringify(order));
    } catch (e) {
      console.warn('Error saving last placed order:', e);
    }
  }
  
  // Get the last placed order from localStorage
  function getLastPlacedOrder() {
    try {
      const lastOrderRaw = localStorage.getItem('lastPlacedOrder');
      if (lastOrderRaw) {
        return JSON.parse(lastOrderRaw);
      }
    } catch (e) {
      console.warn('Error reading last placed order:', e);
    }
    return null;
  }
  
  // Get user-specific notifications key
  function getUserNotificationsKey() {
    return `notifications_${getCurrentUserId()}`;
  }
  
  // Notifications system
  // Create a notification when order status changes
  function createNotification(orderId, message, type) {
    try {
      const userNotificationsKey = getUserNotificationsKey();
      const notifications = JSON.parse(localStorage.getItem(userNotificationsKey) || '[]');
      const notification = {
        id: Date.now(),
        orderId: orderId,
        message: message,
        type: type || 'info', // 'info', 'success', 'warning'
        read: false,
        createdAt: new Date().toISOString()
      };
      notifications.unshift(notification); // Add to beginning
      // Keep only last 100 notifications
      if (notifications.length > 100) {
        notifications.splice(100);
      }
      localStorage.setItem(userNotificationsKey, JSON.stringify(notifications));
      return notification;
    } catch (e) {
      console.warn('Error creating notification:', e);
      return null;
    }
  }
  
  // Get all notifications for current user
  function getNotifications() {
    try {
      const userNotificationsKey = getUserNotificationsKey();
      return JSON.parse(localStorage.getItem(userNotificationsKey) || '[]');
    } catch (e) {
      console.warn('Error reading notifications:', e);
      return [];
    }
  }
  
  // Get unread notifications count for current user
  function getUnreadCount() {
    const notifications = getNotifications();
    return notifications.filter(n => !n.read).length;
  }
  
  // Mark notification as read for current user
  function markAsRead(notificationId) {
    try {
      const userNotificationsKey = getUserNotificationsKey();
      const notifications = getNotifications();
      const notification = notifications.find(n => n.id === notificationId);
      if (notification) {
        notification.read = true;
        localStorage.setItem(userNotificationsKey, JSON.stringify(notifications));
      }
    } catch (e) {
      console.warn('Error marking notification as read:', e);
    }
  }
  
  // Mark all notifications as read for current user
  function markAllAsRead() {
    try {
      const userNotificationsKey = getUserNotificationsKey();
      const notifications = getNotifications();
      notifications.forEach(n => n.read = true);
      localStorage.setItem(userNotificationsKey, JSON.stringify(notifications));
    } catch (e) {
      console.warn('Error marking all notifications as read:', e);
    }
  }
  
  // Delete a notification by ID for current user
  function deleteNotification(notificationId) {
    try {
      const userNotificationsKey = getUserNotificationsKey();
      const notifications = getNotifications();
      const filteredNotifications = notifications.filter(n => n.id !== notificationId);
      localStorage.setItem(userNotificationsKey, JSON.stringify(filteredNotifications));
      return true;
    } catch (e) {
      console.warn('Error deleting notification:', e);
      return false;
    }
  }
  
  // Delete all notifications for current user
  function deleteAllNotifications() {
    try {
      const userNotificationsKey = getUserNotificationsKey();
      localStorage.setItem(userNotificationsKey, JSON.stringify([]));
      return true;
    } catch (e) {
      console.warn('Error deleting all notifications:', e);
      return false;
    }
  }
  
  // Create notification when order status changes to ready
  function notifyOrderReady(orderId, orderDetails) {
    const message = `Your order ${orderId} is now ready for pickup! Please proceed to the store.`;
    return createNotification(orderId, message, 'success');
  }
  
  // Check orders and create notifications for status changes
  function checkOrderStatusChanges() {
    try {
      const orders = JSON.parse(localStorage.getItem('orders') || '[]');
      const lastCheckKey = 'lastOrderStatusCheck';
      const lastCheck = localStorage.getItem(lastCheckKey) || '{}';
      const lastStatusMap = JSON.parse(lastCheck);
      
      orders.forEach(order => {
        const orderId = String(order.id || '');
        const currentStatus = order.status || 'placed';
        const lastStatus = lastStatusMap[orderId] || 'placed';
        
        // If status changed to 'ready' or 'ready for pickup'
        if ((currentStatus === 'ready' || currentStatus === 'ready for pickup') && lastStatus !== currentStatus) {
          notifyOrderReady(orderId, order);
        }
        
        // Update last known status
        lastStatusMap[orderId] = currentStatus;
      });
      
      localStorage.setItem(lastCheckKey, JSON.stringify(lastStatusMap));
    } catch (e) {
      console.warn('Error checking order status changes:', e);
    }
  }
  
  // Export to window
  window.OrderUtils = {
    generateOrderId: generateOrderId,
    saveLastPlacedOrder: saveLastPlacedOrder,
    getLastPlacedOrder: getLastPlacedOrder,
    createNotification: createNotification,
    getNotifications: getNotifications,
    getUnreadCount: getUnreadCount,
    markAsRead: markAsRead,
    markAllAsRead: markAllAsRead,
    deleteNotification: deleteNotification,
    deleteAllNotifications: deleteAllNotifications,
    notifyOrderReady: notifyOrderReady,
    checkOrderStatusChanges: checkOrderStatusChanges
  };
  
})(window);