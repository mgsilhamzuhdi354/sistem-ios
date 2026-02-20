<?php
/**
 * Modern Visitor Monitoring Dashboard
 * PT Indo Ocean - ERP System
 * Futuristic Analytics Hub Design
 */
$currentPage = 'monitoring';
$period = $period ?? 'today';
$visitors = $visitors ?? [];
$stats = $stats ?? ['top_pages' => [], 'top_countries' => []];

// Calculate statistics
$totalVisitors = count($visitors);
$uniqueCountries = count($stats['top_countries'] ?? []);
$uniquePages = count($stats['top_pages'] ?? []);

// Calculate growth percentage (mock data, replace with real calculation)
$growthPercentage = 0;
?>
<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile Visitors | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#3B82F6", // Modern Blue
                        secondary: "#6366F1", // Indigo
                        "background-light": "#F8FAFC",
                        "background-dark": "#0F172A",
                        "glass-white": "rgba(255, 255, 255, 0.7)",
                        "glass-dark": "rgba(30, 41, 59, 0.7)",
                    },
                    fontFamily: {
                        display: ["'Inter'", "sans-serif"],
                        body: ["'Inter'", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "1rem",
                        'xl': "1.5rem",
                        '2xl': "2rem",
                    },
                    backdropBlur: {
                        'xs': '2px',
                    },
                    boxShadow: {
                        'float': '0 20px 40px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01)',
                        'glow': '0 0 20px rgba(59, 130, 246, 0.15)',
                    }
                },
            },
        };
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.05) 0%, transparent 40%);
        }
        .glass-panel {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .dark .glass-panel {
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100 min-h-screen transition-colors duration-300">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="ml-64 flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Header with Dark Mode Toggle -->
            <header class="bg-white dark:bg-gray-800 shadow-sm z-20 px-6 py-4 flex items-center justify-between sticky top-0">
                <div class="flex items-center gap-3">
                    <span class="material-icons-round text-primary text-3xl">analytics</span>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary">
                            Company Profile Visitors
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-medium text-sm"><?= __('monitoring.visitors_subtitle') ?></p>
                    </div>
                </div>
                <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" onclick="document.documentElement.classList.toggle('dark')">
                    <span class="material-icons-round text-gray-500 dark:text-gray-400">dark_mode</span>
                </button>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-6 lg:p-12 overflow-y-auto">
                <!-- Period Filter -->
                <div class="mb-12">
                    <div class="glass-panel bg-glass-white dark:bg-glass-dark px-2 py-2 rounded-full flex gap-1 shadow-float inline-flex">
                        <a href="?period=today" class="px-5 py-2 rounded-full text-sm font-semibold transition-all hover:scale-105 <?= $period === 'today' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' ?>">
                            <?= __('common.today') ?>
                        </a>
                        <a href="?period=week" class="px-5 py-2 rounded-full text-sm font-semibold transition-all hover:scale-105 <?= $period === 'week' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' ?>">
                            <?= __('monitoring.this_week') ?>
                        </a>
                        <a href="?period=month" class="px-5 py-2 rounded-full text-sm font-semibold transition-all hover:scale-105 <?= $period === 'month' ? 'bg-white dark:bg-slate-700 text-primary shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' ?>">
                            <?= __('monitoring.this_month') ?>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left Column -->
                    <div class="lg:col-span-8 space-y-8">
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Total Visitors -->
                            <div class="glass-panel bg-white/60 dark:bg-slate-800/60 p-6 rounded-2xl shadow-float hover:shadow-glow transition-all duration-300 group">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl text-blue-500 dark:text-blue-300 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                                        <span class="material-icons-round">groups</span>
                                    </div>
                                    <span class="text-xs font-semibold text-green-500 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-full">+<?= $growthPercentage ?>%</span>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium"><?= __('monitoring.total_visitors') ?></h3>
                                    <p class="text-4xl font-bold text-slate-800 dark:text-white tracking-tight"><?= $totalVisitors ?></p>
                                </div>
                            </div>

                            <!-- Countries -->
                            <div class="glass-panel bg-white/60 dark:bg-slate-800/60 p-6 rounded-2xl shadow-float hover:shadow-glow transition-all duration-300 group">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl text-indigo-500 dark:text-indigo-300 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                                        <span class="material-icons-round">public</span>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2 py-1 rounded-full">-</span>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium"><?= __('monitoring.countries') ?></h3>
                                    <p class="text-4xl font-bold text-slate-800 dark:text-white tracking-tight"><?= $uniqueCountries ?></p>
                                </div>
                            </div>

                            <!-- Unique Pages -->
                            <div class="glass-panel bg-white/60 dark:bg-slate-800/60 p-6 rounded-2xl shadow-float hover:shadow-glow transition-all duration-300 group">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-purple-50 dark:bg-purple-900/30 rounded-xl text-purple-500 dark:text-purple-300 group-hover:bg-purple-500 group-hover:text-white transition-colors duration-300">
                                        <span class="material-icons-round">pages</span>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2 py-1 rounded-full">-</span>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium"><?= __('monitoring.unique_pages') ?></h3>
                                    <p class="text-4xl font-bold text-slate-800 dark:text-white tracking-tight"><?= $uniquePages ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Visitors Table -->
                        <div class="glass-panel bg-white/80 dark:bg-slate-800/80 rounded-2xl shadow-float p-8 min-h-[300px] flex flex-col relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary"></div>
                            <div class="flex justify-between items-center mb-8">
                                <h2 class="text-xl font-bold text-slate-800 dark:text-white"><?= __('monitoring.recent_visitors') ?></h2>
                                <a class="text-sm font-semibold text-primary hover:text-secondary flex items-center gap-1 transition-colors" href="<?= BASE_URL ?>monitoring/activity">
                                    Full Report <span class="material-icons-round text-base">arrow_forward</span>
                                </a>
                            </div>

                            <?php if (empty($visitors)): ?>
                                <div class="flex-1 flex flex-col items-center justify-center text-center space-y-6 z-10">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-blue-400 blur-xl opacity-20 animate-pulse rounded-full"></div>
                                        <div class="bg-gradient-to-br from-blue-50 to-white dark:from-slate-700 dark:to-slate-800 p-6 rounded-full shadow-lg border border-blue-100 dark:border-slate-600 relative">
                                            <span class="material-icons-round text-5xl text-primary animate-pulse">sensors</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Ready to Sync</h3>
                                        <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                                            Belum ada visitor data. Waiting for the first signal from the fleet.
                                        </p>
                                        <a class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-blue-600 text-white rounded-xl font-medium transition-all shadow-lg hover:shadow-blue-500/30" href="<?= BASE_URL ?>monitoring/integration">
                                            Check Integration Status
                                            <span class="material-icons-round text-sm">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="border-b border-slate-200 dark:border-slate-700">
                                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Time</th>
                                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">IP Address</th>
                                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Country</th>
                                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Page</th>
                                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Device</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                            <?php foreach (array_slice($visitors, 0, 50) as $visitor): ?>
                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                                <td class="py-3 px-4 text-sm text-slate-700 dark:text-slate-300"><?= date('d/m/Y H:i', strtotime($visitor['visited_at'])) ?></td>
                                                <td class="py-3 px-4"><code class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded"><?= htmlspecialchars($visitor['ip_address']) ?></code></td>
                                                <td class="py-3 px-4 text-sm text-slate-700 dark:text-slate-300"><?= htmlspecialchars($visitor['country'] ?? '-') ?></td>
                                                <td class="py-3 px-4 text-sm text-slate-700 dark:text-slate-300"><?= htmlspecialchars($visitor['page_url']) ?></td>
                                                <td class="py-3 px-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                                                        <?= htmlspecialchars($visitor['device_type'] ?? 'Unknown') ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none" style="background-image: linear-gradient(#000 1px, transparent 1px), linear-gradient(90deg, #000 1px, transparent 1px); background-size: 20px 20px;"></div>
                        </div>

                        <!-- Top Pages and Countries -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Top Pages -->
                            <div class="glass-panel bg-white/60 dark:bg-slate-800/60 p-6 rounded-2xl shadow-float">
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                    <span class="material-icons-round text-slate-400">description</span> Top Pages
                                </h3>
                                <?php if (empty($stats['top_pages'])): ?>
                                    <div class="flex items-center justify-center h-32 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                                        <span class="text-sm text-slate-400 font-medium">No data available</span>
                                    </div>
                                <?php else: ?>
                                    <div class="space-y-3">
                                        <?php foreach ($stats['top_pages'] as $page): ?>
                                        <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-600">
                                            <span class="text-sm text-slate-700 dark:text-slate-300 truncate flex-1"><?= htmlspecialchars($page['page_url']) ?></span>
                                            <span class="text-sm font-bold text-primary ml-3"><?= $page['count'] ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Top Countries -->
                            <div class="glass-panel bg-white/60 dark:bg-slate-800/60 p-6 rounded-2xl shadow-float">
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                    <span class="material-icons-round text-slate-400">flag</span> Top Countries
                                </h3>
                                <?php if (empty($stats['top_countries'])): ?>
                                    <div class="flex items-center justify-center h-32 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                                        <span class="text-sm text-slate-400 font-medium">No data available</span>
                                    </div>
                                <?php else: ?>
                                    <div class="space-y-3">
                                        <?php foreach ($stats['top_countries'] as $country): ?>
                                        <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-600">
                                            <span class="text-sm text-slate-700 dark:text-slate-300"><?= htmlspecialchars($country['country']) ?></span>
                                            <span class="text-sm font-bold text-primary"><?= $country['count'] ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Tracking Health -->
                        <div class="glass-panel bg-gradient-to-b from-white to-blue-50/50 dark:from-slate-800 dark:to-slate-900/50 p-6 rounded-2xl shadow-float">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Tracking Health</h3>
                            <div class="flex flex-col items-center justify-center mb-8 relative">
                                <div class="w-48 h-48 rounded-full border-[12px] border-slate-100 dark:border-slate-700 relative flex items-center justify-center">
                                    <div class="absolute inset-0 rounded-full border-[12px] border-primary border-t-transparent border-l-transparent transform -rotate-45 opacity-20"></div>
                                    <div class="absolute inset-0 rounded-full border-[12px] border-primary border-r-transparent border-b-transparent transform rotate-45" style="clip-path: polygon(0 0, 100% 0, 100% 50%, 0 50%);"></div>
                                    <div class="text-center">
                                        <span class="text-4xl font-bold text-primary block">100%</span>
                                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Uptime</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-600">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Script Active</span>
                                    </div>
                                    <span class="material-icons-round text-green-500 text-sm">check_circle</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-600">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Data Flow</span>
                                    </div>
                                    <span class="text-xs font-mono text-slate-400">OPTIMAL</span>
                                </div>
                            </div>
                        </div>

                        <!-- Upgrade Analytics -->
                        <div class="glass-panel bg-primary text-white p-6 rounded-2xl shadow-glow relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full blur-2xl"></div>
                            <div class="absolute -left-4 -bottom-4 w-32 h-32 bg-indigo-500 opacity-30 rounded-full blur-2xl"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mb-4 backdrop-blur-sm">
                                    <span class="material-icons-round text-white">bolt</span>
                                </div>
                                <h3 class="text-lg font-bold mb-2">Upgrade Analytics</h3>
                                <p class="text-blue-100 text-sm mb-4 leading-relaxed">
                                    Get deeper insights into maritime logistics traffic and fleet tracking behavior.
                                </p>
                                <button class="w-full py-2.5 bg-white text-primary text-sm font-bold rounded-lg shadow-sm hover:bg-blue-50 transition-colors">
                                    View Premium Plans
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- System Status Indicator -->
    <div class="fixed bottom-6 right-6 hidden lg:flex items-center gap-2 px-4 py-2 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-full shadow-lg border border-slate-200 dark:border-slate-700">
        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">System Online</span>
    </div>

    <!-- Dark mode persistence -->
    <script>
        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.documentElement.classList.add('dark');
        }

        // Save dark mode preference when toggled
        document.addEventListener('click', function(e) {
            if (e.target.closest('button[onclick*="dark"]')) {
                if (document.documentElement.classList.contains('dark')) {
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                }
            }
        });
    </script>
</body>
</html>
