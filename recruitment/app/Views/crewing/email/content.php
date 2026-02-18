<!-- Crewing Email Center — Gmail-like Interface -->
<style>
    /* ====== EMAIL CLIENT LAYOUT ====== */
    .email-client {
        display: grid; grid-template-columns: 280px 1fr; gap: 0;
        background: white; border-radius: 16px; overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08); min-height: 75vh;
    }

    /* ---- Sidebar ---- */
    .email-sidebar {
        background: #f8f9fc; border-right: 1px solid #e5e7eb; padding: 20px 16px;
        display: flex; flex-direction: column; gap: 6px;
    }
    .btn-compose {
        padding: 14px 20px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white;
        border: none; border-radius: 16px; font-weight: 700; cursor: pointer; font-size: 0.95rem;
        display: flex; align-items: center; gap: 10px; transition: all 0.3s; margin-bottom: 16px;
        box-shadow: 0 4px 15px rgba(99,102,241,0.3);
    }
    .btn-compose:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99,102,241,0.4); }
    .btn-compose i { font-size: 1.1rem; }
    .sidebar-item {
        padding: 11px 16px; border-radius: 10px; cursor: pointer; font-size: 0.88rem;
        display: flex; align-items: center; gap: 10px; color: #4b5563; font-weight: 500;
        transition: all 0.2s; position: relative;
    }
    .sidebar-item:hover { background: #eef2ff; color: #6366f1; }
    .sidebar-item.active { background: #e0e7ff; color: #4f46e5; font-weight: 700; }
    .sidebar-item i { width: 18px; text-align: center; }
    .sidebar-item .badge-count {
        margin-left: auto; background: #6366f1; color: white; font-size: 0.72rem;
        padding: 2px 8px; border-radius: 10px; font-weight: 700;
    }
    .sidebar-divider { height: 1px; background: #e5e7eb; margin: 10px 0; }
    .sidebar-label {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
        color: #9ca3af; padding: 8px 16px 4px;
    }
    .smtp-info {
        margin-top: auto; padding: 12px; border-radius: 10px; font-size: 0.78rem;
    }
    .smtp-info.ok { background: #dcfce7; color: #166534; }
    .smtp-info.no { background: #fef3c7; color: #92400e; }

    /* ---- Main Content Area ---- */
    .email-main { padding: 0; display: flex; flex-direction: column; }

    /* ---- Header Bar ---- */
    .email-topbar {
        padding: 16px 24px; border-bottom: 1px solid #e5e7eb;
        display: flex; align-items: center; justify-content: space-between;
        background: white;
    }
    .email-topbar h3 { margin: 0; font-size: 1.1rem; color: #1e3a5f; font-weight: 700; }
    .email-topbar .search-box {
        display: flex; align-items: center; gap: 8px; background: #f3f4f6;
        padding: 8px 16px; border-radius: 10px; flex: 0 1 300px;
    }
    .email-topbar .search-box input {
        border: none; background: none; outline: none; font-size: 0.85rem;
        font-family: inherit; width: 100%; color: #374151;
    }
    .email-topbar .search-box i { color: #9ca3af; }

    /* ---- Panel Views ---- */
    .email-panel { display: none; flex: 1; overflow-y: auto; }
    .email-panel.active { display: flex; flex-direction: column; }

    /* ====== COMPOSE PANEL ====== */
    .compose-panel { padding: 24px; }
    .compose-form {
        border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; flex: 1;
        display: flex; flex-direction: column;
    }
    .compose-row {
        display: flex; align-items: center; padding: 10px 16px;
        border-bottom: 1px solid #f3f4f6; gap: 10px;
    }
    .compose-row label {
        font-weight: 600; font-size: 0.82rem; color: #6b7280; min-width: 65px;
    }
    .compose-row input, .compose-row select {
        flex: 1; border: none; outline: none; font-family: inherit;
        font-size: 0.9rem; color: #1f2937; background: transparent; padding: 4px 0;
    }
    .compose-row .from-display {
        flex: 1; font-size: 0.9rem; color: #374151;
    }
    .compose-row .from-display .from-name { font-weight: 600; color: #1e3a5f; }
    .compose-row .from-display .from-email { color: #6366f1; font-size: 0.82rem; }
    .compose-body-area {
        flex: 1; padding: 16px; min-height: 250px;
    }
    .compose-body-area textarea {
        width: 100%; min-height: 200px; border: none; outline: none;
        font-family: inherit; font-size: 0.9rem; color: #1f2937; resize: vertical;
        line-height: 1.7;
    }
    .compose-toolbar {
        padding: 12px 16px; border-top: 1px solid #f3f4f6;
        display: flex; align-items: center; gap: 10px; background: #fafbfc;
    }
    .compose-toolbar .btn-send-main {
        padding: 10px 28px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white;
        border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.9rem;
        display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;
    }
    .compose-toolbar .btn-send-main:hover { box-shadow: 0 6px 20px rgba(99,102,241,0.4); transform: translateY(-1px); }
    .compose-toolbar .btn-send-main:disabled { opacity: 0.6; cursor: wait; transform: none; }
    .compose-toolbar .toolbar-btn {
        background: none; border: none; color: #6b7280; font-size: 1.1rem;
        cursor: pointer; padding: 6px 10px; border-radius: 6px; transition: all 0.2s;
    }
    .compose-toolbar .toolbar-btn:hover { background: #eef2ff; color: #6366f1; }
    .compose-toolbar .toolbar-spacer { flex: 1; }

    /* Contact suggestions dropdown */
    .contact-suggestions {
        position: absolute; top: 100%; left: 65px; right: 16px; z-index: 100;
        background: white; border: 1px solid #e5e7eb; border-radius: 10px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12); max-height: 220px; overflow-y: auto;
        display: none;
    }
    .contact-suggestions.show { display: block; }
    .contact-item {
        padding: 10px 16px; cursor: pointer; display: flex; align-items: center; gap: 10px;
        transition: background 0.2s; font-size: 0.85rem;
    }
    .contact-item:hover { background: #eef2ff; }
    .contact-item .contact-avatar {
        width: 32px; height: 32px; border-radius: 50%; background: #6366f1;
        color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.8rem;
    }
    .contact-item .contact-info { flex: 1; }
    .contact-item .contact-name { font-weight: 600; color: #1f2937; }
    .contact-item .contact-email { color: #6b7280; font-size: 0.78rem; }

    /* ====== TEMPLATE SELECTOR ====== */
    .template-insert-row {
        padding: 10px 16px; border-bottom: 1px solid #f3f4f6;
        display: flex; align-items: center; gap: 10px; background: #fffbeb;
    }
    .template-insert-row label { color: #92400e; }
    .template-insert-row select {
        flex: 1; border: 1px solid #fcd34d; border-radius: 8px; padding: 6px 12px;
        font-size: 0.85rem; background: white; outline: none;
    }
    .template-insert-row .btn-insert-tpl {
        padding: 6px 14px; background: #f59e0b; color: white; border: none;
        border-radius: 8px; font-weight: 600; font-size: 0.8rem; cursor: pointer;
    }
    .template-insert-row .btn-insert-tpl:hover { background: #d97706; }

    /* ====== SENT ITEMS LIST ====== */
    .sent-list { flex: 1; }
    .sent-item {
        padding: 14px 24px; border-bottom: 1px solid #f3f4f6;
        display: flex; align-items: center; gap: 14px; cursor: pointer;
        transition: background 0.2s;
    }
    .sent-item:hover { background: #fafbfc; }
    .sent-item .sent-avatar {
        width: 40px; height: 40px; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; font-weight: 700;
        font-size: 0.9rem; color: white; flex-shrink: 0;
    }
    .sent-item .sent-content { flex: 1; min-width: 0; }
    .sent-item .sent-to { font-weight: 600; font-size: 0.88rem; color: #1f2937; }
    .sent-item .sent-subject {
        font-size: 0.82rem; color: #4b5563; white-space: nowrap;
        overflow: hidden; text-overflow: ellipsis;
    }
    .sent-item .sent-meta {
        display: flex; flex-direction: column; align-items: flex-end; gap: 4px;
        flex-shrink: 0;
    }
    .sent-item .sent-time { font-size: 0.75rem; color: #9ca3af; white-space: nowrap; }
    .sent-status {
        padding: 2px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700;
    }
    .sent-status.sent { background: #dcfce7; color: #166534; }
    .sent-status.pending { background: #fef3c7; color: #92400e; }
    .sent-status.failed { background: #fee2e2; color: #991b1b; }
    .sent-template-badge {
        display: inline-block; background: #eef2ff; color: #6366f1; padding: 1px 8px;
        border-radius: 4px; font-size: 0.7rem; font-weight: 600; margin-left: 4px;
    }
    .sent-actions {
        display: flex; align-items: center; gap: 4px; margin-left: 8px;
    }
    .btn-delete-email {
        background: none; border: none; color: #9ca3af; cursor: pointer;
        padding: 6px 8px; border-radius: 6px; font-size: 0.85rem; transition: all 0.2s;
    }
    .btn-delete-email:hover { background: #fee2e2; color: #dc2626; }

    /* ====== TEMPLATES PANEL ====== */
    .templates-grid-panel { padding: 24px; }
    .tpl-card {
        background: white; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px;
        border-left: 4px solid #6366f1; transition: all 0.3s; cursor: pointer;
    }
    .tpl-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .tpl-card h4 { margin: 0 0 6px; color: #1e3a5f; font-size: 1rem; }
    .tpl-card .tpl-subject { color: #6b7280; font-size: 0.82rem; margin-bottom: 10px; }
    .tpl-card .tpl-slug {
        background: #eef2ff; color: #6366f1; padding: 3px 10px; border-radius: 6px;
        font-size: 0.75rem; font-weight: 600; display: inline-block;
    }

    /* ====== ATTACHMENT ====== */
    .attach-section { padding: 0 16px 10px; }
    .attach-bar {
        display: flex; flex-wrap: wrap; gap: 6px; align-items: center;
    }
    .attach-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: #eef2ff; padding: 5px 12px; border-radius: 8px; font-size: 0.8rem;
        color: #374151;
    }
    .attach-chip i { color: #6366f1; }
    .attach-chip .chip-remove {
        background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.9rem; padding: 0 2px;
    }
    .attach-chip .chip-size { color: #9ca3af; font-size: 0.72rem; }

    /* ====== PREVIEW MODAL ====== */
    .preview-modal {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center;
        z-index: 99999; backdrop-filter: blur(4px);
    }
    .preview-modal.show { display: flex; animation: emFadeIn 0.3s ease; }
    .preview-box {
        background: white; border-radius: 20px; max-width: 650px; width: 95%;
        max-height: 85vh; overflow: hidden; display: flex; flex-direction: column;
    }
    .preview-header {
        background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white;
        padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;
    }
    .preview-header h3 { margin: 0; font-size: 1rem; }
    .preview-close { background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; }
    .preview-body { padding: 24px; overflow-y: auto; flex: 1; }

    /* ====== EMPTY STATE ====== */
    .empty-state {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        flex: 1; padding: 60px 20px; color: #9ca3af;
    }
    .empty-state i { font-size: 4rem; margin-bottom: 16px; opacity: 0.3; }
    .empty-state h4 { color: #6b7280; margin: 0 0 6px; }
    .empty-state p { font-size: 0.85rem; margin: 0; }

    /* ====== RESPONSIVE ====== */
    @media (max-width: 768px) {
        .email-client { grid-template-columns: 1fr; }
        .email-sidebar { display: none; }
    }
    @keyframes emFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes emToastIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

<div class="email-client">
    <!-- ====== SIDEBAR ====== -->
    <div class="email-sidebar">
        <button class="btn-compose" onclick="showPanel('compose')">
            <i class="fas fa-pen"></i> <?= getCurrentLanguage() === 'en' ? 'Compose Email' : 'Tulis Email' ?>
        </button>

        <div class="sidebar-item active" onclick="showPanel('compose')" data-panel="compose">
            <i class="fas fa-edit"></i> <?= getCurrentLanguage() === 'en' ? 'Compose' : 'Tulis' ?>
        </div>
        <div class="sidebar-item" onclick="showPanel('sent')" data-panel="sent">
            <i class="fas fa-paper-plane"></i> <?= getCurrentLanguage() === 'en' ? 'Sent' : 'Terkirim' ?>
            <?php if (count($logs) > 0): ?>
                <span class="badge-count"><?= count($logs) ?></span>
            <?php endif; ?>
        </div>
        <div class="sidebar-item" onclick="showPanel('templates')" data-panel="templates">
            <i class="fas fa-file-alt"></i> Template
            <span class="badge-count"><?= count($templates) ?></span>
        </div>

        <div class="sidebar-divider"></div>
        <div class="sidebar-label"><?= getCurrentLanguage() === 'en' ? 'Quick Contacts' : 'Kontak Cepat' ?></div>
        <?php foreach (array_slice($applicants, 0, 5) as $app): ?>
            <div class="sidebar-item" onclick="fillRecipient('<?= htmlspecialchars($app['email']) ?>', '<?= htmlspecialchars($app['full_name']) ?>')">
                <i class="fas fa-user"></i>
                <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($app['full_name']) ?></span>
            </div>
        <?php endforeach; ?>

        <!-- SMTP Status -->
        <?php if ($smtpConfigured): ?>
            <div class="smtp-info ok"><i class="fas fa-check-circle me-1"></i><?= getCurrentLanguage() === 'en' ? 'SMTP Active' : 'SMTP Aktif' ?></div>
        <?php else: ?>
            <div class="smtp-info no"><i class="fas fa-info-circle me-1"></i><?= getCurrentLanguage() === 'en' ? 'Simulation Mode' : 'Mode Simulasi' ?></div>
        <?php endif; ?>
    </div>

    <!-- ====== MAIN CONTENT ====== -->
    <div class="email-main">

        <!-- ====== COMPOSE PANEL ====== -->
        <div class="email-panel active" id="panel-compose">
            <div class="email-topbar">
                <h3><i class="fas fa-pen me-2" style="color:#6366f1;"></i><?= getCurrentLanguage() === 'en' ? 'Compose New Email' : 'Tulis Email Baru' ?></h3>
            </div>
            <div class="compose-panel">
                <div class="compose-form">
                    <!-- From -->
                    <div class="compose-row">
                        <label><?= getCurrentLanguage() === 'en' ? 'From:' : 'Dari:' ?></label>
                        <div class="from-display">
                            <span class="from-name"><?= htmlspecialchars($fromName) ?></span>
                            &lt;<span class="from-email"><?= htmlspecialchars($fromEmail) ?></span>&gt;
                        </div>
                    </div>

                    <!-- To -->
                    <div class="compose-row" style="position:relative;">
                        <label><?= getCurrentLanguage() === 'en' ? 'To:' : 'Kepada:' ?></label>
                        <input type="email" id="toEmailInput" placeholder="<?= getCurrentLanguage() === 'en' ? 'Type recipient email...' : 'Ketik email penerima...' ?>" autocomplete="off"
                               oninput="filterContacts()" onfocus="filterContacts()">
                        <input type="hidden" id="toUserId" value="">
                        <input type="hidden" id="toNameInput" value="">
                        <!-- Contact suggestions -->
                        <div class="contact-suggestions" id="contactSuggestions">
                            <?php foreach ($applicants as $app): ?>
                                <div class="contact-item" onclick="selectContact('<?= htmlspecialchars($app['email']) ?>', '<?= htmlspecialchars($app['full_name']) ?>', <?= $app['user_id'] ?>)">
                                    <div class="contact-avatar" style="background:hsl(<?= crc32($app['email']) % 360 ?>,60%,50%)">
                                        <?= strtoupper(substr($app['full_name'], 0, 1)) ?>
                                    </div>
                                    <div class="contact-info">
                                        <div class="contact-name"><?= htmlspecialchars($app['full_name']) ?></div>
                                        <div class="contact-email"><?= htmlspecialchars($app['email']) ?> — <?= htmlspecialchars($app['vacancy_title'] ?? '') ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Template insert (optional) -->
                    <div class="template-insert-row">
                        <label><i class="fas fa-magic me-1"></i>Template:</label>
                        <select id="templateSelect" onchange="onTemplateSelect()">
                            <option value="">— <?= getCurrentLanguage() === 'en' ? 'Write manually' : 'Tulis manual' ?> —</option>
                            <?php foreach ($templates as $tpl): ?>
                                <option value="<?= $tpl['id'] ?>" data-subject="<?= htmlspecialchars($tpl['subject']) ?>">
                                    <?= htmlspecialchars($tpl['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn-insert-tpl" onclick="previewSelectedTemplate()" title="Preview template">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Subject -->
                    <div class="compose-row">
                        <label>Subject:</label>
                        <input type="text" id="subjectInput" placeholder="<?= getCurrentLanguage() === 'en' ? 'Write email subject...' : 'Tuliskan subject email...' ?>">
                    </div>

                    <!-- Body -->
                    <div class="compose-body-area">
                        <textarea id="bodyInput" placeholder="<?= getCurrentLanguage() === 'en' ? 'Write your message here...&#10;&#10;Supports HTML for better formatting.' : 'Tulis pesan Anda di sini...&#10;&#10;Mendukung HTML untuk format email yang lebih bagus.' ?>"></textarea>
                    </div>

                    <!-- Attachment chips -->
                    <div class="attach-section" id="attachSection" style="display:none;">
                        <div class="attach-bar" id="attachBar"></div>
                    </div>

                    <!-- Toolbar -->
                    <div class="compose-toolbar">
                        <button class="btn-send-main" id="sendBtn" onclick="sendEmail()">
                            <i class="fas fa-paper-plane"></i> <?= getCurrentLanguage() === 'en' ? 'Send' : 'Kirim' ?>
                        </button>
                        <button class="toolbar-btn" onclick="document.getElementById('fileInput').click()" title="Lampirkan file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="file" id="fileInput" multiple accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx" style="display:none;" onchange="handleFiles(this.files)">
                        <button class="toolbar-btn" onclick="previewSelectedTemplate()" title="Preview template">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div class="compose-toolbar-spacer" style="flex:1;"></div>
                        <span style="font-size:0.75rem;color:#9ca3af;">
                            <i class="fas fa-paperclip me-1"></i><span id="attachCount">0</span> <?= getCurrentLanguage() === 'en' ? 'attachments' : 'lampiran' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== SENT PANEL ====== -->
        <div class="email-panel" id="panel-sent">
            <div class="email-topbar">
                <h3><i class="fas fa-paper-plane me-2" style="color:#6366f1;"></i><?= getCurrentLanguage() === 'en' ? 'Sent Emails' : 'Email Terkirim' ?></h3>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="<?= getCurrentLanguage() === 'en' ? 'Search emails...' : 'Cari email...' ?>" id="searchSent" oninput="filterSentList()">
                </div>
            </div>
            <div class="sent-list">
                <?php if (empty($logs)): ?>
                    <div class="empty-state">
                        <i class="fas fa-paper-plane"></i>
                        <h4><?= getCurrentLanguage() === 'en' ? 'No sent emails yet' : 'Belum ada email terkirim' ?></h4>
                        <p><?= getCurrentLanguage() === 'en' ? 'Emails you send will appear here' : 'Email yang Anda kirim akan muncul di sini' ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($logs as $log):
                        $avatarColor = 'hsl(' . (crc32($log['to_email']) % 360) . ',55%,50%)';
                        $initial = strtoupper(substr($log['to_name'] ?? $log['to_email'], 0, 1));
                        $timeAgo = date('d M H:i', strtotime($log['created_at']));
                    ?>
                        <div class="sent-item" data-search="<?= htmlspecialchars(strtolower(($log['to_name'] ?? '') . ' ' . $log['to_email'] . ' ' . $log['subject'])) ?>" data-email-id="<?= $log['id'] ?>">
                            <div class="sent-avatar" style="background:<?= $avatarColor ?>">
                                <?= $initial ?>
                            </div>
                            <div class="sent-content">
                                <div class="sent-to">
                                    <?= htmlspecialchars($log['to_name'] ?? $log['to_email']) ?>
                                    <small style="color:#9ca3af;font-weight:400;">&lt;<?= htmlspecialchars($log['to_email']) ?>&gt;</small>
                                </div>
                                <div class="sent-subject">
                                    <?= htmlspecialchars($log['subject']) ?>
                                    <?php if (!empty($log['template_name'])): ?>
                                        <span class="sent-template-badge"><?= htmlspecialchars($log['template_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="sent-meta">
                                <span class="sent-time"><?= $timeAgo ?></span>
                                <span class="sent-status <?= $log['status'] ?>">
                                    <i class="fas fa-<?= $log['status'] === 'sent' ? 'check' : ($log['status'] === 'failed' ? 'times' : 'clock') ?> me-1"></i>
                                    <?= ucfirst($log['status']) ?>
                                </span>
                            </div>
                            <div class="sent-actions">
                                <button class="btn-delete-email" onclick="deleteEmail(<?= $log['id'] ?>, event)" title="Hapus email">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ====== TEMPLATES PANEL ====== -->
        <div class="email-panel" id="panel-templates">
            <div class="email-topbar">
                <h3><i class="fas fa-file-alt me-2" style="color:#6366f1;"></i><?= getCurrentLanguage() === 'en' ? 'Email Templates' : 'Template Email' ?></h3>
            </div>
            <div class="templates-grid-panel">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
                    <?php foreach ($templates as $tpl): ?>
                        <div class="tpl-card" onclick="previewTemplateById(<?= $tpl['id'] ?>)">
                            <h4><i class="fas fa-envelope-open-text me-2" style="color:#6366f1;"></i><?= htmlspecialchars($tpl['name']) ?></h4>
                            <div class="tpl-subject"><i class="fas fa-tag me-1"></i><?= htmlspecialchars($tpl['subject']) ?></div>
                            <span class="tpl-slug"><?= htmlspecialchars($tpl['slug']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Preview Modal -->
<div class="preview-modal" id="previewModal">
    <div class="preview-box">
        <div class="preview-header">
            <h3><i class="fas fa-eye me-2"></i><?= getCurrentLanguage() === 'en' ? 'Preview Email Template' : 'Pratinjau Template Email' ?></h3>
            <button class="preview-close" onclick="closePreview()"><i class="fas fa-times"></i></button>
        </div>
        <div class="preview-body" id="previewBody">
            <p style="color:#9ca3af;text-align:center;">Loading...</p>
        </div>
    </div>
</div>

<script>
// ===== Toast Notifications =====
(function() {
    if (!document.getElementById('emailToastBox')) {
        var tc = document.createElement('div');
        tc.id = 'emailToastBox';
        tc.style.cssText = 'position:fixed;top:20px;right:20px;z-index:999999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
        document.body.appendChild(tc);
    }
})();
function emToast(msg, type) {
    type = type || 'success';
    var c = document.getElementById('emailToastBox');
    var t = document.createElement('div');
    var bg = { success: '#16a34a', error: '#dc2626', warning: '#d97706' };
    var ic = { success: 'check-circle', error: 'times-circle', warning: 'exclamation-triangle' };
    t.style.cssText = 'padding:16px 22px;border-radius:12px;color:white;font-weight:600;font-size:0.95rem;box-shadow:0 10px 30px rgba(0,0,0,0.25);display:flex;align-items:center;gap:10px;min-width:300px;pointer-events:auto;background:'+(bg[type]||'#3b82f6')+';border-left:5px solid rgba(255,255,255,0.4);animation:emToastIn 0.4s ease;';
    t.innerHTML = '<i class="fas fa-'+(ic[type]||'info-circle')+'" style="font-size:1.2rem;"></i><span>'+msg+'</span>';
    c.appendChild(t);
    setTimeout(function(){t.style.transition='all 0.4s';t.style.transform='translateX(120%)';t.style.opacity='0';setTimeout(function(){t.remove()},400)},4000);
}

// ===== Panel Navigation =====
function showPanel(name) {
    document.querySelectorAll('.email-panel').forEach(function(p){ p.classList.remove('active'); });
    document.getElementById('panel-' + name).classList.add('active');
    document.querySelectorAll('.sidebar-item').forEach(function(s){ s.classList.toggle('active', s.dataset.panel === name); });
}

// ===== Contact Suggestions =====
function filterContacts() {
    var input = document.getElementById('toEmailInput').value.toLowerCase();
    var box = document.getElementById('contactSuggestions');
    var items = box.querySelectorAll('.contact-item');
    var anyVisible = false;
    items.forEach(function(item) {
        var text = item.textContent.toLowerCase();
        var show = text.indexOf(input) >= 0;
        item.style.display = show ? 'flex' : 'none';
        if (show) anyVisible = true;
    });
    box.classList.toggle('show', anyVisible && input.length > 0);
}

function selectContact(email, name, userId) {
    document.getElementById('toEmailInput').value = email;
    document.getElementById('toNameInput').value = name;
    document.getElementById('toUserId').value = userId;
    document.getElementById('contactSuggestions').classList.remove('show');
}

function fillRecipient(email, name) {
    showPanel('compose');
    document.getElementById('toEmailInput').value = email;
    document.getElementById('toNameInput').value = name;
    document.getElementById('toEmailInput').focus();
}

// Hide suggestions on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#contactSuggestions') && !e.target.closest('#toEmailInput')) {
        document.getElementById('contactSuggestions').classList.remove('show');
    }
});

// ===== Template Select =====
function onTemplateSelect() {
    var sel = document.getElementById('templateSelect');
    var opt = sel.options[sel.selectedIndex];
    if (sel.value) {
        document.getElementById('subjectInput').value = opt.dataset.subject || '';
        // Load template body
        fetch('<?= url('/crewing/email/preview') ?>?template_id=' + sel.value)
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    var decoder = document.createElement('textarea');
                    decoder.innerHTML = res.template.body || '';
                    document.getElementById('bodyInput').value = decoder.value;
                }
            });
    }
}

// ===== Preview Template =====
function previewSelectedTemplate() {
    var sel = document.getElementById('templateSelect');
    if (!sel.value) { emToast('Pilih template dulu', 'warning'); return; }
    previewTemplateById(sel.value);
}

function previewTemplateById(tid) {
    var body = document.getElementById('previewBody');
    body.innerHTML = '<p style="color:#9ca3af;text-align:center;"><i class="fas fa-spinner fa-spin"></i> Loading...</p>';
    document.getElementById('previewModal').classList.add('show');
    document.body.style.overflow = 'hidden';

    fetch('<?= url('/crewing/email/preview') ?>?template_id=' + tid)
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (!res.success) { body.innerHTML = '<p style="color:#ef4444;">'+res.message+'</p>'; return; }
            var t = res.template;
            var decoder = document.createElement('textarea');
            decoder.innerHTML = t.body || '';
            var decodedBody = decoder.value;
            body.innerHTML = '<div style="margin-bottom:16px;"><strong style="color:#374151;">Subject:</strong><br><span style="color:#6366f1;font-weight:600;">'+(t.subject||'')+'</span></div>' +
                '<div style="margin-bottom:10px;"><strong style="color:#374151;">Body:</strong></div>' +
                '<iframe id="previewFrame" style="width:100%;min-height:250px;border:1px solid #e5e7eb;border-radius:12px;background:#fafbfc;" frameborder="0"></iframe>';
            var iframe = document.getElementById('previewFrame');
            var doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write('<!DOCTYPE html><html><head><style>body{font-family:Poppins,Arial,sans-serif;font-size:14px;color:#333;line-height:1.6;padding:16px;margin:0;}h1,h2,h3{color:#1e3a5f;}</style></head><body>' + decodedBody + '</body></html>');
            doc.close();
            setTimeout(function(){ try { iframe.style.height = (doc.body.scrollHeight + 20) + 'px'; } catch(e){} }, 200);
        })
        .catch(function(e) { body.innerHTML = '<p style="color:#ef4444;">Error: '+e.message+'</p>'; });
}

function closePreview() {
    document.getElementById('previewModal').classList.remove('show');
    document.body.style.overflow = '';
}
document.getElementById('previewModal').addEventListener('click', function(e) { if (e.target === this) closePreview(); });

// ===== Search Sent List =====
function filterSentList() {
    var q = document.getElementById('searchSent').value.toLowerCase();
    document.querySelectorAll('.sent-item').forEach(function(item) {
        item.style.display = (item.dataset.search || '').indexOf(q) >= 0 ? 'flex' : 'none';
    });
}

// ===== File Attachment Handling =====
var selectedFiles = [];

function handleFiles(files) {
    for (var i = 0; i < files.length; i++) {
        if (files[i].size > 10 * 1024 * 1024) {
            emToast('File "' + files[i].name + '" terlalu besar (max 10MB)', 'error');
            continue;
        }
        selectedFiles.push(files[i]);
    }
    renderAttachments();
    document.getElementById('fileInput').value = '';
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    renderAttachments();
}

function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function fileIcon(name) {
    var ext = name.split('.').pop().toLowerCase();
    if (ext === 'pdf') return 'fa-file-pdf';
    if (['jpg','jpeg','png','gif','webp'].indexOf(ext) >= 0) return 'fa-file-image';
    if (['doc','docx'].indexOf(ext) >= 0) return 'fa-file-word';
    if (['xls','xlsx'].indexOf(ext) >= 0) return 'fa-file-excel';
    return 'fa-file';
}

function renderAttachments() {
    var section = document.getElementById('attachSection');
    var bar = document.getElementById('attachBar');
    var count = document.getElementById('attachCount');
    count.textContent = selectedFiles.length;
    if (selectedFiles.length === 0) { section.style.display = 'none'; bar.innerHTML = ''; return; }
    section.style.display = 'block';
    var html = '';
    for (var i = 0; i < selectedFiles.length; i++) {
        var f = selectedFiles[i];
        html += '<div class="attach-chip"><i class="fas ' + fileIcon(f.name) + '"></i> ' + f.name +
                ' <span class="chip-size">(' + formatSize(f.size) + ')</span>' +
                '<button class="chip-remove" onclick="removeFile(' + i + ')"><i class="fas fa-times"></i></button></div>';
    }
    bar.innerHTML = html;
}

// ===== Delete Email =====
function deleteEmail(emailId, event) {
    event.stopPropagation(); // Prevent click bubbling
    
    // Animate out immediately
    var item = document.querySelector('.sent-item[data-email-id="' + emailId + '"]');
    if (item) {
        item.style.transition = 'all 0.3s ease';
        item.style.opacity = '0';
        item.style.transform = 'translateX(-30px)';
    }
    
    var formData = new FormData();
    formData.append('csrf_token', '<?= csrf_token() ?>');
    formData.append('email_id', emailId);
    
    fetch('<?= url('/crewing/email/delete') ?>', { method: 'POST', body: formData })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            // Remove from DOM after animation
            setTimeout(function() { 
                if (item) item.remove(); 
                emToast(data.message || 'Email berhasil dihapus', 'success');
            }, 300);
        } else {
            // Restore if failed
            if (item) {
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }
            emToast(data.message || 'Gagal menghapus email', 'error');
        }
    })
    .catch(function(e) { 
        // Restore if error
        if (item) {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }
        emToast('Error: ' + e.message, 'error'); 
    });
}

// ===== Send Email =====
function sendEmail() {
    var toEmail = document.getElementById('toEmailInput').value.trim();
    var toName = document.getElementById('toNameInput').value.trim();
    var userId = document.getElementById('toUserId').value || '';
    var templateId = document.getElementById('templateSelect').value || '0';
    var subject = document.getElementById('subjectInput').value.trim();
    var body = document.getElementById('bodyInput').value.trim();
    var btn = document.getElementById('sendBtn');

    if (!toEmail) { emToast('Masukkan email penerima', 'warning'); return; }
    if (!subject) { emToast('Subject email harus diisi', 'warning'); return; }
    if (!body) { emToast('Isi email harus diisi', 'warning'); return; }

    var formData = new FormData();
    formData.append('csrf_token', '<?= csrf_token() ?>');
    formData.append('to_email', toEmail);
    formData.append('to_name', toName || '');
    formData.append('user_id', userId);
    formData.append('template_id', '0'); // Always custom since we fill subject+body
    formData.append('custom_subject', subject);
    formData.append('custom_body', body);

    for (var i = 0; i < selectedFiles.length; i++) {
        formData.append('attachments[]', selectedFiles[i]);
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

    fetch('<?= url('/crewing/email/send') ?>', { method: 'POST', body: formData })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            emToast(data.message || 'Email berhasil dikirim!', 'success');
            // Clear form
            document.getElementById('bodyInput').value = '';
            document.getElementById('subjectInput').value = '';
            document.getElementById('templateSelect').value = '';
            selectedFiles = [];
            renderAttachments();
        } else {
            emToast(data.message || 'Gagal mengirim email', 'error');
        }
    })
    .catch(function(e) { emToast('Error: ' + e.message, 'error'); })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim';
    });
}
</script>
