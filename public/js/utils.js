/**
 * Global popup functionality for replacing browser alerts
 */

// Track if popup has been initialized
let popupInitialized = false;

// Initialize popup when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePopup);
} else {
    initializePopup();
}

// Create popup container if it doesn't exist
function initializePopup() {
    // If already initialized, don't do it again
    if (popupInitialized) {
        return;
    }
    
    // Check if popup already exists in DOM
    if (!document.getElementById('global-popup')) {
        // Popup doesn't exist, create it
        const popupHTML = `
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
        `;
        document.body.insertAdjacentHTML('beforeend', popupHTML);
    }
    
    // Attach event listeners once
    attachEventListeners();
    popupInitialized = true;
}

// Attach event listeners to popup elements (only once)
function attachEventListeners() {
    // Close button
    const closeBtn = document.getElementById('popup-close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', handleCloseClick);
    }
    
    // Click outside to close
    const popup = document.getElementById('global-popup');
    if (popup) {
        popup.addEventListener('click', handleOutsideClick);
    }
}

// Handle close button click
function handleCloseClick() {
    closePopup();
}

// Handle click outside popup
function handleOutsideClick(event) {
    if (event.target.id === 'global-popup') {
        closePopup();
    }
}

// Handle cancel button click
function handleCancelClick(onCancel) {
    return function() {
        closePopup();
        if (onCancel) onCancel();
    };
}

// Handle OK button click (now labeled as "Yes")
function handleOkClick(onConfirm) {
    return function() {
        closePopup();
        if (onConfirm) onConfirm();
    };
}

// Show popup with message (simple alert)
function showPopup(message) {
    // Ensure popup is initialized
    if (!popupInitialized) {
        initializePopup();
    }
    
    const popup = document.getElementById('global-popup');
    const popupMessage = document.getElementById('popup-message');
    const buttonsContainer = document.getElementById('popup-buttons-container');
    const closeBtn = document.getElementById('popup-close-btn');
    
    if (popup && popupMessage) {
        // Hide buttons for simple alerts
        if (buttonsContainer) buttonsContainer.style.display = 'none';
        
        // Hide close button for simple alerts
        if (closeBtn) closeBtn.style.display = 'none';
        
        popupMessage.textContent = message;
        popup.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

// Show confirm dialog with callback
function showConfirm(message, onConfirm, onCancel, showCloseButton = false) {
    // Ensure popup is initialized
    if (!popupInitialized) {
        initializePopup();
    }
    
    const popup = document.getElementById('global-popup');
    const popupMessage = document.getElementById('popup-message');
    const buttonsContainer = document.getElementById('popup-buttons-container');
    const closeBtn = document.getElementById('popup-close-btn');
    const cancelButton = document.getElementById('popup-cancel-btn');
    const okButton = document.getElementById('popup-ok-btn');
    
    if (popup && popupMessage) {
        // Show buttons for confirm dialogs
        if (buttonsContainer) buttonsContainer.style.display = 'block';
        
        // Show/hide close button based on parameter
        if (closeBtn) {
            closeBtn.style.display = showCloseButton ? 'block' : 'none';
        }
        
        popupMessage.textContent = message;
        popup.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Remove any existing event listeners to avoid duplicates
        if (cancelButton) {
            cancelButton.removeEventListener('click', cancelButton._handler);
            const cancelHandler = handleCancelClick(onCancel);
            cancelButton.addEventListener('click', cancelHandler);
            cancelButton._handler = cancelHandler; // Store reference for cleanup
        }
        
        if (okButton) {
            okButton.removeEventListener('click', okButton._handler);
            const okHandler = handleOkClick(onConfirm);
            okButton.addEventListener('click', okHandler);
            okButton._handler = okHandler; // Store reference for cleanup
        }
    }
}

// Close popup
function closePopup() {
    const popup = document.getElementById('global-popup');
    if (popup) {
        popup.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Make functions globally accessible
window.showPopup = showPopup;
window.showConfirm = showConfirm;
window.closePopup = closePopup;
window.initializePopup = initializePopup;