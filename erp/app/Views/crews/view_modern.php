<?php
/**
 * Modern Crew Profile View
 * Professional crew detail page with enhanced UI
 */
$currentPage = 'crews';
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('crews.crew_profile') ?> - IndoOcean ERP</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#0F172A",
                        "background-light": "#F8FAFC",
                        "background-dark": "#020617",
                        "accent-gold": "#B59410",
                        "status-available": "#10B981"
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                }
            }
        };
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-background-light text-slate-900 min-h-screen"
    x-data="{ showSkillModal: false, showUploadModal: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar - Use existing modern sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto ml-0 lg:ml-64">
            <!-- Top Header -->
            <header
                class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex items-center space-x-4">
                    <h1 class="text-lg font-bold text-primary"><?= __('crews.crew_profile') ?></h1>
                </div>
                <div class="flex items-center space-x-6">
                    <button class="relative text-slate-500 hover:text-primary transition-colors">
                        <span class="material-icons">notifications</span>
                        <span
                            class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                </div>
            </header>

            <!-- Profile Content -->
            <div class="p-8 max-w-7xl mx-auto space-y-6">
                <!-- Profile Header Card -->
                <div
                    class="flex flex-col md:flex-row md:items-end justify-between bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <?php if (!empty($crew['photo'])): ?>
                                <img alt="Profile"
                                    class="w-28 h-28 rounded-full border-4 border-slate-50 shadow-sm object-cover"
                                    src="<?= BASE_URL . $crew['photo'] ?>">
                            <?php else: ?>
                                <div
                                    class="w-28 h-28 rounded-full border-4 border-slate-50 shadow-sm bg-blue-600 flex items-center justify-center text-white text-4xl font-bold">
                                    <?= strtoupper(substr($crew['full_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>

                            <?php
                            $statusColors = [
                                'AVAILABLE' => 'bg-green-500',
                                'ON_BOARD' => 'bg-blue-500',
                                'STANDBY' => 'bg-yellow-500',
                                'ON_LEAVE' => 'bg-purple-500',
                                'UNAVAILABLE' => 'bg-red-500'
                            ];
                            $statusColor = $statusColors[$crew['status']] ?? 'bg-gray-500';
                            ?>
                            <div
                                class="absolute bottom-1 right-1 w-6 h-6 <?= $statusColor ?> border-4 border-white rounded-full">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <h2 class="text-3xl font-bold text-primary">
                                <?= htmlspecialchars($crew['full_name']) ?>
                            </h2>
                            <div class="flex items-center space-x-3">
                                <span
                                    class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-mono font-medium">
                                    <?= htmlspecialchars($crew['employee_id']) ?>
                                </span>
                                <?php
                                $statusBadges = [
                                    'AVAILABLE' => 'bg-emerald-50 text-emerald-600',
                                    'ON_BOARD' => 'bg-blue-50 text-blue-600',
                                    'STANDBY' => 'bg-yellow-50 text-yellow-600',
                                    'ON_LEAVE' => 'bg-purple-50 text-purple-600',
                                    'UNAVAILABLE' => 'bg-red-50 text-red-600'
                                ];
                                $badgeClass = $statusBadges[$crew['status']] ?? 'bg-gray-50 text-gray-600';
                                ?>
                                <span
                                    class="<?= $badgeClass ?> px-3 py-1 rounded-full text-[10px] font-bold tracking-wider">
                                    <?= str_replace('_', ' ', $crew['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6 md:mt-0">
                        <a href="<?= BASE_URL ?>crews/<?= $crew['id'] ?>/edit"
                            class="flex items-center space-x-2 px-5 py-2.5 bg-accent-gold text-white rounded-xl font-semibold text-sm hover:opacity-90 transition-all shadow-lg shadow-yellow-600/10">
                            <span class="material-icons text-lg">edit</span>
                            <span><?= __('crews.edit_profile') ?></span>
                        </a>
                    </div>
                </div>

                <!-- Info Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Personal Info Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center space-x-2 mb-6">
                            <span class="material-icons text-slate-400 text-xl">person</span>
                            <h3 class="font-bold text-sm text-slate-500 uppercase tracking-wider"><?= __('crews.personal_info') ?></h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <span class="text-sm text-slate-400"><?= __('crews.gender') ?></span>
                                <span class="text-sm font-semibold">
                                    <?= $crew['gender'] === 'M' ? __('crews.male') : __('crews.female') ?>
                                </span>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <span class="text-sm text-slate-400"><?= __('crews.date_of_birth') ?></span>
                                <span class="text-sm font-semibold">
                                    <?= !empty($crew['date_of_birth']) ? date('d M Y', strtotime($crew['date_of_birth'])) : '-' ?>
                                </span>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <span class="text-sm text-slate-400"><?= __('crews.place_of_birth') ?></span>
                                <span class="text-sm font-semibold">
                                    <?= htmlspecialchars($crew['place_of_birth'] ?? '-') ?>
                                </span>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <span class="text-sm text-slate-400"><?= __('crews.nationality_label') ?></span>
                                <span class="text-sm font-semibold">
                                    <?= htmlspecialchars($crew['nationality'] ?? 'Indonesia') ?>
                                </span>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <span class="text-sm text-slate-400"><?= __('crews.religion') ?></span>
                                <span class="text-sm font-semibold">
                                    <?= htmlspecialchars($crew['religion'] ?? '-') ?>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-slate-400"><?= __('common.status') ?></span>
                                <span class="text-sm font-semibold">
                                    <?= ucfirst($crew['marital_status'] ?? '-') ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Experience Column -->
                    <div class="space-y-6">
                        <!-- Contact Card -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                            <div class="flex items-center space-x-2 mb-6">
                                <span class="material-icons text-slate-400 text-xl">contact_phone</span>
                                <h3 class="font-bold text-sm text-slate-500 uppercase tracking-wider"><?= __('crews.contact_info') ?></h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between border-b border-slate-50 pb-2">
                                    <span class="text-sm text-slate-400">Email</span>
                                    <span class="text-sm font-semibold">
                                        <?= htmlspecialchars($crew['email'] ?? '-') ?>
                                    </span>
                                </div>
                                <div class="flex justify-between border-b border-slate-50 pb-2">
                                    <span class="text-sm text-slate-400"><?= __('crews.phone') ?></span>
                                    <span class="text-sm font-semibold">
                                        <?= htmlspecialchars($crew['phone'] ?? '-') ?>
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-slate-400"><?= __('crews.city') ?></span>
                                    <span class="text-sm font-semibold">
                                        <?= htmlspecialchars($crew['city'] ?? '-') ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Experience Card -->
                        <div
                            class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl shadow-sm border border-blue-100">
                            <div class="flex items-center space-x-2 mb-4">
                                <span class="material-icons text-blue-600 text-xl">sailing</span>
                                <h3 class="font-bold text-sm text-slate-600 uppercase tracking-wider"><?= __('crews.experience') ?></h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-3xl font-bold text-blue-600">
                                        <?= count($experiences) ?>
                                    </p>
                                    <p class="text-[10px] text-slate-500 uppercase font-semibold"><?= __('crews.years_experience') ?></p>
                                </div>
                                <div>
                                    <?php
                                    $totalMonths = 0;
                                    foreach ($experiences as $exp) {
                                        if ($exp['start_date'] && $exp['end_date']) {
                                            $start = new DateTime($exp['start_date']);
                                            $end = new DateTime($exp['end_date']);
                                            $totalMonths += $start->diff($end)->m + ($start->diff($end)->y * 12);
                                        }
                                    }
                                    ?>
                                    <p class="text-3xl font-bold text-blue-600">
                                        <?= $totalMonths ?>
                                    </p>
                                    <p class="text-[10px] text-slate-500 uppercase font-semibold"><?= __('crews.months_sea_time') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Info Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center space-x-2 mb-6">
                            <span class="material-icons text-slate-400 text-xl">account_balance</span>
                            <h3 class="font-bold text-sm text-slate-500 uppercase tracking-wider"><?= __('crews.bank_info') ?></h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <span class="text-sm text-slate-400">Bank</span>
                                <span class="text-sm font-semibold">
                                    <?= htmlspecialchars($crew['bank_name'] ?? '-') ?>
                                </span>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <span class="text-sm text-slate-400"><?= __('crews.account_no') ?></span>
                                <span class="text-sm font-semibold">
                                    <?= htmlspecialchars($crew['bank_account'] ?? '-') ?>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-slate-400"><?= __('crews.account_holder') ?></span>
                                <span class="text-sm font-semibold">
                                    <?= htmlspecialchars($crew['account_holder'] ?? '-') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Contract History -->
                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <div class="flex items-center space-x-2 mb-4 text-primary">
                                <span class="material-icons text-lg">history</span>
                                <span class="font-bold text-sm">Contract History (
                                    <?= count($contractHistory) ?>)
                                </span>
                            </div>
                            <?php if (empty($contractHistory)): ?>
                                <div class="flex flex-col items-center justify-center py-6 text-slate-400">
                                    <span class="material-icons text-4xl mb-2 opacity-30">contract_edit</span>
                                    <p class="text-xs">Belum ada riwayat kontrak</p>
                                </div>
                            <?php else: ?>
                                <div class="space-y-2">
                                    <?php foreach (array_slice($contractHistory, 0, 3) as $contract): ?>
                                        <div class="text-xs p-2 bg-slate-50 rounded">
                                            <p class="font-semibold">
                                                <?= htmlspecialchars($contract['vessel_name']) ?>
                                            </p>
                                            <p class="text-slate-500">
                                                <?= date('M Y', strtotime($contract['start_date'])) ?> -
                                                <?= $contract['end_date'] ? date('M Y', strtotime($contract['end_date'])) : 'Present' ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-slate-400">description</span>
                            <h3 class="font-bold text-sm text-primary">Documents (
                                <?= count($documents) ?>)
                            </h3>
                        </div>
                        <button @click="showUploadModal = true"
                            class="text-xs font-bold text-primary flex items-center space-x-1 hover:underline">
                            <span class="material-icons text-sm">upload</span>
                            <span><?= __('documents.upload_new') ?></span>
                        </button>
                    </div>
                    <?php if (empty($documents)): ?>
                        <div class="p-12 flex flex-col items-center justify-center text-slate-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <span class="material-icons text-3xl opacity-20">cloud_off</span>
                            </div>
                            <p class="text-sm"><?= __('contracts.no_documents') ?></p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">
                                            Document Type</th>
                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">
                                            Number</th>
                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">Issue
                                            Date</th>
                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase">
                                            Expiry Date</th>
                                        <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($documents as $doc): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold">
                                                <?= htmlspecialchars($doc['document_type']) ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono">
                                                <?= htmlspecialchars($doc['document_number']) ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500">
                                                <?= date('d M Y', strtotime($doc['issue_date'])) ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500">
                                                <?= $doc['expiry_date'] ? date('d M Y', strtotime($doc['expiry_date'])) : '-' ?>
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-2">
                                                <button class="p-1.5 text-slate-400 hover:text-primary bg-slate-100 rounded">
                                                    <span class="material-icons text-sm">visibility</span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Skills & Certifications Section -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-slate-400">military_tech</span>
                            <h3 class="font-bold text-sm text-primary">Skills & Certifications (
                                <?= count($skills) ?>)
                            </h3>
                        </div>
                        <button @click="showSkillModal = true"
                            class="bg-primary text-white text-[10px] font-bold px-3 py-1.5 rounded-lg flex items-center space-x-1 hover:bg-slate-800 transition-colors uppercase tracking-tight">
                            <span class="material-icons text-xs">add</span>
                            <span><?= __('crews.add_skill') ?></span>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        Skill Name</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        Level</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        Certificate ID</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        Notes</th>
                                    <th
                                        class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($skills)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                            <span class="material-icons text-4xl opacity-20">workspace_premium</span>
                                            <p class="text-sm mt-2"><?= __('crews.no_skills_added') ?></p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($skills as $skill): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-primary">
                                                <?= htmlspecialchars($skill['skill_name']) ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php
                                                $levelColors = [
                                                    'BASIC' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                    'INTERMEDIATE' => 'bg-green-50 text-green-600 border-green-100',
                                                    'ADVANCED' => 'bg-teal-50 text-teal-600 border-teal-100',
                                                    'EXPERT' => 'bg-purple-50 text-purple-600 border-purple-100'
                                                ];
                                                $profLevel = $skill['proficiency_level'] ?? $skill['level'] ?? 'BASIC';
                                                $levelColor = $levelColors[$profLevel] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                                ?>
                                                <span
                                                    class="px-2.5 py-1 rounded-md text-[10px] font-bold <?= $levelColor ?> border">
                                                    <?= htmlspecialchars($profLevel) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono">
                                                <?= htmlspecialchars($skill['certificate_id'] ?? '-') ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-400">
                                                <?= htmlspecialchars($skill['notes'] ?? '-') ?>
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-2">
                                                <button onclick="editSkill(<?= $skill['id'] ?>)"
                                                    class="p-1.5 text-slate-400 hover:text-primary bg-slate-100 rounded">
                                                    <span class="material-icons text-sm">edit</span>
                                                </button>
                                                <button onclick="deleteSkill(<?= $skill['id'] ?>)"
                                                    class="p-1.5 text-slate-400 hover:text-red-500 bg-slate-100 rounded">
                                                    <span class="material-icons text-sm">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
<!-- Add Skill Modal -->
    <div x-show="showSkillModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showSkillModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showSkillModal = false"
                 class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="showSkillModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form action="<?= BASE_URL ?>crews/<?= $crew['id'] ?>/skills/add" method="POST">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                                <span class="material-icons">military_tech</span>
                                Add New Skill
                            </h3>
                            <button type="button" @click="showSkillModal = false" class="text-slate-400 hover:text-slate-600">
                                <span class="material-icons">close</span>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Skill Name *</label>
                                <input type="text" name="skill_name" required
                                       class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                       placeholder="e.g. Navigation, Welding, etc.">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Proficiency Level *</label>
                                <select name="level" required
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    <option value="">Select Level</option>
                                    <option value="BASIC">Basic</option>
                                    <option value="INTERMEDIATE">Intermediate</option>
                                    <option value="ADVANCED">Advanced</option>
                                    <option value="EXPERT">Expert</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Certificate ID</label>
                                <input type="text" name="certificate_id"
                                       class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                       placeholder="e.g. NAV-2024-001">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Notes</label>
                                <textarea name="notes" rows="3"
                                          class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                          placeholder="Additional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3">
                        <button type="button" @click="showSkillModal = false"
                                class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                            <?= __('common.cancel') ?>
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-slate-800 transition-colors flex items-center gap-2">
                            <span class="material-icons text-sm">add</span>
                            <?= __('crews.add_skill') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Document Modal -->
    <div x-show="showUploadModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showUploadModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showUploadModal = false"
                 class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="showUploadModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form action="<?= BASE_URL ?>crews/<?= $crew['id'] ?>/documents/add" method="POST" enctype="multipart/form-data">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                                <span class="material-icons">upload_file</span>
                                Upload Document
                            </h3>
                            <button type="button" @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600">
                                <span class="material-icons">close</span>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Document Type *</label>
                                <select name="document_type" required
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    <option value="">Select Document Type</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Seaman Book">Seaman Book</option>
                                    <option value="Certificate of Competency">Certificate of Competency</option>
                                    <option value="Medical Certificate">Medical Certificate</option>
                                    <option value="STCW Certificate">STCW Certificate</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Document Number *</label>
                                <input type="text" name="document_number" required
                                       class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                       placeholder="e.g. PSP-123456">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Issue Date *</label>
                                    <input type="date" name="issue_date" required
                                           class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Expiry Date</label>
                                    <input type="date" name="expiry_date"
                                           class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Upload File</label>
                                <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png"
                                       class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <p class="text-xs text-slate-500 mt-1">Accepted formats: PDF, JPG, PNG (Max 5MB)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3">
                        <button type="button" @click="showUploadModal = false"
                                class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                            <?= __('common.cancel') ?>
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-slate-800 transition-colors flex items-center gap-2">
                            <span class="material-icons text-sm">upload</span>
                            <?= __('documents.upload_new') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>

</html>