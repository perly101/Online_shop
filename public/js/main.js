// Main page JavaScript functionality

function goToUserType() {
    // Navigate to user type selection page
    window.location.href = 'user-type.html';
}

// Add some interactive effects
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to the shop now button
    const shopNowBtn = document.querySelector('.shop-now-btn');
    if (shopNowBtn) {
        shopNowBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        shopNowBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
    
    // Add subtle animation to the background buildings
    const buildings = document.querySelectorAll('.building');
    buildings.forEach((building, index) => {
        building.style.animation = `float ${3 + index}s ease-in-out infinite`;
    });
});

// Add CSS animation for floating buildings
const style = document.createElement('style');
style.textContent = `
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
`;
document.head.appendChild(style);
















