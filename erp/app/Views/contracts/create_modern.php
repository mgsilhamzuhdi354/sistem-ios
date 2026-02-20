<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? __('contracts.create_title') ?> - IndoOcean ERP</title>

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#1e40af',
                            gold: '#fbbf24',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .recruitment-crew-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .recruitment-crew-card.selected {
            border-color: #10b981 !important;
            background: #f0fdf4 !important;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased" x-data="contractForm()">

    <!-- Sidebar (Include reusable partial) -->
    <?php $currentPage = 'contracts-create';
    include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen flex flex-col bg-gray-50">

        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 pt-6 sticky top-0 z-40">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <a href="<?= BASE_URL ?>contracts"
                        class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:text-blue-700 hover:border-blue-700 transition-all">
                        <span class="material-icons">arrow_back</span>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900"><?= __('contracts.create_title') ?></h1>
                        <p class="text-sm text-gray-500 mt-1"><?= __('contracts.create_subtitle') ?></p>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="flex items-center max-w-6xl mx-auto pb-6">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex flex-1 items-center gap-3 pb-4 border-b-2 transition-all"
                        :class="currentStep >= index + 1 ? 'border-blue-700' : 'border-gray-100'">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all"
                            :class="currentStep >= index + 1 ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white' : 'bg-gray-100 text-gray-400'">
                            <span x-text="index + 1"></span>
                        </div>
                        <span class="text-sm font-semibold transition-all hidden md:block"
                            :class="currentStep >= index + 1 ? 'text-blue-700' : 'text-gray-400'"
                            x-text="step.name">
                        </span>
                    </div>
                </template>
            </div>
        </header>

        <!-- Form Content -->
        <form action="<?= BASE_URL ?>contracts/store" method="POST" novalidate>
        <div class="flex-1 overflow-y-auto custom-scrollbar p-8">
            <div class="max-w-6xl mx-auto space-y-8">

                <!-- Step 1: Contract Info & Crew -->
                <section x-show="currentStep === 1" x-transition class="space-y-6">
                    <!-- Contract Information -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white flex items-center justify-center">
                                <span class="material-icons">description</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900"><?= __('contracts.contract_info') ?></h2>
                                <p class="text-sm text-gray-500"><?= __('contracts.contract_info_desc') ?></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    <?= __('contracts.contract_no') ?> <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="contract_no" readonly
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm cursor-not-allowed"
                                    value="<?= htmlspecialchars($contractNo ?? 'CTR-2026-0000') ?>">
                                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle"></i> <?= __('contracts.auto_number') ?></p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    <?= __('contracts.contract_type') ?> <span class="text-red-500">*</span>
                                </label>
                                <select name="contract_type" required
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                    <?php foreach ($contractTypes as $key => $label): ?>
                                        <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle"></i> <?= __('contracts.contract_type_hint') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Crew Assignment with Recruitment Integration -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white flex items-center justify-center">
                                <span class="material-icons">person_add</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900"><?= __('contracts.crew_assignment') ?></h2>
                                <p class="text-sm text-gray-500"><?= __('contracts.crew_assignment_desc') ?></p>
                            </div>
                        </div>

                        <?php if (!empty($recruitmentCrews)): ?>
                            <!-- Recruitment Crew Section -->
                            <div class="mb-6 p-6 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl">
                                <div class="flex items-center gap-2 mb-4">
                                    <i class="fas fa-star text-yellow-400 text-xl"></i>
                                    <h4 class="text-white font-bold text-base">🎯 <?= __('contracts.newly_approved') ?> (<?= count($recruitmentCrews) ?>)</h4>
                                </div>
                                <p class="text-white/90 text-sm mb-4"><?= __('contracts.select_crew_desc') ?></p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto custom-scrollbar">
                                    <?php foreach ($recruitmentCrews as $rc): ?>
                                        <div class="recruitment-crew-card bg-white p-4 rounded-xl cursor-pointer transition-all border-2 border-transparent hover:shadow-lg"
                                            @click="selectRecruitmentCrew(<?= htmlspecialchars(json_encode($rc)) ?>)">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1">
                                                    <div class="font-bold text-blue-900 mb-1 text-sm">
                                                        <?= htmlspecialchars($rc['full_name']) ?>
                                                    </div>
                                                    <div class="text-xs text-gray-500 font-mono">
                                                        ID: <?= htmlspecialchars($rc['employee_id']) ?>
                                                    </div>
                                                </div>
                                                <?php if ($rc['days_since_approval'] <= 7): ?>
                                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-md font-bold">NEW</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-xs text-gray-600 mb-2">
                                                <i class="fas fa-briefcase text-amber-500"></i>
                                                <?= htmlspecialchars($rc['rank_name'] ?? 'Not assigned') ?>
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                <i class="fas fa-calendar-check"></i>
                                                <?= $rc['days_since_approval'] ?> <?= __('contracts.days_ago') ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Manual Crew Input -->
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <h5 class="text-sm font-bold text-gray-700 mb-4">
                                <i class="fas fa-keyboard"></i> <?= __('contracts.manual_entry') ?>
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                        Crew ID <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="crew_id" x-model="formData.crewId" required
                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none"
                                        placeholder="<?= __('contracts.enter_crew_id') ?>">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                        Crew Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="crew_name" x-model="formData.crewName" required
                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none"
                                        placeholder="<?= __('contracts.crew_full_name') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Vessel, Client, Rank -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Vessel <span class="text-red-500">*</span>
                                </label>
                                <select name="vessel_id" required
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                    <option value="">-- <?= __('contracts.select_vessel') ?> --</option>
                                    <?php foreach ($vessels as $v): ?>
                                        <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Client / Principal <span class="text-red-500">*</span>
                                </label>
                                <select name="client_id" required
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                    <option value="">-- <?= __('contracts.select_client') ?> --</option>
                                    <?php foreach ($clients as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Rank / Position <span class="text-red-500">*</span>
                                </label>
                                <select name="rank_id" x-model="formData.rankId" required
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                    <option value="">-- <?= __('contracts.select_position') ?> --</option>
                                    <?php foreach ($ranks as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?> (<?= ucfirst($r['department']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Step 2: Period & Salary -->
                <section x-show="currentStep === 2" x-transition class="space-y-6">
                    <!-- Contract Period -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-600 to-pink-600 text-white flex items-center justify-center">
                                <span class="material-icons">event</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900"><?= __('contracts.contract_period') ?></h2>
                                <p class="text-sm text-gray-500"><?= __('contracts.contract_period_desc') ?></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Sign On Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="sign_on_date" x-model="formData.signOnDate" required @change="calculateDuration()"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle"></i> <?= __('contracts.sign_on_hint') ?></p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Sign Off Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="sign_off_date" x-model="formData.signOffDate" required @change="calculateDuration()"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle"></i> <?= __('contracts.sign_off_hint') ?></p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Duration (Months) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="duration_months" x-model="formData.duration" required min="1" max="36"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none"
                                    placeholder="9">
                                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle"></i> <?= __('contracts.auto_from_dates') ?></p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Embarkation Port
                                </label>
                                <input type="text" name="embarkation_port"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none"
                                    placeholder="e.g. Jakarta, Indonesia">
                                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle"></i> <?= __('contracts.embarkation_hint') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Structure -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-600 to-orange-600 text-white flex items-center justify-center">
                                <span class="material-icons">payments</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900"><?= __('contracts.salary_structure') ?></h2>
                                <p class="text-sm text-gray-500"><?= __('contracts.salary_structure_desc') ?></p>
                            </div>
                        </div>

                        <!-- Currency Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Currency</label>
                                <select name="currency_id" x-model="formData.currencyId" @change="toggleExchangeRate()"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                    <?php foreach ($currencies as $cur): ?>
                                        <option value="<?= $cur['id'] ?>" data-code="<?= $cur['code'] ?>"><?= $cur['code'] ?> - <?= $cur['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div x-show="showExchangeRate" x-cloak>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Exchange Rate to USD
                                </label>
                                <input type="text" name="exchange_rate"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none"
                                    placeholder="Contoh: 15800 (1 USD = Rp15.800)">
                                <p class="text-xs text-gray-400 mt-2"><?= __('contracts.exchange_rate_hint') ?></p>
                            </div>
                        </div>

                        <!-- Client Rate -->
                        <div class="p-6 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-200 mb-6">
                            <label class="block text-xs font-bold text-emerald-700 uppercase tracking-wider mb-3">
                                <i class="fas fa-hand-holding-usd"></i> Client Rate (Harga dari Client)
                            </label>
                            <input type="text" name="client_rate" @input="formatCurrency($event.target); calculateTotals()"
                                class="w-full bg-transparent border-none p-0 text-3xl font-bold text-emerald-900 focus:ring-0 outline-none"
                                placeholder="0">
                            <p class="text-xs text-emerald-600 mt-2"><?= __('contracts.client_rate_hint') ?></p>
                        </div>

                        <!-- All Salary Components -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="p-5 bg-gray-50 rounded-xl border border-gray-100">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Basic Salary</label>
                                <input type="text" name="basic_salary" @input="formatCurrency($event.target); calculateTotals()"
                                    class="w-full bg-transparent border-none p-0 text-2xl font-bold text-gray-900 focus:ring-0 outline-none"
                                    placeholder="0">
                                <p class="text-xs text-gray-400 mt-2"><?= __('contracts.basic_salary_hint') ?></p>
                            </div>

                            <div class="p-5 bg-gray-50 rounded-xl border border-gray-100">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Overtime Allowance</label>
                                <input type="text" name="overtime_allowance" @input="formatCurrency($event.target); calculateTotals()"
                                    class="w-full bg-transparent border-none p-0 text-2xl font-bold text-gray-900 focus:ring-0 outline-none"
                                    placeholder="0">
                                <p class="text-xs text-gray-400 mt-2"><?= __('contracts.overtime_hint') ?></p>
                            </div>

                            <div class="p-5 bg-gray-50 rounded-xl border border-gray-100">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Leave Pay</label>
                                <input type="text" name="leave_pay" @input="formatCurrency($event.target); calculateTotals()"
                                    class="w-full bg-transparent border-none p-0 text-2xl font-bold text-gray-900 focus:ring-0 outline-none"
                                    placeholder="0">
                            </div>

                            <div class="p-5 bg-gray-50 rounded-xl border border-gray-100">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Bonus</label>
                                <input type="text" name="bonus" @input="formatCurrency($event.target); calculateTotals()"
                                    class="w-full bg-transparent border-none p-0 text-2xl font-bold text-gray-900 focus:ring-0 outline-none"
                                    placeholder="0">
                            </div>

                            <div class="md:col-span-2 p-5 bg-gray-50 rounded-xl border border-gray-100">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Other Allowance</label>
                                <input type="text" name="other_allowance" @input="formatCurrency($event.target); calculateTotals()"
                                    class="w-full bg-transparent border-none p-0 text-2xl font-bold text-gray-900 focus:ring-0 outline-none"
                                    placeholder="0">
                                <p class="text-xs text-gray-400 mt-2"><?= __('contracts.other_allowance_hint') ?></p>
                            </div>
                        </div>

                        <!-- Real-time Total Salary & Profit Calculator -->
                        <div class="p-6 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-white">
                                <div>
                                    <div class="text-xs opacity-90 mb-2"><i class="fas fa-calculator"></i> Total Salary</div>
                                    <div class="text-3xl font-bold" x-text="formatMoney(totalSalary)">$0</div>
                                    <div class="text-xs opacity-80 mt-1">Basic + Overtime + Leave + Bonus + Other</div>
                                </div>
                                <div>
                                    <div class="text-xs opacity-90 mb-2"><i class="fas fa-money-bill-wave"></i> Client Rate</div>
                                    <div class="text-3xl font-bold" x-text="formatMoney(clientRate)">$0</div>
                                    <div class="text-xs opacity-80 mt-1"><?= __('contracts.from_client') ?></div>
                                </div>
                                <div :class="profit > 0 ? 'bg-emerald-500/20 p-4 rounded-xl' : profit < 0 ? 'bg-red-500/20 p-4 rounded-xl' : ''">
                                    <div class="text-xs opacity-90 mb-2"><i class="fas fa-chart-line"></i> Profit/Loss</div>
                                    <div class="text-3xl font-bold" x-text="formatMoney(profit)">$0</div>
                                    <div class="text-xs opacity-80 mt-1">Client Rate - Total Salary</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Step 3: Tax & Deductions -->
                <section x-show="currentStep === 3" x-transition class="space-y-6">
                    <!-- Tax Settings -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-600 to-pink-600 text-white flex items-center justify-center">
                                <span class="material-icons">percent</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900"><?= __('contracts.tax_settings') ?></h2>
                                <p class="text-sm text-gray-500"><?= __('contracts.tax_settings_desc') ?></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tax Type</label>
                                <select name="tax_type"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                    <?php foreach ($taxTypes as $key => $label): ?>
                                        <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">NPWP Number</label>
                                <input type="text" name="npwp_number" @input="formatNPWP($event.target)" maxlength="20"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none"
                                    placeholder="xx.xxx.xxx.x-xxx.xxx">
                                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle"></i> Format: xx.xxx.xxx.x-xxx.xxx</p>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-600 to-red-600 text-white flex items-center justify-center">
                                    <span class="material-icons">remove_circle</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900"><?= __('contracts.deductions') ?></h2>
                                    <p class="text-sm text-gray-500"><?= __('contracts.deductions_desc') ?></p>
                                </div>
                            </div>
                            <button type="button" @click="addDeduction()"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-all flex items-center gap-2">
                                <span class="material-icons text-sm">add</span>
                                <?= __('contracts.add_deduction') ?>
                            </button>
                        </div>

                        <div class="space-y-4" x-ref="deductionsContainer">
                            <!-- Deduction rows will be added here dynamically -->
                        </div>

                        <!-- Total Deductions Summary -->
                        <div x-show="totalDeductions > 0" class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-yellow-900">
                                    <i class="fas fa-calculator"></i> <?= __('contracts.total_deductions') ?>:
                                </span>
                                <span class="text-xl font-bold text-yellow-900" x-text="formatMoney(totalDeductions)">$0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-600 to-gray-800 text-white flex items-center justify-center">
                                <span class="material-icons">note</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900"><?= __('common.notes') ?></h2>
                                <p class="text-sm text-gray-500"><?= __('contracts.notes_desc') ?></p>
                            </div>
                        </div>

                        <textarea name="notes" rows="4"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none resize-none"
                            placeholder="Additional notes..."></textarea>
                    </div>
                </section>

                <!-- Step 4: Review -->
                <section x-show="currentStep === 4" x-transition
                    class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-600 to-emerald-600 text-white flex items-center justify-center">
                            <span class="material-icons">check_circle</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Review & Submit</h2>
                            <p class="text-sm text-gray-500">Review all details before submitting</p>
                        </div>
                    </div>

                    <div class="p-6 bg-blue-50 border border-blue-200 rounded-xl">
                        <p class="text-sm text-blue-800">
                            <strong>Note:</strong> Once submitted, this contract will be sent for approval. Make sure
                            all information is correct before proceeding.
                        </p>
                    </div>
                </section>
            </div>
        </div>

        <!-- Footer Actions -->
        <footer class="bg-white border-t border-gray-200 px-8 py-5 flex items-center justify-between shadow-lg sticky bottom-0 z-40">
            <button type="button" @click="if(currentStep > 1) currentStep--"
                class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors uppercase tracking-wide"
                :class="currentStep === 1 ? 'invisible' : ''">
                <span class="material-icons text-sm">arrow_back</span>
                <?= __('common.previous') ?>
            </button>

            <div class="flex items-center gap-4">
                <a href="<?= BASE_URL ?>contracts"
                    class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                    <?= __('common.cancel') ?>
                </a>

                <button type="button" @click="nextStep()"
                    x-show="currentStep < totalSteps"
                    class="px-8 py-3 text-sm font-bold text-white bg-blue-700 hover:bg-blue-800 rounded-xl shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
                    <?= __('contracts.next_step') ?>
                    <span class="material-icons text-lg">arrow_forward</span>
                </button>

                <button type="button" x-show="currentStep === totalSteps" @click="submitContract()"
                    class="px-8 py-3 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2">
                    <span class="material-icons text-lg">send</span>
                    <?= __('contracts.submit_contract') ?>
                </button>
            </div>
        </footer>

        </form>

    </main>

    <script>
        function contractForm() {
            return {
                currentStep: 1,
                totalSteps: 4,
                steps: [
                    { name: 'Contract & Crew' },
                    { name: 'Period & Salary' },
                    { name: 'Tax & Deductions' },
                    { name: 'Review' }
                ],
                formData: {
                    crewId: '',
                    crewName: '',
                    rankId: '',
                    signOnDate: '',
                    signOffDate: '',
                    duration: 9,
                    currencyId: 1
                },
                showExchangeRate: false,
                currencyMap: {
                    <?php foreach ($currencies as $cur): ?>
                        '<?= $cur['id'] ?>': '<?= $cur['code'] ?>',
                    <?php endforeach; ?>
                },
                totalSalary: 0,
                clientRate: 0,
                profit: 0,
                totalDeductions: 0,

                selectRecruitmentCrew(crew) {
                    this.formData.crewId = crew.id;
                    this.formData.crewName = crew.full_name;
                    if (crew.current_rank_id) {
                        this.formData.rankId = crew.current_rank_id;
                    }

                    // Visual feedback
                    document.querySelectorAll('.recruitment-crew-card').forEach(card => {
                        card.classList.remove('selected');
                    });
                    event.currentTarget.classList.add('selected');

                    // Smooth scroll
                    setTimeout(() => {
                        document.querySelector('input[name="crew_id"]').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                },

                getCurrencySymbol() {
                    const code = this.currencyMap[this.formData.currencyId] || 'USD';
                    const symbols = {
                        'USD': '$',
                        'IDR': 'Rp ',
                        'EUR': '€',
                        'SGD': 'S$',
                        'GBP': '£',
                        'JPY': '¥',
                        'MYR': 'RM ',
                        'AUD': 'A$',
                        'PHP': '₱',
                    };
                    return symbols[code] || code + ' ';
                },

                getCurrencyLocale() {
                    const code = this.currencyMap[this.formData.currencyId] || 'USD';
                    return code === 'IDR' ? 'id-ID' : 'en-US';
                },

                formatCurrency(input) {
                    let value = input.value.replace(/[^0-9]/g, '');
                    if (value) {
                        input.value = parseInt(value).toLocaleString(this.getCurrencyLocale());
                    }
                },

                parseCurrency(value) {
                    if (typeof value === 'string') {
                        // Remove both commas and dots (thousand separators for en-US and id-ID)
                        return parseFloat(value.replace(/[.,]/g, '')) || 0;
                    }
                    return value || 0;
                },

                formatNPWP(input) {
                    let value = input.value.replace(/[^0-9]/g, '');
                    if (value.length > 0) {
                        let formatted = '';
                        if (value.length >= 2) formatted = value.substr(0, 2);
                        if (value.length >= 3) formatted += '.' + value.substr(2, 3);
                        if (value.length >= 6) formatted += '.' + value.substr(5, 3);
                        if (value.length >= 9) formatted += '.' + value.substr(8, 1);
                        if (value.length >= 10) formatted += '-' + value.substr(9, 3);
                        if (value.length >= 13) formatted += '.' + value.substr(12, 3);
                        input.value = formatted;
                    }
                },

                calculateTotals() {
                    const basic = this.parseCurrency(document.querySelector('input[name="basic_salary"]')?.value || 0);
                    const overtime = this.parseCurrency(document.querySelector('input[name="overtime_allowance"]')?.value || 0);
                    const leave = this.parseCurrency(document.querySelector('input[name="leave_pay"]')?.value || 0);
                    const bonus = this.parseCurrency(document.querySelector('input[name="bonus"]')?.value || 0);
                    const other = this.parseCurrency(document.querySelector('input[name="other_allowance"]')?.value || 0);
                    
                    this.totalSalary = basic + overtime + leave + bonus + other;
                    this.clientRate = this.parseCurrency(document.querySelector('input[name="client_rate"]')?.value || 0);
                    this.profit = this.clientRate - this.totalSalary;

                    // Calculate deductions
                    const deductionInputs = document.querySelectorAll('input[name="deduction_amount[]"]');
                    this.totalDeductions = 0;
                    deductionInputs.forEach(input => {
                        this.totalDeductions += this.parseCurrency(input.value);
                    });
                },

                formatMoney(value) {
                    const symbol = this.getCurrencySymbol();
                    const locale = this.getCurrencyLocale();
                    if (value < 0) {
                        return '-' + symbol + Math.abs(value).toLocaleString(locale);
                    }
                    return symbol + value.toLocaleString(locale);
                },

                calculateDuration() {
                    if (this.formData.signOnDate && this.formData.signOffDate) {
                        const start = new Date(this.formData.signOnDate);
                        const end = new Date(this.formData.signOffDate);
                        
                        if (end <= start) {
                            alert('Sign Off Date harus setelah Sign On Date!');
                            this.formData.signOffDate = '';
                            this.formData.duration = '';
                            return;
                        }
                        
                        const months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
                        this.formData.duration = Math.max(1, months);
                    }
                },

                toggleExchangeRate() {
                    const code = this.currencyMap[this.formData.currencyId] || 'USD';
                    this.showExchangeRate = (code !== 'USD');
                    // Reformat all salary inputs when currency changes
                    this.reformatAllInputs();
                    this.calculateTotals();
                },

                reformatAllInputs() {
                    const fields = ['client_rate', 'basic_salary', 'overtime_allowance', 'leave_pay', 'bonus', 'other_allowance'];
                    fields.forEach(name => {
                        const input = document.querySelector(`input[name="${name}"]`);
                        if (input && input.value) {
                            this.formatCurrency(input);
                        }
                    });
                    // Also reformat deduction amounts
                    document.querySelectorAll('input[name="deduction_amount[]"]').forEach(input => {
                        if (input.value) {
                            this.formatCurrency(input);
                        }
                    });
                },

                addDeduction() {
                    const container = this.$refs.deductionsContainer;
                    const row = document.createElement('div');
                    row.className = 'grid grid-cols-1 md:grid-cols-12 gap-4 items-end p-4 bg-gray-50 rounded-xl border border-gray-200';
                    row.innerHTML = `
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Type</label>
                            <select name="deduction_type[]" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                <?php foreach ($deductionTypes as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                            <input type="text" name="deduction_desc[]" placeholder="Description"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Amount</label>
                            <input type="text" name="deduction_amount[]" placeholder="100000"
                                oninput="contractFormInstance.formatCurrency(this); contractFormInstance.calculateTotals();"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Frequency</label>
                            <select name="deduction_recurring[]" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-700 transition-all outline-none">
                                <option value="monthly">Monthly</option>
                                <option value="onetime">One-time</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 flex justify-end">
                            <button type="button" onclick="this.closest('div.grid').remove(); contractFormInstance.calculateTotals();"
                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all">
                                <span class="material-icons text-sm">delete</span>
                            </button>
                        </div>
                    `;
                    container.appendChild(row);
                    setTimeout(() => {
                        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 100);
                },

                nextStep() {
                    if (this.currentStep < this.totalSteps) {
                        this.currentStep++;
                    }
                },

                submitContract() {
                    const form = this.$root.querySelector('form');
                    if (!form) { alert('Form not found'); return; }
                    
                    // Step 1 validations
                    if (!this.formData.crewId || !this.formData.crewName) {
                        this.currentStep = 1;
                        this.$nextTick(() => alert('Crew ID dan Crew Name wajib diisi!'));
                        return;
                    }
                    
                    const vesselId = form.querySelector('select[name="vessel_id"]')?.value;
                    const clientId = form.querySelector('select[name="client_id"]')?.value;
                    const rankId = form.querySelector('select[name="rank_id"]')?.value;
                    
                    if (!vesselId || !clientId || !rankId) {
                        this.currentStep = 1;
                        this.$nextTick(() => alert('Vessel, Client, dan Rank wajib dipilih!'));
                        return;
                    }
                    
                    // Step 2 validations
                    if (!this.formData.signOnDate || !this.formData.signOffDate) {
                        this.currentStep = 2;
                        this.$nextTick(() => alert('Sign On Date dan Sign Off Date wajib diisi!'));
                        return;
                    }
                    
                    // Add hidden input for submit_approval flag
                    let approvalInput = form.querySelector('input[name="submit_approval"]');
                    if (!approvalInput) {
                        approvalInput = document.createElement('input');
                        approvalInput.type = 'hidden';
                        approvalInput.name = 'submit_approval';
                        form.appendChild(approvalInput);
                    }
                    approvalInput.value = '1';
                    
                    // Submit the form
                    form.submit();
                },

                init() {
                    this.toggleExchangeRate();
                    this.calculateTotals();
                    
                    // Store instance globally for access from dynamic HTML
                    window.contractFormInstance = this;
                }
            }
        }
    </script>

</body>

</html>