<!-- Vacancy Detail -->
<div class="breadcrumb-bar">
    <a href="<?= url('/crewing/vacancies') ?>"><i class="fas fa-arrow-left"></i> <?= getCurrentLanguage() === 'en' ? 'Back to Vacancies' : 'Kembali ke Lowongan' ?></a>
</div>

<div class="detail-container">
    <!-- Main Card -->
    <div class="detail-card">
        <?php if (!empty($vacancy['ship_photo'])): ?>
        <div class="detail-photo">
            <img src="<?= url('/' . $vacancy['ship_photo']) ?>" alt="<?= htmlspecialchars($vacancy['title']) ?>">
        </div>
        <?php endif; ?>

        <div class="detail-header">
            <h1><?= htmlspecialchars($vacancy['title']) ?></h1>
            <div class="detail-meta">
                <?php if ($vacancy['department_name']): ?>
                <span class="meta-badge blue"><i class="fas fa-building"></i> <?= htmlspecialchars($vacancy['department_name']) ?></span>
                <?php endif; ?>
                <?php if ($vacancy['vessel_type_name']): ?>
                <span class="meta-badge purple"><i class="fas fa-ship"></i> <?= htmlspecialchars($vacancy['vessel_type_name']) ?></span>
                <?php endif; ?>
                <span class="meta-badge green"><i class="fas fa-users"></i> <?= $vacancy['applications_count'] ?> <?= getCurrentLanguage() === 'en' ? 'Applicants' : 'Pelamar' ?></span>
            </div>
        </div>

        <?php if ($vacancy['salary_min'] && $vacancy['salary_max']): ?>
        <div class="salary-box">
            <div class="salary-label"><?= getCurrentLanguage() === 'en' ? 'Salary Range' : 'Rentang Gaji' ?></div>
            <div class="salary-amount">$<?= number_format($vacancy['salary_min']) ?> - $<?= number_format($vacancy['salary_max']) ?></div>
        </div>
        <?php endif; ?>

        <div class="detail-section">
            <h3><i class="fas fa-info-circle"></i> <?= getCurrentLanguage() === 'en' ? 'Description' : 'Deskripsi' ?></h3>
            <div class="detail-content">
                <?= nl2br(htmlspecialchars($vacancy['description'])) ?>
            </div>
        </div>

        <?php if (!empty($vacancy['requirements'])): ?>
        <div class="detail-section">
            <h3><i class="fas fa-check-square"></i> <?= getCurrentLanguage() === 'en' ? 'Requirements' : 'Persyaratan' ?></h3>
            <div class="detail-content">
                <?= nl2br(htmlspecialchars($vacancy['requirements'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($vacancy['responsibilities'])): ?>
        <div class="detail-section">
            <h3><i class="fas fa-tasks"></i> <?= getCurrentLanguage() === 'en' ? 'Responsibilities' : 'Tanggung Jawab' ?></h3>
            <div class="detail-content">
                <?= nl2br(htmlspecialchars($vacancy['responsibilities'])) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Share Sidebar -->
    <div class="share-sidebar">
        <a href="<?= url('/crewing/vacancies/edit/' . $vacancy['id']) ?>" class="btn-edit-vacancy">
            <i class="fas fa-edit"></i> <?= getCurrentLanguage() === 'en' ? 'Edit Vacancy' : 'Edit Lowongan' ?>
        </a>
        
        <div class="share-card">
            <h3><i class="fas fa-share-alt"></i> <?= getCurrentLanguage() === 'en' ? 'Share Vacancy' : 'Share Lowongan' ?></h3>
            <p class="share-desc"><?= getCurrentLanguage() === 'en' ? 'Share this vacancy to potential candidates' : 'Bagikan lowongan ini ke kandidat potensial' ?></p>

            <div class="share-stats">
                <div class="stat-item">
                    <div class="stat-number" id="shareCounter"><?= $stats['total_shares'] ?? 0 ?></div>
                    <div class="stat-label">Total Share</div>
                </div>
            </div>

            <div class="share-actions">
                <button onclick="shareViaWhatsApp()" class="share-action-btn whatsapp">
                    <i class="fab fa-whatsapp"></i>
                    <span>Share via WhatsApp</span>
                </button>
                
                <button onclick="copyShareLink()" class="share-action-btn copy">
                    <i class="fas fa-link"></i>
                    <span>Copy Link</span>
                </button>
            </div>

            <div class="link-preview">
                <label>Share Link:</label>
                <input type="text" id="shareLinkInput" value="<?= htmlspecialchars($shareUrl) ?>" readonly onclick="this.select()">
            </div>

            <div id="copySuccess" class="copy-success-msg" style="display:none">
                <i class="fas fa-check-circle"></i> <?= getCurrentLanguage() === 'en' ? 'Link copied successfully!' : 'Link berhasil disalin!' ?>
            </div>
        </div>

        <div class="info-card">
            <div class="info-item">
                <i class="fas fa-calendar"></i>
                <div>
                    <div class="info-label"><?= getCurrentLanguage() === 'en' ? 'Posted' : 'Diposting' ?></div>
                    <div class="info-value"><?= date('d M Y', strtotime($vacancy['created_at'])) ?></div>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-eye"></i>
                <div>
                    <div class="info-label">Link Public</div>
                    <div class="info-value">
                        <a href="javascript:void(0)" onclick="openPublicPreview()" class="public-link">
                            <?= getCurrentLanguage() === 'en' ? 'View Public Page' : 'Lihat Halaman Public' ?> <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Public Preview Modal -->
<div id="publicPreviewModal" class="preview-modal-overlay" style="display:none">
    <div class="preview-modal">
        <div class="preview-modal-header">
            <h3><i class="fas fa-eye"></i> <?= getCurrentLanguage() === 'en' ? 'Public Page Preview' : 'Preview Halaman Public' ?></h3>
            <button onclick="closePublicPreview()" class="preview-close-btn">&times;</button>
        </div>
        <div class="preview-modal-body">
            <div class="preview-banner">
                <i class="fas fa-info-circle"></i>
                <?= getCurrentLanguage() === 'en' ? 'This is how candidates see your vacancy' : 'Ini tampilan lowongan untuk kandidat' ?>
            </div>
            
            <?php if (!empty($vacancy['ship_photo'])): ?>
            <div class="preview-photo">
                <img src="<?= url('/' . $vacancy['ship_photo']) ?>" alt="<?= htmlspecialchars($vacancy['title']) ?>">
            </div>
            <?php endif; ?>
            
            <div class="preview-job-header">
                <h2><?= htmlspecialchars($vacancy['title']) ?></h2>
                <div class="preview-meta">
                    <span class="preview-badge blue"><i class="fas fa-building"></i> <?= htmlspecialchars($vacancy['department_name'] ?? '-') ?></span>
                    <span class="preview-badge purple"><i class="fas fa-ship"></i> <?= htmlspecialchars($vacancy['vessel_type_name'] ?? '-') ?></span>
                    <span class="preview-badge green"><i class="fas fa-users"></i> <?= $vacancy['applications_count'] ?? 0 ?> <?= getCurrentLanguage() === 'en' ? 'Applicants' : 'Pelamar' ?></span>
                </div>
            </div>
            
            <?php if ($vacancy['salary_min'] && $vacancy['salary_max']): ?>
            <div class="preview-salary">
                <div class="preview-salary-label"><?= getCurrentLanguage() === 'en' ? 'Salary Range' : 'Rentang Gaji' ?></div>
                <div class="preview-salary-amount">
                    <?= $vacancy['salary_currency'] ?? 'USD' ?> $<?= number_format($vacancy['salary_min']) ?> - $<?= number_format($vacancy['salary_max']) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($vacancy['description'])): ?>
            <div class="preview-section">
                <h4><i class="fas fa-file-alt"></i> <?= getCurrentLanguage() === 'en' ? 'Description' : 'Deskripsi' ?></h4>
                <div class="preview-text"><?= nl2br(htmlspecialchars($vacancy['description'])) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($vacancy['requirements'])): ?>
            <div class="preview-section">
                <h4><i class="fas fa-list-check"></i> <?= getCurrentLanguage() === 'en' ? 'Requirements' : 'Persyaratan' ?></h4>
                <div class="preview-text"><?= nl2br(htmlspecialchars($vacancy['requirements'])) ?></div>
            </div>
            <?php endif; ?>
            
            <div class="preview-cta">
                <div class="preview-cta-icon"><i class="fas fa-user-plus"></i></div>
                <div class="preview-cta-text">
                    <strong><?= getCurrentLanguage() === 'en' ? 'Candidates will see Login & Register buttons here' : 'Kandidat akan melihat tombol Login & Register di sini' ?></strong>
                    <p><?= getCurrentLanguage() === 'en' ? 'They can create an account and apply for this position' : 'Mereka bisa membuat akun dan melamar posisi ini' ?></p>
                </div>
            </div>
        </div>
        <div class="preview-modal-footer">
            <a href="<?= url('/jobs/' . $vacancy['id']) ?>" target="_blank" class="btn-open-public">
                <i class="fas fa-external-link-alt"></i> <?= getCurrentLanguage() === 'en' ? 'Open in New Tab' : 'Buka di Tab Baru' ?>
            </a>
            <button onclick="closePublicPreview()" class="btn-close-preview">
                <?= getCurrentLanguage() === 'en' ? 'Close' : 'Tutup' ?>
            </button>
        </div>
    </div>
</div>

<style>
.breadcrumb-bar { margin-bottom: 1.5rem; }
.breadcrumb-bar a { display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; text-decoration: none; font-weight: 600; padding: 0.5rem 1rem; background: white; border-radius: 10px; transition: all 0.3s; }
.breadcrumb-bar a:hover { background: #f3f4f6; color: #374151; }

.detail-container { display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; }

/* Main Card */
.detail-card { background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 3px 15px rgba(0,0,0,0.06); }
.detail-photo { width: 100%; height: 300px; border-radius: 12px; overflow: hidden; margin-bottom: 1.5rem; }
.detail-photo img { width: 100%; height: 100%; object-fit: cover; }

.detail-header h1 { margin: 0 0 1rem; font-size: 1.75rem; color: #1f2937; font-weight: 700; }
.detail-meta { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; }
.meta-badge { display: inline-flex; align-items:center; gap: 0.4rem; padding: 0.5rem 0.75rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600; }
.meta-badge.blue { background: #dbeafe; color: #1d4ed8; }
.meta-badge.purple { background: #ede9fe; color: #7c3aed; }
.meta-badge.green { background: #d1fae5; color: #059669; }

.salary-box { background: linear-gradient(135deg, #10b981, #059669); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; color: white; }
.salary-label { font-size: 0.85rem; opacity: 0.9; margin-bottom: 0.25rem; }
.salary-amount { font-size: 1.75rem; font-weight: 700; }

.detail-section { margin-bottom: 2rem; }
.detail-section h3 { display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem; color: #1f2937; margin-bottom: 0.75rem; }
.detail-content { color: #4b5563; line-height: 1.7; font-size: 0.95rem; }

/* Edit Button */
.btn-edit-vacancy {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(245,158,11,0.3);
    transition: all 0.3s;
    margin-bottom: 1.25rem;
}
.btn-edit-vacancy:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245,158,11,0.4);
    color: white;
}

/* Share Sidebar */
.share-sidebar { display: flex; flex-direction: column; gap: 1.25rem; }
.share-card, .info-card { background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 3px 15px rgba(0,0,0,0.06); }

.share-card h3 { display: flex; align-items: center; gap: 0.5rem; margin: 0 0 0.5rem; font-size: 1.1rem; color: #1f2937; }
.share-desc { color: #6b7280; font-size: 0.85rem; margin-bottom: 1.25rem; }

.share-stats { background: #f9fafb; border-radius: 12px; padding: 1rem; margin-bottom: 1.25rem; }
.stat-item { text-align: center; }
.stat-number { font-size: 2rem; font-weight: 700; color: #059669; }
.stat-label { font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem; }

.share-actions { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem; }
.share-action-btn { width: 100%; padding: 0.85rem 1.25rem; border: none; border-radius: 10px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.3s; font-family: inherit; }
.share-action-btn.whatsapp { background: linear-gradient(135deg, #25D366, #128C7E); color: white; }
.share-action-btn.whatsapp:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(37,211,102,0.3); }
.share-action-btn.copy { background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; }
.share-action-btn.copy:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99,102,241,0.3); }

.link-preview { margin-top: 1rem; }
.link-preview label { display: block; font-size: 0.8rem; color: #6b7280; margin-bottom: 0.5rem; font-weight: 600; }
.link-preview input { width: 100%; padding: 0.65rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.8rem; font-family: 'Courier New', monospace; background: #f9fafb; }

.copy-success-msg { margin-top: 0.75rem; padding: 0.75rem; background: #d1fae5; color: #059669; border-radius: 8px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }

.info-card { display: flex; flex-direction: column; gap: 1rem; }
.info-item { display: flex; align-items: flex-start; gap: 0.75rem; }
.info-item i { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 10px; color: #6b7280; font-size: 0.9rem; flex-shrink: 0; }
.info-label { font-size: 0.8rem; color: #9ca3af; margin-bottom: 0.25rem; }
.info-value { font-size: 0.9rem; color: #374151; font-weight: 600; }
.public-link { color: #3b82f6; text-decoration: none; display: flex; align-items: center; gap: 0.3rem; cursor: pointer; }
.public-link:hover { color: #1d4ed8; }

/* Public Preview Modal */
.preview-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.2s ease; }
.preview-modal { background: white; border-radius: 20px; width: 95%; max-width: 680px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: slideUp 0.3s ease; }
.preview-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; }
.preview-modal-header h3 { margin: 0; font-size: 1.1rem; color: #1f2937; display: flex; align-items: center; gap: 0.5rem; }
.preview-close-btn { background: none; border: none; font-size: 1.5rem; color: #9ca3af; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 8px; transition: all 0.2s; }
.preview-close-btn:hover { background: #f3f4f6; color: #374151; }
.preview-modal-body { padding: 1.5rem; overflow-y: auto; flex: 1; }
.preview-banner { background: #dbeafe; color: #1d4ed8; padding: 0.75rem 1rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem; }
.preview-photo { width: 100%; height: 200px; border-radius: 12px; overflow: hidden; margin-bottom: 1.25rem; }
.preview-photo img { width: 100%; height: 100%; object-fit: cover; }
.preview-job-header h2 { margin: 0 0 0.75rem; font-size: 1.5rem; color: #1f2937; }
.preview-meta { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem; }
.preview-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.75rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; }
.preview-badge.blue { background: #dbeafe; color: #1d4ed8; }
.preview-badge.purple { background: #ede9fe; color: #7c3aed; }
.preview-badge.green { background: #d1fae5; color: #059669; }
.preview-salary { background: linear-gradient(135deg, #10b981, #059669); padding: 1rem 1.25rem; border-radius: 12px; color: white; margin-bottom: 1.25rem; }
.preview-salary-label { font-size: 0.8rem; opacity: 0.9; margin-bottom: 0.25rem; }
.preview-salary-amount { font-size: 1.35rem; font-weight: 700; }
.preview-section { margin-bottom: 1.25rem; }
.preview-section h4 { display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem; color: #1f2937; margin: 0 0 0.5rem; }
.preview-text { color: #4b5563; font-size: 0.9rem; line-height: 1.6; }
.preview-cta { background: #f0fdf4; border: 2px dashed #86efac; border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; margin-top: 1rem; }
.preview-cta-icon { width: 48px; height: 48px; background: #22c55e; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; flex-shrink: 0; }
.preview-cta-text strong { display: block; color: #166534; font-size: 0.9rem; margin-bottom: 0.25rem; }
.preview-cta-text p { margin: 0; color: #4ade80; font-size: 0.8rem; }
.preview-modal-footer { display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; }
.btn-open-public { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: all 0.3s; }
.btn-open-public:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,0.3); color: white; }
.btn-close-preview { padding: 0.65rem 1.25rem; background: #f3f4f6; color: #374151; border: none; border-radius: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; font-family: inherit; }
.btn-close-preview:hover { background: #e5e7eb; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

/* Share success toast */
.share-toast { position: fixed; bottom: 2rem; right: 2rem; background: #059669; color: white; padding: 1rem 1.5rem; border-radius: 12px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 8px 25px rgba(5,150,105,0.3); z-index: 10000; animation: slideUp 0.3s ease; }

@media (max-width: 1024px) {
    .detail-container { grid-template-columns: 1fr; }
    .preview-modal { width: 98%; max-height: 95vh; }
}
</style>

<script>
const shareUrl = <?= json_encode($shareUrl) ?>;
const vacancyId = <?= json_encode($vacancy['id']) ?>;
const vacancyTitle = <?= json_encode($vacancy['title']) ?>;
const csrfToken = <?= json_encode(csrf_token()) ?>;
const fullShareUrl = window.location.origin + shareUrl;

function shareViaWhatsApp() {
    const message = `🚢 *Lowongan Kerja PT Indo Ocean*\n\n*${vacancyTitle}*\n\nTertarik? Lihat detail dan apply di:\n${fullShareUrl}`;
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
    
    trackShare('whatsapp');
    window.open(whatsappUrl, '_blank');
}

function copyShareLink() {
    // Use modern clipboard API with fallback
    if (navigator.clipboard) {
        navigator.clipboard.writeText(fullShareUrl).then(() => {
            showCopySuccess();
        });
    } else {
        const input = document.getElementById('shareLinkInput');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        showCopySuccess();
    }
    trackShare('link');
}

function showCopySuccess() {
    const success = document.getElementById('copySuccess');
    success.style.display = 'flex';
    setTimeout(() => success.style.display = 'none', 3000);
}

function trackShare(method) {
    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('method', method);
    
    fetch('<?= url('/crewing/vacancies/share/') ?>' + vacancyId, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update the share counter
            const counter = document.getElementById('shareCounter');
            if (counter) {
                counter.textContent = parseInt(counter.textContent) + 1;
            }
            // Show success toast
            showToast(method === 'whatsapp' ? '✅ Share WhatsApp berhasil dicatat!' : '✅ Link berhasil disalin!');
        }
    })
    .catch(err => console.error('Share tracking error:', err));
}

function showToast(message) {
    const existing = document.querySelector('.share-toast');
    if (existing) existing.remove();
    
    const toast = document.createElement('div');
    toast.className = 'share-toast';
    toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// Public Preview Modal
function openPublicPreview() {
    document.getElementById('publicPreviewModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePublicPreview() {
    document.getElementById('publicPreviewModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Close modal on overlay click
document.getElementById('publicPreviewModal')?.addEventListener('click', function(e) {
    if (e.target === this) closePublicPreview();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePublicPreview();
});

// Update share link input to show full URL
document.addEventListener('DOMContentLoaded', function() {
    const linkInput = document.getElementById('shareLinkInput');
    if (linkInput) linkInput.value = fullShareUrl;
});
</script>
