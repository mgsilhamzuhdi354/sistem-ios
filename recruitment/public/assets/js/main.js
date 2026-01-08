/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Main JavaScript
 */

// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.querySelector('.nav-menu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });

    // Alert close button
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    alertCloseButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const alert = this.closest('.alert');
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.style.display = 'none';
            }, 300);
        });
    });
});

// Form validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(function (field) {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });

    return isValid;
}

// Confirm delete
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Format currency
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
        minimumFractionDigits: 0
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// File size formatter
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Preview uploaded file
function previewFile(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview || !input.files || !input.files[0]) return;

    const file = input.files[0];

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; max-height: 200px;">';
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<div class="file-preview"><i class="fas fa-file"></i><span>' + file.name + '</span><span class="size">' + formatFileSize(file.size) + '</span></div>';
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function () {
    const searchInputs = document.querySelectorAll('.search-input');

    searchInputs.forEach(function (input) {
        let timeout;
        input.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                // Trigger search or filter
                const searchEvent = new CustomEvent('search', { detail: input.value });
                input.dispatchEvent(searchEvent);
            }, 300);
        });
    });
});

// Tab switching
function showTab(tabId) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(function (tab) {
        tab.classList.remove('active');
    });

    // Deactivate all tab buttons
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.classList.remove('active');
    });

    // Show selected tab
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }

    // Activate selected button
    const selectedBtn = document.querySelector('[data-tab="' + tabId + '"]');
    if (selectedBtn) {
        selectedBtn.classList.add('active');
    }
}

// Countdown timer for interviews
function startCountdown(elementId, seconds, onComplete) {
    const element = document.getElementById(elementId);
    if (!element) return;

    let remaining = seconds;

    const interval = setInterval(function () {
        const minutes = Math.floor(remaining / 60);
        const secs = remaining % 60;
        element.textContent = String(minutes).padStart(2, '0') + ':' + String(secs).padStart(2, '0');

        if (remaining <= 30) {
            element.classList.add('warning');
        }

        if (remaining <= 0) {
            clearInterval(interval);
            if (typeof onComplete === 'function') {
                onComplete();
            }
        }

        remaining--;
    }, 1000);

    return interval;
}

console.log('Indo Ocean Crew Recruitment System Loaded');
