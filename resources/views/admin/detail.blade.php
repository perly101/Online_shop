@extends('layouts.app')

@section('title', 'Admin Detail - Absolute Essential Trading')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">
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
    <a href="{{ route('admin.inventory') }}" class="nav-tab">
        <i class="fas fa-clipboard-list"></i> Inventory
    </a>
    <a href="{{ route('admin.analytics') }}" class="nav-tab">
        <i class="fas fa-chart-bar"></i> Analytics
    </a>
    <a href="{{ route('admin.management') }}" class="nav-tab active">
        <i class="fas fa-users"></i> Admins
    </a>
</nav>

<!-- User Management Bar -->
<div class="management-bar">
    <button class="back-btn" onclick="window.location.href='{{ route('admin.management') }}'">
        <i class="fas fa-arrow-left"></i>
    </button>
    <h2 class="management-title">Admin Management</h2>
    <div class="management-actions">
        <button class="search-btn" id="search-btn">
            <i class="fas fa-search"></i>
        </button>
        <button class="btn-add-user" id="add-user-btn">
            <i class="fas fa-plus"></i> Add New Admin
        </button>
    </div>
</div>

<!-- Search Bar (Hidden by default) -->
<div class="search-bar" id="search-bar" style="display: none;">
    <input type="text" id="search-input" class="search-input" placeholder="Search admin...">
    <button class="search-close-btn" id="search-close-btn">
        <i class="fas fa-times"></i>
    </button>
</div>

<!-- Main Content -->
<main class="container">
    <div class="user-detail-layout">
        <!-- Left Panel - Admin Accounts List -->
        <div class="customer-list-panel">
            <div class="accounts-header">
                <h3 class="accounts-title">Admin Accounts</h3>
            </div>
            <div class="customer-list" id="admin-list">
                @foreach($admins as $admin)
                <div class="customer-list-item {{ $admin->id == $selectedAdmin->id ? 'active' : '' }}" 
                     onclick="window.location.href='{{ route('admin.detail', $admin->id) }}'">
                    <div class="customer-info">
                        <div class="customer-name">{{ $admin->admin_id }}</div>
                        <div class="customer-email">Computer: {{ $admin->computer_number }}</div>
                    </div>
                    <div class="customer-status {{ $admin->status === 'active' ? 'status-active' : 'status-inactive' }}">
                        {{ ucfirst($admin->status) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Right Panel - Admin Details -->
        <div class="user-detail-panel" id="user-detail-panel">
            <div class="user-profile">
                <h3 class="user-name" id="user-name">{{ $selectedAdmin->admin_id }}</h3>
                <p class="user-email" id="user-email">Computer Number: {{ $selectedAdmin->computer_number }}</p>
                <p class="user-email">Status: <span class="{{ $selectedAdmin->status === 'active' ? 'text-success' : 'text-danger' }}">{{ ucfirst($selectedAdmin->status) }}</span></p>
                <p class="user-email">Created: <span class="local-time" data-ts="{{ $selectedAdmin->created_at->toIso8601String() }}">{{ $selectedAdmin->created_at->format('M d, Y') }}</span></p>
                <div class="user-actions">
                    <button class="user-action-btn" id="toggle-status-btn" onclick="toggleStatus()">
                        {{ $selectedAdmin->status === 'active' ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                    <button class="user-action-btn" id="change-password-btn" onclick="showChangePassword()">
                        Change Password
                    </button>
                    <button class="user-action-btn btn-danger" id="delete-admin-btn" onclick="deleteSelectedAdmin()">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Admin Modal -->
<div id="add-user-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Admin</h3>
            <button class="close-btn" id="close-add-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="add-user-form">
                @csrf
                <div class="form-group">
                    <label>ID (Format: ADMIN001)</label>
                    <input type="text" id="new-admin-id" class="form-input" placeholder="ADMIN001" required>
                    <span class="error-message" id="error-admin-id"></span>
                </div>
                <div class="form-group">
                    <label>Computer Number</label>
                    <input type="number" id="new-computer-number" class="form-input" value="0" min="0" required>
                    <span class="error-message" id="error-computer-number"></span>
                </div>
                <div class="form-group">
                    <label>Create Password (8 digits)</label>
                    <input type="password" id="new-password" class="form-input" placeholder="12345678" required minlength="8" maxlength="8">
                    <span class="error-message" id="error-password"></span>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" id="cancel-add-user">Cancel</button>
                    <button type="submit" class="btn-save">Add Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="change-password-modal" class="popup-modal" style="display: none;">
    <div class="popup-content">
        <span class="popup-close" id="close-change-password">&times;</span>
        <h3>Change Password</h3>
        <form id="change-password-form">
            @csrf
            <div class="form-group">
                <label for="new-password-input">New Password (8 digits)</label>
                <input type="password" id="new-password-input" class="form-input" placeholder="12345678" required minlength="8" maxlength="8">
                <span class="error-message" id="error-new-password"></span>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm New Password</label>
                <input type="password" id="confirm-password" class="form-input" placeholder="12345678" required minlength="8" maxlength="8">
                <span class="error-message" id="error-confirm-password"></span>
            </div>
            <div class="popup-buttons" style="margin-top: 20px;">
                <button type="button" id="cancel-change-password" class="btn-cancel" style="margin-right: 10px;">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Popup Modal -->
<div id="global-popup" class="popup-modal" style="display: none;">
    <div class="popup-content">
        <span class="popup-close" id="popup-close-btn" style="display: none;">&times;</span>
        <p id="popup-message"></p>
        <div class="popup-buttons" style="margin-top: 20px; display: none;" id="popup-buttons-container">
            <button id="popup-cancel-btn" class="btn-cancel" style="margin-right: 10px;">Cancel</button>
            <button id="popup-ok-btn" class="btn-save">Yes</button>
        </div>
    </div>
</div>

<style>
.error-message {
    color: #e74c3c;
    font-size: 0.85em;
    display: block;
    margin-top: 5px;
}

.user-detail-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 20px;
    margin-top: 20px;
}

.customer-list-panel {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.customer-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background 0.2s;
}

.customer-list-item:hover {
    background: #f9f9f9;
}

.customer-list-item.active {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
}

.customer-info {
    flex: 1;
}

.customer-name {
    font-weight: 600;
    font-size: 1em;
    color: #333;
    margin-bottom: 4px;
}

.customer-email {
    font-size: 0.85em;
    color: #666;
}

.customer-status {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 600;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.user-detail-panel {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.user-profile {
    max-width: 600px;
}

.user-name {
    font-size: 1.8em;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.user-email {
    font-size: 1em;
    color: #666;
    margin-bottom: 8px;
}

.user-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.user-action-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    background: #ffc107;
    color: #333;
}

.user-action-btn:hover {
    background: #ffb300;
    transform: translateY(-2px);
}

.user-action-btn.btn-danger {
    background: #e74c3c;
    color: white;
}

.user-action-btn.btn-danger:hover {
    background: #c0392b;
}

.text-success {
    color: #28a745;
}

.text-danger {
    color: #dc3545;
}

.search-bar {
    background: #fff;
    padding: 15px 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: none;
    align-items: center;
    gap: 10px;
}

.search-input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1em;
}

.search-close-btn {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
}
</style>

<script>
const selectedAdminId = {{ $selectedAdmin->id }};

// Search functionality
document.getElementById('search-btn').addEventListener('click', function() {
    document.getElementById('search-bar').style.display = 'flex';
    document.getElementById('search-input').focus();
});

document.getElementById('search-close-btn').addEventListener('click', function() {
    document.getElementById('search-bar').style.display = 'none';
    document.getElementById('search-input').value = '';
    filterAdmins('');
});

document.getElementById('search-input').addEventListener('input', function(e) {
    filterAdmins(e.target.value);
});

function filterAdmins(searchTerm) {
    const items = document.querySelectorAll('.customer-list-item');
    const term = searchTerm.toLowerCase();
    
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(term) ? 'flex' : 'none';
    });
}

// Add Admin Modal
document.getElementById('add-user-btn').addEventListener('click', function() {
    document.getElementById('add-user-modal').style.display = 'flex';
});

document.getElementById('close-add-modal').addEventListener('click', closeAddModal);
document.getElementById('cancel-add-user').addEventListener('click', closeAddModal);

function closeAddModal() {
    document.getElementById('add-user-modal').style.display = 'none';
    document.getElementById('add-user-form').reset();
    clearErrors();
}

// Add Admin Form Submit
document.getElementById('add-user-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();
    
    const formData = {
        admin_id: document.getElementById('new-admin-id').value,
        computer_number: document.getElementById('new-computer-number').value,
        password: document.getElementById('new-password').value,
        _token: '{{ csrf_token() }}'
    };
    
    try {
        const response = await fetch('{{ route("admin.management.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showPopup('Admin added successfully!');
            closeAddModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            if (data.errors) {
                displayErrors(data.errors);
            } else {
                showPopup(data.message || 'Error adding admin');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showPopup('Error adding admin');
    }
});

// Change Password Modal
function showChangePassword() {
    document.getElementById('change-password-modal').style.display = 'flex';
}

document.getElementById('close-change-password').addEventListener('click', closeChangePasswordModal);
document.getElementById('cancel-change-password').addEventListener('click', closeChangePasswordModal);

function closeChangePasswordModal() {
    document.getElementById('change-password-modal').style.display = 'none';
    document.getElementById('change-password-form').reset();
    clearErrors();
}

document.getElementById('change-password-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();
    
    const newPassword = document.getElementById('new-password-input').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    if (newPassword !== confirmPassword) {
        document.getElementById('error-confirm-password').textContent = 'Passwords do not match';
        return;
    }
    
    try {
        const response = await fetch(`/admin/management/${selectedAdminId}/password`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ password: newPassword })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showPopup('Password updated successfully!');
            closeChangePasswordModal();
        } else {
            if (data.errors && data.errors.password) {
                document.getElementById('error-new-password').textContent = data.errors.password[0];
            } else {
                showPopup(data.message || 'Error updating password');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showPopup('Error updating password');
    }
});

// Toggle Status
async function toggleStatus() {
    const currentStatus = '{{ $selectedAdmin->status }}';
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    
    showConfirmPopup(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this admin?`, async () => {
        try {
            const response = await fetch(`/admin/management/${selectedAdminId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: newStatus })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showPopup('Status updated successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showPopup(data.message || 'Error updating status');
            }
        } catch (error) {
            console.error('Error:', error);
            showPopup('Error updating status');
        }
    });
}

// Delete Admin
function deleteSelectedAdmin() {
    showConfirmPopup('Are you sure you want to delete this admin? This action cannot be undone.', async () => {
        try {
            const response = await fetch(`/admin/management/${selectedAdminId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showPopup('Admin deleted successfully!');
                setTimeout(() => window.location.href = '{{ route("admin.management") }}', 1500);
            } else {
                showPopup(data.message || 'Error deleting admin');
            }
        } catch (error) {
            console.error('Error:', error);
            showPopup('Error deleting admin');
        }
    });
}

// Helper Functions
function displayErrors(errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const errorElement = document.getElementById(`error-${field.replace('_', '-')}`);
        if (errorElement) {
            errorElement.textContent = messages[0];
        }
    }
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
}

function showPopup(message) {
    document.getElementById('popup-message').textContent = message;
    document.getElementById('popup-buttons-container').style.display = 'none';
    document.getElementById('global-popup').style.display = 'flex';
    
    setTimeout(() => {
        document.getElementById('global-popup').style.display = 'none';
    }, 2000);
}

function showConfirmPopup(message, callback) {
    document.getElementById('popup-message').textContent = message;
    document.getElementById('popup-buttons-container').style.display = 'flex';
    document.getElementById('global-popup').style.display = 'flex';
    
    document.getElementById('popup-ok-btn').onclick = function() {
        document.getElementById('global-popup').style.display = 'none';
        callback();
    };
    
    document.getElementById('popup-cancel-btn').onclick = function() {
        document.getElementById('global-popup').style.display = 'none';
    };
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal') || event.target.classList.contains('popup-modal')) {
        event.target.style.display = 'none';
    }
}
</script>
@endsection
