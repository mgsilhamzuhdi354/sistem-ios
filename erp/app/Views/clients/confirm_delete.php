<?php
/**
 * Client Delete Confirmation Page
 * Pure HTML - NO JavaScript needed
 */
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Hapus Client - PT Indo Ocean ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#EAB308",
                        navy: "#0f172a",
                    },
                    fontFamily: { sans: ["Inter", "sans-serif"] },
                },
            },
        };
    </script>
</head>
<body class="bg-slate-100 font-sans flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden">
        <!-- Warning Header -->
        <div class="bg-red-50 border-b border-red-100 p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                <i class="ph-bold ph-warning text-3xl text-red-600"></i>
            </div>
            <h1 class="text-xl font-bold text-red-700">Konfirmasi Hapus Client</h1>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4">
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                <p class="text-sm text-slate-500 mb-1">Client yang akan dihapus:</p>
                <p class="text-lg font-bold text-navy"><?= htmlspecialchars($client['name']) ?></p>
                <?php if (!empty($client['short_name'])): ?>
                    <p class="text-sm text-slate-400"><?= htmlspecialchars($client['short_name']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Info tentang data terkait -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="font-bold text-amber-800 text-sm mb-2">⚠️ Data yang akan ikut terhapus:</p>
                <ul class="text-sm text-amber-700 space-y-1">
                    <li>• <strong><?= $vesselCount ?></strong> kapal terkait</li>
                    <li>• <strong><?= $contractCount ?></strong> kontrak aktif</li>
                    <li>• Semua data payroll, dokumen, dan invoice terkait</li>
                </ul>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-sm font-bold text-red-700">❌ Tindakan ini TIDAK BISA dibatalkan!</p>
            </div>

            <!-- Action Buttons - Pure HTML links, NO JavaScript -->
            <div class="flex gap-3 pt-2">
                <a href="<?= BASE_URL ?>clients"
                    class="flex-1 text-center px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm transition-colors border border-slate-200">
                    ← Batal, Kembali
                </a>
                <a href="<?= BASE_URL ?>clients/delete/<?= $client['id'] ?>"
                    class="flex-1 text-center px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm transition-colors shadow-lg shadow-red-200">
                    🗑️ Ya, Hapus Permanen
                </a>
            </div>
        </div>
    </div>
</body>
</html>
