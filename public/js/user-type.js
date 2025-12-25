// User type selection page JavaScript functionality

function goBack() {
    // Navigate back to main page
    window.location.href = 'index.html';
}

function goToStaffLogin() {
    // Navigate to staff login page
    window.location.href = '/admin/login';
}

function goToCustomerSignup() {
    // Navigate to customer signup page
    window.location.href = 'signup.html';
}

// Add interactive effects
document.addEventListener('DOMContentLoaded', function() {
    // Add click animations to buttons
    const buttons = document.querySelectorAll('.user-type-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
    
    // Add hover effects
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
















