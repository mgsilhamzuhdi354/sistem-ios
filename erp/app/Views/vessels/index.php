<?php
/**
 * Vessels List View
 */
$currentPage = 'vessels';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 data-translate="vessel_management">Manajemen Kapal</h1>
        <p data-translate="vessel_management_subtitle">Kelola kapal armada dan crew-nya</p>
    </div>
    <a href="<?= BASE_URL ?>vessels/create" class="btn btn-primary"><i class="fas fa-plus"></i> <span
            data-translate="btn_add_vessel">Tambah Kapal</span></a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
    <?php foreach ($vessels as $vessel): ?>
        <div class="card" style="padding: 0; overflow: hidden;">
            <div
                style="padding: 16px 20px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center;">
                <div
                    style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--primary-blue), var(--primary-navy)); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fas fa-ship"></i>
                </div>
                <span
                    class="badge badge-<?= $vessel['status'] === 'active' ? 'success' : 'warning' ?>"><?= ucfirst($vessel['status']) ?></span>
            </div>
            <div style="padding: 20px;">
                <h3 style="margin-bottom: 16px;"><?= htmlspecialchars($vessel['name']) ?></h3>
                <table style="width: 100%; font-size: 13px;">
                    <tr>
                        <td style="padding: 6px 0; color: var(--text-muted);" data-translate="vessel_type">Tipe:</td>
                        <td style="padding: 6px 0;">
                            <strong><?= htmlspecialchars($vessel['vessel_type_name'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: var(--text-muted);" data-translate="vessel_imo">IMO:</td>
                        <td style="padding: 6px 0;"><strong><?= htmlspecialchars($vessel['imo_number'] ?? '-') ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: var(--text-muted);" data-translate="vessel_flag">Bendera:</td>
                        <td style="padding: 6px 0;"><strong><?= $vessel['flag_emoji'] ?? '' ?>
                                <?= htmlspecialchars($vessel['flag_state_name'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: var(--text-muted);" data-translate="vessel_owner">Pemilik:</td>
                        <td style="padding: 6px 0;"><strong><?= htmlspecialchars($vessel['owner_name'] ?? '-') ?></strong>
                        </td>
                    </tr>
                </table>
                <div
                    style="margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; color: var(--text-secondary);"><?= $vessel['active_crew_count'] ?? 0 ?>
                        <span data-translate="crew_onboard">Crew di Kapal</span></span>
                </div>
            </div>
            <div
                style="padding: 16px 20px; background: rgba(0,0,0,0.1); border-top: 1px solid var(--border-color); display: flex; justify-content: center; gap: 8px;">
                <a href="<?= BASE_URL ?>vessels/<?= $vessel['id'] ?>" class="btn-icon" title="View"><i
                        class="fas fa-eye"></i></a>
                <a href="<?= BASE_URL ?>vessels/edit/<?= $vessel['id'] ?>" class="btn-icon" title="Edit"><i
                        class="fas fa-edit"></i></a>
                <a href="<?= BASE_URL ?>vessels/crew/<?= $vessel['id'] ?>" class="btn-icon" title="Crew List"><i
                        class="fas fa-users"></i></a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>