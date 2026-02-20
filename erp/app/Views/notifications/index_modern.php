<?php
/**
 * Modern Notifications View
 * Clean white design with modern sidebar
 */
$currentPage = 'notifications';

$typeIcons = [
    'info' => ['info', 'bg-blue-50 text-blue-600'],
    'warning' => ['warning', 'bg-amber-50 text-amber-600'],
    'danger' => ['error', 'bg-red-50 text-red-500'],
    'success' => ['check_circle', 'bg-emerald-50 text-emerald-600'],
];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Notifications' ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('notifications.title') ?></h1>
                <p class="text-[11px] text-slate-400"><?= __('notifications.subtitle') ?></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= BASE_URL ?>notifications/generate"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                    <span class="material-icons text-sm">sync</span> <?= __('notifications.generate_alerts') ?>
                </a>
                <a href="<?= BASE_URL ?>notifications/mark-all-read"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                    <span class="material-icons text-sm">done_all</span> <?= __('notifications.mark_all_read') ?>
                </a>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <?php if (!empty($flash)): ?>
                <?php foreach ($flash as $type => $msg): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium
                        <?= $type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' ?>
                        <?= $type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : '' ?>">
                        <?= $msg ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="flex items-center gap-2 mb-6">
                <span class="material-icons text-blue-600 text-2xl">notifications</span>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight"><?= __('notifications.all_notifications') ?></h2>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in">
                <?php if (empty($notifications)): ?>
                    <div class="py-20 text-center">
                        <div class="bg-slate-100 p-5 rounded-full inline-block mb-4">
                            <span class="material-icons text-4xl text-slate-300">notifications_off</span>
                        </div>
                        <h3 class="text-base font-semibold text-slate-700 mb-1">Tidak ada notifikasi</h3>
                        <p class="text-slate-400 text-sm">Semua bersih! Anda tidak memiliki notifikasi baru.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($notifications as $notif):
                            $ti = $typeIcons[$notif['type'] ?? 'info'] ?? $typeIcons['info'];
                        ?>
                        <div class="flex items-start gap-4 px-5 py-4 hover:bg-blue-50/30 transition-colors <?= !$notif['is_read'] ? 'bg-blue-50/20 border-l-[3px] border-blue-500' : 'border-l-[3px] border-transparent' ?>">
                            <div class="p-2 rounded-lg <?= $ti[1] ?> flex-shrink-0 mt-0.5">
                                <span class="material-icons text-lg"><?= $ti[0] ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                                            <?= htmlspecialchars($notif['title']) ?>
                                            <?php if (!$notif['is_read']): ?>
                                                <span class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-100 text-blue-600 rounded-full">NEW</span>
                                            <?php endif; ?>
                                        </h4>
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed"><?= htmlspecialchars($notif['message']) ?></p>
                                    </div>
                                    <span class="text-[11px] text-slate-400 whitespace-nowrap flex-shrink-0">
                                        <?= date('d M Y H:i', strtotime($notif['created_at'])) ?>
                                    </span>
                                </div>
                                <div class="flex gap-2 mt-2">
                                    <?php if ($notif['link']): ?>
                                        <a href="<?= BASE_URL . $notif['link'] ?>"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold bg-slate-100 hover:bg-blue-100 text-slate-600 hover:text-blue-600 rounded-md transition-colors">
                                            <span class="material-icons text-sm">visibility</span> View
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!$notif['is_read']): ?>
                                        <a href="<?= BASE_URL ?>notifications/mark-read/<?= $notif['id'] ?>"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-md transition-colors">
                                            <span class="material-icons text-sm">check</span> Mark Read
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p>
            </div>
        </div>
    </main>
</div>
</body>
</html>
