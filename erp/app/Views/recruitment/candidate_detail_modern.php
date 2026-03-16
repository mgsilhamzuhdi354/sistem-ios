<?php
/**
 * Modern Candidate Detail View
 * White theme with Tailwind CSS + modern sidebar
 */
$currentPage = 'recruitment-pipeline';
$candidate = $candidate ?? [];

// Status color mapping
$statusColorMap = [
    'Applied' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
    'New' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
    'Screening' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
    'Document Review' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-700'],
    'Interview' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
    'Technical Test' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
    'Final Review' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
    'Approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
    'Hired' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
    'Accepted' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
    'Rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
    'Withdrawn' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700'],
];
$statusName = $candidate['status_name'] ?? 'Unknown';
$statusClasses = $statusColorMap[$statusName] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-600'];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Candidate Detail' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        };
    </script>
    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-d1 { animation-delay: 0.05s; }
        .animate-d2 { animation-delay: 0.1s; }
        .animate-d3 { animation-delay: 0.15s; }
        .animate-d4 { animation-delay: 0.2s; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <!-- Header -->
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <a href="<?= BASE_URL ?>recruitment/pipeline" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-icons text-xl">arrow_back</span>
                </a>
                <div class="h-5 w-px bg-slate-200"></div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight">Detail Kandidat</h1>
            </div>
            <div class="flex items-center gap-2">
                <?php if (empty($candidate['sent_to_erp_at'])): ?>
                    <a href="<?= BASE_URL ?>recruitment/import/<?= $candidate['id'] ?? '' ?>"
                       onclick="return confirm('Import kandidat ini ke ERP?')"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                        <span class="material-icons text-sm">file_download</span>
                        Import to ERP
                    </a>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-500 text-xs font-semibold rounded-lg">
                        <span class="material-icons text-sm">check_circle</span>
                        Synced to ERP
                    </span>
                <?php endif; ?>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
                <?php foreach ($flash as $type => $msg): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium 
                        <?= $type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' ?>
                        <?= $type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : '' ?>
                        <?= $type === 'warning' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' ?>">
                        <?= $msg ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Profile Card -->
                <div class="lg:col-span-1 space-y-5">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in">
                        <!-- Profile Header -->
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 px-6 pt-8 pb-12 text-center relative">
                            <div class="absolute inset-0 opacity-10">
                                <svg width="100%" height="100%" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="350" cy="30" r="80" fill="white" opacity="0.1"/>
                                    <circle cx="50" cy="160" r="60" fill="white" opacity="0.08"/>
                                </svg>
                            </div>
                            <?php if (!empty($candidate['avatar'])): ?>
                                <img src="<?= BASE_URL ?>../recruitment/uploads/avatars/<?= htmlspecialchars($candidate['avatar']) ?>"
                                     class="w-24 h-24 rounded-full object-cover border-4 border-white/30 shadow-lg mx-auto relative z-10">
                            <?php else: ?>
                                <div class="w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm border-4 border-white/30 flex items-center justify-center text-white text-3xl font-bold mx-auto relative z-10">
                                    <?= strtoupper(substr($candidate['full_name'] ?? 'N', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <h2 class="text-lg font-bold text-white mt-4 relative z-10"><?= htmlspecialchars($candidate['full_name'] ?? '') ?></h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?= $statusClasses['bg'] ?> <?= $statusClasses['text'] ?> mt-2 relative z-10">
                                <?= htmlspecialchars($statusName) ?>
                            </span>
                        </div>

                        <!-- Position Badge -->
                        <div class="-mt-4 px-6 relative z-10">
                            <div class="bg-white rounded-lg border border-slate-200 shadow-sm px-4 py-3 flex items-center gap-3">
                                <div class="p-2 bg-blue-50 rounded-lg">
                                    <span class="material-icons text-blue-600 text-lg">work</span>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-medium">Posisi Dilamar</p>
                                    <p class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($candidate['vacancy_title'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="px-6 py-5 space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="material-icons text-slate-400 text-lg mt-0.5">email</span>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-medium">Email</p>
                                    <p class="text-sm text-slate-700"><?= htmlspecialchars($candidate['email'] ?? '-') ?></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-icons text-slate-400 text-lg mt-0.5">phone</span>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-medium">Telepon</p>
                                    <p class="text-sm text-slate-700"><?= htmlspecialchars($candidate['phone'] ?? '-') ?></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-icons text-slate-400 text-lg mt-0.5">badge</span>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-medium">Passport</p>
                                    <p class="text-sm text-slate-700"><?= htmlspecialchars($candidate['passport_no'] ?? '-') ?></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-icons text-slate-400 text-lg mt-0.5">cake</span>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-medium">Tanggal Lahir</p>
                                    <p class="text-sm text-slate-700">
                                        <?= !empty($candidate['date_of_birth']) ? date('d M Y', strtotime($candidate['date_of_birth'])) : '-' ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-icons text-slate-400 text-lg mt-0.5"><?= ($candidate['gender'] ?? '') === 'male' ? 'male' : 'female' ?></span>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-medium">Gender</p>
                                    <p class="text-sm text-slate-700"><?= ucfirst(htmlspecialchars($candidate['gender'] ?? '-')) ?></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-icons text-slate-400 text-lg mt-0.5">flag</span>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-medium">Kewarganegaraan</p>
                                    <p class="text-sm text-slate-700"><?= htmlspecialchars($candidate['nationality'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address & Emergency -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d1">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="material-icons text-blue-500 text-lg">location_on</span>
                                Alamat
                            </h3>
                        </div>
                        <div class="px-5 py-4">
                            <p class="text-sm text-slate-600"><?= htmlspecialchars($candidate['address'] ?? 'Belum diisi') ?></p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <?php if (!empty($candidate['city'])): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 text-slate-600 text-[11px] rounded-full">
                                        <span class="material-icons text-[12px]">location_city</span>
                                        <?= htmlspecialchars($candidate['city']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($candidate['country'])): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 text-slate-600 text-[11px] rounded-full">
                                        <span class="material-icons text-[12px]">public</span>
                                        <?= htmlspecialchars($candidate['country']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="px-5 py-4 border-t border-slate-100">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-3">
                                <span class="material-icons text-red-400 text-lg">emergency</span>
                                Kontak Darurat
                            </h3>
                            <div class="flex items-center gap-3 bg-red-50/50 rounded-lg px-3 py-2.5">
                                <div class="p-1.5 bg-red-100 rounded-full">
                                    <span class="material-icons text-red-500 text-sm">person</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-700"><?= htmlspecialchars($candidate['emergency_name'] ?? 'Belum diisi') ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($candidate['emergency_phone'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scores -->
                    <?php if (!empty($candidate['interview_score']) || !empty($candidate['overall_score'])): ?>
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d2">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="material-icons text-amber-500 text-lg">star</span>
                                Skor Penilaian
                            </h3>
                        </div>
                        <div class="p-5 grid grid-cols-2 gap-4">
                            <?php if (!empty($candidate['interview_score'])): ?>
                            <div class="text-center p-3 bg-amber-50 rounded-lg">
                                <p class="text-2xl font-bold text-amber-600"><?= $candidate['interview_score'] ?></p>
                                <p class="text-[10px] text-slate-500 uppercase tracking-wider mt-1">Interview</p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($candidate['overall_score'])): ?>
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <p class="text-2xl font-bold text-blue-600"><?= $candidate['overall_score'] ?></p>
                                <p class="text-[10px] text-slate-500 uppercase tracking-wider mt-1">Overall</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Documents, Interviews, Medical -->
                <div class="lg:col-span-2 space-y-5">
                    <!-- Documents -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d2">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="material-icons text-blue-500 text-lg">folder_open</span>
                                Dokumen
                            </h3>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-500 rounded-full">
                                <?= count($candidate['documents'] ?? []) ?> file
                            </span>
                        </div>

                        <?php if (!empty($candidate['documents'])): ?>
                            <div class="divide-y divide-slate-100">
                                <?php foreach ($candidate['documents'] as $doc): ?>
                                    <?php
                                    $docStatus = $doc['status'] ?? 'uploaded';
                                    $docStatusMap = [
                                        'verified' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'check_circle'],
                                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'cancel'],
                                        'uploaded' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'pending'],
                                    ];
                                    $ds = $docStatusMap[$docStatus] ?? $docStatusMap['uploaded'];
                                    ?>
                                    <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50 transition-colors group">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-red-50 rounded-lg">
                                                <span class="material-icons text-red-400 text-lg">picture_as_pdf</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-700"><?= htmlspecialchars($doc['type_name'] ?? 'Document') ?></p>
                                                <p class="text-[11px] text-slate-400"><?= !empty($doc['created_at']) ? date('d M Y', strtotime($doc['created_at'])) : '' ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold <?= $ds['bg'] ?> <?= $ds['text'] ?>">
                                                <span class="material-icons text-[12px]"><?= $ds['icon'] ?></span>
                                                <?= ucfirst($docStatus) ?>
                                            </span>
                                            <a href="<?= BASE_URL ?>../recruitment/uploads/documents/<?= htmlspecialchars($doc['file_path'] ?? '') ?>"
                                               target="_blank"
                                               class="p-1.5 rounded-md hover:bg-blue-100 text-slate-400 hover:text-blue-600 transition-colors opacity-0 group-hover:opacity-100">
                                                <span class="material-icons text-lg">visibility</span>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="bg-slate-100 p-4 rounded-full mb-3">
                                    <span class="material-icons text-3xl text-slate-300">folder_off</span>
                                </div>
                                <p class="text-sm text-slate-500">Belum ada dokumen yang diupload</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Interview History -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d3">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="material-icons text-purple-500 text-lg">record_voice_over</span>
                                Riwayat Interview
                            </h3>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-500 rounded-full">
                                <?= count($candidate['interviews'] ?? []) ?> sesi
                            </span>
                        </div>

                        <?php if (!empty($candidate['interviews'])): ?>
                            <div class="p-5 space-y-3">
                                <?php foreach ($candidate['interviews'] as $interview): ?>
                                    <?php
                                    $intStatus = $interview['status'] ?? 'pending';
                                    $intColor = match($intStatus) {
                                        'completed' => 'border-l-emerald-500 bg-emerald-50/30',
                                        'in_progress' => 'border-l-blue-500 bg-blue-50/30',
                                        default => 'border-l-amber-400 bg-amber-50/30',
                                    };
                                    ?>
                                    <div class="border-l-4 <?= $intColor ?> rounded-r-lg px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-semibold text-slate-700">
                                                <?= htmlspecialchars($interview['question_bank_name'] ?? 'Interview') ?>
                                            </h4>
                                            <span class="text-[10px] text-slate-400">
                                                <?= !empty($interview['created_at']) ? date('d M Y', strtotime($interview['created_at'])) : '' ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3 mt-2">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold 
                                                <?= $intStatus === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                                <?= ucfirst($intStatus) ?>
                                            </span>
                                            <?php if (!empty($interview['total_score'])): ?>
                                                <span class="text-xs text-slate-500">
                                                    Skor: <strong class="text-slate-700"><?= $interview['total_score'] ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="bg-slate-100 p-4 rounded-full mb-3">
                                    <span class="material-icons text-3xl text-slate-300">mic_off</span>
                                </div>
                                <p class="text-sm text-slate-500">Belum ada sesi interview</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Medical Checkups -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d4">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="material-icons text-red-400 text-lg">health_and_safety</span>
                                Medical Checkup
                            </h3>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-500 rounded-full">
                                <?= count($candidate['medical_checkups'] ?? []) ?> record
                            </span>
                        </div>

                        <?php if (!empty($candidate['medical_checkups'])): ?>
                            <div class="p-5 space-y-3">
                                <?php foreach ($candidate['medical_checkups'] as $medical): ?>
                                    <?php
                                    $medStatus = $medical['status'] ?? 'pending';
                                    $medColor = match($medStatus) {
                                        'completed', 'passed' => 'border-emerald-200 bg-emerald-50/50',
                                        'failed' => 'border-red-200 bg-red-50/50',
                                        default => 'border-amber-200 bg-amber-50/50',
                                    };
                                    ?>
                                    <div class="border <?= $medColor ?> rounded-lg px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="material-icons text-lg <?= $medStatus === 'completed' || $medStatus === 'passed' ? 'text-emerald-500' : 'text-amber-500' ?>">
                                                    <?= $medStatus === 'completed' || $medStatus === 'passed' ? 'check_circle' : 'schedule' ?>
                                                </span>
                                                <span class="text-sm font-medium text-slate-700"><?= ucfirst($medStatus) ?></span>
                                            </div>
                                            <?php if (!empty($medical['scheduled_date'])): ?>
                                                <span class="text-xs text-slate-500">
                                                    <span class="material-icons text-[12px] align-middle mr-0.5">event</span>
                                                    <?= date('d M Y H:i', strtotime($medical['scheduled_date'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($medical['notes'])): ?>
                                            <p class="text-xs text-slate-500 mt-2"><?= htmlspecialchars($medical['notes']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="bg-slate-100 p-4 rounded-full mb-3">
                                    <span class="material-icons text-3xl text-slate-300">medical_information</span>
                                </div>
                                <p class="text-sm text-slate-500">Belum ada data medical checkup</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Application Timeline -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d4">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                                <span class="material-icons text-slate-400 text-lg">timeline</span>
                                Timeline
                            </h3>
                        </div>
                        <div class="p-5">
                            <div class="relative pl-6 space-y-4 before:absolute before:left-[9px] before:top-1 before:bottom-1 before:w-0.5 before:bg-slate-200">
                                <?php if (!empty($candidate['submitted_at'])): ?>
                                <div class="relative">
                                    <div class="absolute -left-6 top-0.5 w-[18px] h-[18px] rounded-full bg-blue-500 border-2 border-white flex items-center justify-center">
                                        <span class="material-icons text-white text-[10px]">send</span>
                                    </div>
                                    <p class="text-sm font-medium text-slate-700">Lamaran Dikirim</p>
                                    <p class="text-xs text-slate-400"><?= date('d M Y H:i', strtotime($candidate['submitted_at'])) ?></p>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($candidate['sent_to_erp_at'])): ?>
                                <div class="relative">
                                    <div class="absolute -left-6 top-0.5 w-[18px] h-[18px] rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center">
                                        <span class="material-icons text-white text-[10px]">check</span>
                                    </div>
                                    <p class="text-sm font-medium text-slate-700">Dikirim ke ERP</p>
                                    <p class="text-xs text-slate-400"><?= date('d M Y H:i', strtotime($candidate['sent_to_erp_at'])) ?></p>
                                </div>
                                <?php endif; ?>
                                <div class="relative">
                                    <div class="absolute -left-6 top-0.5 w-[18px] h-[18px] rounded-full <?= $statusClasses['bg'] ?> border-2 border-white flex items-center justify-center">
                                        <span class="material-icons <?= $statusClasses['text'] ?> text-[10px]">circle</span>
                                    </div>
                                    <p class="text-sm font-medium text-slate-700">Status: <?= htmlspecialchars($statusName) ?></p>
                                    <p class="text-xs text-slate-400">Status saat ini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System. All rights reserved.</p>
            </div>
        </div>
    </main>
</div>
</body>
</html>
