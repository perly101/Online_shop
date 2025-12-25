document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in
    const loggedInStaff = localStorage.getItem('loggedInStaff');
    if (!loggedInStaff) {
        // Redirect to login page if not logged in
        window.location.href = '../admin-login.html';
        return;
    }
    
    // Make sure popup is initialized
    if (typeof initializePopup === 'function') {
        initializePopup();
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


    // ============================================
    // CUSTOMER DATA
    // ============================================
    // Load customer data from localStorage if available, otherwise use default data
    let customers = [];
    const savedCustomers = localStorage.getItem('adminCustomers');
    if (savedCustomers) {
        customers = JSON.parse(savedCustomers);
    } else {
        customers = [
            {
                id: 'ADMIN001',
                computerNumber: 5,
                status: 'Active',
                password: '12345678'
            },
            {
                id: 'ADMIN002',
                computerNumber: 5,
                status: 'Active',
                password: '87654321'
            },
            {
                id: 'ADMIN003',
                computerNumber: 12,
                status: 'Active',
                password: '11111111'
            },
            {
                id: 'ADMIN004',
                computerNumber: 10,
                status: 'Active',
                password: '22222222'
            },
            {
                id: 'ADMIN005',
                computerNumber: 0,
                status: 'Inactive',
                password: '33333333'
            },
            {
                id: 'ADMIN006',
                computerNumber: 3,
                status: 'Active',
                password: '44444444'
            }
        ];
        // Save default data to localStorage
        localStorage.setItem('adminCustomers', JSON.stringify(customers));
    }

    let filteredCustomers = [...customers];
    // Find the highest existing ID number to set nextCustomerId
    let nextCustomerId = 1;
    customers.forEach(customer => {
        if (customer.id.startsWith('ADMIN')) {
            const num = parseInt(customer.id.substring(5));
            if (!isNaN(num) && num >= nextCustomerId) {
                nextCustomerId = num + 1;
            }
        }
    });

    // ============================================
    // DOM ELEMENTS
    // ============================================
    const customerTableBody = document.getElementById('customer-table-body');
    const searchBtn = document.getElementById('search-btn');
    const searchBar = document.getElementById('search-bar');
    const searchInput = document.getElementById('search-input');
    const searchCloseBtn = document.getElementById('search-close-btn');
    const addUserBtn = document.getElementById('add-user-btn');
    const addUserModal = document.getElementById('add-user-modal');
    const addUserForm = document.getElementById('add-user-form');
    const closeAddModal = document.getElementById('close-add-modal');
    const cancelAddUser = document.getElementById('cancel-add-user');


    // ============================================
    // STATE VARIABLES
    // ============================================

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
     * Renders customer table
     */
    function renderCustomers() {
        customerTableBody.innerHTML = '';

        filteredCustomers.forEach(customer => {
            const row = document.createElement('tr');
            
            row.innerHTML = `
                <td></td>
                <td class="customer-id">${customer.id}</td>
                <td class="computer-number">${customer.computerNumber}</td>
                <td class="status-cell"><span class="status-${customer.status.toLowerCase()}">${customer.status}</span></td>
                <td class="password-cell">${customer.password}</td>
            `;
            
            // Add click handler for row
            row.addEventListener('click', (e) => {
                window.location.href = `admin-detail.html?id=${customer.id}`;
            });
            
            row.style.cursor = 'pointer';
            customerTableBody.appendChild(row);
        });
    }

    /**
     * Filters customers based on search
     */
    function filterCustomers(searchTerm) {
        if (!searchTerm.trim()) {
            filteredCustomers = [...customers];
        } else {
            filteredCustomers = customers.filter(customer => {
                const search = searchTerm.toLowerCase();
                return customer.id.toLowerCase().includes(search) ||
                       customer.status.toLowerCase().includes(search);
            });
        }
        renderCustomers();
    }

    /**
     * Shows search bar
     */
    function showSearchBar() {
        searchBar.style.display = 'flex';
        searchInput.focus();
    }

    /**
     * Hides search bar
     */
    function hideSearchBar() {
        searchBar.style.display = 'none';
        searchInput.value = '';
        filterCustomers('');
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
            window.showPopup('Please enter a valid ID in the format ADMIN001');
            return;
        }

        // Validate password format
        if (!password || !/^\d{8}$/.test(password)) {
            window.showPopup('Password must be exactly 8 digits');
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
            console.log('Showing popup with message:', message);
            // Use global functions directly instead of window object
            showPopup(message);
            return;
        }

        const newCustomer = {
            id: userId,
            computerNumber: computerNumber,
            status: status,
            password: password
        };

        console.log('Adding new customer:', newCustomer);
        customers.push(newCustomer);
        nextCustomerId++;
        // Save updated customers to localStorage
        localStorage.setItem('adminCustomers', JSON.stringify(customers));
        filterCustomers(searchInput.value);
        closeAddUserModal();
        console.log('Customer added successfully');
    }



    // ============================================
    // EVENT LISTENERS
    // ============================================
    
    searchBtn.addEventListener('click', showSearchBar);
    searchCloseBtn.addEventListener('click', hideSearchBar);
    searchInput.addEventListener('input', (e) => {
        filterCustomers(e.target.value);
    });

    addUserBtn.addEventListener('click', openAddUserModal);
    closeAddModal.addEventListener('click', closeAddUserModal);
    cancelAddUser.addEventListener('click', closeAddUserModal);
    addUserForm.addEventListener('submit', addUser);



    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === addUserModal) {
            closeAddUserModal();
        }
    });

    // ============================================
    // INITIALIZATION
    // ============================================
    
    renderCustomers();
});