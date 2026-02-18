<!-- Lowongan Kerja - Crewing -->
<div class="welcome-banner" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #3b82f6 100%);">
    <div class="welcome-content" style="display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div class="welcome-icon"><i class="fas fa-briefcase"></i></div>
            <div class="welcome-text">
                <h1><?= getCurrentLanguage() === 'en' ? 'Job Vacancies' : 'Lowongan Kerja' ?></h1>
                <p><?= getCurrentLanguage() === 'en' ? 'Share vacancies to potential candidates' : 'Bagikan lowongan ke kandidat potensial' ?></p>
            </div>
        </div>
        <a href="<?= url('/crewing/vacancies/create') ?>" class="btn-add-vacancy">
            <i class="fas fa-plus"></i> <?= getCurrentLanguage() === 'en' ? 'Add Vacancy' : 'Tambah Lowongan' ?>
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-card">
    <form method="GET" action="<?= url('/crewing/vacancies') ?>" class="filter-form">
        <div class="filter-row">
            <div class="filter-item">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="<?= getCurrentLanguage() === 'en' ? 'Search position...' : 'Cari posisi...' ?>" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="filter-item">
                <i class="fas fa-building"></i>
                <select name="department">
                    <option value=""><?= getCurrentLanguage() === 'en' ? 'All Departments' : 'Semua Departemen' ?></option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= ($_GET['department'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-item">
                <i class="fas fa-ship"></i>
                <select name="vessel_type">
                    <option value=""><?= getCurrentLanguage() === 'en' ? 'All Vessel Types' : 'Semua Jenis Kapal' ?></option>
                    <?php foreach ($vesselTypes as $vt): ?>
                        <option value="<?= $vt['id'] ?>" <?= ($_GET['vessel_type'] ?? '') == $vt['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($vt['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
</div>

<!-- Vacancies Grid -->
<?php if (empty($vacancies)): ?>
    <div class="empty-card">
        <div class="empty-icon"><i class="fas fa-briefcase"></i></div>
        <h3><?= getCurrentLanguage() === 'en' ? 'No Vacancies' : 'Tidak Ada Lowongan' ?></h3>
        <p><?= getCurrentLanguage() === 'en' ? 'No vacancies have been published yet.' : 'Saat ini belum ada lowongan yang dipublikasikan.' ?></p>
    </div>
<?php else: ?>
    <div class="vacancies-grid">
        <?php foreach ($vacancies as $vacancy): ?>
        <div class="vacancy-card">
            <!-- Ship Photo -->
            <?php if (!empty($vacancy['ship_photo'])): ?>
            <div class="vacancy-photo">
                <img src="<?= url('/' . $vacancy['ship_photo']) ?>" alt="<?= htmlspecialchars($vacancy['title']) ?>">
                <div class="vacancy-overlay"></div>
            </div>
            <?php endif; ?>

            <!-- Card Content -->
            <div class="vacancy-content">
                <h3 class="vacancy-title"><?= htmlspecialchars($vacancy['title']) ?></h3>
                
                <div class="vacancy-meta">
                    <?php if ($vacancy['department_name']): ?>
                    <span class="meta-item"><i class="fas fa-building"></i> <?= htmlspecialchars($vacancy['department_name']) ?></span>
                    <?php endif; ?>
                    <?php if ($vacancy['vessel_type_name']): ?>
                    <span class="meta-item"><i class="fas fa-ship"></i> <?= htmlspecialchars($vacancy['vessel_type_name']) ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($vacancy['salary_min'] && $vacancy['salary_max']): ?>
                <div class="vacancy-salary">
                    <i class="fas fa-dollar-sign"></i>
                    $<?= number_format($vacancy['salary_min']) ?> - $<?= number_format($vacancy['salary_max']) ?>
                </div>
                <?php endif; ?>

                <div class="vacancy-stats">
                    <span class="stat-badge"><i class="fas fa-users"></i> <?= $vacancy['applications_count'] ?> <?= getCurrentLanguage() === 'en' ? 'Applicants' : 'Pelamar' ?></span>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="vacancy-footer">
                <a href="<?= url('/crewing/vacancies/detail/' . $vacancy['id']) ?>" class="btn-detail">
                    <i class="fas fa-eye"></i> <?= getCurrentLanguage() === 'en' ? 'View Detail' : 'Lihat Detail' ?>
                </a>
                <button onclick="quickShare(<?= $vacancy['id'] ?>, '<?= htmlspecialchars(addslashes($vacancy['title']), ENT_QUOTES) ?>')" class="btn-share">
                    <i class="fas fa-share-alt"></i> Share
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Quick Share Modal -->
<div id="shareModal" class="modal-overlay" style="display:none">
    <div class="modal-box">
        <div class="modal-header">
            <i class="fas fa-share-alt"></i> <?= getCurrentLanguage() === 'en' ? 'Share Vacancy' : 'Share Lowongan' ?>
            <button onclick="closeShareModal()" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <h4 id="shareTitle" class="share-title"></h4>
            <div class="share-buttons">
                <button onclick="shareWhatsApp()" class="share-btn whatsapp">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </button>
                <button onclick="copyLink()" class="share-btn copy">
                    <i class="fas fa-link"></i> Copy Link
                </button>
            </div>
            <div class="share-link-box">
                <input type="text" id="shareLinkInput" readonly onclick="this.select()">
            </div>
            <div id="copySuccess" class="copy-success" style="display:none">
                <i class="fas fa-check-circle"></i> <?= getCurrentLanguage() === 'en' ? 'Link copied successfully!' : 'Link berhasil disalin!' ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Add Vacancy Button */
.btn-add-vacancy {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: white;
    color: #6366f1;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(255,255,255,0.2);
    transition: all 0.3s;
}
.btn-add-vacancy:hover {
    background: rgba(255,255,255,0.95);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255,255,255,0.3);
    color: #6366f1;
}

/* Filter Card */
.filter-card { background: white; border-radius: 16px; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.filter-form { display: flex; }
.filter-row { display: flex; gap: 0.75rem; align-items: center; flex: 1; }
.filter-item { flex: 1; position: relative; }
.filter-item i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.85rem; }
.filter-item input, .filter-item select { width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.9rem; font-family: inherit; transition: all 0.3s; }
.filter-item input:focus, .filter-item select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); outline: none; }
.btn-filter { padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.3s; white-space: nowrap; }
.btn-filter:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59,130,246,0.3); }

/* Vacancies Grid */
.vacancies-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.25rem; }

/* Vacancy Card */
.vacancy-card { background: white; border-radius: 16px; box-shadow: 0 3px 15px rgba(0,0,0,0.06); overflow: hidden; transition: all 0.3s; border: 1px solid #f3f4f6; }
.vacancy-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.12); transform: translateY(-4px); border-color: #dbeafe; }

.vacancy-photo { position: relative; height: 180px; overflow: hidden; }
.vacancy-photo img { width: 100%; height: 100%; object-fit: cover; }
.vacancy-overlay { position: absolute; bottom: 0; left: 0; right: 0; height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.4), transparent); }

.vacancy-content { padding: 1.25rem; }
.vacancy-title { margin: 0 0 0.75rem; font-size: 1.1rem; color: #1f2937; font-weight: 700; line-height: 1.3; }
.vacancy-meta { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem; }
.meta-item { display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #6b7280; padding: 0.3rem 0.6rem; background: #f3f4f6; border-radius: 8px; }
.meta-item i { font-size: 0.75rem; color: #9ca3af; }

.vacancy-salary { display: flex; align-items: center; gap: 0.4rem; font-size: 0.95rem; color: #059669; font-weight: 600; margin-bottom: 0.75rem; padding: 0.5rem 0.75rem; background: #d1fae5; border-radius: 10px; width: fit-content; }
.vacancy-salary i { font-size: 0.85rem; }

.vacancy-stats { display: flex; gap: 0.5rem; }
.stat-badge { display: flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.75rem; background: #ede9fe; color: #7c3aed; border-radius: 10px; font-size: 0.8rem; font-weight: 600; }
.stat-badge i { font-size: 0.75rem; }

.vacancy-footer { display: flex; gap: 0.75rem; padding: 1rem 1.25rem; background: #f8fafc; border-top: 1px solid #f3f4f6; }
.btn-detail, .btn-share { flex: 1; padding: 0.65rem 1rem; border-radius: 10px; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 0.4rem; transition: all 0.3s; cursor: pointer; border: none; font-family: inherit; text-decoration: none; }
.btn-detail { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }
.btn-detail:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(59,130,246,0.3); color: white; }
.btn-share { background: white; color: #6b7280; border: 2px solid #e5e7eb; }
.btn-share:hover { background: #10b981; color: white; border-color: #10b981; transform: translateY(-2px); }

/* Share Modal */
.share-title { margin: 0 0 1.25rem; font-size: 0.95rem; color: #374151; }
.share-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem; }
.share-btn { padding: 0.75rem; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; font-family: inherit; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.3s; }
.share-btn.whatsapp { background: linear-gradient(135deg, #25D366, #128C7E); color: white; }
.share-btn.whatsapp:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(37,211,102,0.3); }
.share-btn.copy { background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; }
.share-btn.copy:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99,102,241,0.3); }

.share-link-box { margin-top: 1rem; }
.share-link-box input { width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.85rem; font-family: 'Courier New', monospace; background: #f9fafb; }

.copy-success { margin-top: 0.75rem; padding: 0.75rem; background: #d1fae5; color: #059669; border-radius: 10px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }

@media (max-width: 768px) {
    .vacancies-grid { grid-template-columns: 1fr; }
    .filter-row { flex-direction: column; }
    .filter-item { width: 100%; }
}
</style>

<script>
let currentVacancyId = null;
let currentShareUrl = null;
let currentWhatsAppUrl = null;

function quickShare(vacancyId, title) {
    currentVacancyId = vacancyId;
    
    // Generate share link via AJAX
    fetch('<?= url('/crewing/vacancies/share/') ?>' + vacancyId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ method: 'link' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentShareUrl = data.shareUrl;
            currentWhatsAppUrl = data.whatsappUrl;
            document.getElementById('shareTitle').textContent = title;
            document.getElementById('shareLinkInput').value = currentShareUrl;
            document.getElementById('shareModal').style.display = 'flex';
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error(err));
}

function closeShareModal() {
    document.getElementById('shareModal').style.display = 'none';
    document.getElementById('copySuccess').style.display = 'none';
}

function shareWhatsApp() {
    if (currentWhatsAppUrl) {
        window.open(currentWhatsAppUrl, '_blank');
    }
}

function copyLink() {
    const input = document.getElementById('shareLinkInput');
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    const success = document.getElementById('copySuccess');
    success.style.display = 'flex';
    setTimeout(() => success.style.display = 'none', 3000);
}

// Close modal on overlay click
document.getElementById('shareModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeShareModal();
});
</script>
