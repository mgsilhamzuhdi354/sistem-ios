<?php
/**
 * Admin Checklist - Detail View (Stage 2 of Recruitment Flow)
 * Shows checklist items with toggle/pass/reject + interactive book-style modals
 * Sub-items inside modals are clickable → shows real data from DB (Level 2 alerts)
 */
$currentPage = 'admin-checklist';
$crew = $crew ?? [];
$checklist = $checklist ?? [];
$documents = $documents ?? [];
$progress = $progress ?? 0;
$progress_total = $progress_total ?? 6;

$checklistItems = [
    'document_check' => [
        'label' => 'Document Check', 
        'icon' => 'folder_open', 
        'desc' => 'Periksa kelengkapan dokumen: Passport, Seaman Book, COC, STCW, dll.', 
        'color' => 'blue',
        'detail_icon' => 'fact_check',
        'detail_title' => 'Verifikasi Dokumen',
        'detail_info' => [
            ['icon' => 'description', 'label' => 'Passport', 'key' => 'passport'],
            ['icon' => 'menu_book', 'label' => 'Seaman Book / BST', 'key' => 'seaman_book'],
            ['icon' => 'verified', 'label' => 'COC (Certificate of Competency)', 'key' => 'coc'],
            ['icon' => 'school', 'label' => 'STCW / BST Certificates', 'key' => 'stcw_bst'],
            ['icon' => 'badge', 'label' => 'KTP / Identitas', 'key' => 'ktp'],
            ['icon' => 'health_and_safety', 'label' => 'Medical Certificate', 'key' => 'medical_cert'],
        ]
    ],
    'owner_interview' => [
        'label' => 'Owner Interview', 
        'icon' => 'record_voice_over', 
        'desc' => 'Interview oleh Owner/Manager. Pass atau Reject.', 
        'color' => 'purple',
        'detail_icon' => 'groups',
        'detail_title' => 'Interview dengan Owner',
        'detail_info' => [
            ['icon' => 'person', 'label' => 'Data Profil Crew', 'key' => 'profil_crew'],
            ['icon' => 'work', 'label' => 'Pengalaman Kerja', 'key' => 'pengalaman_kerja'],
            ['icon' => 'psychology', 'label' => 'Skills & Sertifikat', 'key' => 'skills'],
            ['icon' => 'notes', 'label' => 'Catatan Interview', 'key' => 'catatan_interview'],
        ]
    ],
    'pengantar_mcu' => [
        'label' => 'Pengantar MCU', 
        'icon' => 'medical_services', 
        'desc' => 'Buat surat pengantar Medical Check-Up untuk kandidat.', 
        'color' => 'orange',
        'detail_icon' => 'local_hospital',
        'detail_title' => 'Medical Check-Up',
        'detail_info' => [
            ['icon' => 'monitor_heart', 'label' => 'Medical Certificate', 'key' => 'medical_cert'],
            ['icon' => 'vaccines', 'label' => 'Yellow Fever Vaccination', 'key' => 'yellow_fever'],
            ['icon' => 'coronavirus', 'label' => 'COVID-19 Vaccination', 'key' => 'covid_vax'],
            ['icon' => 'notes', 'label' => 'Catatan MCU', 'key' => 'catatan_mcu'],
        ]
    ],
    'agreement_kontrak' => [
        'label' => 'Agreement Kontrak', 
        'icon' => 'handshake', 
        'desc' => 'Kandidat review & setujui kontrak kerja.', 
        'color' => 'teal',
        'detail_icon' => 'gavel',
        'detail_title' => 'Persetujuan Kontrak',
        'detail_info' => [
            ['icon' => 'today', 'label' => 'Durasi Kontrak', 'key' => 'durasi_kontrak'],
            ['icon' => 'payments', 'label' => 'Gaji & Tunjangan', 'key' => 'gaji_tunjangan'],
            ['icon' => 'flight', 'label' => 'Penempatan', 'key' => 'penempatan'],
            ['icon' => 'article', 'label' => 'Terms & Conditions', 'key' => 'terms_conditions'],
            ['icon' => 'draw', 'label' => 'Tanda Tangan & Approval', 'key' => 'tanda_tangan'],
        ]
    ],
    'admin_charge' => [
        'label' => 'Admin Charge', 
        'icon' => 'payments', 
        'desc' => 'Proses administrasi biaya/charge kandidat.', 
        'color' => 'indigo',
        'detail_icon' => 'receipt_long',
        'detail_title' => 'Administrasi Biaya',
        'detail_info' => [
            ['icon' => 'account_balance', 'label' => 'Biaya Admin', 'key' => 'biaya_admin'],
            ['icon' => 'credit_card', 'label' => 'Pembayaran', 'key' => 'pembayaran'],
            ['icon' => 'receipt', 'label' => 'Kwitansi / Invoice', 'key' => 'kwitansi_invoice'],
            ['icon' => 'savings', 'label' => 'Potongan Gaji', 'key' => 'potongan'],
        ]
    ],
    'ok_to_board' => [
        'label' => 'OK to Board', 
        'icon' => 'flight_takeoff', 
        'desc' => 'Final approval — kandidat siap diberangkatkan.', 
        'color' => 'green',
        'detail_icon' => 'rocket_launch',
        'detail_title' => 'Siap Diberangkatkan',
        'detail_info' => [
            ['icon' => 'check_circle', 'label' => 'Dokumen Lengkap', 'key' => 'dokumen_lengkap'],
            ['icon' => 'how_to_reg', 'label' => 'Interview Approved', 'key' => 'interview_approved'],
            ['icon' => 'health_and_safety', 'label' => 'Medical Fit', 'key' => 'medical_fit'],
            ['icon' => 'handshake', 'label' => 'Kontrak Signed', 'key' => 'kontrak_signed'],
            ['icon' => 'flight_takeoff', 'label' => 'Ready to Deploy', 'key' => 'ready_deploy'],
        ]
    ],
];

$colorMap = [
    'blue' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'icon' => 'text-blue-600', 'gradient' => 'from-blue-500 to-blue-700', 'ring' => 'ring-blue-200', 'light' => '#EFF6FF'],
    'purple' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'icon' => 'text-purple-600', 'gradient' => 'from-purple-500 to-purple-700', 'ring' => 'ring-purple-200', 'light' => '#FAF5FF'],
    'orange' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'icon' => 'text-orange-600', 'gradient' => 'from-orange-500 to-orange-700', 'ring' => 'ring-orange-200', 'light' => '#FFF7ED'],
    'teal' => ['bg' => 'bg-teal-50', 'border' => 'border-teal-200', 'icon' => 'text-teal-600', 'gradient' => 'from-teal-500 to-teal-700', 'ring' => 'ring-teal-200', 'light' => '#F0FDFA'],
    'indigo' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'icon' => 'text-indigo-600', 'gradient' => 'from-indigo-500 to-indigo-700', 'ring' => 'ring-indigo-200', 'light' => '#EEF2FF'],
    'green' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'icon' => 'text-green-600', 'gradient' => 'from-green-500 to-green-700', 'ring' => 'ring-green-200', 'light' => '#F0FDF4'],
];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Checklist' ?> - IndoOcean ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };</script>
    <style>
        .progress-bar { transition: width 0.5s ease; }
        .checklist-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
        .checklist-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.08); transform: translateY(-2px); }
        
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes bookOpen { 
            0% { opacity: 0; transform: perspective(800px) rotateY(-90deg) scale(0.8); }
            40% { transform: perspective(800px) rotateY(10deg) scale(1.02); }
            70% { transform: perspective(800px) rotateY(-5deg) scale(1.01); }
            100% { opacity: 1; transform: perspective(800px) rotateY(0deg) scale(1); }
        }
        @keyframes bookClose { 
            0% { opacity: 1; transform: perspective(800px) rotateY(0deg) scale(1); }
            100% { opacity: 0; transform: perspective(800px) rotateY(90deg) scale(0.8); }
        }
        @keyframes itemReveal {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-15px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes slideUp {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-15px) scale(0.95); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
        }
        @keyframes backdropIn {
            from { opacity: 0; } to { opacity: 1; }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes toastIn { from { transform: translateY(100px) scale(0.8); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
        @keyframes toastOut { from { transform: translateY(0); opacity: 1; } to { transform: translateY(100px); opacity: 0; } }

        .animate-fade-in { animation: fadeInUp 0.4s ease forwards; }
        .animate-delay-0 { animation-delay: 0.05s; }
        .animate-delay-1 { animation-delay: 0.1s; }
        .animate-delay-2 { animation-delay: 0.15s; }
        .animate-delay-3 { animation-delay: 0.2s; }
        .animate-delay-4 { animation-delay: 0.25s; }
        .animate-delay-5 { animation-delay: 0.3s; }
        
        .book-modal { animation: bookOpen 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; transform-origin: left center; }
        .book-modal-close { animation: bookClose 0.3s ease forwards; }
        .backdrop-animate { animation: backdropIn 0.3s ease forwards; }
        .item-reveal { animation: itemReveal 0.4s ease forwards; opacity: 0; }
        .pulse-glow { animation: pulseGlow 2s infinite; }
        
        .shimmer-bg {
            background: linear-gradient(90deg, transparent 25%, rgba(255,255,255,0.3) 50%, transparent 75%);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
        
        /* Clickable sub-items */
        .sub-item-clickable { 
            transition: all 0.2s ease; 
            cursor: pointer; 
            border-radius: 0.75rem;
        }
        .sub-item-clickable:hover { 
            background: rgba(59, 130, 246, 0.06); 
            transform: translateX(4px); 
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .sub-item-clickable:active { transform: translateX(2px) scale(0.99); }
        
        /* Level 2 data alert */
        .data-alert { animation: slideDown 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .data-alert-close { animation: slideUp 0.25s ease forwards; }
        .data-field { transition: all 0.15s ease; }
        .data-field:hover { background: rgba(0, 0, 0, 0.02); }
        .data-field-highlight { background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(59,130,246,0.03)) !important; border: 1px solid rgba(59,130,246,0.15); }
        
        .spinner { animation: spin 0.8s linear infinite; }
        .toast-in { animation: toastIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .toast-out { animation: toastOut 0.3s ease forwards; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">

    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <!-- Header -->
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <a href="<?= BASE_URL ?>AdminChecklist" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-icons text-lg">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-base font-bold text-slate-800"><?= htmlspecialchars($crew['full_name'] ?? '') ?></h1>
                    <p class="text-xs text-slate-400"><?= $crew['rank_name'] ?? '' ?> • <?= $crew['employee_id'] ?? '' ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold <?= $progress == $progress_total ? 'text-green-600' : 'text-slate-600' ?>"><?= $progress ?>/<?= $progress_total ?> Items</span>
                <div class="w-32 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full progress-bar <?= $progress == $progress_total ? 'bg-green-500' : 'bg-blue-500' ?>" style="width: <?= round(($progress / $progress_total) * 100) ?>%"></div>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <?php if (!empty($flash)): ?>
                <div class="mb-4 px-4 py-3 rounded-lg text-sm <?= ($flash['type'] ?? '') === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                    <?= $flash['message'] ?? '' ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- LEFT: Checklist Items -->
                <div class="lg:col-span-2 space-y-3">
                    <h2 class="text-sm font-bold text-slate-600 uppercase tracking-wide mb-2">
                        <span class="material-icons text-sm align-middle mr-1">checklist</span> Checklist Items
                        <span class="text-xs font-normal text-slate-400 ml-2">Klik item untuk melihat detail</span>
                    </h2>
                    
                    <?php $idx = 0; foreach ($checklistItems as $key => $item): 
                        $val = $checklist[$key] ?? 0;
                        $notes = $checklist[$key . '_notes'] ?? '';
                        $at = $checklist[$key . '_at'] ?? null;
                        $c = $colorMap[$item['color']];
                        $isPassed = ($val == 1);
                        $isRejected = ($val == 2);
                    ?>
                    <div class="checklist-card bg-white rounded-xl border <?= $isPassed ? 'border-green-200 bg-green-50/30' : ($isRejected ? 'border-red-200 bg-red-50/30' : 'border-slate-200') ?> p-4 opacity-0 animate-fade-in animate-delay-<?= $idx ?>" 
                         id="item-<?= $key ?>"
                         onclick="openBookModal('<?= $key ?>')"
                         role="button">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 <?= $isPassed ? 'bg-green-100' : ($isRejected ? 'bg-red-100' : $c['bg']) ?> transition-all">
                                <?php if ($isPassed): ?>
                                    <span class="material-icons text-green-600 text-xl">check_circle</span>
                                <?php elseif ($isRejected): ?>
                                    <span class="material-icons text-red-600 text-xl">cancel</span>
                                <?php else: ?>
                                    <span class="material-icons <?= $c['icon'] ?> text-xl"><?= $item['icon'] ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-sm <?= $isPassed ? 'text-green-700' : ($isRejected ? 'text-red-700' : 'text-slate-800') ?>"><?= $item['label'] ?></h3>
                                    <?php if ($isPassed): ?>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">PASSED ✓</span>
                                    <?php elseif ($isRejected): ?>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">REJECTED ✗</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-600">PENDING</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-slate-400 mb-1"><?= $item['desc'] ?></p>
                                <?php if ($at): ?><p class="text-xs text-slate-300"><?= date('d M Y H:i', strtotime($at)) ?></p><?php endif; ?>
                                <?php if ($notes): ?><p class="text-xs text-slate-500 mt-1 italic">"<?= htmlspecialchars($notes) ?>"</p><?php endif; ?>
                            </div>
                            <?php if (($checklist['status'] ?? 'in_progress') === 'in_progress'): ?>
                            <div class="flex items-center gap-1.5 flex-shrink-0" onclick="event.stopPropagation();">
                                <button onclick="event.stopPropagation(); showNotes('<?= $key ?>')" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors" title="Add Notes">
                                    <span class="material-icons text-base">edit_note</span>
                                </button>
                                <?php if ($key === 'owner_interview'): ?>
                                    <?php if ($isPassed): ?>
                                        <button onclick="event.stopPropagation(); updateItem('<?= $key ?>', 0)" class="px-3 py-1.5 text-xs font-medium text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"><span class="material-icons text-xs align-middle mr-0.5">undo</span> Undo</button>
                                    <?php elseif ($isRejected): ?>
                                        <button onclick="event.stopPropagation(); updateItem('<?= $key ?>', 0)" class="px-3 py-1.5 text-xs font-medium text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"><span class="material-icons text-xs align-middle mr-0.5">undo</span> Undo</button>
                                    <?php else: ?>
                                        <button onclick="event.stopPropagation(); updateItem('<?= $key ?>', 1)" class="px-3 py-1.5 text-xs font-bold text-white bg-green-500 hover:bg-green-600 rounded-lg transition-colors shadow-sm">✅ Pass</button>
                                        <button onclick="event.stopPropagation(); updateItem('<?= $key ?>', 2)" class="px-3 py-1.5 text-xs font-bold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors shadow-sm">❌ Reject</button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($isPassed): ?>
                                        <button onclick="event.stopPropagation(); updateItem('<?= $key ?>', 0)" class="px-3 py-1.5 text-xs font-medium text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"><span class="material-icons text-xs align-middle mr-0.5">undo</span> Undo</button>
                                    <?php else: ?>
                                        <button onclick="event.stopPropagation(); updateItem('<?= $key ?>', 1)" class="px-3 py-1.5 text-xs font-bold text-white bg-blue-500 hover:bg-blue-600 rounded-lg transition-colors shadow-sm">✅ Pass</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-3 hidden" id="notes-form-<?= $key ?>" onclick="event.stopPropagation();">
                            <textarea class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all" rows="2" placeholder="Tambah catatan..." id="notes-<?= $key ?>"><?= htmlspecialchars($notes) ?></textarea>
                        </div>
                    </div>
                    <?php $idx++; endforeach; ?>

                    <!-- Action Buttons — Admin Checklist controls recruitment status -->
                    <?php if (($checklist['status'] ?? 'in_progress') === 'in_progress'): ?>
                    <div class="mt-6 pt-4 border-t border-slate-200">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">
                            <span class="material-icons text-xs align-middle mr-1">swap_horiz</span> Kontrol Status Recruitment
                        </h3>
                        <div class="flex items-center gap-3">
                            <?php if ($progress == $progress_total): ?>
                                <a href="<?= BASE_URL ?>AdminChecklist/complete/<?= $crew['id'] ?>" 
                                   class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold text-sm rounded-xl flex items-center gap-2 transition-all shadow-lg shadow-green-200 pulse-glow" 
                                   onclick="return confirm('Semua checklist selesai! Status recruitment akan diubah ke Approved. Lanjutkan?')">
                                    <span class="material-icons text-sm">check_circle</span> ✅ Approve Kandidat
                                </a>
                            <?php else: ?>
                                <button disabled class="px-6 py-2.5 bg-slate-200 text-slate-400 font-semibold text-sm rounded-xl flex items-center gap-2 cursor-not-allowed">
                                    <span class="material-icons text-sm">block</span> Selesaikan semua item (<?= $progress ?>/<?= $progress_total ?>)
                                </button>
                            <?php endif; ?>
                            <button onclick="showRejectModal()" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-medium text-sm rounded-xl flex items-center gap-2 transition-colors">
                                <span class="material-icons text-sm">cancel</span> Reject Kandidat
                            </button>
                        </div>
                    </div>
                    <?php elseif (($checklist['status'] ?? '') === 'rejected'): ?>
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center gap-2 mb-1"><span class="material-icons text-red-600">cancel</span><span class="font-semibold text-red-700">Kandidat Ditolak</span></div>
                        <?php if (!empty($checklist['rejected_reason'])): ?><p class="text-sm text-red-600 ml-8">Alasan: <?= htmlspecialchars($checklist['rejected_reason']) ?></p><?php endif; ?>
                    </div>
                    <?php elseif (($checklist['status'] ?? '') === 'completed'): ?>
                    <div class="mt-6 pt-4 border-t border-slate-200">
                        <div class="p-4 bg-green-50 border border-green-200 rounded-xl mb-3">
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-green-600">check_circle</span>
                                <span class="font-semibold text-green-700">✅ Checklist Selesai — Status: Approved</span>
                            </div>
                        </div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">
                            <span class="material-icons text-xs align-middle mr-1">swap_horiz</span> Langkah Selanjutnya
                        </h3>
                        <div class="flex items-center gap-3">
                            <a href="<?= BASE_URL ?>Operational/detail/<?= $crew['id'] ?>" 
                               class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-semibold text-sm rounded-xl flex items-center gap-2 transition-all shadow-lg shadow-blue-200 pulse-glow">
                                <span class="material-icons text-sm">flight_takeoff</span> Lanjut ke Operational
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT: Crew Data Sidebar -->
                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-slate-200 p-5">
                        <div class="text-center mb-4">
                            <?php if (!empty($crew['photo'])): ?>
                                <img src="<?= BASE_URL . $crew['photo'] ?>" class="w-20 h-20 mx-auto rounded-full object-cover border-4 border-blue-100 mb-3" alt="">
                            <?php else: ?>
                                <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl mb-3 shadow-lg shadow-blue-100">
                                    <?= strtoupper(substr($crew['full_name'] ?? 'U', 0, 2)) ?>
                                </div>
                            <?php endif; ?>
                            <h3 class="font-bold text-slate-800"><?= htmlspecialchars($crew['full_name'] ?? '') ?></h3>
                            <p class="text-sm text-blue-600 font-medium"><?= $crew['rank_name'] ?? 'N/A' ?></p>
                            <p class="text-xs text-slate-400"><?= $crew['employee_id'] ?? '' ?></p>
                        </div>
                        <div class="space-y-2 text-xs">
                            <?php if (!empty($crew['email'])): ?><div class="flex items-center gap-2"><span class="material-icons text-slate-300 text-sm">email</span><span class="text-slate-600 truncate"><?= htmlspecialchars($crew['email']) ?></span></div><?php endif; ?>
                            <?php if (!empty($crew['phone'])): ?><div class="flex items-center gap-2"><span class="material-icons text-slate-300 text-sm">phone</span><span class="text-slate-600"><?= htmlspecialchars($crew['phone']) ?></span></div><?php endif; ?>
                            <?php if (!empty($crew['gender'])): ?><div class="flex items-center gap-2"><span class="material-icons text-slate-300 text-sm">person</span><span class="text-slate-600"><?= ucfirst($crew['gender']) ?></span></div><?php endif; ?>
                            <?php if (!empty($crew['birth_date'])): ?><div class="flex items-center gap-2"><span class="material-icons text-slate-300 text-sm">cake</span><span class="text-slate-600"><?= date('d M Y', strtotime($crew['birth_date'])) ?></span></div><?php endif; ?>
                            <?php if (!empty($crew['nationality'])): ?><div class="flex items-center gap-2"><span class="material-icons text-slate-300 text-sm">flag</span><span class="text-slate-600"><?= htmlspecialchars($crew['nationality']) ?></span></div><?php endif; ?>
                            <?php if (!empty($crew['address'])): ?><div class="flex items-start gap-2"><span class="material-icons text-slate-300 text-sm mt-0.5">location_on</span><span class="text-slate-600"><?= htmlspecialchars($crew['address']) ?><?= !empty($crew['city']) ? ', '.htmlspecialchars($crew['city']) : '' ?></span></div><?php endif; ?>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <a href="<?= BASE_URL ?>crews/view/<?= $crew['id'] ?>" class="text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                                <span class="material-icons text-sm">open_in_new</span> Lihat Profil Lengkap
                            </a>
                        </div>
                    </div>

                    <?php 
                    $recruiterInfo = $recruiterInfo ?? null;
                    if ($recruiterInfo): ?>
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-200 p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-icons text-indigo-600 text-lg">person_pin</span>
                            <h3 class="font-semibold text-sm text-indigo-800">PIC / Perekrut</h3>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-indigo-400 text-sm">person</span>
                                <span class="text-indigo-700 font-semibold"><?= htmlspecialchars($recruiterInfo['recruiter_name']) ?></span>
                            </div>
                            <?php if (!empty($recruiterInfo['recruiter_email'])): ?>
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-indigo-400 text-sm">email</span>
                                <span class="text-indigo-600"><?= htmlspecialchars($recruiterInfo['recruiter_email']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($recruiterInfo['assigned_at'])): ?>
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-indigo-400 text-sm">schedule</span>
                                <span class="text-indigo-500">Assigned: <?= date('d M Y', strtotime($recruiterInfo['assigned_at'])) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="bg-white rounded-xl border border-slate-200 p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-icons text-blue-600 text-lg">folder</span>
                            <h3 class="font-semibold text-sm text-slate-800">Dokumen</h3>
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full ml-auto"><?= count($documents) ?></span>
                        </div>
                        <?php if (empty($documents)): ?><p class="text-xs text-slate-400">Belum ada dokumen yang di-upload.</p>
                        <?php else: ?>
                            <div class="space-y-2"><?php foreach ($documents as $doc): ?>
                                <div class="flex items-center gap-2 text-xs p-2 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                                    <span class="material-icons text-slate-400 text-sm">description</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-slate-700 truncate"><?= htmlspecialchars($doc['type_name'] ?? $doc['document_name'] ?? 'Document') ?></p>
                                        <?php if (!empty($doc['expiry_date'])): ?><p class="text-slate-400">Exp: <?= date('d M Y', strtotime($doc['expiry_date'])) ?></p><?php endif; ?>
                                    </div>
                                    <?php if (!empty($doc['file_path'])): ?><a href="<?= BASE_URL . $doc['file_path'] ?>" target="_blank" class="text-blue-600"><span class="material-icons text-sm">download</span></a><?php endif; ?>
                                </div>
                            <?php endforeach; ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($crew['emergency_name'])): ?>
                    <div class="bg-white rounded-xl border border-slate-200 p-5">
                        <div class="flex items-center gap-2 mb-3"><span class="material-icons text-red-500 text-lg">emergency</span><h3 class="font-semibold text-sm text-slate-800">Kontak Darurat</h3></div>
                        <div class="space-y-1 text-xs">
                            <p class="text-slate-700 font-medium"><?= htmlspecialchars($crew['emergency_name']) ?></p>
                            <?php if (!empty($crew['emergency_phone'])): ?><p class="text-slate-500"><?= htmlspecialchars($crew['emergency_phone']) ?></p><?php endif; ?>
                            <?php if (!empty($crew['emergency_relation'])): ?><p class="text-slate-400"><?= htmlspecialchars($crew['emergency_relation']) ?></p><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ═══════════════════════════════════════════════ -->
<!-- LEVEL 1: Book Modal (Checklist Item Detail)    -->
<!-- ═══════════════════════════════════════════════ -->
<div id="bookModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" onclick="closeBookModal(event)">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm backdrop-animate"></div>
    <div id="bookContent" class="relative w-full max-w-lg mx-4 book-modal" onclick="event.stopPropagation();">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div id="bookHeader" class="relative p-6 text-white overflow-hidden">
                <div class="absolute inset-0 shimmer-bg"></div>
                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <span id="bookIcon" class="material-icons text-3xl"></span>
                    </div>
                    <div>
                        <h2 id="bookTitle" class="text-xl font-bold"></h2>
                        <p id="bookSubtitle" class="text-sm opacity-80 mt-0.5"></p>
                    </div>
                </div>
                <div class="relative z-10 mt-4">
                    <div id="bookStatus" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm"></div>
                </div>
            </div>
            <div class="p-6">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                    <span class="material-icons text-xs align-middle mr-1">list_alt</span> Detail Verifikasi
                    <span class="text-[10px] font-normal text-slate-300 ml-1">— klik item untuk lihat data</span>
                </h4>
                <div id="bookItems" class="space-y-1"></div>
                <div id="bookNotes" class="hidden mt-4 p-3 bg-amber-50 rounded-xl border border-amber-200">
                    <div class="flex items-center gap-2 mb-1"><span class="material-icons text-amber-500 text-sm">sticky_note_2</span><span class="text-xs font-bold text-amber-700">Catatan</span></div>
                    <p id="bookNotesText" class="text-xs text-amber-600 italic"></p>
                </div>
                <div id="bookTimestamp" class="hidden mt-3 text-xs text-slate-400 flex items-center gap-1.5">
                    <span class="material-icons text-xs">schedule</span><span id="bookTimestampText"></span>
                </div>
            </div>
            <div class="px-6 pb-6 flex items-center gap-2">
                <button onclick="closeBookModal()" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-xl transition-colors flex items-center justify-center gap-2">
                    <span class="material-icons text-sm">close</span> Tutup
                </button>
                <div id="bookActions" class="flex-1 flex gap-2"></div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════ -->
<!-- LEVEL 2: Data Alert (Sub-Item Real Data)       -->
<!-- ═══════════════════════════════════════════════ -->
<div id="dataAlert" class="fixed inset-0 z-[60] flex items-center justify-center hidden" onclick="closeDataAlert(event)">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-[2px]" style="animation: backdropIn 0.2s ease forwards;"></div>
    <div id="dataAlertContent" class="relative w-full max-w-md mx-4 data-alert" onclick="event.stopPropagation();">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
            <!-- Data Alert Header -->
            <div class="bg-gradient-to-r from-slate-700 to-slate-900 px-5 py-4 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                        <span id="dataAlertIcon" class="material-icons text-xl"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 id="dataAlertTitle" class="font-bold text-sm truncate"></h3>
                        <p class="text-xs text-slate-300">Data dari sistem</p>
                    </div>
                    <button onclick="closeDataAlert()" class="p-1 rounded-lg hover:bg-white/10 transition-colors">
                        <span class="material-icons text-lg">close</span>
                    </button>
                </div>
            </div>
            <!-- Data Fields -->
            <div id="dataAlertBody" class="p-4 max-h-[55vh] overflow-y-auto">
                <!-- Loading state -->
                <div id="dataAlertLoader" class="flex flex-col items-center justify-center py-8">
                    <span class="material-icons text-blue-500 text-3xl spinner">refresh</span>
                    <p class="text-xs text-slate-400 mt-2">Mengambil data...</p>
                </div>
                <!-- Data fields container -->
                <div id="dataAlertFields" class="space-y-1 hidden"></div>
                <!-- Empty state -->
                <div id="dataAlertEmpty" class="hidden text-center py-6">
                    <span class="material-icons text-slate-200 text-4xl">inbox</span>
                    <p id="dataAlertEmptyMsg" class="text-sm text-slate-400 mt-2"></p>
                    <a id="dataAlertLink" href="#" class="hidden mt-3 inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold rounded-xl transition-colors shadow-lg">
                        <span class="material-icons text-sm">arrow_forward</span>
                        <span id="dataAlertLinkText"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-2">Reject Kandidat</h3>
        <p class="text-sm text-slate-500 mb-4">Data tetap tersimpan sebagai arsip.</p>
        <form action="<?= BASE_URL ?>AdminChecklist/reject/<?= $crew['id'] ?>" method="POST">
            <textarea name="reason" class="w-full px-4 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-red-400" rows="3" placeholder="Alasan penolakan..." required></textarea>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg">Reject</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div id="toastContainer" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[70] hidden">
    <div id="toastEl" class="px-6 py-3 rounded-2xl shadow-2xl text-sm font-semibold flex items-center gap-3 toast-in">
        <span id="toastIcon" class="material-icons text-lg"></span><span id="toastMsg"></span>
    </div>
</div>

<script>
// Data from PHP
const itemData = <?= json_encode(array_map(function($key, $item) use ($checklist, $colorMap) {
    return [
        'key' => $key,
        'label' => $item['label'],
        'icon' => $item['detail_icon'],
        'title' => $item['detail_title'],
        'desc' => $item['desc'],
        'color' => $item['color'],
        'gradient' => $colorMap[$item['color']]['gradient'],
        'info' => $item['detail_info'],
        'value' => $checklist[$key] ?? 0,
        'notes' => $checklist[$key . '_notes'] ?? '',
        'at' => $checklist[$key . '_at'] ?? null,
    ];
}, array_keys($checklistItems), array_values($checklistItems))) ?>;

const checklistId = <?= $checklist['id'] ?? 0 ?>;
const crewId = <?= $crew['id'] ?? 0 ?>;
const baseUrl = '<?= BASE_URL ?>';
const checklistStatus = '<?= $checklist['status'] ?? 'in_progress' ?>';

// ═══════════════ LEVEL 1: BOOK MODAL ═══════════════
function openBookModal(key) {
    const item = itemData.find(i => i.key === key);
    if (!item) return;
    
    const modal = document.getElementById('bookModal');
    const content = document.getElementById('bookContent');
    const header = document.getElementById('bookHeader');
    
    header.className = `relative p-6 text-white overflow-hidden bg-gradient-to-r ${item.gradient}`;
    document.getElementById('bookIcon').textContent = item.icon;
    document.getElementById('bookTitle').textContent = item.title;
    document.getElementById('bookSubtitle').textContent = item.desc;
    
    const statusEl = document.getElementById('bookStatus');
    if (item.value == 1) {
        statusEl.innerHTML = '<span class="material-icons text-xs">check_circle</span> PASSED';
        statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-500/30 text-white';
    } else if (item.value == 2) {
        statusEl.innerHTML = '<span class="material-icons text-xs">cancel</span> REJECTED';
        statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-500/30 text-white';
    } else {
        statusEl.innerHTML = '<span class="material-icons text-xs">pending</span> PENDING';
        statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white';
    }
    
    // Build clickable sub-items
    const itemsEl = document.getElementById('bookItems');
    itemsEl.innerHTML = '';
    item.info.forEach((info, i) => {
        const div = document.createElement('div');
        div.className = 'sub-item-clickable flex items-center gap-3 p-3 item-reveal';
        div.style.animationDelay = `${0.1 + (i * 0.07)}s`;
        div.onclick = () => openDataAlert(item.key, info.key, info.label, info.icon);
        div.innerHTML = `
            <div class="w-9 h-9 rounded-lg ${item.value == 1 ? 'bg-green-100' : 'bg-slate-100'} flex items-center justify-center flex-shrink-0">
                <span class="material-icons text-sm ${item.value == 1 ? 'text-green-600' : 'text-slate-500'}">${item.value == 1 ? 'check' : info.icon}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-700">${info.label}</p>
            </div>
            <span class="material-icons text-slate-300 text-sm flex-shrink-0">chevron_right</span>
        `;
        itemsEl.appendChild(div);
    });
    
    // Notes
    const notesEl = document.getElementById('bookNotes');
    if (item.notes) {
        notesEl.classList.remove('hidden');
        document.getElementById('bookNotesText').textContent = `"${item.notes}"`;
    } else {
        notesEl.classList.add('hidden');
    }
    
    // Timestamp
    const tsEl = document.getElementById('bookTimestamp');
    if (item.at) {
        tsEl.classList.remove('hidden');
        document.getElementById('bookTimestampText').textContent = 'Diupdate: ' + item.at;
    } else {
        tsEl.classList.add('hidden');
    }
    
    // Actions
    const actionsEl = document.getElementById('bookActions');
    actionsEl.innerHTML = '';
    if (checklistStatus === 'in_progress') {
        if (item.value == 0) {
            actionsEl.innerHTML = `<button onclick="updateItemFromModal('${key}', 1)" class="flex-1 px-4 py-2.5 bg-gradient-to-r ${item.gradient} hover:opacity-90 text-white text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg"><span class="material-icons text-sm">check</span> Pass</button>`;
            if (key === 'owner_interview') {
                actionsEl.innerHTML += `<button onclick="updateItemFromModal('${key}', 2)" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl transition-colors flex items-center justify-center gap-2"><span class="material-icons text-sm">close</span> Reject</button>`;
            }
        } else {
            actionsEl.innerHTML = `<button onclick="updateItemFromModal('${key}', 0)" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-xl transition-colors flex items-center justify-center gap-2"><span class="material-icons text-sm">undo</span> Undo</button>`;
        }
    }
    
    content.classList.remove('book-modal-close');
    content.classList.add('book-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBookModal(e) {
    if (e && e.target !== document.getElementById('bookModal') && !e.target.closest('.backdrop-animate')) return;
    const content = document.getElementById('bookContent');
    content.classList.remove('book-modal');
    content.classList.add('book-modal-close');
    setTimeout(() => {
        document.getElementById('bookModal').classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function updateItemFromModal(item, value) {
    closeBookModal();
    setTimeout(() => updateItem(item, value), 350);
}

// ═══════════════ LEVEL 2: DATA ALERT ═══════════════
function openDataAlert(category, subItem, title, icon) {
    const alert = document.getElementById('dataAlert');
    const content = document.getElementById('dataAlertContent');
    
    document.getElementById('dataAlertTitle').textContent = title;
    document.getElementById('dataAlertIcon').textContent = icon;
    
    // Show loading
    document.getElementById('dataAlertLoader').classList.remove('hidden');
    document.getElementById('dataAlertFields').classList.add('hidden');
    document.getElementById('dataAlertEmpty').classList.add('hidden');
    
    // Show alert
    content.classList.remove('data-alert-close');
    content.classList.add('data-alert');
    alert.classList.remove('hidden');
    
    // Fetch real data via AJAX
    fetch(`${baseUrl}AdminChecklist/getSubItemData/${crewId}/${category}/${subItem}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('dataAlertLoader').classList.add('hidden');
        
        if (!data.success) {
            showDataAlertEmpty(data.message || 'Gagal mengambil data', null);
            return;
        }
        
        // Update title if different
        if (data.title) document.getElementById('dataAlertTitle').textContent = data.title;
        
        if (data.empty_message) {
            showDataAlertEmpty(data.empty_message, data.link);
        } else {
            showDataAlertFields(data.fields || []);
        }
    })
    .catch(err => {
        document.getElementById('dataAlertLoader').classList.add('hidden');
        showDataAlertEmpty('Gagal mengambil data. Coba lagi.', null);
    });
}

function showDataAlertFields(fields) {
    const container = document.getElementById('dataAlertFields');
    container.innerHTML = '';
    
    fields.forEach((f, i) => {
        const div = document.createElement('div');
        const isHighlight = f.highlight || false;
        div.className = `data-field flex items-start gap-3 px-3 py-2.5 rounded-xl ${isHighlight ? 'data-field-highlight' : ''}`;
        div.style.opacity = '0';
        div.style.animation = `fadeInUp 0.3s ease ${0.05 * i}s forwards`;
        
        let valueHtml = `<span class="${isHighlight ? 'font-bold text-blue-700' : 'text-slate-600'}">${escHtml(f.value)}</span>`;
        if (f.link) {
            valueHtml = `<a href="${f.link}" target="_blank" class="text-blue-600 hover:text-blue-700 font-medium underline">${escHtml(f.value)}</a>`;
        }
        
        div.innerHTML = `
            <div class="w-8 h-8 rounded-lg ${isHighlight ? 'bg-blue-100' : 'bg-slate-50'} flex items-center justify-center flex-shrink-0 mt-0.5">
                <span class="material-icons text-sm ${isHighlight ? 'text-blue-600' : 'text-slate-400'}">${f.icon}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">${escHtml(f.label)}</p>
                <p class="text-sm mt-0.5 break-words">${valueHtml}</p>
            </div>
        `;
        container.appendChild(div);
    });
    
    container.classList.remove('hidden');
    document.getElementById('dataAlertEmpty').classList.add('hidden');
}

function showDataAlertEmpty(message, link) {
    const emptyEl = document.getElementById('dataAlertEmpty');
    document.getElementById('dataAlertEmptyMsg').textContent = message;
    
    const linkEl = document.getElementById('dataAlertLink');
    if (link) {
        linkEl.href = link.url;
        document.getElementById('dataAlertLinkText').textContent = link.label;
        linkEl.classList.remove('hidden');
    } else {
        linkEl.classList.add('hidden');
    }
    
    emptyEl.classList.remove('hidden');
    document.getElementById('dataAlertFields').classList.add('hidden');
}

function closeDataAlert(e) {
    if (e && e.target !== document.getElementById('dataAlert') && !e.target.closest('[style*="backdropIn"]')) return;
    
    const content = document.getElementById('dataAlertContent');
    content.classList.remove('data-alert');
    content.classList.add('data-alert-close');
    setTimeout(() => {
        document.getElementById('dataAlert').classList.add('hidden');
    }, 250);
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

// ═══════════════ COMMON UTILITIES ═══════════════
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const el = document.getElementById('toastEl');
    document.getElementById('toastMsg').textContent = message;
    const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-blue-600' };
    const icons = { success: 'check_circle', error: 'error', info: 'info' };
    el.className = `px-6 py-3 rounded-2xl shadow-2xl text-sm font-semibold flex items-center gap-3 toast-in ${colors[type] || colors.info} text-white`;
    document.getElementById('toastIcon').textContent = icons[type] || icons.info;
    container.classList.remove('hidden');
    setTimeout(() => { el.classList.remove('toast-in'); el.classList.add('toast-out'); setTimeout(() => container.classList.add('hidden'), 300); }, 2500);
}

function updateItem(item, value) {
    const notes = document.getElementById('notes-' + item)?.value || '';
    if (item === 'owner_interview' && value === 2) {
        if (!confirm('Yakin reject Owner Interview? Kandidat otomatis masuk arsip rejected.')) return;
    }
    const card = document.getElementById('item-' + item);
    if (card) { card.style.opacity = '0.6'; card.style.pointerEvents = 'none'; }
    
    fetch(baseUrl + 'AdminChecklist/updateItem', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: `checklist_id=${checklistId}&item=${item}&value=${value}&notes=${encodeURIComponent(notes)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.rejected) { showToast(data.message, 'error'); setTimeout(() => location.href = baseUrl + 'AdminChecklist', 1500); }
            else { showToast(data.message, 'success'); setTimeout(() => location.reload(), 800); }
        } else { showToast('Error: ' + data.message, 'error'); if (card) { card.style.opacity = '1'; card.style.pointerEvents = ''; } }
    })
    .catch(() => { showToast('Network error', 'error'); if (card) { card.style.opacity = '1'; card.style.pointerEvents = ''; } });
}

function showNotes(item) { document.getElementById('notes-form-' + item)?.classList.toggle('hidden'); }
function showRejectModal() { document.getElementById('rejectModal').classList.remove('hidden'); }

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (!document.getElementById('dataAlert').classList.contains('hidden')) { closeDataAlert(); }
        else if (!document.getElementById('bookModal').classList.contains('hidden')) { closeBookModal(); }
        document.getElementById('rejectModal').classList.add('hidden');
    }
});
</script>
</body>
</html>
