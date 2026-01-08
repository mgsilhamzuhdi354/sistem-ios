/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Admin JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Alert close buttons
    document.querySelectorAll('.alert-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const alert = this.closest('.alert');
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.remove();
            }, 300);
        });
    });

    // DataTable search filtering
    const tableSearch = document.querySelector('.table-search');
    if (tableSearch) {
        tableSearch.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(function (cb) {
                cb.checked = selectAllCheckbox.checked;
            });
        });
    }

    // Confirm delete forms
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Status change confirmation
    document.querySelectorAll('.status-select').forEach(function (select) {
        const originalValue = select.value;
        select.addEventListener('change', function () {
            if (!confirm('Are you sure you want to change the status?')) {
                this.value = originalValue;
            }
        });
    });

    // File upload preview
    document.querySelectorAll('.file-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const fileName = this.files[0]?.name || 'No file selected';
            const preview = this.nextElementSibling;
            if (preview && preview.classList.contains('file-name')) {
                preview.textContent = fileName;
            }
        });
    });

    // Initialize tooltips
    document.querySelectorAll('[data-tooltip]').forEach(function (el) {
        el.addEventListener('mouseenter', function () {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.dataset.tooltip;
            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            tooltip.style.left = rect.left + (rect.width - tooltip.offsetWidth) / 2 + 'px';
        });

        el.addEventListener('mouseleave', function () {
            document.querySelectorAll('.tooltip').forEach(function (t) {
                t.remove();
            });
        });
    });
});

// Drag and drop for pipeline kanban
function initKanban() {
    const cards = document.querySelectorAll('.pipeline-card');
    const columns = document.querySelectorAll('.column-body');

    cards.forEach(function (card) {
        card.draggable = true;

        card.addEventListener('dragstart', function (e) {
            e.dataTransfer.setData('text/plain', this.dataset.id);
            this.classList.add('dragging');
        });

        card.addEventListener('dragend', function () {
            this.classList.remove('dragging');
        });
    });

    columns.forEach(function (column) {
        column.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        column.addEventListener('dragleave', function () {
            this.classList.remove('drag-over');
        });

        column.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            const cardId = e.dataTransfer.getData('text/plain');
            const card = document.querySelector('.pipeline-card[data-id="' + cardId + '"]');
            const newStatusId = this.dataset.statusId;

            if (card && newStatusId) {
                // Update card position visually
                this.appendChild(card);

                // Send AJAX request to update status
                updateApplicationStatus(cardId, newStatusId);
            }
        });
    });
}

// AJAX status update
function updateApplicationStatus(applicationId, statusId) {
    const formData = new FormData();
    formData.append('status_id', statusId);
    formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

    fetch('/PT_indoocean/recruitment/public/admin/applicants/status/' + applicationId, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Status updated successfully', 'success');
            } else {
                showNotification('Failed to update status', 'error');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
        });
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check' : 'exclamation') + '-circle"></i> ' + message;

    document.body.appendChild(notification);

    setTimeout(function () {
        notification.classList.add('show');
    }, 10);

    setTimeout(function () {
        notification.classList.remove('show');
        setTimeout(function () {
            notification.remove();
        }, 300);
    }, 3000);
}

// Export data to CSV
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    rows.forEach(function (row) {
        const cells = row.querySelectorAll('td, th');
        const rowData = [];
        cells.forEach(function (cell) {
            rowData.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });

    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = (filename || 'export') + '.csv';
    a.click();
    URL.revokeObjectURL(url);
}

// Initialize Kanban if on pipeline page
if (document.querySelector('.pipeline-board')) {
    initKanban();
}

console.log('Admin Dashboard Loaded');
