<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vessel Management | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-blue': '#1e40af',
                        'brand-gold': '#f59e0b',
                        primary: "#1e3a8a",
                        secondary: "#d4af37",
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 40px -8px rgba(0, 0, 0, 0.12), 0 12px 20px -8px rgba(0, 0, 0, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.6);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-panel:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 60px -10px rgba(0, 0, 0, 0.16), 0 18px 30px -10px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        .circular-chart {
            display: block;
            margin: 0 auto;
            max-width: 80%;
            max-height: 250px;
        }

        .circle-bg {
            fill: none;
            stroke: #e2e8f0;
            stroke-width: 3.8;
        }

        .circle {
            fill: none;
            stroke-width: 2.8;
            stroke-linecap: round;
            animation: progress 1s ease-out forwards;
        }

        @keyframes progress {
            0% {
                stroke-dasharray: 0 100;
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Modern Compact Sidebar -->
        <?php
        $currentPage = 'vessels';
        include APPPATH . 'Views/partials/modern_sidebar.php';
        ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Header -->
            <header class="h-20 flex items-center justify-between px-8 z-10 flex-shrink-0">
                <div class="flex items-center text-slate-500 text-sm">
                    <span class="material-icons-round text-lg mr-2">home</span>
                    <span class="mx-2">/</span>
                    <span><?= __('sidebar.vessels') ?></span>
                    <span class="mx-2">/</span>
                    <span class="text-slate-800 font-medium"><?= __('sidebar.management') ?></span>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Language Toggle -->
                    <button
                        class="h-10 px-3 rounded-lg bg-white/60 border border-slate-200 text-slate-600 font-medium text-xs flex items-center gap-2 hover:bg-white/80 transition-colors shadow-sm">
                        <span>ID</span>
                        <span class="w-px h-4 bg-slate-300"></span>
                        <span class="text-primary font-bold">EN</span>
                    </button>

                    <!-- Notifications -->
                    <button
                        class="h-10 w-10 rounded-full bg-white/60 border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-white/80 transition-colors relative shadow-sm">
                        <span class="material-icons-round">notifications</span>
                        <span
                            class="absolute top-2 right-2 h-2.5 w-2.5 rounded-full bg-red-500 border-2 border-white"></span>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto px-8 pb-10 custom-scrollbar">
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 mt-2">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 tracking-tight mb-2"><?= __('vessels.title') ?></h1>
                        <p class="text-slate-500 max-w-2xl"><?= __('vessels.subtitle') ?></p>
                    </div>

                    <a href="<?= BASE_URL ?>vessels/create"
                        class="px-6 py-3 bg-secondary hover:bg-amber-600 text-white rounded-xl shadow-[0_10px_20px_-5px_rgba(212,175,55,0.5)] hover:shadow-[0_15px_30px_-5px_rgba(212,175,55,0.6)] flex items-center gap-2 font-medium transition-all transform hover:-translate-y-0.5 border border-white/20">
                        <span class="material-icons-round text-xl">add</span>
                        <?= __('vessels.create_title') ?>
                    </a>
                </div>

                <!-- Vessel Cards Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                    <?php if (!empty($vessels)): ?>
                        <?php foreach ($vessels as $vessel): ?>
                            <?php
                            $crewCount = $vessel['crew_count'] ?? 0;
                            $crewCapacity = $vessel['crew_capacity'] ?? 10;
                            $crewPercentage = $crewCapacity > 0 ? round(($crewCount / $crewCapacity) * 100) : 0;
                            $strokeDasharray = $crewPercentage . ', 100';
                            ?>

                            <div class="glass-panel rounded-2xl p-0 overflow-hidden group flex flex-col bg-white/70">
                                <!-- Vessel Image -->
                                <div class="relative h-48 w-full overflow-hidden">
                                    <?php if (!empty($vessel['image_url'])): ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($vessel['image_url']) ?>"
                                            alt="<?= htmlspecialchars($vessel['name']) ?>"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 grayscale-[10%] group-hover:grayscale-0">
                                    <?php else: ?>
                                        <div
                                            class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                            <span class="material-icons-round text-6xl text-blue-400">directions_boat</span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Status Badge -->
                                    <div class="absolute top-4 right-4 flex items-center gap-2 px-3 py-1.5 rounded-full 
                                        <?= $vessel['status'] === 'active' ? 'bg-emerald-500/20 border-emerald-500/30 text-emerald-800' : 'bg-slate-500/20 border-slate-500/30 text-slate-800' ?> 
                                        backdrop-blur-md border shadow-sm">
                                        <?php if ($vessel['status'] === 'active'): ?>
                                            <span class="relative flex h-2.5 w-2.5">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-600"></span>
                                            </span>
                                        <?php else: ?>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-slate-600"></span>
                                        <?php endif; ?>
                                        <span class="text-xs font-bold tracking-wide uppercase">
                                            <?= htmlspecialchars($vessel['status']) ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Vessel Details -->
                                <div class="p-6 flex-1 flex flex-col">
                                    <!-- Header with Crew Chart -->
                                    <div class="flex justify-between items-start mb-6">
                                        <div>
                                            <h2 class="text-2xl font-bold text-slate-800 mb-1">
                                                <?= htmlspecialchars($vessel['name']) ?>
                                            </h2>
                                            <p
                                                class="text-xs font-medium text-slate-600 bg-slate-200 px-2 py-1 rounded inline-block">
                                                <?= htmlspecialchars($vessel['type'] ?? $vessel['vessel_type'] ?? 'N/A') ?>
                                            </p>
                                        </div>

                                        <!-- Crew Percentage Chart -->
                                        <div class="w-16 h-16 relative group-hover:scale-105 transition-transform">
                                            <svg class="circular-chart <?= $crewPercentage > 0 ? 'text-secondary' : 'text-slate-300' ?>"
                                                viewBox="0 0 36 36">
                                                <path class="circle-bg"
                                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831">
                                                </path>
                                                <path class="circle stroke-current"
                                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                                    stroke-dasharray="<?= $strokeDasharray ?>"></path>
                                            </svg>
                                            <div
                                                class="absolute inset-0 flex flex-col items-center justify-center text-[10px] font-bold text-slate-600 leading-tight">
                                                <span>
                                                    <?= $crewPercentage ?>%
                                                </span>
                                                <span class="text-[8px] font-normal opacity-70"><?= __('sidebar.crew') ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vessel Info Grid -->
                                    <div
                                        class="grid grid-cols-2 gap-y-4 gap-x-2 text-sm mb-6 bg-slate-50/50 rounded-xl p-4 border border-slate-100">
                                        <div class="col-span-1">
                                            <p class="text-xs text-slate-400 uppercase tracking-wider mb-0.5"><?= __('vessels.imo_number') ?></p>
                                            <p class="font-medium text-slate-700 font-mono">
                                                <?= htmlspecialchars($vessel['imo_number']) ?>
                                            </p>
                                        </div>
                                        <div class="col-span-1">
                                            <p class="text-xs text-slate-400 uppercase tracking-wider mb-0.5"><?= __('vessels.flag') ?></p>
                                            <p class="font-medium text-slate-700">
                                                <?= htmlspecialchars($vessel['flag_state'] ?? 'N/A') ?>
                                            </p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-xs text-slate-400 uppercase tracking-wider mb-0.5"><?= __('vessels.owner') ?></p>
                                            <div class="flex items-center gap-2">
                                                <span class="material-icons-round text-base text-slate-400">verified_user</span>
                                                <p class="font-medium text-slate-700 truncate">
                                                    <?= htmlspecialchars($vessel['owner_name'] ?? 'N/A') ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Actions -->
                                    <div
                                        class="flex items-center justify-between text-xs text-slate-500 mt-auto pt-4 border-t border-slate-200">
                                        <span>
                                            <?= $crewCount ?> <?= __('crews.onboard') ?>
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <a href="<?= BASE_URL ?>vessels/<?= $vessel['id'] ?>"
                                                class="h-8 w-8 rounded-lg flex items-center justify-center bg-white hover:bg-primary hover:text-white text-slate-500 transition-colors shadow-sm border border-slate-100"
                                                title="<?= __('common.view') ?>">
                                                <span class="material-icons-round text-lg">visibility</span>
                                            </a>
                                            <a href="<?= BASE_URL ?>vessels/edit/<?= $vessel['id'] ?>"
                                                class="h-8 w-8 rounded-lg flex items-center justify-center bg-white hover:bg-secondary hover:text-white text-slate-500 transition-colors shadow-sm border border-slate-100"
                                                title="<?= __('common.edit') ?>">
                                                <span class="material-icons-round text-lg">edit</span>
                                            </a>
                                            <a href="<?= BASE_URL ?>vessels/crew/<?= $vessel['id'] ?>"
                                                class="h-8 w-8 rounded-lg flex items-center justify-center bg-white hover:bg-emerald-600 hover:text-white text-slate-500 transition-colors shadow-sm border border-slate-100"
                                                title="<?= __('sidebar.crew') ?>">
                                                <span class="material-icons-round text-lg">groups</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Add New Vessel Card -->
                    <a href="<?= BASE_URL ?>vessels/create"
                        class="glass-panel rounded-2xl p-0 overflow-hidden !border-dashed !border-2 border-slate-300 bg-transparent opacity-60 hover:opacity-100 cursor-pointer group flex flex-col">
                        <div class="h-full flex flex-col items-center justify-center p-8 text-center">
                            <div
                                class="h-16 w-16 rounded-full bg-slate-200 flex items-center justify-center mb-4 group-hover:bg-secondary group-hover:text-white transition-colors">
                                <span
                                    class="material-icons-round text-3xl text-slate-400 group-hover:text-white">add</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-600"><?= __('vessels.create_title') ?></h3>
                            <p class="text-sm text-slate-400 mt-2"><?= __('vessels.subtitle') ?></p>
                        </div>
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-panel p-4 rounded-xl flex items-center gap-4 border border-slate-100">
                        <div
                            class="h-12 w-12 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <span class="material-icons-round">sailing</span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold"><?= __('vessels.list_title') ?></p>
                            <p class="text-xl font-bold text-slate-800">
                                <?= count($vessels ?? []) ?>
                            </p>
                        </div>
                    </div>

                    <div class="glass-panel p-4 rounded-xl flex items-center gap-4 border border-slate-100">
                        <div class="h-12 w-12 rounded-lg bg-blue-100 text-primary flex items-center justify-center">
                            <span class="material-icons-round">group</span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold"><?= __('dashboard.active_crew') ?></p>
                            <p class="text-xl font-bold text-slate-800">
                                <?= $total_crew ?? 0 ?>
                            </p>
                        </div>
                    </div>

                    <div class="glass-panel p-4 rounded-xl flex items-center gap-4 border border-slate-100">
                        <div class="h-12 w-12 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                            <span class="material-icons-round">warning_amber</span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold"><?= __('common.status') ?></p>
                            <p class="text-xl font-bold text-slate-800">
                                <?= $maintenance_count ?? 0 ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>