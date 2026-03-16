<?php
/**
 * PT Indo Ocean - ERP System
 * Crew Movement Monitoring Timeline
 */
$currentPage = 'crew-monitoring';

$actionLabels = [
    'created' => ['label' => 'Kontrak Dibuat', 'icon' => 'add_circle', 'color' => 'blue'],
    'approved' => ['label' => 'Kontrak Disetujui', 'icon' => 'check_circle', 'color' => 'emerald'],
    'rejected' => ['label' => 'Kontrak Ditolak', 'icon' => 'cancel', 'color' => 'red'],
    'updated' => ['label' => 'Kontrak Diupdate', 'icon' => 'edit', 'color' => 'amber'],
    'renewed' => ['label' => 'Kontrak Diperpanjang', 'icon' => 'autorenew', 'color' => 'purple'],
    'created_from_renewal' => ['label' => 'Perpanjangan Baru', 'icon' => 'fiber_new', 'color' => 'indigo'],
    'terminated' => ['label' => 'Kontrak Diterminasi', 'icon' => 'remove_circle', 'color' => 'red'],
    'completed' => ['label' => 'Kontrak Selesai', 'icon' => 'task_alt', 'color' => 'slate'],
];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Crew Monitoring') ?> - PT Indo Ocean</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'brand-blue': '#1e40af', 'brand-gold': '#fbbf24' },
                    fontFamily: { sans: ["Inter", "sans-serif"] }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .timeline-line { position: absolute; left: 24px; top: 0; bottom: 0; width: 2px; background: #e2e8f0; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <main class="flex-1 overflow-y-auto ml-0 lg:ml-64">
            <!-- Header -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex items-center space-x-3">
                    <span class="material-icons-round text-brand-blue text-2xl">timeline</span>
                    <h1 class="text-xl font-bold text-slate-800">Crew Movement Monitoring</h1>
                </div>
            </header>

            <div class="p-8 max-w-7xl mx-auto space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200/50">
                        <p class="text-3xl font-bold text-slate-800"><?= $stats['active_count'] ?? 0 ?></p>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mt-1">Aktif / Onboard</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-5 border border-emerald-200/50">
                        <p class="text-3xl font-bold text-slate-800"><?= $stats['completed_count'] ?? 0 ?></p>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mt-1">Selesai</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200/50">
                        <p class="text-3xl font-bold text-slate-800"><?= $stats['renewed_count'] ?? 0 ?></p>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider mt-1">Diperpanjang</p>
                    </div>
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5 border border-red-200/50">
                        <p class="text-3xl font-bold text-slate-800"><?= $stats['terminated_count'] ?? 0 ?></p>
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mt-1">Diterminasi</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                    <form method="GET" action="<?= BASE_URL ?>contracts/timeline" class="flex flex-col md:flex-row gap-3 items-end">
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Kru</label>
                            <input type="text" name="crew" value="<?= htmlspecialchars($filters['crew'] ?? '') ?>"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none"
                                placeholder="Cari nama kru...">
                        </div>
                        <div class="w-44">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Kapal</label>
                            <select name="vessel" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none">
                                <option value="">Semua Kapal</option>
                                <?php foreach ($vessels ?? [] as $v): ?>
                                    <option value="<?= $v['id'] ?>" <?= ($filters['vessel'] ?? '') == $v['id'] ? 'selected' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="w-44">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Tipe Event</label>
                            <select name="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none">
                                <option value="">Semua Tipe</option>
                                <option value="created" <?= ($filters['type'] ?? '') === 'created' ? 'selected' : '' ?>>Dibuat</option>
                                <option value="approved" <?= ($filters['type'] ?? '') === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                                <option value="renewed" <?= ($filters['type'] ?? '') === 'renewed' ? 'selected' : '' ?>>Diperpanjang</option>
                                <option value="terminated" <?= ($filters['type'] ?? '') === 'terminated' ? 'selected' : '' ?>>Diterminasi</option>
                            </select>
                        </div>
                        <div class="w-36">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Dari</label>
                            <input type="date" name="from" value="<?= htmlspecialchars($filters['from'] ?? '') ?>"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none">
                        </div>
                        <div class="w-36">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Sampai</label>
                            <input type="date" name="to" value="<?= htmlspecialchars($filters['to'] ?? '') ?>"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-brand-blue text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors flex items-center gap-1">
                            <span class="material-icons-round text-sm">search</span>
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-slate-800">Timeline Pergerakan Kru</h2>
                        <span class="text-sm text-slate-400"><?= count($events) ?> event ditemukan</span>
                    </div>

                    <?php if (empty($events)): ?>
                        <div class="text-center py-16">
                            <span class="material-icons-round text-slate-200 text-6xl mb-4 block">event_busy</span>
                            <p class="text-slate-400 font-medium">Tidak ada event dalam periode ini</p>
                            <p class="text-slate-300 text-sm mt-1">Coba perluas rentang tanggal atau ubah filter</p>
                        </div>
                    <?php else: ?>
                        <div class="relative pl-12">
                            <div class="timeline-line"></div>
                            <?php 
                            $lastDate = '';
                            foreach ($events as $event):
                                $info = $actionLabels[$event['action']] ?? ['label' => ucfirst($event['action']), 'icon' => 'circle', 'color' => 'slate'];
                                $eventDate = date('d M Y', strtotime($event['event_date']));
                                $eventTime = date('H:i', strtotime($event['event_date']));
                                $showDate = $eventDate !== $lastDate;
                                $lastDate = $eventDate;
                            ?>
                                <?php if ($showDate): ?>
                                    <div class="mb-4 -ml-12 mt-6 first:mt-0">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                            <?= $eventDate ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <div class="relative mb-6 group">
                                    <!-- Timeline dot -->
                                    <div class="absolute -left-12 top-1 w-12 flex justify-center">
                                        <div class="w-8 h-8 rounded-full bg-<?= $info['color'] ?>-100 border-2 border-<?= $info['color'] ?>-300 flex items-center justify-center z-10">
                                            <span class="material-icons-round text-<?= $info['color'] ?>-600 text-sm"><?= $info['icon'] ?></span>
                                        </div>
                                    </div>

                                    <!-- Event card -->
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 hover:border-<?= $info['color'] ?>-200 hover:shadow-sm transition-all group-hover:bg-white">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-<?= $info['color'] ?>-100 text-<?= $info['color'] ?>-700 border border-<?= $info['color'] ?>-200">
                                                        <?= $info['label'] ?>
                                                    </span>
                                                    <span class="text-xs text-slate-400"><?= $eventTime ?></span>
                                                </div>
                                                <h4 class="font-bold text-slate-800">
                                                    <a href="<?= BASE_URL ?>crews/<?= $event['crew_id'] ?>" class="hover:text-brand-blue transition-colors">
                                                        <?= htmlspecialchars($event['crew_name'] ?? '-') ?>
                                                    </a>
                                                </h4>
                                                <div class="flex items-center gap-4 mt-1 text-xs text-slate-500">
                                                    <?php if ($event['vessel_name']): ?>
                                                        <span class="flex items-center gap-1">
                                                            <span class="material-icons-round text-xs">directions_boat</span>
                                                            <?= htmlspecialchars($event['vessel_name']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($event['rank_name']): ?>
                                                        <span class="flex items-center gap-1">
                                                            <span class="material-icons-round text-xs">badge</span>
                                                            <?= htmlspecialchars($event['rank_name']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="flex items-center gap-1">
                                                        <span class="material-icons-round text-xs">description</span>
                                                        <a href="<?= BASE_URL ?>contracts/view/<?= $event['contract_id'] ?>" class="hover:text-brand-blue">
                                                            <?= htmlspecialchars($event['contract_no'] ?? '-') ?>
                                                        </a>
                                                    </span>
                                                </div>
                                                <?php if ($event['action'] === 'terminated' && $event['termination_reason']): ?>
                                                    <p class="mt-2 text-xs text-red-600 bg-red-50 rounded px-2 py-1 inline-block">
                                                        Alasan: <?= htmlspecialchars($event['termination_reason']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <?php if ($event['action'] === 'renewed' || $event['action'] === 'created_from_renewal'): ?>
                                                    <p class="mt-2 text-xs text-purple-600 bg-purple-50 rounded px-2 py-1 inline-block">
                                                        <span class="material-icons-round text-xs align-middle">autorenew</span>
                                                        Kontrak perpanjangan
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right text-xs text-slate-400">
                                                <?php if ($event['user_name']): ?>
                                                    <span>by <?= htmlspecialchars($event['user_name']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
