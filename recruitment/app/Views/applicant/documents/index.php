<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Documents - PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/applicant.css') ?>">
    <style>
        .language-selector {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .language-selector select {
            padding: 6px 10px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background: white;
            font-size: 13px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="applicant-body">
    <!-- Sidebar -->
    <aside class="applicant-sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Indo Ocean</span></a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?= url('/applicant/dashboard') ?>" class="nav-link"><i class="fas fa-home"></i><span data-translate="nav.dashboard">Dashboard</span></a></li>
                <li><a href="<?= url('/jobs') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span data-translate="nav.jobs">Job Vacancies</span></a></li>
                <li><a href="<?= url('/applicant/applications') ?>" class="nav-link"><i class="fas fa-file-alt"></i><span data-translate="nav.applications">My Applications</span></a></li>
                <li><a href="<?= url('/applicant/documents') ?>" class="nav-link active"><i class="fas fa-folder"></i><span data-translate="nav.documents">Documents</span></a></li>
                <li><a href="<?= url('/applicant/interview') ?>" class="nav-link"><i class="fas fa-video"></i><span data-translate="nav.interview">Interview</span></a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span data-translate="nav.logout">Logout</span></a>
        </div>
    </aside>

    <main class="applicant-main">
        <header class="page-header-bar">
            <h1><i class="fas fa-folder"></i> <span data-translate="documents.title">My Documents</span></h1>
            <div class="language-selector">
                <select id="langSelect">
                    <option value="en">🇺🇸 EN</option>
                    <option value="id">🇮🇩 ID</option>
                    <option value="zh">🇨🇳 中文</option>
                </select>
            </div>
        </header>

        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>

        <div class="documents-info">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong data-translate="documents.uploadRequiredTitle">Upload Required Documents</strong>
                    <p data-translate="documents.uploadRequiredDesc">Please upload all required documents to complete your profile. Accepted formats: PDF, JPG, PNG, DOC (max 5MB)</p>
                </div>
            </div>
        </div>

        <div class="documents-grid">
            <?php foreach ($types as $type): ?>
                <?php $uploaded = $documentsByType[$type['id']] ?? null; ?>
                <div class="document-card <?= $uploaded ? 'uploaded' : '' ?>">
                    <div class="doc-header">
                        <div class="doc-icon <?= $uploaded ? 'success' : 'pending' ?>">
                            <i class="fas fa-<?= $uploaded ? 'check' : 'file-upload' ?>"></i>
                        </div>
                        <div class="doc-info">
                            <h3><?= htmlspecialchars($type['name']) ?></h3>
                            <?php if ($type['is_required']): ?>
                                <span class="required-badge" data-translate="documents.required">Required</span>
                            <?php else: ?>
                                <span class="optional-badge" data-translate="documents.optional">Optional</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($uploaded): ?>
                        <div class="doc-details">
                            <p><i class="fas fa-file"></i> <?= htmlspecialchars($uploaded['original_name']) ?></p>
                            <p><i class="fas fa-calendar"></i> <span data-translate="documents.uploaded">Uploaded</span>: <?= date('d M Y', strtotime($uploaded['created_at'])) ?></p>
                            <?php if ($uploaded['expiry_date']): ?>
                                <p><i class="fas fa-clock"></i> <span data-translate="documents.expiryDate">Expires</span>: <?= date('d M Y', strtotime($uploaded['expiry_date'])) ?></p>
                            <?php endif; ?>
                            <div class="verification-status <?= $uploaded['verification_status'] ?>">
                                <?php if ($uploaded['verification_status'] === 'verified'): ?>
                                    <i class="fas fa-check-circle"></i> <span data-translate="documents.verified">Verified</span>
                                <?php elseif ($uploaded['verification_status'] === 'rejected'): ?>
                                    <i class="fas fa-times-circle"></i> <span data-translate="documents.rejected">Rejected</span>
                                <?php else: ?>
                                    <i class="fas fa-clock"></i> <span data-translate="documents.pending">Pending Review</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= url('/applicant/documents/upload') ?>" method="POST" enctype="multipart/form-data" class="upload-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="document_type_id" value="<?= $type['id'] ?>">
                        
                        <div class="form-group">
                            <input type="file" name="document_file" id="file_<?= $type['id'] ?>" class="file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            <label for="file_<?= $type['id'] ?>" class="file-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span data-translate="<?= $uploaded ? 'documents.replaceFile' : 'documents.selectFile' ?>"><?= $uploaded ? 'Replace File' : 'Choose File' ?></span>
                            </label>
                        </div>

                        <div class="extra-fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label data-translate="documents.documentNumber">Document Number</label>
                                    <input type="text" name="document_number" placeholder="e.g. A12345678">
                                </div>
                                <div class="form-group">
                                    <label data-translate="documents.expiryDate">Expiry Date</label>
                                    <input type="date" name="expiry_date">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-upload"></i> <span data-translate="<?= $uploaded ? 'documents.updateDocument' : 'documents.uploadBtn' ?>"><?= $uploaded ? 'Update Document' : 'Upload' ?></span>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <style>
    .page-header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-header-bar h1 { font-size: 24px; color: #1a1a2e; display: flex; align-items: center; gap: 12px; }
    .page-header-bar h1 i { color: #0A2463; }
    .alert-info { background: #d1ecf1; color: #0c5460; display: flex; gap: 15px; align-items: flex-start; }
    .alert-info p { margin: 5px 0 0; font-size: 13px; }
    .documents-info { margin-bottom: 30px; }
    .documents-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
    .document-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 2px solid transparent; transition: all 0.3s ease; }
    .document-card.uploaded { border-color: #28a745; }
    .doc-header { display: flex; gap: 15px; margin-bottom: 20px; }
    .doc-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .doc-icon.pending { background: #fff3cd; color: #856404; }
    .doc-icon.success { background: #d4edda; color: #155724; }
    .doc-info h3 { font-size: 16px; color: #1a1a2e; margin-bottom: 5px; }
    .required-badge { background: #f8d7da; color: #721c24; padding: 3px 10px; border-radius: 20px; font-size: 11px; }
    .optional-badge { background: #e2e3e5; color: #383d41; padding: 3px 10px; border-radius: 20px; font-size: 11px; }
    .doc-details { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
    .doc-details p { font-size: 13px; color: #666; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
    .doc-details p:last-of-type { margin-bottom: 0; }
    .verification-status { margin-top: 10px; padding: 8px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; }
    .verification-status.verified { background: #d4edda; color: #155724; }
    .verification-status.rejected { background: #f8d7da; color: #721c24; }
    .verification-status.pending { background: #fff3cd; color: #856404; }
    .file-input { display: none; }
    .file-label { display: flex; align-items: center; justify-content: center; gap: 10px; padding: 15px; border: 2px dashed #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; margin-bottom: 15px; }
    .file-label:hover { border-color: #0A2463; background: #f0f4ff; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
    .form-group label { display: block; font-size: 13px; margin-bottom: 5px; color: #333; }
    .form-group input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
    </style>

    <script src="<?= asset('js/translate-recruitment.js') ?>"></script>
    <script>
    document.querySelectorAll('.file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const label = this.nextElementSibling;
            const fileName = this.files[0]?.name || 'Choose File';
            label.querySelector('span').textContent = fileName;
            this.closest('.upload-form').querySelector('.extra-fields').style.display = 'block';
        });
    });
    </script>
</body>
</html>
