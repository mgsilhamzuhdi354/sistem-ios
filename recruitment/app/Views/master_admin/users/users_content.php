<!-- Master Admin Users Content - Redesigned -->
<style>
.users-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.users-header h1 { font-size: 1.75rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.75rem; }
.users-header .btn-add { background: linear-gradient(135deg, #1e3a5f, #2c5282); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
.users-header .btn-add:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(30, 58, 95, 0.3); }

.user-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 1rem; }
.stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.stat-icon.master { background: linear-gradient(135deg, #7c3aed, #a855f7); color: white; }
.stat-icon.admin { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: white; }
.stat-icon.leader { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white; }
.stat-icon.crewing { background: linear-gradient(135deg, #10b981, #34d399); color: white; }
.stat-info .stat-value { font-size: 2rem; font-weight: 700; color: #1e293b; line-height: 1; }
.stat-info .stat-label { color: #64748b; font-size: 0.875rem; margin-top: 0.25rem; }

.users-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
.user-section { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden; }
.section-header { padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
.section-header.master { background: linear-gradient(135deg, #7c3aed, #a855f7); color: white; }
.section-header.admin { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: white; }
.section-header.leader { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white; }
.section-header.crewing { background: linear-gradient(135deg, #10b981, #34d399); color: white; }
.section-header h3 { font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin: 0; }
.section-header .count { background: rgba(255,255,255,0.2); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; }

.user-list { max-height: 350px; overflow-y: auto; }
.user-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
.user-item:hover { background: #f8fafc; }
.user-item:last-child { border-bottom: none; }

.user-info { display: flex; align-items: center; gap: 1rem; }
.user-avatar { width: 45px; height: 45px; border-radius: 12px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #64748b; overflow: hidden; }
.user-avatar img { width: 100%; height: 100%; object-fit: cover; }
.user-avatar.online { box-shadow: 0 0 0 3px #10b981; }
.user-details .user-name { font-weight: 600; color: #1e293b; }
.user-details .user-email { font-size: 0.8rem; color: #64748b; }
.user-details .user-meta { font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem; }
.user-meta .badge { padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 500; }
.badge-online { background: #dcfce7; color: #16a34a; }
.badge-offline { background: #f1f5f9; color: #64748b; }

.user-actions { display: flex; gap: 0.5rem; }
.action-btn { width: 36px; height: 36px; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.action-btn.edit { background: #e0f2fe; color: #0284c7; }
.action-btn.edit:hover { background: #0284c7; color: white; }
.action-btn.delete { background: #fee2e2; color: #dc2626; }
.action-btn.delete:hover { background: #dc2626; color: white; }

.empty-state { padding: 3rem; text-align: center; color: #94a3b8; }
.empty-state i { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.5; }
</style>

<div class="users-header">
    <h1><i class="fas fa-users-cog"></i> User Management</h1>
    <a href="<?= url('/master-admin/users/create') ?>" class="btn-add">
        <i class="fas fa-plus"></i> Add New User
    </a>
</div>

<!-- Stats Cards -->
<div class="user-stats">
    <div class="stat-card">
        <div class="stat-icon master"><i class="fas fa-crown"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= count($masterAdmins ?? []) ?></div>
            <div class="stat-label">Master Admin</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon admin"><i class="fas fa-user-shield"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= count($admins ?? []) ?></div>
            <div class="stat-label">Admins</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon leader"><i class="fas fa-user-tie"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= count($leaders ?? []) ?></div>
            <div class="stat-label">Leaders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon crewing"><i class="fas fa-id-badge"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= count($crewingStaff ?? []) ?></div>
            <div class="stat-label">Crewing Staff</div>
        </div>
    </div>
</div>

<!-- Users Grid -->
<div class="users-grid">
    <!-- Master Admins -->
    <div class="user-section">
        <div class="section-header master">
            <h3><i class="fas fa-crown"></i> Master Admins</h3>
            <span class="count"><?= count($masterAdmins ?? []) ?></span>
        </div>
        <div class="user-list">
            <?php if (empty($masterAdmins)): ?>
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <p>No master admins</p>
            </div>
            <?php else: ?>
            <?php foreach ($masterAdmins as $user): ?>
            <div class="user-item">
                <div class="user-info">
                    <div class="user-avatar <?= $user['is_online'] ? 'online' : '' ?>">
                        <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= url('/uploads/avatars/' . $user['avatar']) ?>" alt="">
                        <?php else: ?>
                        <?= strtoupper(substr($user['full_name'], 0, 2)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="user-meta">
                            <span class="badge <?= $user['is_online'] ? 'badge-online' : 'badge-offline' ?>">
                                <?= $user['is_online'] ? '● Online' : '○ Offline' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="user-actions">
                    <a href="<?= url('/master-admin/users/edit/' . $user['id']) ?>" class="action-btn edit" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Admins -->
    <div class="user-section">
        <div class="section-header admin">
            <h3><i class="fas fa-user-shield"></i> Admins</h3>
            <span class="count"><?= count($admins ?? []) ?></span>
        </div>
        <div class="user-list">
            <?php if (empty($admins)): ?>
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <p>No admins yet</p>
            </div>
            <?php else: ?>
            <?php foreach ($admins as $user): ?>
            <div class="user-item">
                <div class="user-info">
                    <div class="user-avatar <?= $user['is_online'] ? 'online' : '' ?>">
                        <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= url('/uploads/avatars/' . $user['avatar']) ?>" alt="">
                        <?php else: ?>
                        <?= strtoupper(substr($user['full_name'], 0, 2)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="user-meta">
                            <span class="badge <?= $user['is_online'] ? 'badge-online' : 'badge-offline' ?>">
                                <?= $user['is_online'] ? '● Online' : '○ Offline' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="user-actions">
                    <a href="<?= url('/master-admin/users/edit/' . $user['id']) ?>" class="action-btn edit" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form action="<?= url('/master-admin/users/delete/' . $user['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this admin?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="action-btn delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Leaders -->
    <div class="user-section">
        <div class="section-header leader">
            <h3><i class="fas fa-user-tie"></i> Leaders</h3>
            <span class="count"><?= count($leaders ?? []) ?></span>
        </div>
        <div class="user-list">
            <?php if (empty($leaders)): ?>
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <p>No leaders yet</p>
            </div>
            <?php else: ?>
            <?php foreach ($leaders as $user): ?>
            <div class="user-item">
                <div class="user-info">
                    <div class="user-avatar <?= $user['is_online'] ? 'online' : '' ?>">
                        <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= url('/uploads/avatars/' . $user['avatar']) ?>" alt="">
                        <?php else: ?>
                        <?= strtoupper(substr($user['full_name'], 0, 2)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="user-meta">
                            <?= $user['department'] ?? 'No Department' ?> • <?= $user['employee_id'] ?? '-' ?>
                        </div>
                    </div>
                </div>
                <div class="user-actions">
                    <a href="<?= url('/master-admin/users/edit/' . $user['id']) ?>" class="action-btn edit" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form action="<?= url('/master-admin/users/delete/' . $user['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this leader?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="action-btn delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Crewing Staff -->
    <div class="user-section">
        <div class="section-header crewing">
            <h3><i class="fas fa-id-badge"></i> Crewing Staff</h3>
            <span class="count"><?= count($crewingStaff ?? []) ?></span>
        </div>
        <div class="user-list">
            <?php if (empty($crewingStaff)): ?>
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <p>No crewing staff yet</p>
            </div>
            <?php else: ?>
            <?php foreach ($crewingStaff as $user): ?>
            <div class="user-item">
                <div class="user-info">
                    <div class="user-avatar <?= $user['is_online'] ? 'online' : '' ?>">
                        <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= url('/uploads/avatars/' . $user['avatar']) ?>" alt="">
                        <?php else: ?>
                        <?= strtoupper(substr($user['full_name'], 0, 2)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="user-meta">
                            <?= $user['specialization'] ?? 'All Departments' ?> • <?= $user['active_assignments'] ?? 0 ?> active
                        </div>
                    </div>
                </div>
                <div class="user-actions">
                    <a href="<?= url('/master-admin/users/edit/' . $user['id']) ?>" class="action-btn edit" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form action="<?= url('/master-admin/users/delete/' . $user['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this crewing staff?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="action-btn delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
