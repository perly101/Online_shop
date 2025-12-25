// Global variables
let currentPage = null;
let users = JSON.parse(localStorage.getItem('users')) || [];

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize the application
function initializeApp() {
    // Hide all pages initially
    hideAllPages();
    
    // Add event listeners
    addEventListeners();
    
    // Check if user is already logged in
    checkLoginStatus();
}

// Add event listeners
function addEventListeners() {
    // Form submission listeners
    document.getElementById('signupForm').addEventListener('submit', handleSignup);
    document.getElementById('loginFormElement').addEventListener('submit', handleLogin);
    
    // Input validation listeners
    addInputValidation();
}

// Add input validation
function addInputValidation() {
    const emailInputs = document.querySelectorAll('input[type="email"]');
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    
    emailInputs.forEach(input => {
        input.addEventListener('blur', validateEmail);
        input.addEventListener('input', clearFieldError);
    });
    
    passwordInputs.forEach(input => {
        input.addEventListener('blur', validatePassword);
        input.addEventListener('input', clearFieldError);
    });
}

// Page navigation functions
function hideAllPages() {
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => {
        page.classList.remove('active');
    });
    currentPage = null;
}

function showPage(pageId) {
    hideAllPages();
    document.getElementById(pageId).classList.add('active');
    currentPage = pageId;
}

function goBack() {
    if (currentPage === 'roleSelectionPage') {
        // Go back to home page
        hideAllPages();
        currentPage = null;
    } else if (currentPage === 'createAccountPage' || currentPage === 'loginPage') {
        // Go back to role selection
        showPage('roleSelectionPage');
    }
}

// Role selection functions
function selectRole(role) {
    if (role === 'customer') {
        showPage('createAccountPage');
        clearForm('signupForm');
    } else if (role === 'staff') {
        // For now, show a message that this is for future development
        showMessage('Staff/Admin functionality is coming soon!', 'success');
        // You can add admin login page here in the future
    }
}

// Page navigation functions
function showCreateAccountPage() {
    showPage('createAccountPage');
    clearForm('signupForm');
}

function showLoginPage() {
    showPage('loginPage');
    clearForm('loginFormElement');
}

// Form handling functions
function handleSignup(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const email = formData.get('email').trim();
    const password = formData.get('password');
    
    // Validate inputs
    if (!validateEmail({ target: { value: email } }) || !validatePassword({ target: { value: password } })) {
        return;
    }
    
    // Check if user already exists
    if (userExists(email)) {
        showMessage('User with this email already exists!', 'error');
        return;
    }
    
    // Create new user
    const newUser = {
        id: Date.now(),
        email: email,
        password: password, // In real app, hash this password
        createdAt: new Date().toISOString()
    };
    
    users.push(newUser);
    localStorage.setItem('users', JSON.stringify(users));

    // Auto-login the newly created user and redirect to shop
    localStorage.setItem('currentUser', JSON.stringify({ id: newUser.id, email: newUser.email, loginTime: new Date().toISOString() }));
    showMessage('Account created and logged in! Redirecting to the shop...', 'success');
    clearForm('signupForm');
    setTimeout(() => {
        // redirect to shop home page
        window.location.href = '/shop';
    }, 1100);
}

function handleLogin(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const email = formData.get('email').trim();
    const password = formData.get('password');
    
    // Validate inputs
    if (!validateEmail({ target: { value: email } }) || !validatePassword({ target: { value: password } })) {
        return;
    }
    
    // Find user
    const user = users.find(u => u.email === email && u.password === password);
    
    if (user) {
        // Store login session
        localStorage.setItem('currentUser', JSON.stringify({
            id: user.id,
            email: user.email,
            loginTime: new Date().toISOString()
        }));
        
        showMessage('Login successful! Welcome back!', 'success');
        clearForm('loginFormElement');
        hideAllPages();
        
        // Redirect to shop home page
        setTimeout(() => {
            window.location.href = '/shop';
        }, 900);
    } else {
        showMessage('Invalid email or password!', 'error');
    }
}

// Validation functions
function validateEmail(event) {
    const email = event.target.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!email) {
        showFieldError(event.target, 'Email is required');
        return false;
    }
    
    if (!emailRegex.test(email)) {
        showFieldError(event.target, 'Please enter a valid email address');
        return false;
    }
    
    clearFieldError(event.target);
    return true;
}

function validatePassword(event) {
    const password = event.target.value;
    
    if (!password) {
        showFieldError(event.target, 'Password is required');
        return false;
    }
    
    if (password.length < 6) {
        showFieldError(event.target, 'Password must be at least 6 characters long');
        return false;
    }
    
    clearFieldError(event.target);
    return true;
}

function showFieldError(input, message) {
    clearFieldError(input);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '5px';
    
    input.parentNode.appendChild(errorDiv);
    input.style.borderColor = '#dc3545';
}

function clearFieldError(input) {
    const existingError = input.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    input.style.borderColor = '';
}

// Utility functions
function userExists(email) {
    return users.some(user => user.email === email);
}

function clearForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        // Clear any field errors
        const fieldErrors = form.querySelectorAll('.field-error');
        fieldErrors.forEach(error => error.remove());
        
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            input.style.borderColor = '';
        });
    }
}

function checkLoginStatus() {
    const currentUser = localStorage.getItem('currentUser');
    if (currentUser) {
        const user = JSON.parse(currentUser);
        showMessage(`Welcome back, ${user.email}!`, 'success');
    }
}

// Password visibility toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.parentNode.querySelector('.toggle-password');
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Forgot password functionality
function handleForgotPassword() {
    const email = document.getElementById('loginEmail').value.trim();
    
    if (!email) {
        showMessage('Please enter your email address first', 'error');
        return;
    }
    
    if (!userExists(email)) {
        showMessage('No account found with this email address', 'error');
        return;
    }
    
    // In a real app, you would send a password reset email
    showMessage('Password reset instructions have been sent to your email', 'success');
}

// Shop now functionality
function showShopNow() {
    const currentUser = localStorage.getItem('currentUser');
    
    if (!currentUser) {
        showMessage('Please log in to access the shop', 'error');
        showPage('roleSelectionPage');
        return;
    }
    
    // Redirect to the real shop-home page
    window.location.href = '/shop';
}

// Message display system
function showMessage(text, type = 'success') {
    const container = document.getElementById('messageContainer');
    
    // Remove existing messages
    const existingMessages = container.querySelectorAll('.message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create new message
    const message = document.createElement('div');
    message.className = `message ${type}`;
    message.textContent = text;
    
    container.appendChild(message);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (message.parentNode) {
            message.remove();
        }
    }, 5000);
}

// Logout functionality
function logout() {
    localStorage.removeItem('currentUser');
    showMessage('You have been logged out', 'success');
    hideAllPages();
}

// Add logout button if user is logged in
function addLogoutButton() {
    const currentUser = localStorage.getItem('currentUser');
    if (currentUser) {
        const authButtons = document.querySelector('.auth-buttons');
        if (authButtons && !document.querySelector('.logout-btn')) {
            const logoutBtn = document.createElement('button');
            logoutBtn.className = 'auth-btn logout-btn';
            logoutBtn.textContent = 'Logout';
            logoutBtn.onclick = logout;
            authButtons.appendChild(logoutBtn);
        }
    }
}

// Initialize logout button
document.addEventListener('DOMContentLoaded', addLogoutButton);

// Add some demo data for testing
function addDemoData() {
    if (users.length === 0) {
        const demoUser = {
            id: 1,
            email: 'demo@example.com',
            password: 'password123',
            createdAt: new Date().toISOString()
        };
        users.push(demoUser);
        localStorage.setItem('users', JSON.stringify(users));
    }
}

// Initialize demo data
addDemoData();

// Add keyboard shortcuts
document.addEventListener('keydown', function(event) {
    // Escape key to close pages
    if (event.key === 'Escape' && currentPage) {
        goBack();
    }
    
    // Enter key to submit forms
    if (event.key === 'Enter' && currentPage) {
        const form = document.querySelector('.page.active form');
        if (form) {
            form.dispatchEvent(new Event('submit'));
        }
    }
});

// Add form animations
function addFormAnimations() {
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => {
        page.addEventListener('animationend', function() {
            this.style.animation = '';
        });
    });
}

// Initialize animations
addFormAnimations();

// Add loading states to buttons
function addLoadingState(button) {
    button.classList.add('loading');
    button.disabled = true;
    const originalText = button.textContent;
    button.textContent = 'Processing...';
    
    return function removeLoadingState() {
        button.classList.remove('loading');
        button.disabled = false;
        button.textContent = originalText;
    };
}

// Enhanced form submission with loading states
const originalHandleSignup = handleSignup;
const originalHandleLogin = handleLogin;

function handleSignup(event) {
    const button = event.target.querySelector('.submit-btn');
    const removeLoading = addLoadingState(button);
    
    setTimeout(() => {
        originalHandleSignup(event);
        removeLoading();
    }, 1000);
}

function handleLogin(event) {
    const button = event.target.querySelector('.submit-btn');
    const removeLoading = addLoadingState(button);
    
    setTimeout(() => {
        originalHandleLogin(event);
        removeLoading();
    }, 1000);
}

// Add click outside to close pages
document.addEventListener('click', function(event) {
    if (currentPage && !event.target.closest('.page') && !event.target.closest('.shop-now-btn')) {
        hideAllPages();
    }
});

// Add smooth scrolling for better UX
function smoothScrollTo(element) {
    element.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}

// Add form focus management
function manageFormFocus() {
    const activePage = document.querySelector('.page.active');
    if (activePage) {
        const firstInput = activePage.querySelector('input');
        if (firstInput) {
            firstInput.focus();
        }
    }
}

// Call focus management when pages are shown
const originalShowLoginPage = showLoginPage;
const originalShowCreateAccountPage = showCreateAccountPage;

function showLoginPage() {
    originalShowLoginPage();
    setTimeout(manageFormFocus, 100);
}

function showCreateAccountPage() {
    originalShowCreateAccountPage();
    setTimeout(manageFormFocus, 100);
}
