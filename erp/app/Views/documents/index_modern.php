<?php
/**
 * Modern Document Management Overview
 */
$currentPage = 'documents';
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('documents.title') ?> | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#CA8A04',
                        'background-light': '#F8FAFC',
                        'background-dark': '#0F172A',
                        'surface-light': '#FFFFFF',
                        'surface-dark': '#1E293B',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }

        /* Book Opening Animation */
        @keyframes bookOpen {
            0% { transform: perspective(1200px) rotateY(-90deg) scale(0.6); opacity: 0; }
            40% { transform: perspective(1200px) rotateY(-20deg) scale(0.9); opacity: 0.8; }
            70% { transform: perspective(1200px) rotateY(5deg) scale(1.02); opacity: 1; }
            100% { transform: perspective(1200px) rotateY(0deg) scale(1); opacity: 1; }
        }
        @keyframes bookClose {
            0% { transform: perspective(1200px) rotateY(0deg) scale(1); opacity: 1; }
            100% { transform: perspective(1200px) rotateY(-90deg) scale(0.6); opacity: 0; }
        }
        @keyframes pageFlip {
            0% { transform: perspective(800px) rotateY(90deg); opacity: 0; }
            50% { transform: perspective(800px) rotateY(-5deg); opacity: 1; }
            100% { transform: perspective(800px) rotateY(0deg); opacity: 1; }
        }
        @keyframes slideInRow {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .book-modal { animation: bookOpen 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; transform-origin: left center; }
        .book-modal-closing { animation: bookClose 0.4s ease-in forwards; transform-origin: left center; }
        .book-page { animation: pageFlip 0.5s ease-out forwards; transform-origin: left center; }
        .book-row { animation: slideInRow 0.3s ease-out forwards; opacity: 0; }
        .book-spine {
            background: linear-gradient(135deg, #92400e 0%, #78350f 50%, #92400e 100%);
            box-shadow: inset -3px 0 8px rgba(0,0,0,0.3), inset 3px 0 4px rgba(255,255,255,0.1);
        }
        .book-cover {
            background: linear-gradient(145deg, #fefce8 0%, #fef9c3 30%, #fef08a 100%);
            box-shadow: 0 20px 60px -15px rgba(0,0,0,0.3), 0 0 0 1px rgba(146,64,14,0.1);
        }
        .book-cover-blue {
            background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 30%, #bfdbfe 100%);
            box-shadow: 0 20px 60px -15px rgba(0,0,0,0.3), 0 0 0 1px rgba(29,78,216,0.1);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 flex-shrink-0 shadow-sm">
                <!-- Breadcrumb -->
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-400 font-medium"><?= __('sidebar.crews') ?></span>
                    <span class="material-icons-outlined text-gray-400 text-sm">chevron_right</span>
                    <span class="text-gray-800 dark:text-white font-semibold"><?= __('documents.title') ?></span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Language Toggle -->
                    <div class="flex items-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md px-1 p-0.5">
                        <button class="px-3 py-1 text-xs font-medium bg-primary text-white rounded shadow-sm">ID</button>
                        <button class="px-3 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">EN</button>
                    </div>

                    <!-- Notifications -->
                    <button class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors relative">
                        <span class="material-icons-outlined">notifications</span>
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 bg-red-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <div class="max-w-7xl mx-auto space-y-8">
                    <!-- Page Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1"><?= __('documents.title') ?></h1>
                            <p class="text-gray-500 dark:text-gray-400"><?= __('documents.subtitle') ?></p>
                        </div>
                        <button onclick="document.getElementById('uploadAlertModal').classList.remove('hidden')" 
                                class="bg-primary hover:bg-yellow-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center font-medium transform active:scale-95">
                            <span class="material-icons mr-2">add</span>
                            <?= __('documents.upload_document') ?>
                        </button>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Valid Documents -->
                        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"><?= __('documents.valid') ?></p>
                                <h3 class="text-3xl font-bold text-gray-900 dark:text-white"><?= $statusCounts['valid'] ?? 0 ?></h3>
                            </div>
                            <div class="h-12 w-12 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-500 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                                <span class="material-icons">check_circle</span>
                            </div>
                        </div>

                        <!-- Expiring Soon -->
                        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"><?= __('documents.expiring_soon') ?></p>
                                <h3 class="text-3xl font-bold text-gray-900 dark:text-white"><?= $statusCounts['expiring_soon'] ?? 0 ?></h3>
                            </div>
                            <div class="h-12 w-12 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-500 dark:text-yellow-400 group-hover:scale-110 transition-transform">
                                <span class="material-icons">warning</span>
                            </div>
                        </div>

                        <!-- Expired -->
                        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"><?= __('documents.expired') ?></p>
                                <h3 class="text-3xl font-bold text-gray-900 dark:text-white"><?= $statusCounts['expired'] ?? 0 ?></h3>
                            </div>
                            <div class="h-12 w-12 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-500 dark:text-red-400 group-hover:scale-110 transition-transform">
                                <span class="material-icons">cancel</span>
                            </div>
                        </div>

                        <!-- Total Documents (Clickable) -->
                        <div onclick="openCrewBookModal()" class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border-2 border-blue-200 dark:border-blue-700 flex items-center justify-between group hover:shadow-lg hover:border-blue-400 transition-all cursor-pointer relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"><?= __('documents.total_documents') ?></p>
                                <h3 class="text-3xl font-bold text-gray-900 dark:text-white"><?= array_sum($statusCounts) ?></h3>
                                <p class="text-[10px] text-blue-500 font-semibold mt-1 flex items-center gap-1"><span class="material-icons" style="font-size:12px">touch_app</span> Klik untuk lihat</p>
                            </div>
                            <div class="h-12 w-12 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 dark:text-blue-400 group-hover:scale-110 group-hover:rotate-6 transition-transform relative">
                                <span class="material-icons">menu_book</span>
                            </div>
                        </div>
                    </div>

                    <!-- Expired Documents Alert -->
                    <?php if (!empty($expired)): ?>
                        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border-l-4 border-red-500 shadow-sm overflow-hidden">
                            <div class="p-6">
                                <h4 class="text-lg font-bold text-red-600 dark:text-red-400 mb-4 flex items-center">
                                    <span class="material-icons mr-2">error</span>
                                    <?= __('documents.expired_documents') ?> (<?= count($expired) ?>)
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('sidebar.crews') ?></th>
                                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('documents.document_name') ?></th>
                                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('documents.document_type') ?></th>
                                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('documents.expired_on') ?></th>
                                                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('common.actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                            <?php foreach (array_slice($expired, 0, 10) as $doc): ?>
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                                    <td class="py-4 px-4">
                                                        <a href="<?= BASE_URL ?>crews/<?= $doc['crew_id'] ?>" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                                            <?= htmlspecialchars($doc['crew_name']) ?>
                                                        </a>
                                                        <div class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($doc['employee_id']) ?></div>
                                                    </td>
                                                    <td class="py-4 px-4 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($doc['document_name']) ?></td>
                                                    <td class="py-4 px-4">
                                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
                                                            <?= htmlspecialchars($doc['type_name'] ?? $doc['document_type']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="py-4 px-4 text-sm font-medium text-red-600 dark:text-red-400">
                                                        <?= date('d M Y', strtotime($doc['expiry_date'])) ?>
                                                    </td>
                                                    <td class="py-4 px-4 text-right">
                                                        <a href="<?= BASE_URL ?>documents/<?= $doc['crew_id'] ?>" 
                                                           class="inline-flex items-center px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors text-sm font-medium">
                                                            <span class="material-icons text-sm mr-1">folder_open</span>
                                                            <?= __('common.view') ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Expiring Soon Documents -->
                    <?php if (!empty($expiring)): ?>
                        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border-l-4 border-yellow-500 shadow-sm overflow-hidden">
                            <div class="p-6">
                                <h4 class="text-lg font-bold text-yellow-600 dark:text-yellow-400 mb-4 flex items-center">
                                    <span class="material-icons mr-2">schedule</span>
                                    <?= __('documents.expiring_in_90_days') ?> (<?= count($expiring) ?>)
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('sidebar.crews') ?></th>
                                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('documents.document_name') ?></th>
                                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('documents.document_type') ?></th>
                                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('documents.expires_on') ?></th>
                                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('documents.days_remaining') ?></th>
                                                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase"><?= __('common.actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                            <?php foreach ($expiring as $doc): ?>
                                                <?php $daysLeft = max(0, floor((strtotime($doc['expiry_date']) - time()) / 86400)); ?>
                                                <?php $urgencyColor = $daysLeft < 30 ? 'text-red-600 dark:text-red-400' : ($daysLeft < 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400'); ?>
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                                    <td class="py-4 px-4">
                                                        <a href="<?= BASE_URL ?>crews/<?= $doc['crew_id'] ?>" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                                            <?= htmlspecialchars($doc['crew_name']) ?>
                                                        </a>
                                                        <div class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($doc['employee_id']) ?></div>
                                                    </td>
                                                    <td class="py-4 px-4 text-sm text-gray-700 dark:text-gray-300"><?= htmlspecialchars($doc['document_name']) ?></td>
                                                    <td class="py-4 px-4">
                                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
                                                            <?= htmlspecialchars($doc['type_name'] ?? $doc['document_type']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="py-4 px-4 text-sm text-gray-700 dark:text-gray-300">
                                                        <?= date('d M Y', strtotime($doc['expiry_date'])) ?>
                                                    </td>
                                                    <td class="py-4 px-4">
                                                        <span class="text-sm font-bold <?= $urgencyColor ?>"><?= $daysLeft ?> <?= __('documents.days') ?></span>
                                                    </td>
                                                    <td class="py-4 px-4 text-right">
                                                        <a href="<?= BASE_URL ?>documents/<?= $doc['crew_id'] ?>" 
                                                           class="inline-flex items-center px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors text-sm font-medium">
                                                            <span class="material-icons text-sm mr-1">folder_open</span>
                                                            <?= __('common.view') ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- All Valid State -->
                    <?php if (empty($expired) && empty($expiring)): ?>
                        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-gray-200 dark:border-gray-700 p-12 flex flex-col items-center justify-center text-center shadow-sm min-h-[400px]">
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 p-6 rounded-full mb-6">
                                <span class="material-icons text-6xl text-emerald-500 dark:text-emerald-400">verified</span>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?= __('documents.all_valid') ?></h2>
                            <p class="text-gray-500 dark:text-gray-400 max-w-md">
                                <?= __('documents.all_valid_desc') ?>
                            </p>
                            <div class="mt-8 flex space-x-4">
                                <button class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <?= __('documents.view_report') ?>
                                </button>
                                <button class="px-5 py-2.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                    <?= __('documents.manage_settings') ?>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <!-- Upload Alert Modal -->
    <div id="uploadAlertModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 w-full max-w-xl mx-4 overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 px-8 py-6 border-b border-amber-100">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <span class="material-icons text-amber-600 text-3xl">info</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900"><?= __('documents.upload_crew_doc') ?></h3>
                        <p class="text-sm text-amber-700 mt-1"><?= __('documents.important_info') ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-8 py-6">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-5">
                    <div class="flex gap-3">
                        <span class="material-icons text-blue-500 mt-0.5 flex-shrink-0">lightbulb</span>
                        <div>
                            <p class="text-sm font-semibold text-blue-800 mb-1"><?= __('documents.how_to_upload') ?></p>
                            <p class="text-sm text-blue-700 leading-relaxed">
                                <?= __('documents.upload_instruction') ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-start gap-3 text-sm text-gray-600">
                    <span class="material-icons text-gray-400 text-lg mt-0.5">arrow_forward</span>
                    <p><?= __('documents.upload_steps') ?></p>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                <button onclick="document.getElementById('uploadAlertModal').classList.add('hidden')" 
                        class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-white transition-colors text-sm">
                    <?= __('common.close') ?>
                </button>
                <a href="<?= BASE_URL ?>crews" 
                   class="px-5 py-2.5 rounded-lg bg-primary hover:bg-blue-800 text-white font-medium transition-colors text-sm flex items-center gap-2 shadow-sm">
                    <span class="material-icons text-base">badge</span>
                    <?= __('documents.open_crew_data') ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Book Modal: Crew List -->
    <div id="crewBookModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" onclick="if(event.target===this) closeCrewBookModal()">
        <div class="flex items-stretch max-w-3xl w-full mx-4">
            <!-- Book Spine -->
            <div class="book-spine w-6 rounded-l-lg flex-shrink-0 hidden md:block"></div>
            <!-- Book Content -->
            <div id="crewBookContent" class="book-cover rounded-r-2xl md:rounded-l-none rounded-l-2xl w-full overflow-hidden">
                <!-- Book Cover Header -->
                <div class="bg-gradient-to-r from-amber-700 to-amber-800 px-8 py-6 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-2 left-4 w-32 h-32 border border-white/30 rounded-full"></div>
                        <div class="absolute bottom-2 right-4 w-24 h-24 border border-white/20 rounded-full"></div>
                    </div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <span class="material-icons text-white text-3xl">menu_book</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Buku Dokumen Kru</h3>
                                <p class="text-amber-200 text-sm">Pilih kru untuk melihat dokumen</p>
                            </div>
                        </div>
                        <button onclick="closeCrewBookModal()" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                            <span class="material-icons text-white">close</span>
                        </button>
                    </div>
                </div>
                <!-- Crew List Body -->
                <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar" id="crewBookBody">
                    <div class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
                        <span class="ml-3 text-gray-500">Membuka buku...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Book Modal: Crew Documents -->
    <div id="crewDocsBookModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm" onclick="if(event.target===this) closeCrewDocsBookModal()">
        <div class="flex items-stretch max-w-4xl w-full mx-4">
            <!-- Book Spine -->
            <div class="book-spine w-6 rounded-l-lg flex-shrink-0 hidden md:block"></div>
            <!-- Book Content -->
            <div id="crewDocsBookContent" class="book-cover-blue rounded-r-2xl md:rounded-l-none rounded-l-2xl w-full overflow-hidden">
                <!-- Book Cover Header -->
                <div class="bg-gradient-to-r from-blue-700 to-blue-800 px-8 py-6 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-2 left-4 w-32 h-32 border border-white/30 rounded-full"></div>
                        <div class="absolute bottom-2 right-4 w-24 h-24 border border-white/20 rounded-full"></div>
                    </div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <button onclick="closeCrewDocsBookModal()" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                                <span class="material-icons text-white">arrow_back</span>
                            </button>
                            <div class="h-12 w-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <span class="material-icons text-white text-2xl">folder_open</span>
                            </div>
                            <div>
                                <h3 id="crewDocsTitle" class="text-lg font-bold text-white">Dokumen Kru</h3>
                                <p id="crewDocsSubtitle" class="text-blue-200 text-sm"></p>
                            </div>
                        </div>
                        <button onclick="closeCrewDocsBookModal()" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                            <span class="material-icons text-white">close</span>
                        </button>
                    </div>
                </div>
                <!-- Documents Body -->
                <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar" id="crewDocsBookBody">
                    <div class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="ml-3 text-gray-500">Membuka halaman...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Preview Modal -->
    <div id="docPreviewModal" class="hidden fixed inset-0 z-[70] flex items-center justify-center bg-black/60 backdrop-blur-sm" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl mx-4 overflow-hidden" style="max-height:90vh">
            <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b">
                <h4 id="docPreviewTitle" class="text-sm font-bold text-slate-800"></h4>
                <div class="flex items-center gap-2">
                    <a id="docPreviewDownload" href="#" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 text-sm font-medium">
                        <span class="material-icons text-sm">download</span> Download
                    </a>
                    <button onclick="document.getElementById('docPreviewModal').classList.add('hidden')" class="p-1.5 hover:bg-gray-200 rounded-full">
                        <span class="material-icons text-gray-500">close</span>
                    </button>
                </div>
            </div>
            <div class="bg-gray-100" style="height:75vh">
                <iframe id="docPreviewFrame" src="" class="w-full h-full border-0"></iframe>
            </div>
        </div>
    </div>

    <script>
        const BASE = '<?= BASE_URL ?>';

        // Open Crew Book Modal
        function openCrewBookModal() {
            const modal = document.getElementById('crewBookModal');
            const content = document.getElementById('crewBookContent');
            modal.classList.remove('hidden');
            content.classList.remove('book-modal-closing');
            content.classList.add('book-modal');
            loadCrewList();
        }

        function closeCrewBookModal() {
            const modal = document.getElementById('crewBookModal');
            const content = document.getElementById('crewBookContent');
            content.classList.add('book-modal-closing');
            setTimeout(() => modal.classList.add('hidden'), 400);
        }

        // Load crew list via AJAX
        async function loadCrewList() {
            const body = document.getElementById('crewBookBody');
            body.innerHTML = `<div class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
                <span class="ml-3 text-gray-500">Membuka buku...</span>
            </div>`;

            try {
                const res = await fetch(BASE + 'documents/apiCrewList');
                const data = await res.json();

                if (data.success && data.crews.length > 0) {
                    let html = '<div class="space-y-2">';
                    data.crews.forEach((crew, i) => {
                        const initials = crew.full_name.substring(0, 2).toUpperCase();
                        const delay = i * 0.06;
                        html += `
                        <div class="book-row flex items-center justify-between p-4 bg-white rounded-xl border border-amber-100 hover:border-amber-300 hover:shadow-md transition-all group cursor-pointer" 
                             style="animation-delay: ${delay}s"
                             onclick="openCrewDocsBook(${crew.id}, '${crew.full_name.replace(/'/g, "\\'")}'    )">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                    ${initials}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">${crew.full_name}</p>
                                    <p class="text-xs text-slate-400">${crew.employee_id || 'No ID'}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-full">
                                    ${crew.doc_count} file
                                </span>
                                <button class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition-all shadow-sm group-hover:shadow-md">
                                    <span class="material-icons" style="font-size:14px">folder_open</span> Lihat File
                                </button>
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                    body.innerHTML = html;
                } else {
                    body.innerHTML = `<div class="text-center py-12">
                        <span class="material-icons text-5xl text-gray-300">folder_off</span>
                        <p class="text-gray-500 mt-3">Belum ada dokumen kru</p>
                    </div>`;
                }
            } catch (err) {
                body.innerHTML = `<div class="text-center py-12">
                    <span class="material-icons text-5xl text-red-300">error</span>
                    <p class="text-red-500 mt-3">Gagal memuat data</p>
                </div>`;
            }
        }

        // Open Crew Documents Book Modal
        function openCrewDocsBook(crewId, crewName) {
            const modal = document.getElementById('crewDocsBookModal');
            const content = document.getElementById('crewDocsBookContent');
            document.getElementById('crewDocsTitle').textContent = 'Dokumen: ' + crewName;
            document.getElementById('crewDocsSubtitle').textContent = '';
            modal.classList.remove('hidden');
            content.classList.remove('book-modal-closing');
            content.classList.add('book-modal', 'book-page');
            loadCrewDocs(crewId);
        }

        function closeCrewDocsBookModal() {
            const modal = document.getElementById('crewDocsBookModal');
            const content = document.getElementById('crewDocsBookContent');
            content.classList.add('book-modal-closing');
            setTimeout(() => modal.classList.add('hidden'), 400);
        }

        // Load crew documents
        async function loadCrewDocs(crewId) {
            const body = document.getElementById('crewDocsBookBody');
            body.innerHTML = `<div class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-gray-500">Membuka halaman...</span>
            </div>`;

            try {
                const res = await fetch(BASE + 'documents/apiCrewDocs/' + crewId);
                const data = await res.json();

                if (data.success && data.documents.length > 0) {
                    document.getElementById('crewDocsSubtitle').textContent = data.documents.length + ' dokumen ditemukan';
                    let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">';
                    data.documents.forEach((doc, i) => {
                        const delay = i * 0.08;
                        const ext = (doc.file_name || '').split('.').pop().toUpperCase();
                        const icon = getDocIcon(ext);
                        const statusBadge = getStatusBadge(doc.status);
                        const expiryText = doc.expiry_date ? formatDocDate(doc.expiry_date) : 'Tidak ada';
                        html += `
                        <div class="book-row bg-white rounded-xl border border-blue-100 hover:border-blue-300 hover:shadow-lg transition-all p-4 cursor-pointer group" 
                             style="animation-delay: ${delay}s"
                             onclick="previewDocument(${doc.id}, '${(doc.document_name || doc.file_name || '').replace(/'/g, "\\'")}')">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-14 rounded-lg ${icon.bg} flex flex-col items-center justify-center flex-shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                                    <span class="material-icons ${icon.color}" style="font-size:20px">${icon.icon}</span>
                                    <span class="text-[8px] font-bold ${icon.color} mt-0.5">${ext}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm text-slate-800 truncate">${doc.document_name || doc.file_name}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">${doc.type_name || doc.document_type || '-'}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        ${statusBadge}
                                        <span class="text-[10px] text-slate-400">Exp: ${expiryText}</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-icons text-blue-500" style="font-size:20px">visibility</span>
                                </div>
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                    body.innerHTML = html;
                } else {
                    body.innerHTML = `<div class="text-center py-12">
                        <span class="material-icons text-5xl text-gray-300">folder_off</span>
                        <p class="text-gray-500 mt-3">Belum ada dokumen</p>
                    </div>`;
                }
            } catch (err) {
                body.innerHTML = `<div class="text-center py-12">
                    <span class="material-icons text-5xl text-red-300">error</span>
                    <p class="text-red-500 mt-3">Gagal memuat dokumen</p>
                </div>`;
            }
        }

        function getDocIcon(ext) {
            switch(ext) {
                case 'PDF': return { icon: 'picture_as_pdf', bg: 'bg-red-50', color: 'text-red-500' };
                case 'JPG': case 'JPEG': case 'PNG': case 'GIF': return { icon: 'image', bg: 'bg-green-50', color: 'text-green-500' };
                case 'DOC': case 'DOCX': return { icon: 'description', bg: 'bg-blue-50', color: 'text-blue-500' };
                default: return { icon: 'insert_drive_file', bg: 'bg-gray-50', color: 'text-gray-500' };
            }
        }

        function getStatusBadge(status) {
            switch(status) {
                case 'valid': return '<span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded">Valid</span>';
                case 'expiring_soon': return '<span class="px-1.5 py-0.5 bg-yellow-50 text-yellow-600 text-[10px] font-bold rounded">Segera Expired</span>';
                case 'expired': return '<span class="px-1.5 py-0.5 bg-red-50 text-red-600 text-[10px] font-bold rounded">Expired</span>';
                default: return '<span class="px-1.5 py-0.5 bg-gray-50 text-gray-500 text-[10px] font-bold rounded">-</span>';
            }
        }

        function formatDocDate(dateStr) {
            const d = new Date(dateStr);
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        function previewDocument(docId, docName) {
            document.getElementById('docPreviewTitle').textContent = docName;
            document.getElementById('docPreviewFrame').src = BASE + 'documents/preview/' + docId;
            document.getElementById('docPreviewDownload').href = BASE + 'documents/download/' + docId;
            document.getElementById('docPreviewModal').classList.remove('hidden');
        }
    </script>

</body>
</html>
