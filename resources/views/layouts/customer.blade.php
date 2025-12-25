<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absolute Essential Trading')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root{--gold1:#ffd54a;--gold2:#f0ad06}
        *{margin:0;padding:0;box-sizing:border-box}
        html,body{height:100%;margin:0;padding:0;font-family:Georgia, 'Times New Roman', serif;color:#111;overflow-x:hidden}
        body{background-image:url('{{ asset("images/Backgound.png") }}');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed}
        
        /* Fixed Topbar */
        .topbar{display:flex;align-items:center;justify-content:space-between;padding:20px 26px;background:linear-gradient(90deg,var(--gold1),var(--gold2));box-shadow:0 2px 6px rgba(0,0,0,0.08);position:fixed;top:0;left:0;right:0;width:100%;z-index:1000;box-sizing:border-box}
        .brand-section{display:flex;align-items:center;gap:12px}
        .brand{font-size:20px;font-weight:700;text-decoration:none;color:#000}
        .icons{display:flex;gap:12px;align-items:center}
        .icon-btn{width:40px;height:40px;border-radius:6px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.15);color:#000;cursor:pointer;border:none;padding:0;transition:background 0.2s;text-decoration:none}
        .icon-btn:hover{background:rgba(0,0,0,0.25)}
        .icon-btn i{font-size:18px}
        #backBtn{background:transparent;width:auto;height:auto;padding:0}
        #backBtn:hover{background:transparent}
        
        /* Main Content Area */
        .main-content{margin-top:72px;min-height:calc(100vh - 72px - 60px);position:relative;padding-bottom:20px}
        .page-overlay{position:fixed;inset:0;top:72px;background:linear-gradient(rgba(255,244,200,0.65), rgba(255,244,200,0.65));pointer-events:none;z-index:0}
        
        /* Footer */
        .footer{background:linear-gradient(90deg,var(--gold1),var(--gold2));padding:4px 26px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 -2px 6px rgba(0,0,0,0.08);position:fixed;bottom:0;left:0;right:0;z-index:10}
        .footer-left{display:flex;align-items:center;gap:12px;font-size:14px}
        .footer-right{display:flex;gap:12px;align-items:center}
        .footer-icon{position:relative;width:40px;height:40px;border-radius:6px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.15);color:#000;cursor:pointer;transition:background 0.2s}
        .footer-icon:hover{background:rgba(0,0,0,0.25)}
        .footer-icon i{font-size:18px}
        .notification-dot{position:absolute;top:4px;right:4px;width:8px;height:8px;background:#e74c3c;border-radius:50%;border:2px solid var(--gold1)}
        .notification-dot.hidden{display:none}
        
        /* Sidebar */
        .sidebar-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.30);backdrop-filter:blur(6px);display:none;z-index:9999;transition:opacity 0.3s}
        .sidebar-overlay.active{display:block;opacity:1}
        .sidebar{position:fixed;right:-300px;top:0;bottom:0;width:280px;background:#fff;box-shadow:-2px 0 20px rgba(0,0,0,0.15);transition:right 0.3s ease;z-index:10000;display:flex;flex-direction:column}
        .sidebar.active{right:0}
        .sidebar-header{background:linear-gradient(90deg,var(--gold1),var(--gold2));padding:20px;display:flex;align-items:center;justify-content:space-between;border-bottom:2px solid rgba(0,0,0,0.1)}
        .sidebar-title{font-size:20px;font-weight:700;color:#111}
        .sidebar-close{background:rgba(0,0,0,0.15);border:none;width:36px;height:36px;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s}
        .sidebar-close:hover{background:rgba(0,0,0,0.25)}
        .sidebar-close i{font-size:18px}
        .sidebar-content{flex:1;overflow-y:auto;padding:0}
        .sidebar-menu{list-style:none;padding:0;margin:0}
        .sidebar-menu li{border-bottom:1px solid #f0f0f0}
        .sidebar-menu a{display:flex;align-items:center;gap:12px;padding:18px 20px;color:#333;text-decoration:none;font-size:16px;transition:background 0.2s}
        .sidebar-menu a:hover{background:#f9f9f9}
        .sidebar-menu i{width:24px;text-align:center;color:#666;font-size:18px}
        
        /* Popup */
        .popup-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.30);backdrop-filter:blur(6px);display:none;align-items:center;justify-content:center;z-index:10000}
        .popup-content{background:#fff;border-radius:12px;box-shadow:0 30px 60px rgba(0,0,0,0.25);width:90%;max-width:500px;position:relative;border:2px solid rgba(240,173,6,0.3);padding:20px}
        .popup-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:15px}
        .popup-header h3{margin:0;font-size:20px;font-weight:700;color:#111}
        .popup-close{background:transparent;border:none;font-size:24px;cursor:pointer;padding:4px 8px;color:#666;width:30px;height:30px;display:flex;align-items:center;justify-content:center}
        .popup-message{font-size:16px;color:#333;margin-bottom:20px;line-height:1.4}
        .popup-buttons{display:flex;justify-content:flex-end;gap:10px}
        .popup-button{background:#f0f0f0;border:none;padding:10px 20px;border-radius:6px;font-weight:600;cursor:pointer;font-size:14px}
        .popup-button:hover{background:#e0e0e0}
        
        /* Notifications Modal */
        .notifications-modal{position:fixed;right:-100%;top:0;bottom:0;width:350px;max-width:90%;background:#fff;box-shadow:-2px 0 20px rgba(0,0,0,0.15);transition:right 0.3s ease;z-index:10001;display:flex;flex-direction:column}
        .notifications-modal.active{right:0}
        .notifications-modal-header{background:linear-gradient(90deg,var(--gold1),var(--gold2));padding:20px;display:flex;align-items:center;justify-content:space-between;border-bottom:2px solid rgba(0,0,0,0.1)}
        .notifications-modal-title{font-size:20px;font-weight:700;color:#111}
        .notifications-modal-content{flex:1;overflow-y:auto;padding:15px}
        .notification-item{background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:15px;margin-bottom:12px;position:relative}
        .notification-item.unread{background:#f0f7ff;border-color:#3498db}
        .notification-item .notification-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;margin-bottom:10px}
        .notification-item .notification-icon.success{background:#27ae60}
        .notification-item .notification-icon.info{background:#3498db}
        .notification-item .notification-icon.warning{background:#f39c12}
        .notification-item .notification-icon.error{background:#e74c3c}
        .notification-item .notification-message{font-size:14px;color:#333;margin-bottom:8px;line-height:1.4}
        .notification-item .notification-time{font-size:12px;color:#999}
        .notification-item .mark-read-btn{position:absolute;top:10px;right:10px;background:#3498db;color:#fff;border:none;padding:4px 10px;border-radius:4px;font-size:11px;cursor:pointer}
        .notification-item .mark-read-btn:hover{background:#2980b9}
        .notification-item.unread .unread-indicator{position:absolute;top:15px;right:15px;width:8px;height:8px;background:#3498db;border-radius:50%}
        .no-notifications{text-align:center;padding:40px 20px;color:#999}
        .no-notifications i{font-size:48px;margin-bottom:15px;color:#ddd}
        .notifications-actions{padding:15px;border-top:1px solid #e0e0e0;display:flex;gap:10px}
        .notifications-actions button{flex:1;background:#f0f0f0;border:none;padding:10px;border-radius:6px;font-weight:600;cursor:pointer;font-size:13px}
        .notifications-actions button:hover{background:#e0e0e0}
        .notifications-actions button.delete-all{background:#e74c3c;color:#fff}
        .notifications-actions button.delete-all:hover{background:#c0392b}
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Fixed Navigation Bar -->
    <header class="topbar">
        <div class="brand-section">
            @if(Request::is('shop'))
                <a href="{{ route('home') }}" class="icon-btn" id="backBtn" title="Home"><i class="fas fa-arrow-left"></i></a>
            @else
                <a href="{{ route('shop.index') }}" class="icon-btn" id="backBtn" title="Back to Shop"><i class="fas fa-arrow-left"></i></a>
            @endif
            <a href="{{ route('shop.index') }}" class="brand">Absolute Essential Trading</a>
        </div>
        <div class="icons">
            <a href="{{ route('shop.index') }}" class="icon-btn" title="Shop"><i class="fas fa-home"></i></a>
            <a href="{{ route('orders.index') }}" class="icon-btn" title="Orders"><i class="fas fa-box-open"></i></a>
            <a href="{{ route('cart.index') }}" class="icon-btn" title="Cart"><i class="fas fa-shopping-cart"></i></a>
            <button class="icon-btn" id="menuBtn" title="Menu"><i class="fas fa-bars"></i></button>
        </div>
    </header>

    <div class="page-overlay"></div>
    
    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-left">
            <span>0929 222 4309</span>
            <span>Open 8:00 AM - Closes 8:30 PM</span>
        </div>
        <div class="footer-right">
            <div class="footer-icon" id="messagesIcon" title="Messages">
                <i class="fas fa-envelope"></i>
                <span class="notification-dot hidden" id="notificationDot"></span>
            </div>
            <div class="footer-icon" title="Profile" onclick="window.location='{{ route('profile.index') }}'">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </footer>
    
    <!-- Sidebar Menu -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-title">Menu</div>
            <button class="sidebar-close" id="sidebarClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li><a href="{{ route('contact.index') }}"><i class="fas fa-phone"></i> Contact Us</a></li>
                <li><a href="{{ route('reviews.index') }}"><i class="fas fa-star"></i> Review Service</a></li>
                <li><a href="#" id="logoutLink" style="color:#e74c3c"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Logout Confirmation Popup -->
    <div id="logoutPopup" class="popup-overlay">
        <div class="popup-content">
            <div class="popup-header">
                <h3>Confirm Logout</h3>
                <button class="popup-close" id="logoutClose">&times;</button>
            </div>
            <div class="popup-message">Are you sure you want to log out?</div>
            <div class="popup-buttons">
                <button class="popup-button" id="logoutNoBtn">No</button>
                <button class="popup-button" id="logoutYesBtn" style="background:#ffd54a;">Yes</button>
            </div>
        </div>
    </div>

    <!-- Notifications Modal -->
    <div class="sidebar-overlay" id="notificationsOverlay"></div>
    <div class="notifications-modal" id="notificationsModal">
        <div class="notifications-modal-header">
            <div class="notifications-modal-title">Notifications</div>
            <button class="sidebar-close" id="notificationsClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="notifications-modal-content" id="notificationsContent">
            <div class="no-notifications">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading notifications...</p>
            </div>
        </div>
        <div class="notifications-actions">
            <button id="markAllReadBtn">Mark All Read</button>
            <button id="deleteAllBtn" class="delete-all">Delete All</button>
        </div>
    </div>

    <script>
        // Check for unread notifications
        function checkUnreadNotifications() {
            fetch('{{ route('notifications.unread-count') }}')
                .then(response => response.json())
                .then(data => {
                    const dot = document.getElementById('notificationDot');
                    if (data.count > 0) {
                        dot.classList.remove('hidden');
                    } else {
                        dot.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error checking notifications:', error);
                });
        }
        
        // Load notifications
        function loadNotifications() {
            fetch('{{ route('notifications.index') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const content = document.getElementById('notificationsContent');
                
                if (data.notifications && data.notifications.length > 0) {
                    content.innerHTML = data.notifications.map(notif => `
                        <div class="notification-item ${!notif.read ? 'unread' : ''}" data-id="${notif.id}">
                            <div class="notification-icon ${notif.type}">
                                <i class="fas fa-${notif.type === 'success' ? 'check' : (notif.type === 'warning' ? 'exclamation' : 'info')}"></i>
                            </div>
                            ${!notif.read ? '<div class="unread-indicator"></div>' : ''}
                            <div class="notification-message">${notif.message}</div>
                            <div class="notification-time">
                                <i class="fas fa-clock"></i> ${notif.time_ago}
                            </div>
                            ${!notif.read ? `<button class="mark-read-btn" onclick="markAsRead(${notif.id})">Mark Read</button>` : ''}
                        </div>
                    `).join('');
                } else {
                    content.innerHTML = `
                        <div class="no-notifications">
                            <i class="fas fa-inbox"></i>
                            <p>No notifications yet</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('notificationsContent').innerHTML = `
                    <div class="no-notifications">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Error loading notifications</p>
                    </div>
                `;
            });
        }
        
        // Mark notification as read
        window.markAsRead = function(notificationId) {
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                    checkUnreadNotifications();
                }
            });
        };
        
        // Mark all as read
        document.getElementById('markAllReadBtn').addEventListener('click', function() {
            if (confirm('Mark all notifications as read?')) {
                fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                        checkUnreadNotifications();
                    }
                });
            }
        });
        
        // Delete all notifications
        document.getElementById('deleteAllBtn').addEventListener('click', function() {
            if (confirm('Delete all notifications? This cannot be undone.')) {
                fetch('/notifications/delete-all', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                        checkUnreadNotifications();
                    }
                });
            }
        });
        
        // Open notifications modal
        document.getElementById('messagesIcon').addEventListener('click', function() {
            document.getElementById('notificationsModal').classList.add('active');
            document.getElementById('notificationsOverlay').classList.add('active');
            loadNotifications();
        });
        
        // Close notifications modal
        document.getElementById('notificationsClose').addEventListener('click', function() {
            document.getElementById('notificationsModal').classList.remove('active');
            document.getElementById('notificationsOverlay').classList.remove('active');
        });
        
        document.getElementById('notificationsOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                document.getElementById('notificationsModal').classList.remove('active');
                this.classList.remove('active');
            }
        });
        
        // Check notifications on page load
        checkUnreadNotifications();
        
        // Check notifications every 30 seconds
        setInterval(checkUnreadNotifications, 30000);
        
        // Menu button
        document.getElementById('menuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('active');
            document.getElementById('sidebarOverlay').classList.add('active');
        });
        
        // Close sidebar
        document.getElementById('sidebarClose').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('sidebarOverlay').classList.remove('active');
        });
        
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('sidebarOverlay').classList.remove('active');
        });
        
        // Logout confirmation
        function showLogoutConfirmation() {
            document.getElementById('logoutPopup').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function hideLogoutConfirmation() {
            document.getElementById('logoutPopup').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function handleLogout() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('logout') }}';
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
        
        document.getElementById('logoutLink').addEventListener('click', function(e) {
            e.preventDefault();
            showLogoutConfirmation();
        });
        
        document.getElementById('logoutYesBtn').addEventListener('click', handleLogout);
        document.getElementById('logoutNoBtn').addEventListener('click', hideLogoutConfirmation);
        document.getElementById('logoutClose').addEventListener('click', hideLogoutConfirmation);
        
        document.getElementById('logoutPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                hideLogoutConfirmation();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const popup = document.getElementById('logoutPopup');
                if (popup && popup.style.display === 'flex') {
                    hideLogoutConfirmation();
                }
            }
        });
    </script>

    <script>
        // Convert ISO timestamps in data-ts into local time display
        if (!window.formatLocalTimes) {
            window.formatLocalTimes = function() {
                document.querySelectorAll('.local-time[data-ts], .order-date[data-ts]').forEach(el => {
                    const ts = el.getAttribute('data-ts');
                    if (!ts) return;
                    const d = new Date(ts);
                    if (isNaN(d)) return;
                    const datePart = d.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' });
                    const timePart = d.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit', hour12: true });
                    el.textContent = `${datePart} ${timePart}`;
                });
            };
            document.addEventListener('DOMContentLoaded', window.formatLocalTimes);
        }
    </script>

    @stack('scripts')
</body>
</html>
