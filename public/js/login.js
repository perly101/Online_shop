// Login page JavaScript functionality

function goBack() {
    // Navigate back to user type selection page
    window.location.href = 'user-type.html';
}

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.querySelector('.password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z" fill="currentColor"/>
            </svg>
        `;
    } else {
        passwordInput.type = 'password';
        toggleBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
            </svg>
        `;
    }
}

// Custom alert function using popup if available, otherwise fallback to alert
function showAlert(message, title = "") {
    // Check if custom popup is available
    if (typeof showCustomPopup === 'function') {
        showCustomPopup(message, title);
    } else {
        // Fallback to native alert
        alert(message);
    }
}

function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    // Basic validation
    if (!email || !password) {
        showAlert('Please fill in all fields.');
        return;
    }
    
    // Check against stored users array (if available)
    const users = JSON.parse(localStorage.getItem('users')) || [];
    const matched = users.find(u => u.email === email && u.password === password);
    if (matched) {
        // set unified currentUser with full user data and redirect to shop home
        localStorage.setItem('currentUser', JSON.stringify(matched));
        window.location.href = '/shop';
        return;
    }

    // Fallback: check legacy single-user keys (older demo flow)
    const storedEmail = localStorage.getItem('userEmail');
    const storedPassword = localStorage.getItem('userPassword');
    if (email === storedEmail && password === storedPassword) {
        const fallbackUser = { id: Date.now(), email: email };
        localStorage.setItem('currentUser', JSON.stringify({ id: fallbackUser.id, email: fallbackUser.email, loginTime: new Date().toISOString() }));
        window.location.href = '/shop';
        return;
    }

    showAlert('Invalid email or password. Please try again or create an account.');
}

function handleForgotPassword() {
    showAlert('Password reset functionality will be implemented later. Please contact support.');
}

// Add interactive effects
document.addEventListener('DOMContentLoaded', function() {
    // Add focus effects to input fields
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.boxShadow = '0 0 0 3px rgba(212, 175, 55, 0.3)';
            this.style.background = '#e0e0e0';
        });
        
        input.addEventListener('blur', function() {
            this.style.boxShadow = 'none';
            this.style.background = '#f0f0f0';
        });
    });
    
    // Add button animations
    const loginBtn = document.querySelector('.login-btn');
    if (loginBtn) {
        loginBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        loginBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
    
    // Add close button animation
    const closeBtn = document.querySelector('.close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(90deg)';
        });
        
        closeBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    }
    
    // Add forgot password link functionality
    const forgotLink = document.querySelector('.forgot-link');
    if (forgotLink) {
        forgotLink.addEventListener('click', function(e) {
            e.preventDefault();
            handleForgotPassword();
        });
    }
});
