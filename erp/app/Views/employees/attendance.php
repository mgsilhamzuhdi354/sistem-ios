<?php
/**
 * Data Absen View - Modern Design matching Production
 * PT Indo Ocean - ERP System
 */
$monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$currentMonthName = $monthNames[intval($month) - 1];
?>

<style>
    /* Modern Attendance Styles */
    .attendance-dashboard {
        padding: 20px;
    }

    .attendance-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .attendance-title h1 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filter-section {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-select {
        min-width: 180px;
        padding: 12px 16px;
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 12px;
        background: white;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .btn-search {
        padding: 12px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.3s ease;
    }

    .btn-search:hover {
        transform: translateY(-2px);
    }

    .btn-export {
        padding: 12px 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Attendance Table */
    .attendance-table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attendance-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .attendance-table th {
        padding: 14px 12px;
        color: white;
        font-weight: 600;
        text-align: center;
        font-size: 0.85rem;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .attendance-table td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.9rem;
    }

    .attendance-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }

    /* Status Badges */
    .badge-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-masuk {
        background: rgba(16, 185, 129, 0.15);
        color: #059669;
    }

    .badge-belum {
        background: rgba(245, 158, 11, 0.15);
        color: #d97706;
    }

    .badge-izin {
        background: rgba(6, 182, 212, 0.15);
        color: #0891b2;
    }

    .badge-sakit {
        background: rgba(99, 102, 241, 0.15);
        color: #6366f1;
    }

    .badge-alpha {
        background: rgba(239, 68, 68, 0.15);
        color: #dc2626;
    }

    .employee-cell {
        text-align: left !important;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .employee-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1rem;
    }

    .employee-info {
        text-align: left;
    }

    .employee-name {
        font-weight: 600;
        color: #1f2937;
    }

    .employee-shift {
        font-size: 0.8rem;
        color: #9ca3af;
    }

    /* Date columns */
    .date-cell {
        min-width: 100px;
    }

    .time-in {
        color: #059669;
        font-weight: 600;
    }

    .time-out {
        color: #0891b2;
        font-weight: 600;
    }

    .time-late {
        color: #f59e0b;
        font-size: 0.8rem;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 64px;
        display: block;
        margin-bottom: 16px;
        background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Scroll container */
    .table-scroll {
        overflow-x: auto;
    }

    /* Info box */
    .info-box {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.1) 100%);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-box i {
        color: #f59e0b;
        font-size: 1.5rem;
    }

    .info-box-content h4 {
        margin: 0 0 4px;
        color: #d97706;
        font-weight: 600;
    }

    .info-box-content p {
        margin: 0;
        color: #92400e;
        font-size: 0.9rem;
    }

    /* Live badge */
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
</style>

<div class="attendance-dashboard">
    <div class="attendance-header">
        <div class="attendance-title">
            <h1><i class="fas fa-calendar-check"></i> Data Absen</h1>
            <p style="margin-top: 4px; color: #6b7280;">
                Rekap absensi <?= isset($startDate) ? date('d M Y', strtotime($startDate)) : date('d M Y') ?>
                <?= (isset($startDate) && isset($endDate) && $startDate !== $endDate) ? ' - ' . date('d M Y', strtotime($endDate)) : '' ?>
                <span class="live-badge">LIVE</span>
            </p>
        </div>
        <div class="filter-section">
            <select id="employeeFilter" class="filter-select">
                <option value="">Pilih Pegawai</option>
                <?php if (isset($employees) && !empty($employees)): ?>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= (isset($selectedEmployee) && $selectedEmployee == $emp['id']) ? 'selected' : '' ?>><?= htmlspecialchars($emp['nama'] ?? $emp['name'] ?? '') ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <input type="date" id="startDate" class="filter-select" value="<?= $startDate ?? date('Y-m-d') ?>"
                style="min-width: 150px;">
            <input type="date" id="endDate" class="filter-select" value="<?= $endDate ?? date('Y-m-d') ?>" style="min-width: 150px;">
            <button class="btn-search" onclick="applyFilter()">
                <i class="fas fa-search"></i>
            </button>
            <button class="btn-export" onclick="exportData()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <?php if (!$success): ?>
        <div class="info-box"
            style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%); border-color: rgba(239, 68, 68, 0.3);">
            <i class="fas fa-exclamation-triangle" style="color: #dc2626;"></i>
            <div class="info-box-content">
                <h4 style="color: #b91c1c;">Connection Error</h4>
                <p style="color: #991b1b;">Tidak dapat terhubung ke sistem HRIS untuk mengambil data absensi.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($attendanceData) || (isset($attendanceData['data']) && empty($attendanceData['data']))): ?>
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div class="info-box-content">
                <h4>Data Absensi Kosong</h4>
                <p>Belum ada data absensi untuk periode ini. Pastikan data sudah diimport dari sistem produksi.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Attendance Table -->
    <div class="attendance-table-container">
        <div class="table-scroll">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="text-align: left; min-width: 200px;">Nama Pegawai</th>
                        <th>Shift</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Telat</th>
                        <th>Lokasi Masuk</th>
                        <th>Foto Masuk</th>
                        <th>Keterangan Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Lokasi Pulang</th>
                        <th>Keterangan Pulang</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">
                    <?php
                    $records = [];
                    if (is_array($attendanceData)) {
                        if (isset($attendanceData['data']) && is_array($attendanceData['data'])) {
                            $records = $attendanceData['data'];
                        } elseif (isset($attendanceData['records'])) {
                            $records = $attendanceData['records'];
                        } else {
                            $records = $attendanceData;
                        }
                    }
                    ?>
                    <?php if (!empty($records)): ?>
                        <?php $no = 1;
                        foreach ($records as $record): ?>
                            <?php
                            $empName = $record['user']['name'] ?? $record['nama'] ?? '-';
                            $empInitial = strtoupper(substr($empName, 0, 1));
                            $shiftName = $record['shift']['nama_shift'] ?? $record['shift']['nama'] ?? '-';
                            $tanggal = $record['tanggal'] ?? '-';
                            $jamMasuk = $record['jam_masuk'] ?? '-';
                            $jamPulang = $record['jam_pulang'] ?? '-';
                            $telat = $record['telat'] ?? 0;
                            $lokasiMasuk = $record['lokasi_masuk'] ?? '-';
                            $lokasiPulang = $record['lokasi_pulang'] ?? '-';
                            $keteranganMasuk = $record['keterangan_masuk'] ?? '-';
                            $keteranganPulang = $record['keterangan_pulang'] ?? '-';
                            $fotoMasuk = $record['foto_masuk'] ?? null;
                            $statusAbsen = $record['status_absen'] ?? 'Belum Absen';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <div class="employee-cell">
                                        <div class="employee-avatar"><?= $empInitial ?></div>
                                        <div class="employee-info">
                                            <div class="employee-name"><?= htmlspecialchars($empName) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($shiftName) ?></td>
                                <td><?= $tanggal !== '-' ? date('d M Y', strtotime($tanggal)) : '-' ?></td>
                                <td>
                                    <?php if ($jamMasuk !== '-' && $jamMasuk): ?>
                                        <span class="badge-status badge-masuk"><?= $jamMasuk ?></span>
                                    <?php else: ?>
                                        <span class="badge-status badge-belum">Belum Absen</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($telat > 0): ?>
                                        <span class="time-late"><?= $telat ?> menit</span>
                                    <?php else: ?>
                                        <span style="color: #10b981;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(substr($lokasiMasuk, 0, 20)) ?>...</td>
                                <td>
                                    <?php if ($fotoMasuk): ?>
                                        <span class="badge-status badge-masuk">Ada</span>
                                    <?php else: ?>
                                        <span class="badge-status badge-belum">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($keteranganMasuk) ?></td>
                                <td>
                                    <?php if ($jamPulang !== '-' && $jamPulang): ?>
                                        <span class="badge-status badge-izin"><?= $jamPulang ?></span>
                                    <?php else: ?>
                                        <span class="badge-status badge-belum">Belum Absen</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(substr($lokasiPulang, 0, 20)) ?>...</td>
                                <td><?= htmlspecialchars($keteranganPulang) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>Belum ada data absensi</h3>
                                <p>Data absensi akan muncul setelah diimport dari sistem HRIS produksi</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function applyFilter() {
        const employee = document.getElementById('employeeFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        let url = '<?= BASE_URL ?>employees/attendance?month=<?= $month ?>&year=<?= $year ?>';
        if (employee) url += '&employee_id=' + employee;
        if (startDate) url += '&start_date=' + startDate;
        if (endDate) url += '&end_date=' + endDate;

        window.location.href = url;
    }

    function exportData() {
        alert('Fitur export akan segera tersedia');
    }

    function changeMonth(month) {
        window.location.href = '<?= BASE_URL ?>employees/attendance?month=' + month + '&year=<?= $year ?>';
    }

    function changeYear(year) {
        window.location.href = '<?= BASE_URL ?>employees/attendance?month=<?= $month ?>&year=' + year;
    }
</script>