/**
 * Crewing Module JavaScript
 * PT Indo Ocean Crew Services
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize notifications badge
    updateNotificationBadge();

    // Alert close buttons
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('.alert').remove();
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
});

// Update notification badge
function updateNotificationBadge() {
    const badge = document.getElementById('notificationBadge');
    if (!badge) return;

    // This could be replaced with an AJAX call to get real count
    fetch('/PT_indoocean/recruitment/public/api/notifications/count')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(() => {
            badge.style.display = 'none';
        });
}

// Confirm before dangerous actions
function confirmAction(message) {
    return confirm(message || 'Are you sure?');
}

// Format date for display
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add toast styles
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 1001;
    }
    
    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }
    
    .toast-success {
        border-left: 4px solid #27ae60;
    }
    
    .toast-success i {
        color: #27ae60;
    }
    
    .toast-info {
        border-left: 4px solid #3498db;
    }
    
    .toast-info i {
        color: #3498db;
    }
    
    .toast-error {
        border-left: 4px solid #e74c3c;
    }
    
    .toast-error i {
        color: #e74c3c;
    }
`;
document.head.appendChild(toastStyles);
