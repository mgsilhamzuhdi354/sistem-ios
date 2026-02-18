<?php
/**
 * Modern Skill Matrix View - Tailwind CSS
 */
$currentPage = 'skill_matrix';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crew Skill Matrix - IndoOcean ERP</title>

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
                        'brand-blue': '#0A2463',
                        'brand-gold': '#D4AF37',
                        'bg-dark': '#0B1121',
                        'bg-card': '#1a1f36',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.3);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 transition-colors" x-data="{ 
          searchQuery: '', 
          levelFilter: '',
          openSections: { management: true, crew: true, employee: false, recruitment: false }
      }">

    <div class="flex min-h-screen">
        <!-- Sidebar - Use existing modern sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden ml-0 lg:ml-64">
            <!-- Top Header -->
            <header
                class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 lg:px-8 z-10 flex-shrink-0 shadow-sm">
                <div class="flex items-center gap-4">
                    <button
                        class="lg:hidden p-2 rounded-md text-slate-600 hover:bg-slate-100">
                        <span class="material-icons">menu</span>
                    </button>
                    <h2 class="text-lg font-semibold text-slate-900 hidden sm:block">Crew Management
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <div
                        class="hidden md:flex items-center bg-slate-100 rounded-full px-3 py-1.5 border border-transparent focus-within:border-brand-gold transition-colors">
                        <span class="material-icons text-slate-400 text-sm">search</span>
                        <input type="text" placeholder="Search globally..."
                            class="bg-transparent border-none text-sm focus:ring-0 text-slate-900 placeholder-slate-400 w-48">
                    </div>
                    <button
                        class="p-2 relative rounded-full hover:bg-slate-100 text-slate-600 transition-colors">
                        <span class="material-icons">notifications</span>
                        <span
                            class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-6 lg:p-8 custom-scrollbar">
                <div class="max-w-7xl mx-auto space-y-8">

                    <!-- Page Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-slate-900 mb-1">Crew Skill Matrix</h1>
                            <p class="text-sm text-slate-600">Competency matrix and certification
                                tracking for all crew members.</p>
                        </div>
                        <a href="<?= BASE_URL ?>crews/modern"
                            class="inline-flex items-center gap-2 bg-brand-gold hover:bg-yellow-600 text-white px-5 py-2.5 rounded-lg font-medium shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                            <span class="material-icons text-sm">arrow_back</span>
                            Back to Crew List
                        </a>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Total Skills -->
                        <div
                            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p
                                        class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Total Skills</p>
                                    <h3 class="text-3xl font-bold text-slate-900 mt-1">
                                        <?= $statistics['total_unique_skills'] ?? 0 ?>
                                    </h3>
                                </div>
                                <div
                                    class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-600">
                                    <span class="material-icons">stars</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500">Unique skill types registered</p>
                        </div>

                        <!-- Crew with Skills -->
                        <div
                            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p
                                        class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Crew with Skills</p>
                                    <h3 class="text-3xl font-bold text-slate-900 mt-1">
                                        <?= $statistics['total_crew_with_skills'] ?? 0 ?>
                                    </h3>
                                </div>
                                <div
                                    class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg text-green-600">
                                    <span class="material-icons">people</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500">Members actively registered</p>
                        </div>

                        <!-- Expert Level -->
                        <div
                            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p
                                        class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Expert Level</p>
                                    <h3 class="text-3xl font-bold text-slate-900 mt-1">
                                        <?= $statistics['expert_count'] ?? 0 ?>
                                    </h3>
                                </div>
                                <div
                                    class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-purple-600">
                                    <span class="material-icons">workspace_premium</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500">Expert certifications held</p>
                        </div>

                        <!-- Total Entries -->
                        <div
                            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p
                                        class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Total Entries</p>
                                    <h3 class="text-3xl font-bold text-slate-900 mt-1">
                                        <?= $statistics['total_skill_entries'] ?? 0 ?>
                                    </h3>
                                </div>
                                <div
                                    class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg text-amber-600">
                                    <span class="material-icons">library_books</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500">Total skill records</p>
                        </div>
                    </div>

                    <?php if (empty($skillMatrix)): ?>
                        <!-- Empty State -->
                        <div
                            class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                            <div class="text-6xl text-slate-300 dark:text-slate-600 mb-4">
                                <span class="material-icons" style="font-size: 64px;">grid_view</span>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-700 mb-2">No Skills Data</h3>
                            <p class="text-slate-500 max-w-md mx-auto mb-6">
                                No crew members have registered skills yet. Add crew members and their skills to view the
                                competency matrix.
                            </p>
                            <a href="<?= BASE_URL ?>crews/modern"
                                class="inline-flex items-center gap-2 bg-brand-gold hover:bg-yellow-600 text-white px-5 py-2.5 rounded-lg font-medium">
                                <span class="material-icons text-sm">groups</span>
                                Go to Crew List
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Competency Matrix Table -->
                        <div
                            class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <!-- Table Header -->
                            <div
                                class="p-5 border-b border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-brand-gold">grid_view</span>
                                    <h3 class="font-bold text-slate-900">Competency Matrix</h3>
                                </div>

                                <div class="flex gap-3 w-full sm:w-auto">
                                    <!-- Search -->
                                    <div class="relative group flex-1 sm:flex-none">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="material-icons text-slate-400 text-sm">search</span>
                                        </span>
                                        <input type="text" x-model="searchQuery" placeholder="Search skills..."
                                            class="pl-9 pr-4 py-2 w-full sm:w-64 bg-slate-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-brand-gold/30 text-slate-900 placeholder-slate-400">
                                    </div>

                                    <!-- Level Filter -->
                                    <select x-model="levelFilter"
                                        class="py-2 pl-3 pr-8 bg-slate-50 border-none rounded-lg text-sm text-slate-900 focus:ring-2 focus:ring-brand-gold/30 cursor-pointer">
                                        <option value="">All Levels</option>
                                        <option value="basic">Basic</option>
                                        <option value="intermediate">Intermediate</option>
                                        <option value="advanced">Advanced</option>
                                        <option value="expert">Expert</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-slate-50/30/30 border-b border-slate-200">
                                            <th
                                                class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider w-1/4">
                                                Skill Name
                                            </th>
                                            <th
                                                class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider w-1/2">
                                                Crew Members (Name - Level - Cert)
                                            </th>
                                            <th
                                                class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider text-center">
                                                Avg Level
                                            </th>
                                            <th
                                                class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">
                                                Count
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                        <?php foreach ($skillMatrix as $skillName => $crew): ?>
                                            <?php
                                            // Calculate average level
                                            $levelValues = ['basic' => 1, 'intermediate' => 2, 'advanced' => 3, 'expert' => 4];
                                            $levels = array_column($crew, 'skill_level');
                                            $avg = array_sum(array_map(fn($l) => $levelValues[$l] ?? 0, $levels)) / count($levels);
                                            $avgLevel = ['Basic', 'Intermediate', 'Advanced', 'Expert'][floor($avg) - 1] ?? 'Basic';
                                            $avgLevelColors = [
                                                'Basic' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                'Intermediate' => 'bg-blue-50 dark:bg-blue-900/10 text-blue-700 border-blue-100 dark:border-blue-800/30',
                                                'Advanced' => 'bg-emerald-50 dark:bg-emerald-900/10 text-emerald-700 border-emerald-100 dark:border-emerald-800/30',
                                                'Expert' => 'bg-amber-50 dark:bg-amber-900/10 text-amber-700 border-amber-100 dark:border-amber-800/30'
                                            ];

                                            // Build level filter string
                                            $levelStr = implode(',', array_unique($levels));
                                            ?>
                                            <tr class="hover:bg-slate-50/20 transition-colors group"
                                                x-show="(!searchQuery || '<?= strtolower($skillName) ?>'.includes(searchQuery.toLowerCase())) && 
                                                    (!levelFilter || '<?= $levelStr ?>'.includes(levelFilter))">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="p-1.5 rounded-full bg-brand-gold/10 text-brand-gold">
                                                            <span class="material-icons text-sm">explore</span>
                                                        </div>
                                                        <span class="font-medium text-slate-900">
                                                            <?= htmlspecialchars($skillName) ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-wrap gap-2">
                                                        <?php foreach ($crew as $member): ?>
                                                            <?php
                                                            $levelColors = [
                                                                'basic' => 'bg-slate-100 text-slate-700',
                                                                'intermediate' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600',
                                                                'advanced' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600',
                                                                'expert' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600'
                                                            ];
                                                            $levelColor = $levelColors[$member['skill_level']] ?? $levelColors['basic'];

                                                            // Generate initials
                                                            $initials = strtoupper(substr($member['crew_name'], 0, 2));
                                                            ?>
                                                            <a href="<?= BASE_URL ?>crews/<?= $member['crew_id'] ?>"
                                                                class="inline-flex items-center gap-3 bg-white border border-slate-200 rounded-full px-1 py-1 pr-4 shadow-sm hover:shadow-md transition-shadow">
                                                                <div
                                                                    class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                                                    <?= $initials ?>
                                                                </div>
                                                                <div
                                                                    class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                                                                    <span
                                                                        class="text-sm font-medium text-slate-900">
                                                                        <?= htmlspecialchars($member['crew_name']) ?>
                                                                    </span>
                                                                    <span
                                                                        class="px-2 py-0.5 rounded text-[10px] font-bold <?= $levelColor ?> border border-current/10">
                                                                        <?= strtoupper($member['skill_level']) ?>
                                                                    </span>
                                                                    <?php if (!empty($member['certificate_id'])): ?>
                                                                        <span class="text-[10px] text-slate-400 font-mono">
                                                                            <?= htmlspecialchars($member['certificate_id']) ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span
                                                        class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold <?= $avgLevelColors[$avgLevel] ?> border">
                                                        <?= strtoupper($avgLevel) ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <span
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-900 text-xs font-bold shadow-sm">
                                                        <?= count($crew) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </main>
    </div>

</body>

</html>
