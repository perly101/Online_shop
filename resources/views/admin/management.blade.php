@extends('layouts.app')

@section('title', 'Admin Management - Absolute Essential Trading')

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
    <button class="back-btn" onclick="history.back()">
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
    <!-- Admin Accounts Card -->
    <div class="customer-accounts-card">
        <div class="accounts-header">
            <h3 class="accounts-title">Admin Accounts</h3>
        </div>

        <div class="table-container">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Computer Number</th>
                        <th>Status</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody id="admin-table-body">
                    @foreach($admins as $admin)
                    <tr data-admin-id="{{ $admin->id }}" onclick="window.location.href='{{ route('admin.detail', $admin->id) }}'" style="cursor: pointer;">
                        <td>
                            <button class="btn-delete-admin" onclick="event.stopPropagation(); deleteAdmin({{ $admin->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                        <td>{{ $admin->admin_id }}</td>
                        <td>{{ $admin->computer_number }}</td>
                        <td>
                            <select class="status-select" onclick="event.stopPropagation();" onchange="updateStatus({{ $admin->id }}, this.value)">
                                <option value="active" {{ $admin->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $admin->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn-change-password" onclick="event.stopPropagation(); changePassword({{ $admin->id }})">
                                Change Password
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
<div id="change-password-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Change Password</h3>
            <button class="close-btn" id="close-password-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="change-password-form">
                @csrf
                <input type="hidden" id="change-password-admin-id">
                <div class="form-group">
                    <label>New Password (8 digits)</label>
                    <input type="password" id="change-password-input" class="form-input" placeholder="12345678" required minlength="8" maxlength="8">
                    <span class="error-message" id="error-change-password"></span>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" id="cancel-change-password">Cancel</button>
                    <button type="submit" class="btn-save">Update Password</button>
                </div>
            </form>
        </div>
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

.btn-delete-admin {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-delete-admin:hover {
    background: #c0392b;
}

.status-select {
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}

.btn-change-password {
    background: #3498db;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-change-password:hover {
    background: #2980b9;
}
</style>

<script>
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
    const rows = document.querySelectorAll('#admin-table-body tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
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

// Update Status
async function updateStatus(adminId, status) {
    try {
        const response = await fetch(`/admin/management/${adminId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showPopup('Status updated successfully!');
        } else {
            showPopup(data.message || 'Error updating status');
        }
    } catch (error) {
        console.error('Error:', error);
        showPopup('Error updating status');
    }
}

// Delete Admin
function deleteAdmin(adminId) {
    showConfirmPopup('Are you sure you want to delete this admin?', async () => {
        try {
            const response = await fetch(`/admin/management/${adminId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showPopup('Admin deleted successfully!');
                document.querySelector(`tr[data-admin-id="${adminId}"]`).remove();
            } else {
                showPopup(data.message || 'Error deleting admin');
            }
        } catch (error) {
            console.error('Error:', error);
            showPopup('Error deleting admin');
        }
    });
}

// Change Password Modal
function changePassword(adminId) {
    document.getElementById('change-password-admin-id').value = adminId;
    document.getElementById('change-password-modal').style.display = 'flex';
}

document.getElementById('close-password-modal').addEventListener('click', closePasswordModal);
document.getElementById('cancel-change-password').addEventListener('click', closePasswordModal);

function closePasswordModal() {
    document.getElementById('change-password-modal').style.display = 'none';
    document.getElementById('change-password-form').reset();
    clearErrors();
}

document.getElementById('change-password-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();
    
    const adminId = document.getElementById('change-password-admin-id').value;
    const password = document.getElementById('change-password-input').value;
    
    try {
        const response = await fetch(`/admin/management/${adminId}/password`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showPopup('Password updated successfully!');
            closePasswordModal();
        } else {
            if (data.errors) {
                document.getElementById('error-change-password').textContent = data.errors.password[0];
            } else {
                showPopup(data.message || 'Error updating password');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showPopup('Error updating password');
    }
});

// Popup Functions
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
</script>
@endsection
