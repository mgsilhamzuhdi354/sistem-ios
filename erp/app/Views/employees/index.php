<?php
/**
 * Employee List View - Modern UI with Realtime Data
 * PT Indo Ocean - ERP System
 */
?>

<style>
    /* Modern Glassmorphism Styling */
    .employee-dashboard {
        padding: 20px;
    }

    .page-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-title h1 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title h1 i {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        animation: pulse-glow 2s infinite;
    }

    .live-badge::before {
        content: '';
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        animation: blink 1.5s infinite;
    }

    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 5px rgba(16, 185, 129, 0.5);
        }

        50% {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.8);
        }
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    /* Summary Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card-modern {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s ease;
    }

    .stat-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-icon-modern {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon-modern.gradient-blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-icon-modern.gradient-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .stat-icon-modern.gradient-gold {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    }

    .stat-icon-modern.gradient-red {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .stat-info-modern h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary, #1F2937);
        margin: 0;
    }

    .stat-info-modern p {
        margin: 0;
        color: var(--text-muted, #6B7280);
        font-size: 0.9rem;
    }

    /* Employee Table Modern */
    .employee-table-container {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .employee-table {
        width: 100%;
        border-collapse: collapse;
    }

    .employee-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .employee-table th {
        padding: 16px 20px;
        color: white;
        font-weight: 600;
        text-align: left;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .employee-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .employee-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }

    .employee-table td {
        padding: 16px 20px;
        vertical-align: middle;
    }

    .employee-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .employee-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .employee-name {
        font-weight: 600;
        color: var(--text-primary, #1F2937);
    }

    .employee-email {
        font-size: 0.85rem;
        color: var(--text-muted, #6B7280);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-attendance {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }

    .btn-kpi {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .btn-performance {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .btn-view {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    }

    /* Status Badge */
    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.aktif {
        background: rgba(16, 185, 129, 0.15);
        color: #059669;
    }

    .status-badge.inactive {
        background: rgba(239, 68, 68, 0.15);
        color: #dc2626;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 24px;
        width: 90%;
        max-width: 600px;
        max-height: 85vh;
        overflow: hidden;
        transform: translateY(20px);
        transition: transform 0.3s ease;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
    }

    .modal-overlay.active .modal-content {
        transform: translateY(0);
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1.2rem;
        transition: background 0.3s ease;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .modal-body {
        padding: 24px;
        overflow-y: auto;
        max-height: calc(85vh - 80px);
    }

    /* KPI Score Display */
    .kpi-score-container {
        text-align: center;
        padding: 20px 0;
    }

    .kpi-score-circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: conic-gradient(from 0deg, #10b981 0%, #059669 var(--score-percent), #e5e7eb var(--score-percent));
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        position: relative;
    }

    .kpi-score-circle::before {
        content: '';
        width: 110px;
        height: 110px;
        background: white;
        border-radius: 50%;
        position: absolute;
    }

    .kpi-score-value {
        position: relative;
        z-index: 1;
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Attendance Stats */
    .attendance-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .attendance-stat-card {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        padding: 16px;
        border-radius: 12px;
        text-align: center;
    }

    .attendance-stat-card h4 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .attendance-stat-card p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-muted, #6B7280);
    }

    .attendance-stat-card.hadir h4 {
        color: #059669;
    }

    .attendance-stat-card.izin h4 {
        color: #0891b2;
    }

    .attendance-stat-card.sakit h4 {
        color: #f59e0b;
    }

    .attendance-stat-card.alpha h4 {
        color: #dc2626;
    }

    /* Loading Spinner */
    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        gap: 16px;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Performance Breakdown */
    .performance-breakdown {
        space-y: 12px;
    }

    .breakdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .breakdown-bar {
        flex: 1;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .breakdown-fill {
        height: 100%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    .breakdown-label {
        min-width: 120px;
        font-weight: 500;
    }

    .breakdown-value {
        min-width: 60px;
        text-align: right;
        font-weight: 600;
        color: #059669;
    }

    /* Dark Mode Support */
    body.dark-mode .stat-card-modern,
    body.dark-mode .employee-table-container {
        background: linear-gradient(135deg, rgba(30, 30, 30, 0.95) 0%, rgba(20, 20, 20, 0.9) 100%);
    }

    body.dark-mode .employee-table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    body.dark-mode .modal-content {
        background: #1f2937;
    }

    body.dark-mode .stat-info-modern h3,
    body.dark-mode .employee-name {
        color: #f9fafb;
    }

    body.dark-mode .kpi-score-circle::before {
        background: #1f2937;
    }
</style>

<div class="employee-dashboard">
    <div class="page-title">
        <div>
            <h1>
                <i class="fas fa-users"></i> Data Karyawan
            </h1>
            <p style="margin-top: 4px; color: var(--text-muted);">
                Kelola data karyawan secara real-time
                <span class="live-badge">LIVE</span>
            </p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <select id="statusFilter" class="form-control"
                style="min-width: 180px; border-radius: 12px; padding: 10px 16px;"
                onchange="filterByStatus(this.value)">
                <option value="">Semua Status</option>
                <option value="Aktif" <?= ($statusFilter ?? '') === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="Probation" <?= ($statusFilter ?? '') === 'Probation' ? 'selected' : '' ?>>Probation</option>
                <option value="Resign" <?= ($statusFilter ?? '') === 'Resign' ? 'selected' : '' ?>>Resign</option>
            </select>
        </div>
    </div>

    <?php if (!$success): ?>
        <div class="alert alert-danger" style="border-radius: 16px; padding: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Connection Error</strong><br>
            Tidak dapat terhubung ke sistem HRIS.
            <?= htmlspecialchars($error ?? 'Unknown error') ?>
        </div>
    <?php else: ?>

        <?php
        $totalEmployees = count($employees);
        $aktif = count(array_filter($employees, fn($e) => strtolower($e['status'] ?? $e['status_karyawan'] ?? '') === 'aktif'));
        $probation = count(array_filter($employees, fn($e) => strtolower($e['status'] ?? $e['status_karyawan'] ?? '') === 'probation'));
        $resign = count(array_filter($employees, fn($e) => in_array(strtolower($e['status'] ?? $e['status_karyawan'] ?? ''), ['resign', 'nonaktif'])));
        ?>

        <!-- Summary Stats -->
        <div class="stats-grid">
            <div class="stat-card-modern">
                <div class="stat-icon-modern gradient-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info-modern">
                    <h3><?= $totalEmployees ?></h3>
                    <p>Total Karyawan</p>
                </div>
            </div>
            <div class="stat-card-modern">
                <div class="stat-icon-modern gradient-green">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info-modern">
                    <h3><?= $aktif ?></h3>
                    <p>Karyawan Aktif</p>
                </div>
            </div>
            <div class="stat-card-modern">
                <div class="stat-icon-modern gradient-gold">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-info-modern">
                    <h3><?= $probation ?></h3>
                    <p>Probation</p>
                </div>
            </div>
            <div class="stat-card-modern">
                <div class="stat-icon-modern gradient-red">
                    <i class="fas fa-user-minus"></i>
                </div>
                <div class="stat-info-modern">
                    <h3><?= $resign ?></h3>
                    <p>Resign/Nonaktif</p>
                </div>
            </div>
        </div>

        <!-- Employee Table -->
        <div class="employee-table-container">
            <table class="employee-table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Jabatan</th>
                        <th>Departemen</th>
                        <th>Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 60px;">
                                <i class="fas fa-users-slash"
                                    style="font-size: 48px; color: #d1d5db; display: block; margin-bottom: 16px;"></i>
                                <span style="color: #9ca3af;">Tidak ada data karyawan</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($employees as $employee): ?>
                            <?php
                            // Map field names
                            $empId = $employee['id'] ?? 0;
                            $empName = $employee['nama'] ?? $employee['name'] ?? $employee['nama_lengkap'] ?? '-';
                            $empEmail = $employee['email'] ?? '-';
                            $empInitials = strtoupper(substr($empName, 0, 1));

                            // Jabatan
                            $empJabatan = '-';
                            if (isset($employee['jabatan']) && is_array($employee['jabatan'])) {
                                $empJabatan = $employee['jabatan']['nama_jabatan'] ?? $employee['jabatan']['nama'] ?? '-';
                            } elseif (isset($employee['jabatan']) && is_string($employee['jabatan'])) {
                                $empJabatan = $employee['jabatan'];
                            }

                            // Departemen/Lokasi
                            $empDept = '-';
                            if (isset($employee['lokasi']) && is_array($employee['lokasi'])) {
                                $empDept = $employee['lokasi']['nama_lokasi'] ?? $employee['lokasi']['nama'] ?? '-';
                            } elseif (isset($employee['departemen'])) {
                                $empDept = $employee['departemen'];
                            }

                            // Status
                            $empStatus = $employee['status_karyawan'] ?? $employee['status'] ?? 'Aktif';
                            if (empty($empStatus) || $empStatus === 'Unknown') {
                                $empStatus = 'Aktif';
                            }
                            $statusClass = strtolower($empStatus) === 'aktif' ? 'aktif' : 'inactive';
                            ?>
                            <tr>
                                <td>
                                    <div class="employee-info">
                                        <div class="employee-avatar"><?= $empInitials ?></div>
                                        <div>
                                            <div class="employee-name"><?= htmlspecialchars($empName) ?></div>
                                            <div class="employee-email"><?= htmlspecialchars($empEmail) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($empJabatan) ?></td>
                                <td><?= htmlspecialchars($empDept) ?></td>
                                <td>
                                    <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($empStatus) ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons" style="justify-content: center;">
                                        <button class="btn-action btn-attendance"
                                            onclick="showAttendance(<?= $empId ?>, '<?= htmlspecialchars($empName) ?>')"
                                            title="Lihat Absensi">
                                            <i class="fas fa-calendar-check"></i>
                                        </button>
                                        <button class="btn-action btn-kpi"
                                            onclick="showKPI(<?= $empId ?>, '<?= htmlspecialchars($empName) ?>')"
                                            title="Lihat KPI Score">
                                            <i class="fas fa-chart-pie"></i>
                                        </button>
                                        <button class="btn-action btn-performance"
                                            onclick="showPerformance(<?= $empId ?>, '<?= htmlspecialchars($empName) ?>')"
                                            title="Lihat Kinerja">
                                            <i class="fas fa-chart-line"></i>
                                        </button>
                                        <a href="<?= BASE_URL ?>employees/<?= $empId ?>" class="btn-action btn-view" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>

<!-- Modal Template -->
<div id="dataModal" class="modal-overlay" onclick="closeModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3 id="modalTitle"><i class="fas fa-spinner fa-spin"></i> Loading...</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<script>
    const API_BASE = '<?= $_ENV['HRIS_API_URL'] ?? 'http://localhost/absensi/aplikasiabsensibygerry/public/api' ?>';
    const currentMonth = new Date().getMonth() + 1;
    const currentYear = new Date().getFullYear();

    function openModal(title, icon) {
        document.getElementById('modalTitle').innerHTML = `<i class="fas ${icon}"></i> ${title}`;
        document.getElementById('modalBody').innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Memuat data...</p>
        </div>
    `;
        document.getElementById('dataModal').classList.add('active');
    }

    function closeModal(e) {
        if (e && e.target !== e.currentTarget) return;
        document.getElementById('dataModal').classList.remove('active');
    }

    async function fetchAPI(endpoint) {
        try {
            const response = await fetch(`${API_BASE}${endpoint}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('API Error:', error);
            return { code: 500, message: error.message, data: null };
        }
    }

    async function showAttendance(employeeId, employeeName) {
        openModal(`Absensi - ${employeeName}`, 'fa-calendar-check');

        const result = await fetchAPI(`/attendance/employee/${employeeId}?month=${currentMonth}&year=${currentYear}`);

        if (result.code === 200 && result.data) {
            const stats = result.data.stats || { hadir: 0, izin: 0, sakit: 0, alpha: 0, telat: 0 };
            const records = result.data.records || [];

            document.getElementById('modalBody').innerHTML = `
            <div style="margin-bottom: 16px; text-align: center; color: #6b7280;">
                <i class="fas fa-calendar"></i> ${getMonthName(currentMonth)} ${currentYear}
            </div>
            
            <div class="attendance-stats">
                <div class="attendance-stat-card hadir">
                    <h4>${stats.hadir || 0}</h4>
                    <p>Hadir</p>
                </div>
                <div class="attendance-stat-card izin">
                    <h4>${stats.izin || 0}</h4>
                    <p>Izin</p>
                </div>
                <div class="attendance-stat-card sakit">
                    <h4>${stats.sakit || 0}</h4>
                    <p>Sakit</p>
                </div>
                <div class="attendance-stat-card alpha">
                    <h4>${stats.alpha || 0}</h4>
                    <p>Alpha</p>
                </div>
            </div>
            
            <div style="margin-top: 16px; padding: 12px; background: #f0fdf4; border-radius: 12px; text-align: center;">
                <strong style="color: #059669;">Telat ${stats.telat || 0} kali</strong> bulan ini
            </div>
            
            ${records.length > 0 ? `
                <h4 style="margin-top: 24px; margin-bottom: 12px;">Riwayat Terakhir</h4>
                <div style="max-height: 200px; overflow-y: auto;">
                    ${records.slice(0, 10).map(r => `
                        <div style="display: flex; justify-content: space-between; padding: 8px; border-bottom: 1px solid #f3f4f6;">
                            <span>${formatDate(r.tanggal)}</span>
                            <span class="status-badge ${r.status_absen === 'Masuk' ? 'aktif' : 'inactive'}">${r.status_absen}</span>
                        </div>
                    `).join('')}
                </div>
            ` : ''}
        `;
        } else {
            document.getElementById('modalBody').innerHTML = `
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                <i class="fas fa-calendar-times" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                <p>Data absensi tidak tersedia</p>
                <small>${result.message || 'Tidak dapat memuat data'}</small>
            </div>
        `;
        }
    }

    async function showKPI(employeeId, employeeName) {
        openModal(`KPI Score - ${employeeName}`, 'fa-chart-pie');

        const result = await fetchAPI(`/performance/employee/${employeeId}`);

        if (result.code === 200 && result.data) {
            const score = result.data.current_score || 0;
            const totalScore = result.data.total_score || 0;
            const breakdown = result.data.breakdown || [];
            const scorePercent = Math.min(100, score);

            document.getElementById('modalBody').innerHTML = `
            <div class="kpi-score-container">
                <div class="kpi-score-circle" style="--score-percent: ${scorePercent}%;">
                    <span class="kpi-score-value">${score}</span>
                </div>
                <p style="color: #6b7280; margin-bottom: 24px;">Penilaian Berjalan</p>
                
                <div style="display: flex; justify-content: center; gap: 32px; margin-bottom: 24px;">
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">${totalScore}</div>
                        <div style="color: #6b7280; font-size: 0.85rem;">Total Skor</div>
                    </div>
                </div>
            </div>
            
            ${breakdown.length > 0 ? `
                <h4 style="margin-bottom: 12px;">Breakdown Kinerja</h4>
                <div class="performance-breakdown">
                    ${breakdown.map(b => `
                        <div class="breakdown-item">
                            <span class="breakdown-label">${b.jenis}</span>
                            <div class="breakdown-bar">
                                <div class="breakdown-fill" style="width: ${Math.min(100, b.total)}%;"></div>
                            </div>
                            <span class="breakdown-value">${b.total}</span>
                        </div>
                    `).join('')}
                </div>
            ` : `
                <div style="text-align: center; padding: 20px; color: #9ca3af; background: #f9fafb; border-radius: 12px;">
                    <i class="fas fa-info-circle"></i> Breakdown detail tidak tersedia
                </div>
            `}
        `;
        } else {
            document.getElementById('modalBody').innerHTML = `
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                <i class="fas fa-chart-pie" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                <p>Data KPI tidak tersedia</p>
                <small>${result.message || 'Tidak dapat memuat data'}</small>
            </div>
        `;
        }
    }

    async function showPerformance(employeeId, employeeName) {
        openModal(`Kinerja - ${employeeName}`, 'fa-chart-line');

        const result = await fetchAPI(`/performance/employee/${employeeId}`);

        if (result.code === 200 && result.data) {
            const records = result.data.records || [];
            const currentScore = result.data.current_score || 0;
            const totalScore = result.data.total_score || 0;

            document.getElementById('modalBody').innerHTML = `
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
                <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1)); padding: 20px; border-radius: 16px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: 700; color: #059669;">${currentScore}</div>
                    <div style="color: #6b7280;">Skor Berjalan</div>
                </div>
                <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); padding: 20px; border-radius: 16px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: 700; color: #6366f1;">${totalScore}</div>
                    <div style="color: #6b7280;">Total Nilai</div>
                </div>
            </div>
            
            ${records.length > 0 ? `
                <h4 style="margin-bottom: 12px;">Riwayat Penilaian</h4>
                <div style="max-height: 300px; overflow-y: auto;">
                    ${records.slice(0, 15).map(r => `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #f3f4f6;">
                            <div>
                                <div style="font-weight: 500;">${r.jenis?.nama || 'Penilaian'}</div>
                                <div style="font-size: 0.8rem; color: #9ca3af;">${formatDate(r.tanggal)}</div>
                            </div>
                            <div style="font-weight: 700; color: ${r.nilai >= 0 ? '#10b981' : '#ef4444'};">
                                ${r.nilai >= 0 ? '+' : ''}${r.nilai}
                            </div>
                        </div>
                    `).join('')}
                </div>
            ` : `
                <div style="text-align: center; padding: 40px; color: #9ca3af; background: #f9fafb; border-radius: 12px;">
                    <i class="fas fa-clipboard-list" style="font-size: 36px; display: block; margin-bottom: 12px;"></i>
                    <p>Belum ada riwayat penilaian</p>
                </div>
            `}
        `;
        } else {
            document.getElementById('modalBody').innerHTML = `
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                <i class="fas fa-chart-line" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                <p>Data kinerja tidak tersedia</p>
                <small>${result.message || 'Tidak dapat memuat data'}</small>
            </div>
        `;
        }
    }

    function filterByStatus(status) {
        if (status) {
            window.location.href = '<?= BASE_URL ?>employees?status=' + status;
        } else {
            window.location.href = '<?= BASE_URL ?>employees';
        }
    }

    function getMonthName(month) {
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return months[month - 1];
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
</script>