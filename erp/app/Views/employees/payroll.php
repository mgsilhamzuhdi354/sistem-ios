<?php
/**
 * Employee Payroll View
 * Display payroll data from HRIS system
 */
$currentPage = 'employees';
$months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// Ensure month and year are integers
$month = isset($month) ? (int) $month : (int) date('m');
$year = isset($year) ? (int) $year : (int) date('Y');

// Validate month range
if ($month < 1 || $month > 12) {
    $month = (int) date('m');
}

ob_start();
?>

<div class="page-header">
    <div>
        <h1>Payroll Karyawan</h1>
        <p>Data payroll karyawan dari sistem HRIS Absensi</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>employees" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="<?= BASE_URL ?>employees/payroll" style="display: flex; gap: 15px; align-items: end;">
        <div style="flex: 1;">
            <label>Bulan</label>
            <select name="bulan" class="form-control">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>><?= $months[$m] ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div style="flex: 1;">
            <label>Tahun</label>
            <select name="tahun" class="form-control">
                <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
                    <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
</div>

<?php if (!$success): ?>
    <!-- Error State -->
    <div class="card">
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--danger); margin-bottom: 15px;"></i>
            <h3>Gagal Memuat Data</h3>
            <p><?= $flash['error'] ?? 'Tidak dapat terhubung ke sistem HRIS' ?></p>
        </div>
    </div>
<?php elseif (empty($payrollData)): ?>
    <!-- Empty State -->
    <div class="card">
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fas fa-file-invoice-dollar" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px;"></i>
            <h3>Tidak Ada Data Payroll</h3>
            <p>Belum ada data payroll untuk periode <?= $months[$month] ?>     <?= $year ?></p>
        </div>
    </div>
<?php else: ?>
    <!-- Payroll Data Table -->
    <div class="table-card">
        <div class="card-header">
            <h3><i class="fas fa-money-bill-wave"></i> Daftar Payroll - <?= $months[$month] ?>     <?= $year ?></h3>
            <button onclick="window.print()" class="btn btn-secondary btn-sm">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th style="text-align: right;">Gaji Pokok</th>
                        <th style="text-align: right;">Tunjangan</th>
                        <th style="text-align: right;">Lembur</th>
                        <th style="text-align: right;">Potongan</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $totalGajiPokok = 0;
                    $totalTunjangan = 0;
                    $totalLembur = 0;
                    $totalPotongan = 0;
                    $totalGaji = 0;

                    foreach ($payrollData as $row):
                        $gajiPokok = $row['gaji_pokok'] ?? 0;
                        $tunjangan = $row['tunjangan'] ?? 0;
                        $lembur = $row['lembur'] ?? 0;
                        $potongan = $row['potongan'] ?? 0;
                        $total = $gajiPokok + $tunjangan + $lembur - $potongan;

                        $totalGajiPokok += $gajiPokok;
                        $totalTunjangan += $tunjangan;
                        $totalLembur += $lembur;
                        $totalPotongan += $potongan;
                        $totalGaji += $total;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nik'] ?? '-') ?></td>
                            <td><strong><?= htmlspecialchars($row['nama'] ?? '-') ?></strong></td>
                            <td><?= htmlspecialchars($row['jabatan'] ?? '-') ?></td>
                            <td style="text-align: right;">Rp <?= number_format($gajiPokok, 0, ',', '.') ?></td>
                            <td style="text-align: right;">Rp <?= number_format($tunjangan, 0, ',', '.') ?></td>
                            <td style="text-align: right;">Rp <?= number_format($lembur, 0, ',', '.') ?></td>
                            <td style="text-align: right; color: var(--danger);">Rp <?= number_format($potongan, 0, ',', '.') ?>
                            </td>
                            <td style="text-align: right; font-weight: bold; color: var(--success);">
                                Rp <?= number_format($total, 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: var(--surface); font-weight: bold;">
                        <td colspan="4" style="text-align: right;">TOTAL</td>
                        <td style="text-align: right;">Rp <?= number_format($totalGajiPokok, 0, ',', '.') ?></td>
                        <td style="text-align: right;">Rp <?= number_format($totalTunjangan, 0, ',', '.') ?></td>
                        <td style="text-align: right;">Rp <?= number_format($totalLembur, 0, ',', '.') ?></td>
                        <td style="text-align: right; color: var(--danger);">Rp
                            <?= number_format($totalPotongan, 0, ',', '.') ?></td>
                        <td style="text-align: right; color: var(--success); font-size: 1.1em;">
                            Rp <?= number_format($totalGaji, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean(); 
include APPPATH . 'Views/layouts/main.php';
?>