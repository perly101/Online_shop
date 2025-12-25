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
    // CUSTOMER DATA
    // ============================================
    let customers = [
        {
            id: 'ADMIN001',
            computerNumber: 5,
            status: 'Active',
            password: '12345678',
            image: 'https://via.placeholder.com/150/CCCCCC/666666?text=ADMIN001'
        },
        {
            id: 'ADMIN002',
            computerNumber: 5,
            status: 'Active',
            password: '87654321',
            image: 'https://via.placeholder.com/150/CCCCCC/666666?text=ADMIN002'
        },
        {
            id: 'ADMIN003',
            computerNumber: 12,
            status: 'Active',
            password: '11111111',
            image: 'https://via.placeholder.com/150/CCCCCC/666666?text=ADMIN003'
        },
        {
            id: 'ADMIN004',
            computerNumber: 10,
            status: 'Active',
            password: '22222222',
            image: 'https://via.placeholder.com/150/CCCCCC/666666?text=ADMIN004'
        },
        {
            id: 'ADMIN005',
            computerNumber: 0,
            status: 'Inactive',
            password: '33333333',
            image: 'https://via.placeholder.com/150/CCCCCC/666666?text=ADMIN005'
        },
        {
            id: 'ADMIN006',
            computerNumber: 3,
            status: 'Active',
            password: '44444444',
            image: 'https://via.placeholder.com/150/CCCCCC/666666?text=ADMIN006'
        }
    ];

    /**
     * Saves customer data to localStorage
     */
    function saveCustomersToLocalStorage() {
        localStorage.setItem('adminCustomers', JSON.stringify(customers));
    }

    /**
     * Loads customer data from localStorage
     */
    function loadCustomersFromLocalStorage() {
        const storedCustomers = localStorage.getItem('adminCustomers');
        if (storedCustomers) {
            try {
                customers = JSON.parse(storedCustomers);
            } catch (e) {
                console.error('Error parsing stored customers data:', e);
            }
        }
    }

    // ============================================
    // GET SELECTED CUSTOMER ID FROM URL
    // ============================================
    const urlParams = new URLSearchParams(window.location.search);
    let selectedCustomerId = urlParams.get('id') || 'ADMIN001';

    // ============================================
    // DOM ELEMENTS
    // ============================================
    const customerList = document.getElementById('customer-list');
    const userDetailPanel = document.getElementById('user-detail-panel');
    const userName = document.getElementById('user-name');
    const userEmail = document.getElementById('user-email');
    const userTotalSpent = document.getElementById('user-total-spent');
    const changePasswordBtn = document.getElementById('change-password-btn');
    const addUserBtn = document.getElementById('add-user-btn');
    const deleteAccountBtn = document.querySelector('.accounts-actions .action-icon-btn'); // The delete button is now the first (and only) button
    
    // Add User Modal Elements
    const addUserModal = document.getElementById('add-user-modal');
    const addUserForm = document.getElementById('add-user-form');
    const closeAddModal = document.getElementById('close-add-modal');
    const cancelAddUser = document.getElementById('cancel-add-user');
    
    // Change Password Modal Elements
    const changePasswordModal = document.getElementById('change-password-modal');
    const closeChangePassword = document.getElementById('close-change-password');
    const cancelChangePassword = document.getElementById('cancel-change-password');
    const savePasswordBtn = document.getElementById('save-password');
    const currentPasswordInput = document.getElementById('current-password');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');

    // ============================================
    // FUNCTIONS
    // ============================================

    /**
     * Formats currency
     */
    function formatCurrency(amount) {
        return `Php. ${amount.toLocaleString()}`;
    }

    /**
     * Renders customer list
     */
    function renderCustomerList() {
        console.log('Rendering customer list');
        customerList.innerHTML = '';

        customers.forEach(customer => {
            const item = document.createElement('div');
            item.className = `customer-list-item ${customer.id == selectedCustomerId ? 'active' : ''}`;
            item.innerHTML = `
                <span class="customer-id">${customer.id}</span>
                <span>${customer.computerNumber}</span>
                <span class="status-${customer.status.toLowerCase()}">${customer.status}</span>
                <span>${customer.password}</span>
            `;
            item.addEventListener('click', () => {
                selectedCustomerId = customer.id;
                updateUserDetail();
                renderCustomerList();
            });
            customerList.appendChild(item);
        });
        console.log('Customer list rendered');
    }

    /**
     * Updates user detail panel
     */
    function updateUserDetail() {
        console.log('Updating user detail for:', selectedCustomerId);
        const customer = customers.find(c => c.id == selectedCustomerId);
        if (!customer) {
            console.log('Customer not found');
            return;
        }
        console.log('Found customer:', customer);

        userName.textContent = customer.id;
        userEmail.textContent = `Computer #${customer.computerNumber}`;
        userTotalSpent.textContent = `Status: ${customer.status}`;
        console.log('User detail updated');
    }
    
    /**
     * Opens the change password modal
     */
    function openChangePasswordModal() {
        // Clear any previous input
        currentPasswordInput.value = '';
        newPasswordInput.value = '';
        confirmPasswordInput.value = '';
        
        // Show the modal
        changePasswordModal.style.display = 'block';
    }
    
    /**
     * Closes the change password modal
     */
    function closeChangePasswordModal() {
        changePasswordModal.style.display = 'none';
    }
    
    /**
     * Changes the password for the selected customer
     */
    function changePassword() {
        const currentPassword = currentPasswordInput.value;
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        // Validate inputs
        if (!currentPassword || !newPassword || !confirmPassword) {
            showPopup('Please fill in all fields.');
            return;
        }
        
        // Check if new password matches confirm password
        if (newPassword !== confirmPassword) {
            showPopup('New password and confirm password do not match.');
            return;
        }
        
        // Find the customer
        const customer = customers.find(c => c.id == selectedCustomerId);
        if (!customer) {
            showPopup('Customer not found.');
            return;
        }
        
        // Check if current password is correct
        if (currentPassword !== customer.password) {
            showPopup('Current password is incorrect.');
            return;
        }
        
        // Check if new password is different from current password
        if (newPassword === customer.password) {
            showPopup('New password must be different from current password.');
            return;
        }
        
        // Check if new password meets minimum length requirement
        if (newPassword.length < 8) {
            showPopup('Password must be at least 8 characters long.');
            return;
        }
        
        // Update the password
        customer.password = newPassword;
        
        // Save to localStorage
        saveCustomersToLocalStorage();
        
        // Close the modal
        closeChangePasswordModal();
        
        // Show success message
        showPopup('Password changed successfully.');
        
        // Re-render the customer list to show the updated password
        renderCustomerList();
    }
    
    /**
     * Opens add user modal
     */
    function openAddUserModal() {
        addUserModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    /**
     * Closes add user modal
     */
    function closeAddUserModal() {
        addUserModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        addUserForm.reset();
    }
    
    /**
     * Adds a new user
     */
    function addUser(event) {
        event.preventDefault();
        
        // Get form values
        const userId = document.getElementById('new-user-id').value.trim();
        const computerNumberInput = document.getElementById('new-user-computer-number').value;
        const computerNumber = parseInt(computerNumberInput) || 0;
        const password = document.getElementById('new-user-password').value;
        const status = 'Enter ID'; // Default status
        
        // Validate ID format
        if (!userId || !/^ADMIN\d{3}$/.test(userId)) {
            showPopup('Please enter a valid ID in the format ADMIN001');
            return;
        }
        
        // Validate password format
        if (!password || !/^\d{8}$/.test(password)) {
            showPopup('Password must be exactly 8 digits');
            return;
        }
        
        // Check for uniqueness across all fields and collect duplicate information
        const duplicates = [];
        
        // Check ID uniqueness
        if (customers.some(customer => customer.id === userId)) {
            duplicates.push('ID');
        }
        
        // Check Computer Number uniqueness (ensure proper type comparison)
        const compNumberToCheck = Number(computerNumber);
        if (customers.some(customer => Number(customer.computerNumber) === compNumberToCheck)) {
            duplicates.push('Computer Number');
        }
        
        // Check Password uniqueness
        if (customers.some(customer => customer.password === password)) {
            duplicates.push('Password');
        }
        
        // If there are duplicates, show combined message
        if (duplicates.length > 0) {
            let message;
            if (duplicates.length === 1) {
                message = `${duplicates[0]} already exists. Please use a unique ${duplicates[0].toLowerCase()}.`;
            } else if (duplicates.length === 2) {
                message = `${duplicates[0]} and ${duplicates[1]} already exist. Please use unique values.`;
            } else {
                message = `${duplicates[0]}, ${duplicates[1]}, and ${duplicates[2]} already exist. Please use unique values.`;
            }
            showPopup(message);
            return;
        }
        
        const newCustomer = {
            id: userId,
            computerNumber: computerNumber,
            status: status,
            password: password
        };
        
        customers.push(newCustomer);
        
        // Save updated customers to localStorage
        saveCustomersToLocalStorage();
        
        // Re-render customer list
        renderCustomerList();
        
        closeAddUserModal();
        
        showPopup('Admin added successfully.');
    }
    
    /**
     * Deletes the selected account
     */
    function deleteAccount() {
        // Find the customer
        const customer = customers.find(c => c.id == selectedCustomerId);
        if (!customer) {
            showPopup('Customer not found.');
            return;
        }
        
        // Prevent deleting the currently logged in user
        const loggedInStaff = localStorage.getItem('loggedInStaff');
        if (loggedInStaff) {
            try {
                const staff = JSON.parse(loggedInStaff);
                if (staff.id === customer.id) {
                    showPopup('You cannot delete your own account.');
                    return;
                }
            } catch (e) {
                console.error('Error parsing logged in staff data:', e);
            }
        }
        
        // Show confirmation popup
        showConfirm(`Are you sure you want to delete account ${customer.id}?`, function() {
            // Remove the customer
            customers = customers.filter(c => c.id !== customer.id);
            
            // Save updated customers to localStorage
            saveCustomersToLocalStorage();
            
            // If we deleted the currently selected customer, select the first one
            if (selectedCustomerId === customer.id) {
                selectedCustomerId = customers.length > 0 ? customers[0].id : '';
            }
            
            // Re-render the customer list
            renderCustomerList();
            updateUserDetail();
            
            showPopup('Account deleted successfully.');
        });
    }
    // ============================================
    // EVENT LISTENERS
    // ============================================
    
    changePasswordBtn.addEventListener('click', () => {
        openChangePasswordModal();
    });
    
    // Add New Admin button event listener
    if (addUserBtn) {
        addUserBtn.addEventListener('click', openAddUserModal);
    }
    
    // Delete Account button event listener
    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', deleteAccount);
    }
    
    // Add User Modal Event Listeners
    if (closeAddModal) {
        closeAddModal.addEventListener('click', closeAddUserModal);
    }
    if (cancelAddUser) {
        cancelAddUser.addEventListener('click', closeAddUserModal);
    }
    if (addUserForm) {
        addUserForm.addEventListener('submit', addUser);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === addUserModal) {
            closeAddUserModal();
        }
    });
    
    // Change Password Modal Event Listeners
    closeChangePassword.addEventListener('click', closeChangePasswordModal);
    cancelChangePassword.addEventListener('click', closeChangePasswordModal);
    savePasswordBtn.addEventListener('click', changePassword);
    
    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === changePasswordModal) {
            closeChangePasswordModal();
        }
    });
    // ============================================
    // INITIALIZATION
    // ============================================
    
    // Load customer data from localStorage
    loadCustomersFromLocalStorage();
    
    console.log('Initializing user detail page');
    console.log('Selected customer ID:', selectedCustomerId);
    console.log('Available customers:', customers);
    
    renderCustomerList();
    updateUserDetail();
    
    console.log('User detail page initialized');
});