<?php
/**
 * Operational - Index (Stage 3 of Recruitment Flow)
 */
$currentPage = 'operational';
$operationals = $operationals ?? [];
$stats = $stats ?? ['total' => 0, 'pending' => 0, 'completed' => 0];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operational - IndoOcean ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };</script>
    <style>
        .card-hover { transition: all 0.2s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">

    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <span class="material-icons text-green-600">flight_takeoff</span>
                <div>
                    <h1 class="text-base font-bold text-slate-800">Operational</h1>
                    <p class="text-xs text-slate-400">Stage 3 — Persiapan Keberangkatan Crew</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= BASE_URL ?>AdminChecklist" class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 flex items-center gap-1">
                    <span class="material-icons text-sm">checklist</span> Admin Checklist
                </a>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
                <div class="mb-4 px-4 py-3 rounded-lg text-sm <?= ($flash['type'] ?? '') === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                    <?= $flash['message'] ?? '' ?>
                </div>
            <?php endif; ?>

            <!-- Flow Indicator -->
            <div class="mb-6 bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-2 text-slate-400">
                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-sm font-bold text-slate-500">1</div>
                        <span class="text-xs font-medium">Crewing Data</span>
                    </div>
                    <div class="w-12 h-0.5 bg-slate-200"></div>
                    <div class="flex items-center gap-2 text-slate-400">
                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-sm font-bold text-slate-500">2</div>
                        <span class="text-xs font-medium">Admin Checklist</span>
                    </div>
                    <div class="w-12 h-0.5 bg-slate-200"></div>
                    <div class="flex items-center gap-2 text-green-600">
                        <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-sm font-bold text-white">3</div>
                        <span class="text-xs font-bold">Operational</span>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $stats['total'] ?></p>
                            <p class="text-xs text-slate-500 mt-1">Total Crew</p>
                        </div>
                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-green-600">groups</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-2xl font-bold text-amber-600"><?= $stats['pending'] ?></p>
                            <p class="text-xs text-slate-500 mt-1">Perlu Dilengkapi</p>
                        </div>
                        <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-amber-600">pending</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-2xl font-bold text-green-600"><?= $stats['completed'] ?></p>
                            <p class="text-xs text-slate-500 mt-1">Selesai / On Board</p>
                        </div>
                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                            <span class="material-icons text-green-600">check_circle</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operationals Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="material-icons text-green-600 text-lg">flight</span>
                    <h2 class="font-semibold text-slate-800">Daftar Crew Operational</h2>
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full"><?= count($operationals) ?></span>
                </div>

                <?php if (empty($operationals)): ?>
                    <div class="p-12 text-center">
                        <span class="material-icons text-slate-300 text-5xl mb-3">flight_takeoff</span>
                        <p class="text-slate-400 text-sm">Belum ada crew di tahap operational.</p>
                        <p class="text-slate-300 text-xs mt-1">Crew akan muncul setelah Admin Checklist diselesaikan.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-left">
                                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Crew</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Hotel</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Tgl Keluar</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Tiket</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Airport</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($operationals as $op): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                <?= strtoupper(substr($op['full_name'] ?? 'U', 0, 2)) ?>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-800"><?= htmlspecialchars($op['full_name'] ?? '') ?></p>
                                                <p class="text-xs text-slate-400"><?= $op['rank_name'] ?? '' ?> • <?= $op['employee_id'] ?? '' ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($op['hotel_name'] ?? '-') ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= $op['hotel_checkout_date'] ? date('d M Y', strtotime($op['hotel_checkout_date'])) : '-' ?></td>
                                    <td class="px-5 py-3">
                                        <?php if ($op['ticket_booking_code']): ?>
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-mono text-xs rounded"><?= htmlspecialchars($op['ticket_booking_code']) ?></span>
                                        <?php else: ?>
                                            <span class="text-slate-300">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-slate-600">
                                        <?php if ($op['airport_depart'] || $op['airport_arrival']): ?>
                                            <?= htmlspecialchars($op['airport_depart'] ?? '?') ?> → <?= htmlspecialchars($op['airport_arrival'] ?? '?') ?>
                                        <?php else: ?>
                                            <span class="text-slate-300">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3">
                                        <?php if ($op['op_status'] === 'completed'): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">✅ Done</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">⏳ Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3">
                                        <a href="<?= BASE_URL ?>Operational/detail/<?= $op['crew_id'] ?>" class="text-blue-600 hover:text-blue-700 font-medium text-xs flex items-center gap-1">
                                            <span class="material-icons text-sm">edit</span> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="h-8"></div>
        </div>
    </main>
</div>
</body>
</html>
