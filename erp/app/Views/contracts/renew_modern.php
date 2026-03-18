<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Renew Contract' ?> - IndoOcean ERP</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { blue: '#1e40af', gold: '#fbbf24' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">

    <?php $currentPage = 'contracts';
    include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="ml-64 min-h-screen flex flex-col bg-gray-50">

        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-6 sticky top-0 z-40">
            <div class="flex items-center justify-between max-w-5xl mx-auto">
                <div class="flex items-center gap-4">
                    <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>"
                        class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:text-blue-700 hover:border-blue-700 transition-all">
                        <span class="material-icons">arrow_back</span>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">🔄 Renew Contract</h1>
                        <p class="text-sm text-gray-500 mt-1">Create a new contract based on <span class="font-semibold text-blue-700"><?= htmlspecialchars($contract['contract_no']) ?></span></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-full">
                        <span class="material-icons text-xs align-middle">autorenew</span> Renewal
                    </span>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-5xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                    <!-- Left: Previous Contract Summary -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 text-white flex items-center justify-center">
                                    <span class="material-icons text-lg">description</span>
                                </div>
                                <h2 class="text-base font-bold text-gray-900">📋 Previous Contract</h2>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                    <span class="text-xs text-gray-500 font-medium">Contract No</span>
                                    <span class="text-sm font-bold text-gray-900"><?= htmlspecialchars($contract['contract_no']) ?></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                    <span class="text-xs text-gray-500 font-medium">Crew Name</span>
                                    <span class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($contract['crew_name']) ?></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                    <span class="text-xs text-gray-500 font-medium">Rank</span>
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($contract['rank_name'] ?? '-') ?></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                    <span class="text-xs text-gray-500 font-medium">Vessel</span>
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($contract['vessel_name'] ?? '-') ?></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                    <span class="text-xs text-gray-500 font-medium">Sign Off Date</span>
                                    <span class="text-sm text-gray-700"><?= $contract['sign_off_date'] ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?></span>
                                </div>
                            </div>

                            <!-- Monthly Salary Highlight -->
                            <div class="mt-5 p-4 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl border border-amber-200">
                                <div class="text-xs text-amber-700 font-semibold mb-1">Monthly Salary</div>
                                <div class="text-2xl font-bold text-amber-800">
                                    $<?= number_format($contract['total_monthly'] ?? 0, 2) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Info Note -->
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl flex gap-3">
                            <span class="material-icons text-blue-500 mt-0.5">info</span>
                            <p class="text-sm text-blue-700">
                                Salary, tax settings, and allowances will be automatically copied from the previous contract.
                            </p>
                        </div>
                    </div>

                    <!-- Right: New Contract Form -->
                    <div class="lg:col-span-3">
                        <form method="POST" action="<?= BASE_URL ?>contracts/renew/<?= $contract['id'] ?>">
                            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white flex items-center justify-center">
                                        <span class="material-icons text-lg">add_circle</span>
                                    </div>
                                    <h2 class="text-base font-bold text-gray-900">✨ New Contract Details</h2>
                                </div>

                                <!-- Contract Number -->
                                <div class="mb-5">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">New Contract Number</label>
                                    <input type="text" name="contract_no" readonly
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono cursor-not-allowed"
                                        value="<?= htmlspecialchars($newContractNo ?? '') ?>">
                                </div>

                                <!-- Vessel & Client -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Vessel</label>
                                        <select name="vessel_id"
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                            <?php foreach ($vessels as $v): ?>
                                                <option value="<?= $v['id'] ?>" <?= ($contract['vessel_id'] ?? '') == $v['id'] ? 'selected' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Client</label>
                                        <select name="client_id"
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                            <?php foreach ($clients as $c): ?>
                                                <option value="<?= $c['id'] ?>" <?= ($contract['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Rank -->
                                <div class="mb-5">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rank</label>
                                    <select name="rank_id"
                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                        <?php foreach ($ranks as $r): ?>
                                            <option value="<?= $r['id'] ?>" <?= ($contract['rank_id'] ?? '') == $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <hr class="my-6 border-gray-100">

                                <!-- Period -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">New Sign On Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="sign_on_date" id="signOnDate" required
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none"
                                            value="<?= date('Y-m-d', strtotime($contract['sign_off_date'] ?? 'now')) ?>">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Duration (Months) <span class="text-red-500">*</span></label>
                                        <input type="number" name="duration_months" id="durationMonths" min="1" max="36" required
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none"
                                            value="<?= $contract['duration_months'] ?? 9 ?>">
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">New Sign Off Date</label>
                                    <input type="date" name="sign_off_date" id="signOffDate"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm cursor-not-allowed"
                                        readonly>
                                    <p class="text-xs text-gray-400 mt-1.5">
                                        <span class="material-icons text-xs align-middle">auto_awesome</span>
                                        Auto-calculated from Sign On Date + Duration
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                                    <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>"
                                        class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                                        Cancel
                                    </a>
                                    <button type="submit"
                                        class="px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2">
                                        <span class="material-icons text-lg">autorenew</span>
                                        Create Renewal Contract
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <script>
        const signOnInput = document.getElementById('signOnDate');
        const durationInput = document.getElementById('durationMonths');
        const signOffInput = document.getElementById('signOffDate');

        function calculateSignOff() {
            const signOn = signOnInput.value;
            const months = parseInt(durationInput.value) || 9;
            if (signOn) {
                const date = new Date(signOn);
                date.setMonth(date.getMonth() + months);
                signOffInput.value = date.toISOString().split('T')[0];
            }
        }

        signOnInput.addEventListener('change', calculateSignOff);
        durationInput.addEventListener('change', calculateSignOff);
        durationInput.addEventListener('input', calculateSignOff);
        calculateSignOff();
    </script>

</body>

</html>
