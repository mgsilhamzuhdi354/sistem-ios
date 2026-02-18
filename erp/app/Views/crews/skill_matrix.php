<?php
/**
 * Skill Matrix View - Enhanced Version
 */
$currentPage = 'crews';
ob_start();

// Helper function for level badge
function getLevelBadge($level)
{
    $colors = [
        'basic' => '#6B7280',
        'intermediate' => '#3B82F6',
        'advanced' => '#10B981',
        'expert' => '#D4AF37'
    ];
    $color = $colors[$level] ?? '#6B7280';
    return "<span style='background: {$color}20; color: {$color}; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;'>" . strtoupper($level) . "</span>";
}
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-network"></i> Crew Skill Matrix</h1>
        <p>Competency matrix of all crew members</p>
    </div>
    <a href="<?= BASE_URL ?>crews" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Crew List
    </a>
</div>

<?php if (!empty($statistics)): ?>
    <!-- Statistics Cards -->
    <div class="grid-4" style="gap: 20px; margin-bottom: 24px;">
        <div class="card">
            <h5 style="color: var(--text-muted); font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Total
                Skills</h5>
            <div style="font-size: 32px; font-weight: 700; color: var(--accent-gold);">
                <?= $statistics['total_unique_skills'] ?? 0 ?>
            </div>
            <small style="color: var(--text-muted);">Unique skill types</small>
        </div>

        <div class="card">
            <h5 style="color: var(--text-muted); font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Crew with
                Skills</h5>
            <div style="font-size: 32px; font-weight: 700; color: #3B82F6;">
                <?= $statistics['total_crew_with_skills'] ?? 0 ?>
            </div>
            <small style="color: var(--text-muted);">Members registered</small>
        </div>

        <div class="card">
            <h5 style="color: var(--text-muted); font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Expert
                Level</h5>
            <div style="font-size: 32px; font-weight: 700; color: #D4AF37;">
                <?= $statistics['expert_count'] ?? 0 ?>
            </div>
            <small style="color: var(--text-muted);">Expert certifications</small>
        </div>

        <div class="card">
            <h5 style="color: var(--text-muted); font-size: 13px; text-transform: uppercase; margin-bottom: 8px;">Total
                Entries</h5>
            <div style="font-size: 32px; font-weight: 700; color: #10B981;">
                <?= $statistics['total_skill_entries'] ?? 0 ?>
            </div>
            <small style="color: var(--text-muted);">Skill records</small>
        </div>
    </div>
<?php endif; ?>

<?php if (empty($skillMatrix)): ?>
    <!-- Empty State -->
    <div class="card" style="text-align: center; padding: 60px 20px;">
        <div style="font-size: 64px; color: var(--text-muted); margin-bottom: 20px;">
            <i class="fas fa-chart-network"></i>
        </div>
        <h3 style="color: var(--text-muted); margin-bottom: 12px;">No Skills Data</h3>
        <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 24px;">
            No crew members have registered skills yet. Add crew members and their skills to view the competency matrix.
        </p>
        <a href="<?= BASE_URL ?>crews" class="btn btn-primary">
            <i class="fas fa-users"></i> Go to Crew List
        </a>
    </div>
<?php else: ?>
    <!-- Skill Matrix Table -->
    <div class="card">
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="color: var(--accent-gold);"><i class="fas fa-table"></i> Competency Matrix</h4>
            <div style="display: flex; gap: 12px;">
                <input type="text" id="searchSkills" placeholder="Search skills... "
                    style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-dark); color: var(--text-light);">
                <select id="filterLevel"
                    style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-dark); color: var(--text-light);">
                    <option value="">All Levels</option>
                    <option value="basic">Basic</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="expert">Expert</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 200px;">Skill Name</th>
                        <th>Crew Members (Name - Level - Cert)</th>
                        <th style="width: 120px; text-align: center;">Avg Level</th>
                        <th style="width: 80px; text-align: center;">Count</th>
                    </tr>
                </thead>
                <tbody id="skillMatrixBody">
                    <?php foreach ($skillMatrix as $skillName => $crew): ?>
                        <?php
                        // Calculate average level
                        $levelValues = ['basic' => 1, 'intermediate' => 2, 'advanced' => 3, 'expert' => 4];
                        $levels = array_column($crew, 'skill_level');
                        $avg = array_sum(array_map(fn($l) => $levelValues[$l] ?? 0, $levels)) / count($levels);
                        $avgLevelText = ['Basic', 'Intermediate', 'Advanced', 'Expert'][floor($avg) - 1] ?? 'Basic';
                        $avgLevelKey = array_search($avgLevelText, ['Basic' => 'basic', 'Intermediate' => 'intermediate', 'Advanced' => 'advanced', 'Expert' => 'expert']) ?: 'basic';
                        ?>
                        <tr class="skill-row" data-skill="<?= strtolower($skillName) ?>"
                            data-level="<?= implode(',', $levels) ?>">
                            <td>
                                <strong style="color: var(--accent-gold);">
                                    <i class="fas fa-certificate" style="margin-right: 6px;"></i>
                                    <?= htmlspecialchars($skillName) ?>
                                </strong>
                            </td>
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    <?php foreach ($crew as $member): ?>
                                        <?php
                                        $statusColor = $member['crew_status'] === 'available' ? '#10B981' : '#3B82F6';
                                        ?>
                                        <a href="<?= BASE_URL ?>crews/<?= $member['crew_id'] ?>"
                                            style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 20px; text-decoration: none; border-left: 3px solid <?= $statusColor ?>;"
                                            title="View crew details">
                                            <span style="color: var(--text-light); font-weight: 500;">
                                                <?= htmlspecialchars($member['crew_name']) ?>
                                            </span>
                                            <span>-</span>
                                            <?= getLevelBadge($member['skill_level']) ?>
                                            <?php if (!empty($member['certificate_id'])): ?>
                                                <span style="color: var(--text-muted); font-size: 11px;">
                                                    <i class="fas fa-id-card"></i> <?= htmlspecialchars($member['certificate_id']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <?= getLevelBadge(strtolower($avgLevelText)) ?>
                            </td>
                            <td style="text-align: center;">
                                <span
                                    style="background: rgba(139, 92, 246, 0.2); color: #8B5CF6; padding: 4px 10px; border-radius: 12px; font-weight: 600;">
                                    <?= count($crew) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
    // Search and filter functionality
    document.getElementById('searchSkills').addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.skill-row');

        rows.forEach(row => {
            const skillName = row.dataset.skill;
            if (skillName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    document.getElementById('filterLevel').addEventListener('change', function (e) {
        const level = e.target.value;
        const rows = document.querySelectorAll('.skill-row');

        if (!level) {
            rows.forEach(row => row.style.display = '');
            return;
        }

        rows.forEach(row => {
            const levels = row.dataset.level.split(',');
            if (levels.includes(level)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>