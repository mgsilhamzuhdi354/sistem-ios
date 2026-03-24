<?php
/**
 * Modern Suspend Contract Confirmation Page
 * PT Indo Ocean - ERP System
 */
$currentPage = $currentPage ?? 'contracts';
$contract = $contract ?? [];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suspend Contract | PT Indo Ocean ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#6366F1', secondary: '#D4AF37' }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 text-slate-800 antialiased">
    <div class="flex h-screen overflow-hidden">
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <main class="ml-64 flex-1 overflow-y-auto bg-gray-100">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-8 py-6 animate-fade-in">
                <div class="flex items-center gap-4">
                    <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>" class="rounded-lg bg-gray-100 p-2 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition-colors">
                        <span class="material-icons-outlined text-xl">arrow_back</span>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Suspend Kontrak</h1>
                        <p class="text-sm text-gray-500 mt-0.5"><?= htmlspecialchars($contract['contract_no'] ?? '') ?> — <?= htmlspecialchars($contract['crew_name'] ?? '') ?></p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-8 max-w-2xl mx-auto animate-fade-in">
                <div class="rounded-2xl bg-white shadow-lg border border-gray-100 overflow-hidden">
                    <!-- Warning Banner -->
                    <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-8 py-6">
                        <div class="flex items-center gap-4 text-white">
                            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-icons-outlined text-3xl">pause_circle</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold">Suspend Kontrak (OFF)</h2>
                                <p class="text-orange-100 text-sm mt-1">Kontrak akan di-nonaktifkan sementara.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Impact Info -->
                    <div class="px-8 py-6 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="material-icons-outlined text-orange-500 text-lg">info</span>
                            Dampak Suspend:
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <span class="material-icons-outlined text-red-400 text-base mt-0.5">remove_circle</span>
                                Payroll kru ini <strong>tidak akan di-generate</strong> lagi
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-icons-outlined text-red-400 text-base mt-0.5">remove_circle</span>
                                Pendapatan dan margin vessel/client <strong>otomatis berkurang</strong>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-icons-outlined text-red-400 text-base mt-0.5">remove_circle</span>
                                Dashboard active contracts <strong>otomatis berkurang</strong>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-icons-outlined text-green-500 text-base mt-0.5">check_circle</span>
                                Kontrak bisa <strong>di-reactivate (ON)</strong> kapan saja
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-icons-outlined text-green-500 text-base mt-0.5">check_circle</span>
                                Semua data <strong>tetap tersimpan</strong>, tidak dihapus
                            </li>
                        </ul>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="<?= BASE_URL ?>contracts/suspend/<?= $contract['id'] ?>">
                        <div class="px-8 py-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Suspend (opsional)</label>
                            <textarea name="suspend_reason" rows="3" placeholder="Contoh: Kontrak habis, menunggu perpanjangan..." 
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500 placeholder-gray-400"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 px-8 py-5 bg-gray-50 border-t border-gray-100">
                            <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>" 
                                class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit" 
                                class="rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-2.5 text-sm font-bold text-white hover:from-orange-600 hover:to-amber-600 transition-all shadow-lg shadow-orange-500/20 flex items-center gap-2">
                                <span class="material-icons-outlined text-lg">pause_circle</span>
                                Suspend Kontrak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
