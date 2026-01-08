// ERP Crew Contract - Main JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    // Modal functionality
    window.openModal = function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
        }
    };

    window.closeModal = function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    };

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    // Tab functionality
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const tabGroup = this.closest('.tabs');
            tabGroup.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Show corresponding tab content
            const tabId = this.dataset.tab;
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === tabId) {
                    content.classList.add('active');
                }
            });
        });
    });

    // Format currency input
    document.querySelectorAll('.currency-input').forEach(input => {
        input.addEventListener('blur', function () {
            let value = this.value.replace(/[^0-9]/g, '');
            if (value) {
                this.value = new Intl.NumberFormat('en-US').format(value);
            }
        });
    });

    // Calculate contract duration
    const signOnDate = document.getElementById('sign_on_date');
    const signOffDate = document.getElementById('sign_off_date');
    const durationField = document.getElementById('duration');

    function calculateDuration() {
        if (signOnDate && signOffDate && signOnDate.value && signOffDate.value) {
            const start = new Date(signOnDate.value);
            const end = new Date(signOffDate.value);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const months = Math.floor(diffDays / 30);
            const days = diffDays % 30;

            if (durationField) {
                durationField.value = `${months} bulan ${days} hari`;
            }
        }
    }

    if (signOnDate) signOnDate.addEventListener('change', calculateDuration);
    if (signOffDate) signOffDate.addEventListener('change', calculateDuration);

    // Notification bell animation
    const notifBtn = document.querySelector('.notification-btn');
    if (notifBtn) {
        setInterval(() => {
            notifBtn.classList.add('shake');
            setTimeout(() => notifBtn.classList.remove('shake'), 500);
        }, 10000);
    }
});

// Add shake animation
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: rotate(0); }
        25% { transform: rotate(-10deg); }
        75% { transform: rotate(10deg); }
    }
    .shake { animation: shake 0.5s ease-in-out; }
`;
document.head.appendChild(style);
