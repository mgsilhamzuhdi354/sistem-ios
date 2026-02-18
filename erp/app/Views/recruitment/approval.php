<?php
/**
 * Recruitment Approval Center View
 */
?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Approval Center</h1>
        <p>Review dan approve crew dari recruitment system</p>
    </div>
    <a href="<?= BASE_URL ?>recruitment/pipeline" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Pipeline
    </a>
</div>

<?php if (empty($pendingApprovals)): ?>
    <!-- Hero Empty State -->
    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 60px 40px; text-align: center; margin-bottom: 32px; box-shadow: 0 20px 60px rgba(16, 185, 129, 0.15); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 40L40 0H20L0 20M40 40V20L20 40\'/%3E%3C/g%3E%3C/svg%3E'); opacity: 0.3;"></div>
        <div style="position: relative; z-index: 1;">
            <div style="width: 120px; height: 120px; background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
                <i class="fas fa-check-circle" style="font-size: 60px; color: white;"></i>
            </div>
            <h2 style="font-size: 32px; font-weight: 800; color: white; margin-bottom: 12px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                All Clear! 🎉
            </h2>
            <p style="font-size: 18px; color: rgba(255, 255, 255, 0.95); max-width: 600px; margin: 0 auto; line-height: 1.6;">
                Tidak ada approval yang pending saat ini.<br>
                Semua aplikasi recruitment telah ditinjau dan diproses.
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid-3" style="margin-bottom: 32px; gap: 20px;">
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.06); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'">
            <div style="display: flex; align-items: center; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                    <i class="fas fa-check-double" style="font-size: 20px; color: white;"></i>
                </div>
                <div>
                    <div style="font-size: 28px; font-weight: 700; color: #1f2937;"><?= $stats['pending_count'] ?? 0 ?></div>
                    <div style="font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Pending</div>
                </div>
            </div>
            <p style="font-size: 13px; color: #9ca3af; margin: 0;">
                <?= ($stats['pending_count'] ?? 0) > 0 ? 'Menunggu approval' : 'Tidak ada yang menunggu' ?>
            </p>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.06); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'">
            <div style="display: flex; align-items: center; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                    <i class="fas fa-user-check" style="font-size: 20px; color: white;"></i>
                </div>
                <div>
                    <div style="font-size: 28px; font-weight: 700; color: #1f2937;"><?= $stats['total_processed'] ?? 0 ?></div>
                    <div style="font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Processed</div>
                </div>
            </div>
            <p style="font-size: 13px; color: #9ca3af; margin: 0;">Diproses bulan ini</p>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.06); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'">
            <div style="display: flex; align-items: center; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                    <i class="fas fa-clock" style="font-size: 20px; color: white;"></i>
                </div>
                <div>
                    <div style="font-size: 28px; font-weight: 700; color: #1f2937;"><?= $stats['avg_processing_time'] ?? 0 ?>h</div>
                    <div style="font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Avg Time</div>
                </div>
            </div>
            <p style="font-size: 13px; color: #9ca3af; margin: 0;">Waktu rata-rata proses</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <h3 style="font-size: 20px; font-weight: 700; color: #1f2937; margin-bottom: 20px;">
        <i class="fas fa-bolt" style="color: #f59e0b; margin-right: 8px;"></i> Quick Actions
    </h3>
    <div class="grid-3" style="gap: 20px;">
        <a href="<?= BASE_URL ?>recruitment/pipeline" style="text-decoration: none; display: block; background: white; border-radius: 12px; padding: 28px 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.06); transition: all 0.3s; position: relative; overflow: hidden;" onmouseover="this.style.boxShadow='0 12px 32px rgba(59,130,246,0.15)'; this.style.transform='translateY(-6px)'; this.style.borderColor='#3b82f6'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'; this.style.borderColor='rgba(0,0,0,0.06)'">
            <div style="position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(59, 130, 246, 0.03)); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                    <i class="fas fa-funnel-dollar" style="font-size: 26px; color: white;"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Recruitment Pipeline</h4>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5; margin-bottom: 16px;">Monitor dan kelola semua kandidat dalam proses recruitment</p>
                <div style="display: inline-flex; align-items: center; color: #3b82f6; font-weight: 600; font-size: 14px;">
                    Buka Pipeline <i class="fas fa-arrow-right" style="margin-left: 8px; font-size: 12px;"></i>
                </div>
            </div>
        </a>

        <a href="<?= BASE_URL ?>recruitment/onboarding" style="text-decoration: none; display: block; background: white; border-radius: 12px; padding: 28px 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.06); transition: all 0.3s; position: relative; overflow: hidden;" onmouseover="this.style.boxShadow='0 12px 32px rgba(168,85,247,0.15)'; this.style.transform='translateY(-6px)'; this.style.borderColor='#a855f7'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'; this.style.borderColor='rgba(0,0,0,0.06)'">
            <div style="position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; background: linear-gradient(135deg, rgba(168, 85, 247, 0.08), rgba(168, 85, 247, 0.03)); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(168, 85, 247, 0.3);">
                    <i class="fas fa-user-plus" style="font-size: 26px; color: white;"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Auto-Onboarding</h4>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5; margin-bottom: 16px;">Onboard crew yang sudah approved langsung ke sistem ERP</p>
                <div style="display: inline-flex; align-items: center; color: #a855f7; font-weight: 600; font-size: 14px;">
                    Onboarding Center <i class="fas fa-arrow-right" style="margin-left: 8px; font-size: 12px;"></i>
                </div>
            </div>
        </a>

        <a href="<?= BASE_URL ?>recruitment" style="text-decoration: none; display: block; background: white; border-radius: 12px; padding: 28px 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.06); transition: all 0.3s; position: relative; overflow: hidden;" onmouseover="this.style.boxShadow='0 12px 32px rgba(212,175,55,0.15)'; this.style.transform='translateY(-6px)'; this.style.borderColor='#d4af37'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'; this.style.transform='translateY(0)'; this.style.borderColor='rgba(0,0,0,0.06)'">
            <div style="position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), rgba(212, 175, 55, 0.03)); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #d4af37 0%, #b8941e 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);">
                    <i class="fas fa-users" style="font-size: 26px; color: white;"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Recruitment Hub</h4>
                <p style="font-size: 14px; color: #6b7280; line-height: 1.5; margin-bottom: 16px;">Dashboard pusat untuk semua aktivitas recruitment system</p>
                <div style="display: inline-flex; align-items: center; color: #d4af37; font-weight: 600; font-size: 14px;">
                    Buka Dashboard <i class="fas fa-arrow-right" style="margin-left: 8px; font-size: 12px;"></i>
                </div>
            </div>
        </a>
    </div>
<?php else: ?>
    <div class="card">
        <h3 style="margin-bottom: 16px;">Pending Approvals (
            <?= count($pendingApprovals) ?>)
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Applicant Name</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Interview Score</th>
                    <th>Applied Date</th>
                    <th>Recommended By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingApprovals as $app): ?>
                    <tr>
                        <td><strong>
                                <?= htmlspecialchars($app['applicant_name']) ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars($app['position_applied']) ?>
                        </td>
                        <td>
                            <span
                                class="badge badge-<?= $app['application_status'] === 'interview_passed' ? 'success' : 'info' ?>">
                                <?= ucwords(str_replace('_', ' ', $app['application_status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($app['interview_score']): ?>
                                <strong style="color: var(--accent-gold);">
                                    <?= $app['interview_score'] ?>/100
                                </strong>
                            <?php else: ?>
                                <span class="badge badge-secondary">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= date('d M Y', strtotime($app['created_at'])) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($app['recommended_by'] ?? '-') ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <button onclick="approveApplication(<?= $app['id'] ?>)" class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button onclick="rejectApplication(<?= $app['id'] ?>)" class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
    function approveApplication(id) {
        if (confirm('Approve aplikasi ini? Crew akan siap di-onboard ke ERP.')) {
            // Redirect to approval endpoint
            window.location.href = '<?= BASE_URL ?>recruitment/approve/' + id;
        }
    }

    function rejectApplication(id) {
        if (confirm('Reject aplikasi ini?')) {
            window.location.href = '<?= BASE_URL ?>recruitment/reject/' + id;
        }
    }
</script>