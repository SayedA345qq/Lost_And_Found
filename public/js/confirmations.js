// Global confirmation system
class ConfirmationSystem {
    constructor() {
        this.currentAction = null;
        this.currentForm = null;
    }

    // Show confirmation modal
    showConfirmation(options = {}) {
        const {
            title = 'Confirm Action',
            message = 'Are you sure you want to proceed?',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            confirmClass = 'bg-red-500 hover:bg-red-700',
            icon = 'warning',
            onConfirm = null,
            modalId = 'confirmationModal'
        } = options;

        // Update modal content
        document.getElementById(modalId + 'Title').textContent = title;
        document.getElementById(modalId + 'Message').textContent = message;
        
        const confirmBtn = document.getElementById(modalId + 'Confirm');
        confirmBtn.textContent = confirmText;
        confirmBtn.className = `px-4 py-2 ${confirmClass} text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2`;
        
        // Set up confirm action
        confirmBtn.onclick = () => {
            if (onConfirm) {
                onConfirm();
            }
            this.closeModal(modalId);
        };

        // Show modal
        document.getElementById(modalId).classList.remove('hidden');
    }

    // Close modal
    closeModal(modalId = 'confirmationModal') {
        document.getElementById(modalId).classList.add('hidden');
        this.currentAction = null;
        this.currentForm = null;
    }

    // Confirm delete post
    confirmDeletePost(postId, postTitle, redirectUrl = null) {
        this.showConfirmation({
            title: 'Delete Post',
            message: `Are you sure you want to delete "${postTitle}"? This action cannot be undone.`,
            confirmText: 'Delete',
            confirmClass: 'bg-red-500 hover:bg-red-700',
            icon: 'warning',
            onConfirm: () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/posts/${postId}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Confirm bulk delete posts
    confirmBulkDelete(count) {
        this.showConfirmation({
            title: 'Delete Multiple Posts',
            message: `Are you sure you want to delete ${count} selected post(s)? This action cannot be undone.`,
            confirmText: 'Delete All',
            confirmClass: 'bg-red-500 hover:bg-red-700',
            icon: 'warning',
            onConfirm: () => {
                document.getElementById('bulk-delete-form').submit();
            }
        });
    }

    // Confirm status update
    confirmStatusUpdate(postTitle, newStatus) {
        const statusText = newStatus === 'resolved' ? 'mark as resolved' : 'mark as active';
        this.showConfirmation({
            title: 'Update Status',
            message: `Are you sure you want to ${statusText} "${postTitle}"?`,
            confirmText: 'Update',
            confirmClass: 'bg-blue-500 hover:bg-blue-700',
            icon: 'info',
            onConfirm: () => {
                if (this.currentForm) {
                    this.currentForm.submit();
                }
            }
        });
    }

    // Confirm claim submission
    confirmClaim(postTitle) {
        this.showConfirmation({
            title: 'Submit Claim',
            message: `Are you sure you want to claim "${postTitle}"? The owner will be notified.`,
            confirmText: 'Submit Claim',
            confirmClass: 'bg-green-500 hover:bg-green-700',
            icon: 'info',
            onConfirm: () => {
                if (this.currentForm) {
                    this.currentForm.submit();
                }
            }
        });
    }

    // Confirm found notification
    confirmFoundNotification(postTitle) {
        this.showConfirmation({
            title: 'Notify Owner',
            message: `Are you sure you want to notify the owner that you found "${postTitle}"?`,
            confirmText: 'Send Notification',
            confirmClass: 'bg-orange-500 hover:bg-orange-700',
            icon: 'info',
            onConfirm: () => {
                if (this.currentForm) {
                    this.currentForm.submit();
                }
            }
        });
    }

    // Confirm claim response (accept/reject)
    confirmClaimResponse(action, claimerName) {
        const actionText = action === 'accept' ? 'accept' : 'reject';
        const actionColor = action === 'accept' ? 'bg-green-500 hover:bg-green-700' : 'bg-red-500 hover:bg-red-700';
        
        this.showConfirmation({
            title: `${action.charAt(0).toUpperCase() + action.slice(1)} Claim`,
            message: `Are you sure you want to ${actionText} the claim from ${claimerName}?`,
            confirmText: action.charAt(0).toUpperCase() + action.slice(1),
            confirmClass: actionColor,
            icon: action === 'accept' ? 'info' : 'warning',
            onConfirm: () => {
                if (this.currentForm) {
                    this.currentForm.submit();
                }
            }
        });
    }

    // Confirm comment deletion
    confirmDeleteComment() {
        this.showConfirmation({
            title: 'Delete Comment',
            message: 'Are you sure you want to delete this comment? This action cannot be undone.',
            confirmText: 'Delete',
            confirmClass: 'bg-red-500 hover:bg-red-700',
            icon: 'warning',
            onConfirm: () => {
                if (this.currentForm) {
                    this.currentForm.submit();
                }
            }
        });
    }

    // Confirm logout
    confirmLogout() {
        this.showConfirmation({
            title: 'Logout',
            message: 'Are you sure you want to logout?',
            confirmText: 'Logout',
            confirmClass: 'bg-gray-500 hover:bg-gray-700',
            icon: 'info',
            onConfirm: () => {
                if (this.currentForm) {
                    this.currentForm.submit();
                }
            }
        });
    }

    // Confirm clear conversation
    confirmClearConversation() {
        this.showConfirmation({
            title: 'Clear Conversation',
            message: 'Are you sure you want to clear this conversation? This will remove it from your inbox but the other user will still see it unless they also clear it.',
            confirmText: 'Clear Conversation',
            confirmClass: 'bg-red-500 hover:bg-red-700',
            icon: 'warning',
            onConfirm: () => {
                if (this.currentForm) {
                    this.currentForm.submit();
                }
            }
        });
    }

    // Show success message
    showSuccess(message, duration = 3000) {
        this.showToast(message, 'success', duration);
    }

    // Show error message
    showError(message, duration = 5000) {
        this.showToast(message, 'error', duration);
    }

    // Show info message
    showInfo(message, duration = 3000) {
        this.showToast(message, 'info', duration);
    }

    // Show toast notification
    showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 
                       type === 'error' ? 'bg-red-500' : 
                       type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
        
        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, duration);
    }
}

// Initialize global confirmation system
const confirmationSystem = new ConfirmationSystem();

// Global helper functions
function closeModal(modalId) {
    confirmationSystem.closeModal(modalId);
}

function confirmDeletePost(postId, postTitle) {
    confirmationSystem.confirmDeletePost(postId, postTitle);
}

function confirmBulkDelete(count) {
    confirmationSystem.confirmBulkDelete(count);
}

function confirmStatusUpdate(form, postTitle, newStatus) {
    confirmationSystem.currentForm = form;
    confirmationSystem.confirmStatusUpdate(postTitle, newStatus);
}

function confirmClaim(form, postTitle) {
    confirmationSystem.currentForm = form;
    confirmationSystem.confirmClaim(postTitle);
}

function confirmFoundNotification(form, postTitle) {
    confirmationSystem.currentForm = form;
    confirmationSystem.confirmFoundNotification(postTitle);
}

function confirmClaimResponse(form, action, claimerName) {
    confirmationSystem.currentForm = form;
    confirmationSystem.confirmClaimResponse(action, claimerName);
}

function confirmDeleteComment(form) {
    confirmationSystem.currentForm = form;
    confirmationSystem.confirmDeleteComment();
}

function confirmLogout(form) {
    confirmationSystem.currentForm = form;
    confirmationSystem.confirmLogout();
}

function confirmClearConversation(form) {
    confirmationSystem.currentForm = form;
    confirmationSystem.confirmClearConversation();
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('[id$="Modal"]');
    modals.forEach(modal => {
        if (event.target === modal) {
            confirmationSystem.closeModal(modal.id);
        }
    });
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const visibleModals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
        visibleModals.forEach(modal => {
            confirmationSystem.closeModal(modal.id);
        });
    }
});

// Show success/error messages from session
document.addEventListener('DOMContentLoaded', function() {
    // Check for Laravel session messages
    const successMessage = document.querySelector('meta[name="success-message"]');
    const errorMessage = document.querySelector('meta[name="error-message"]');
    
    if (successMessage) {
        confirmationSystem.showSuccess(successMessage.getAttribute('content'));
    }
    
    if (errorMessage) {
        confirmationSystem.showError(errorMessage.getAttribute('content'));
    }
});