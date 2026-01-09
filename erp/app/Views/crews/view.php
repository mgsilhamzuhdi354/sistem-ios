<?php
/**
 * Crew Detail View
 */
$currentPage = 'crews';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; align-items: center; gap: 20px;">
        <?php if ($crew['photo']): ?>
            <img src="<?= BASE_URL . $crew['photo'] ?>" alt="" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-gold);">
        <?php else: ?>
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($crew['full_name']) ?>&background=0A2463&color=fff&size=80" alt="" style="width: 80px; height: 80px; border-radius: 50%;">
        <?php endif; ?>
        <div>
            <h1><?= htmlspecialchars($crew['full_name']) ?></h1>
            <p>
                <code style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold); padding: 4px 10px; border-radius: 4px; margin-right: 10px;">
                    <?= htmlspecialchars($crew['employee_id']) ?>
                </code>
                <?php
                $statusColors = [
                    'available' => ['#10B981', 'Available'],
                    'onboard' => ['#3B82F6', 'On Board'],
                    'leave' => ['#F59E0B', 'On Leave'],
                    'blacklisted' => ['#EF4444', 'Blacklisted'],
                    'retired' => ['#6B7280', 'Retired']
                ];
                $statusInfo = $statusColors[$crew['status']] ?? ['#6B7280', $crew['status']];
                ?>
                <span class="badge" style="background: <?= $statusInfo[0] ?>; color: #fff;"><?= $statusInfo[1] ?></span>
            </p>
        </div>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?= BASE_URL ?>documents/<?= $crew['id'] ?>" class="btn btn-secondary">
            <i class="fas fa-folder-open"></i> Documents
        </a>
        <?php if ($this->checkPermission('crews', 'edit')): ?>
        <a href="<?= BASE_URL ?>crews/edit/<?= $crew['id'] ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="grid-3" style="gap: 20px; margin-bottom: 24px;">
    <!-- Personal Info -->
    <div class="card">
        <h4 style="margin-bottom: 16px; color: var(--accent-gold);"><i class="fas fa-user"></i> Informasi Pribadi</h4>
        <table style="width: 100%; font-size: 14px;">
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Jenis Kelamin</td><td style="text-align: right;"><?= $crew['gender'] === 'male' ? 'Laki-laki' : 'Perempuan' ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Tanggal Lahir</td><td style="text-align: right;"><?= $crew['birth_date'] ? date('d M Y', strtotime($crew['birth_date'])) : '-' ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Tempat Lahir</td><td style="text-align: right;"><?= htmlspecialchars($crew['birth_place'] ?: '-') ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Kewarganegaraan</td><td style="text-align: right;"><?= htmlspecialchars($crew['nationality'] ?: '-') ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Agama</td><td style="text-align: right;"><?= htmlspecialchars($crew['religion'] ?: '-') ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Status</td><td style="text-align: right;"><?= ucfirst(str_replace('_', ' ', $crew['marital_status'])) ?></td></tr>
        </table>
    </div>
    
    <!-- Contact -->
    <div class="card">
        <h4 style="margin-bottom: 16px; color: var(--accent-gold);"><i class="fas fa-address-book"></i> Kontak</h4>
        <table style="width: 100%; font-size: 14px;">
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Email</td><td style="text-align: right;"><?= htmlspecialchars($crew['email'] ?: '-') ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Telepon</td><td style="text-align: right;"><?= htmlspecialchars($crew['phone'] ?: '-') ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">WhatsApp</td><td style="text-align: right;"><?= htmlspecialchars($crew['whatsapp'] ?: '-') ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Kota</td><td style="text-align: right;"><?= htmlspecialchars($crew['city'] ?: '-') ?></td></tr>
        </table>
        
        <?php if ($crew['emergency_name']): ?>
        <hr style="margin: 16px 0; border-color: var(--border-color);">
        <h5 style="margin-bottom: 12px; color: var(--warning);"><i class="fas fa-exclamation-triangle"></i> Darurat</h5>
        <table style="width: 100%; font-size: 14px;">
            <tr><td style="color: var(--text-muted); padding: 4px 0;">Nama</td><td style="text-align: right;"><?= htmlspecialchars($crew['emergency_name']) ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 4px 0;">Hubungan</td><td style="text-align: right;"><?= htmlspecialchars($crew['emergency_relation'] ?: '-') ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 4px 0;">Telepon</td><td style="text-align: right;"><?= htmlspecialchars($crew['emergency_phone'] ?: '-') ?></td></tr>
        </table>
        <?php endif; ?>
    </div>
    
    <!-- Banking -->
    <div class="card">
        <h4 style="margin-bottom: 16px; color: var(--accent-gold);"><i class="fas fa-university"></i> Bank</h4>
        <table style="width: 100%; font-size: 14px;">
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Bank</td><td style="text-align: right;"><?= htmlspecialchars($crew['bank_name'] ?: '-') ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">No. Rekening</td><td style="text-align: right;"><?= htmlspecialchars($crew['bank_account'] ?: '-') ?></td></tr>
            <tr><td style="color: var(--text-muted); padding: 6px 0;">Atas Nama</td><td style="text-align: right;"><?= htmlspecialchars($crew['bank_holder'] ?: '-') ?></td></tr>
        </table>
        
        <hr style="margin: 16px 0; border-color: var(--border-color);">
        <h5 style="margin-bottom: 12px; color: var(--info);"><i class="fas fa-anchor"></i> Pengalaman</h5>
        <table style="width: 100%; font-size: 14px;">
            <tr><td style="color: var(--text-muted); padding: 4px 0;">Pengalaman</td><td style="text-align: right;"><?= $crew['years_experience'] ?? 0 ?> tahun</td></tr>
            <tr><td style="color: var(--text-muted); padding: 4px 0;">Total Sea Time</td><td style="text-align: right;"><?= $crew['total_sea_time_months'] ?? 0 ?> bulan</td></tr>
        </table>
    </div>
</div>

<!-- Documents -->
<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h4 style="color: var(--accent-gold);"><i class="fas fa-file-alt"></i> Documents (<?= count($documents) ?>)</h4>
        <?php if ($this->checkPermission('documents', 'create')): ?>
        <a href="<?= BASE_URL ?>documents/upload/<?= $crew['id'] ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-upload"></i> Upload
        </a>
        <?php endif; ?>
    </div>
    
    <?php if (empty($documents)): ?>
        <p style="color: var(--text-muted); text-align: center; padding: 20px;">Belum ada dokumen</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px;">
            <?php foreach ($documents as $doc): ?>
                <div style="background: rgba(0,0,0,0.2); padding: 12px; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: rgba(139, 92, 246, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-<?= strpos($doc['mime_type'], 'pdf') !== false ? 'file-pdf' : 'file-image' ?>" style="color: #8B5CF6;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($doc['document_name']) ?></div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <?php if ($doc['expiry_date']): ?>
                                <?php
                                $daysLeft = (strtotime($doc['expiry_date']) - time()) / 86400;
                                $expColor = $daysLeft < 0 ? 'var(--danger)' : ($daysLeft < 90 ? 'var(--warning)' : 'var(--success)');
                                ?>
                                <span style="color: <?= $expColor ?>;">Exp: <?= date('d M Y', strtotime($doc['expiry_date'])) ?></span>
                            <?php else: ?>
                                No expiry
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>documents/preview/<?= $doc['id'] ?>" target="_blank" class="btn-icon" title="Preview">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Contract History -->
<div class="card" style="margin-bottom: 24px;">
    <h4 style="margin-bottom: 16px; color: var(--accent-gold);"><i class="fas fa-history"></i> Sejarah Kontrak (<?= count($contractHistory) ?>)</h4>
    
    <?php if (empty($contractHistory)): ?>
        <p style="color: var(--text-muted); text-align: center; padding: 20px;">Belum ada riwayat kontrak</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Contract No</th>
                    <th>Vessel</th>
                    <th>Client</th>
                    <th>Period</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contractHistory as $contract): ?>
                    <tr>
                        <td><a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>"><?= htmlspecialchars($contract['contract_no']) ?></a></td>
                        <td><?= htmlspecialchars($contract['vessel_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($contract['client_name'] ?? '-') ?></td>
                        <td><?= date('d M Y', strtotime($contract['sign_on_date'])) ?> - <?= date('d M Y', strtotime($contract['sign_off_date'])) ?></td>
                        <td>
                            <span class="badge badge-<?= $contract['status'] === 'active' ? 'success' : ($contract['status'] === 'completed' ? 'info' : 'secondary') ?>">
                                <?= ucfirst($contract['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Skills -->
<div class="card">
    <h4 style="margin-bottom: 16px; color: var(--accent-gold);"><i class="fas fa-certificate"></i> Skills (<?= count($skills) ?>)</h4>
    
    <?php if (empty($skills)): ?>
        <p style="color: var(--text-muted); text-align: center; padding: 20px;">Belum ada skill tercatat</p>
    <?php else: ?>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <?php foreach ($skills as $skill): ?>
                <?php
                $levelColors = ['basic' => '#6B7280', 'intermediate' => '#3B82F6', 'advanced' => '#10B981', 'expert' => '#D4AF37'];
                $color = $levelColors[$skill['skill_level']] ?? '#6B7280';
                ?>
                <span style="background: <?= $color ?>20; color: <?= $color ?>; padding: 6px 12px; border-radius: 20px; font-size: 13px;">
                    <?= htmlspecialchars($skill['skill_name']) ?>
                    <small style="opacity: 0.7;">(<?= ucfirst($skill['skill_level']) ?>)</small>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
