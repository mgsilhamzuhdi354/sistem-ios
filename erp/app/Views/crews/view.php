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
            <img src="<?= BASE_URL . $crew['photo'] ?>" alt=""
                style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-gold);">
        <?php else: ?>
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($crew['full_name']) ?>&background=0A2463&color=fff&size=80"
                alt="" style="width: 80px; height: 80px; border-radius: 50%;">
        <?php endif; ?>
        <div>
            <h1><?= htmlspecialchars($crew['full_name']) ?></h1>
            <p>
                <code
                    style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold); padding: 4px 10px; border-radius: 4px; margin-right: 10px;">
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
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Jenis Kelamin</td>
                <td style="text-align: right;"><?= $crew['gender'] === 'male' ? 'Laki-laki' : 'Perempuan' ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Tanggal Lahir</td>
                <td style="text-align: right;">
                    <?= $crew['birth_date'] ? date('d M Y', strtotime($crew['birth_date'])) : '-' ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Tempat Lahir</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['birth_place'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Kewarganegaraan</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['nationality'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Agama</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['religion'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Status</td>
                <td style="text-align: right;"><?= ucfirst(str_replace('_', ' ', $crew['marital_status'])) ?></td>
            </tr>
        </table>
    </div>

    <!-- Contact -->
    <div class="card">
        <h4 style="margin-bottom: 16px; color: var(--accent-gold);"><i class="fas fa-address-book"></i> Kontak</h4>
        <table style="width: 100%; font-size: 14px;">
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Email</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['email'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Telepon</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['phone'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">WhatsApp</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['whatsapp'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Kota</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['city'] ?: '-') ?></td>
            </tr>
        </table>

        <?php if ($crew['emergency_name']): ?>
            <hr style="margin: 16px 0; border-color: var(--border-color);">
            <h5 style="margin-bottom: 12px; color: var(--warning);"><i class="fas fa-exclamation-triangle"></i> Darurat</h5>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="color: var(--text-muted); padding: 4px 0;">Nama</td>
                    <td style="text-align: right;"><?= htmlspecialchars($crew['emergency_name']) ?></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); padding: 4px 0;">Hubungan</td>
                    <td style="text-align: right;"><?= htmlspecialchars($crew['emergency_relation'] ?: '-') ?></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); padding: 4px 0;">Telepon</td>
                    <td style="text-align: right;"><?= htmlspecialchars($crew['emergency_phone'] ?: '-') ?></td>
                </tr>
            </table>
        <?php endif; ?>
    </div>

    <!-- Banking -->
    <div class="card">
        <h4 style="margin-bottom: 16px; color: var(--accent-gold);"><i class="fas fa-university"></i> Bank</h4>
        <table style="width: 100%; font-size: 14px;">
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Bank</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['bank_name'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">No. Rekening</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['bank_account'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 6px 0;">Atas Nama</td>
                <td style="text-align: right;"><?= htmlspecialchars($crew['bank_holder'] ?: '-') ?></td>
            </tr>
        </table>

        <hr style="margin: 16px 0; border-color: var(--border-color);">
        <h5 style="margin-bottom: 12px; color: var(--info);"><i class="fas fa-anchor"></i> Pengalaman</h5>
        <table style="width: 100%; font-size: 14px;">
            <tr>
                <td style="color: var(--text-muted); padding: 4px 0;">Pengalaman</td>
                <td style="text-align: right;"><?= $crew['years_experience'] ?? 0 ?> tahun</td>
            </tr>
            <tr>
                <td style="color: var(--text-muted); padding: 4px 0;">Total Sea Time</td>
                <td style="text-align: right;"><?= $crew['total_sea_time_months'] ?? 0 ?> bulan</td>
            </tr>
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
                <div
                    style="background: rgba(0,0,0,0.2); padding: 12px; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                    <div
                        style="width: 40px; height: 40px; background: rgba(139, 92, 246, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-<?= strpos($doc['mime_type'], 'pdf') !== false ? 'file-pdf' : 'file-image' ?>"
                            style="color: #8B5CF6;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= htmlspecialchars($doc['document_name']) ?></div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <?php if ($doc['expiry_date']): ?>
                                <?php
                                $daysLeft = (strtotime($doc['expiry_date']) - time()) / 86400;
                                $expColor = $daysLeft < 0 ? 'var(--danger)' : ($daysLeft < 90 ? 'var(--warning)' : 'var(--success)');
                                ?>
                                <span style="color: <?= $expColor ?>;">Exp:
                                    <?= date('d M Y', strtotime($doc['expiry_date'])) ?></span>
                            <?php else: ?>
                                No expiry
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>documents/preview/<?= $doc['id'] ?>" target="_blank" class="btn-icon"
                        title="Preview">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Contract History -->
<div class="card" style="margin-bottom: 24px;">
    <h4 style="margin-bottom: 16px; color: var(--accent-gold);"><i class="fas fa-history"></i> Sejarah Kontrak
        (<?= count($contractHistory) ?>)</h4>

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
                        <td><a
                                href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>"><?= htmlspecialchars($contract['contract_no']) ?></a>
                        </td>
                        <td><?= htmlspecialchars($contract['vessel_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($contract['client_name'] ?? '-') ?></td>
                        <td><?= date('d M Y', strtotime($contract['sign_on_date'])) ?> -
                            <?= date('d M Y', strtotime($contract['sign_off_date'])) ?></td>
                        <td>
                            <span
                                class="badge badge-<?= $contract['status'] === 'active' ? 'success' : ($contract['status'] === 'completed' ? 'info' : 'secondary') ?>">
                                <?= ucfirst($contract['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Skills & Certifications -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h4 style="color: var(--accent-gold);"><i class="fas fa-certificate"></i> Skills & Certifications
            (<?= count($skills) ?>)</h4>
        <?php if ($this->checkPermission('crews', 'edit')): ?>
            <button class="btn btn-sm btn-primary" onclick="openSkillModal()">
                <i class="fas fa-plus"></i> Add Skill
            </button>
        <?php endif; ?>
    </div>

    <?php if (empty($skills)): ?>
        <p style="color: var(--text-muted); text-align: center; padding: 20px;">Belum ada skill tercatat. Klik tombol "Add
            Skill" untuk menambahkan.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table" style="font-size: 14px;">
                <thead>
                    <tr>
                        <th>Skill Name</th>
                        <th>Level</th>
                        <th>Certificate ID</th>
                        <th>Notes</th>
                        <?php if ($this->checkPermission('crews', 'edit')): ?>
                            <th style="width: 100px;">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="skills-table-body">
                    <?php foreach ($skills as $skill): ?>
                        <?php
                        $levelColors = ['basic' => '#6B7280', 'intermediate' => '#3B82F6', 'advanced' => '#10B981', 'expert' => '#D4AF37'];
                        $color = $levelColors[$skill['skill_level']] ?? '#6B7280';
                        ?>
                        <tr id="skill-row-<?= $skill['id'] ?>">
                            <td><strong><?= htmlspecialchars($skill['skill_name']) ?></strong></td>
                            <td>
                                <span
                                    style="background: <?= $color ?>20; color: <?= $color ?>; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    <?= strtoupper($skill['skill_level']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($skill['certificate_id'] ?: '-') ?></td>
                            <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                title="<?= htmlspecialchars($skill['notes'] ?: '') ?>">
                                <?= htmlspecialchars($skill['notes'] ?: '-') ?>
                            </td>
                            <?php if ($this->checkPermission('crews', 'edit')): ?>
                                <td>
                                    <button class="btn-icon" onclick='editSkill(<?= json_encode($skill) ?>)' title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($this->checkPermission('crews', 'delete')): ?>
                                        <button class="btn-icon"
                                            onclick="deleteSkill(<?= $skill['id'] ?>, '<?= htmlspecialchars($skill['skill_name']) ?>')"
                                            title="Delete" style="color: var(--danger);">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Skill Modal -->
<div id="skillModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="skillModalTitle">Add Skill</h5>
                <button type="button" class="close" onclick="closeSkillModal()">&times;</button>
            </div>
            <form id="skillForm" onsubmit="saveSkill(event)">
                <div class="modal-body">
                    <input type="hidden" id="skill_id" name="skill_id">

                    <div class="form-group">
                        <label for="skill_name">Skill Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="skill_name" name="skill_name" class="form-control" required
                            list="common-skills" placeholder="e.g. Navigation, Engine Maintenance">
                        <datalist id="common-skills">
                            <option value="Navigation">
                            <option value="Bridge Watchkeeping">
                            <option value="ECDIS Operation">
                            <option value="Radar Operation">
                            <option value="GMDSS Communication">
                            <option value="Engine Maintenance">
                            <option value="Electrical Systems">
                            <option value="Hydraulic Systems">
                            <option value="Refrigeration">
                            <option value="Welding">
                            <option value="Firefighting">
                            <option value="First Aid">
                            <option value="Crisis Management">
                            <option value="Security Awareness">
                            <option value="Survival at Sea">
                            <option value="Cargo Handling">
                            <option value="Mooring Operations">
                            <option value="Deck Operations">
                            <option value="Crane Operations">
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="skill_level">Skill Level <span style="color: var(--danger);">*</span></label>
                        <select id="skill_level" name="skill_level" class="form-control" required>
                            <option value="basic">Basic</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="certificate_id">Certificate ID</label>
                        <input type="text" id="certificate_id" name="certificate_id" class="form-control"
                            placeholder="e.g. NAV-2024-001">
                        <small class="form-text text-muted">Optional: Certificate number if applicable</small>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3"
                            placeholder="Additional notes about this skill..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeSkillModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Skill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal {
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
        animation: fadeIn 0.3s;
    }

    .modal-dialog {
        position: relative;
        margin: 50px auto;
        max-width: 600px;
        animation: slideDown 0.3s;
    }

    .modal-content {
        background-color: var(--bg-card);
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h5 {
        margin: 0;
        color: var(--accent-gold);
    }

    .close {
        background: none;
        border: none;
        font-size: 28px;
        font-weight: 300;
        color: var(--text-muted);
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        line-height: 1;
    }

    .close:hover {
        color: var(--danger);
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<script>
    const BASE_URL = '<?= BASE_URL ?>';
    const CREW_ID = <?= $crew['id'] ?>;

    function openSkillModal(skill = null) {
        const modal = document.getElementById('skillModal');
        const form = document.getElementById('skillForm');
        const title = document.getElementById('skillModalTitle');

        if (skill) {
            title.textContent = 'Edit Skill';
            document.getElementById('skill_id').value = skill.id;
            document.getElementById('skill_name').value = skill.skill_name;
            document.getElementById('skill_name').readOnly = true; // Don't allow changing skill name
            document.getElementById('skill_level').value = skill.skill_level;
            document.getElementById('certificate_id').value = skill.certificate_id || '';
            document.getElementById('notes').value = skill.notes || '';
        } else {
            title.textContent = 'Add Skill';
            form.reset();
            document.getElementById('skill_id').value = '';
            document.getElementById('skill_name').readOnly = false;
        }

        modal.style.display = 'block';
    }

    function closeSkillModal() {
        document.getElementById('skillModal').style.display = 'none';
        document.getElementById('skillForm').reset();
    }

    function editSkill(skill) {
        openSkillModal(skill);
    }

    async function saveSkill(event) {
        event.preventDefault();

        const form = event.target;
        const skillId = document.getElementById('skill_id').value;
        const formData = new FormData(form);

        const url = skillId ?
            BASE_URL + 'crews/updateSkill/' + skillId :
            BASE_URL + 'crews/addSkill/' + CREW_ID;

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                closeSkillModal();
                showMessage(result.message, 'success');
                // Reload page to show updated skills
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(result.message, 'error');
            }
        } catch (error) {
            showMessage('An error occurred. Please try again.', 'error');
        }
    }

    async function deleteSkill(skillId, skillName) {
        if (!confirm(`Are you sure you want to delete skill "${skillName}"?`)) {
            return;
        }

        try {
            const response = await fetch(BASE_URL + 'crews/deleteSkill/' + skillId, {
                method: 'POST'
            });

            const result = await response.json();

            if (result.success) {
                showMessage(result.message, 'success');
                // Remove row from table
                const row = document.getElementById('skill-row-' + skillId);
                if (row) {
                    row.remove();
                }
                // Reload to update count
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(result.message, 'error');
            }
        } catch (error) {
            showMessage('An error occurred. Please try again.', 'error');
        }
    }

    function showMessage(message, type) {
        // Simple alert for now, can be replaced with better notification
        const icon = type === 'success' ? '✓' : '✗';
        const style = type === 'success' ? 'background: #10B981; color: white;' : 'background: #EF4444; color: white;';

        const msgDiv = document.createElement('div');
        msgDiv.style = `${style} padding: 16px 24px; border-radius: 8px; position: fixed; top: 20px; right: 20px; z-index: 9999; animation: slideInRight 0.3s;`;
        msgDiv.innerHTML = `<strong>${icon}</strong> ${message}`;
        document.body.appendChild(msgDiv);

        setTimeout(() => msgDiv.remove(), 3000);
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        const modal = document.getElementById('skillModal');
        if (event.target === modal) {
            closeSkillModal();
        }
    }
</script>


<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>