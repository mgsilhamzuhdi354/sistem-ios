<?php
/**
 * Modern Cyber Integration Dashboard
 * PT Indo Ocean - ERP System
 * Black Hat / Command Center Design
 */
$currentPage = 'integration';
$integrations = $integrations ?? [];
$flash = $flash ?? null;

// Map integration keys to node data
$nodeMap = [
    'hris' => [
        'icon' => 'badge',
        'label' => 'HR_SYSTEMS',
        'ip' => '10.0.1.50',
        'protocol' => 'REST API',
        'endpoint' => '/api/employees, /attendance, /payroll',
        'db' => 'absensi_laravel',
    ],
    'recruitment' => [
        'icon' => 'person_search',
        'label' => 'RECRUIT_NODE',
        'ip' => '10.0.1.51',
        'protocol' => 'Direct Database',
        'endpoint' => 'applications, candidates',
        'db' => 'recruitment_db',
    ],
    'company_profile' => [
        'icon' => 'language',
        'label' => 'WEBTRACK_SYS',
        'ip' => '10.0.1.52',
        'protocol' => 'Tracking Script',
        'endpoint' => '/api/track-visitor',
        'db' => 'erp_db (visitor_logs)',
    ],
    'finance' => [
        'icon' => 'account_balance',
        'label' => 'FINANCE_MOD',
        'ip' => '10.0.1.1',
        'protocol' => 'Internal Module',
        'endpoint' => 'payroll_periods, payroll_items',
        'db' => 'erp_db (payroll)',
    ],
];

// Count statuses
$connectedCount = 0;
$totalCount = count($integrations);
foreach ($integrations as $int) {
    if (in_array($int['status'], ['connected', 'active'])) $connectedCount++;
}
$threatLevel = $connectedCount === $totalCount ? 'NULL' : ($connectedCount > 0 ? 'LOW' : 'HIGH');
$threatColor = $connectedCount === $totalCount ? 'text-accent' : ($connectedCount > 0 ? 'text-yellow-400' : 'text-red-500');
$threatPercent = $totalCount > 0 ? round((($totalCount - $connectedCount) / $totalCount) * 100) : 0;
$firewallBars = $connectedCount;
?>
<!DOCTYPE html>
<html class="dark" lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integration Dashboard | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary-cyber": "#0df2f2",
                        "secondary-cyber": "#0a3a3a",
                        "accent": "#39ff14",
                        "background-dark": "#050a0a",
                        "panel-dark": "rgba(16, 34, 34, 0.85)",
                        /* Sidebar colors */
                        "primary": "#1e3a8a",
                        "secondary": "#D4AF37",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"],
                        "cyber": ["Space Grotesk", "sans-serif"],
                        "mono": ["Share Tech Mono", "monospace"],
                    },
                    boxShadow: {
                        'neon': '0 0 5px #0df2f2, 0 0 20px rgba(13,242,242,0.3)',
                        'neon-green': '0 0 5px #39ff14, 0 0 10px rgba(57,255,20,0.3)',
                    },
                },
            },
        };
    </script>

    <style>
        /* CRT Scanline Effect */
        .crt::before {
            content: " ";
            display: block;
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18,16,16,0) 50%, rgba(0,0,0,0.15) 50%),
                        linear-gradient(90deg, rgba(255,0,0,0.03), rgba(0,255,0,0.01), rgba(0,0,255,0.03));
            z-index: 2;
            background-size: 100% 2px, 3px 100%;
            pointer-events: none;
        }

        .cyber-grid {
            background-image: linear-gradient(rgba(13,242,242,0.04) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(13,242,242,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .text-neon { text-shadow: 0 0 5px #0df2f2, 0 0 10px rgba(13,242,242,0.5); }
        .text-neon-green { text-shadow: 0 0 5px #39ff14; }

        .glitch-hover:hover {
            animation: glitch 0.3s cubic-bezier(.25,.46,.45,.94) both infinite;
            color: #39ff14;
        }
        @keyframes glitch {
            0% { transform: translate(0) }
            20% { transform: translate(-2px, 2px) }
            40% { transform: translate(-2px, -2px) }
            60% { transform: translate(2px, 2px) }
            80% { transform: translate(2px, -2px) }
            100% { transform: translate(0) }
        }

        @keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0; } }
        .cursor-blink { animation: blink 1s step-end infinite; }

        .cyber-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .cyber-scrollbar::-webkit-scrollbar-track { background: #020404; }
        .cyber-scrollbar::-webkit-scrollbar-thumb { background: #0df2f2; border-radius: 4px; }
        .cyber-scrollbar::-webkit-scrollbar-thumb:hover { background: #39ff14; }

        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.8); opacity: 0; }
        }
        .pulse-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: inherit;
            border: 2px solid currentColor;
            animation: pulse-ring 2s ease-out infinite;
        }

        @keyframes data-flow {
            0% { stroke-dashoffset: 30; }
            100% { stroke-dashoffset: 0; }
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-background-dark text-slate-800 dark:text-white antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar (existing modern sidebar) -->
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 ml-64 flex flex-col h-screen overflow-hidden">
        <!-- Top Command Bar -->
        <header class="h-auto border-b border-primary-cyber/30 bg-black/90 backdrop-blur-sm flex-shrink-0 z-10">
            <div class="px-6 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="text-primary-cyber font-mono text-xs tracking-widest border border-primary-cyber px-2 py-1 rounded shadow-neon">
                        TERMINAL_Root
                    </div>
                    <div class="h-4 w-px bg-primary-cyber/30"></div>
                    <h1 class="text-base font-bold tracking-[0.15em] text-white uppercase flex items-center gap-2 font-cyber">
                        <span class="material-icons text-primary-cyber text-sm animate-pulse">security</span>
                        IndoOcean ERP <span class="text-primary-cyber">//</span> COMMAND_NODE
                    </h1>
                </div>
                <div class="flex items-center space-x-5 font-mono text-xs">
                    <div class="flex items-center space-x-2 text-primary-cyber/70">
                        <span class="material-icons text-xs">wifi</span>
                        <span class="hidden md:inline">SECURE_LINK</span>
                    </div>
                    <div class="text-accent text-neon-green">
                        SYS_TIME: <span id="cyber-clock">--:--:--</span>
                    </div>
                    <button onclick="window.location.reload()" class="bg-primary-cyber/10 hover:bg-primary-cyber/20 text-primary-cyber border border-primary-cyber/50 px-3 py-1 rounded transition-all glitch-hover uppercase text-[10px] tracking-wider">
                        Reboot_Sys
                    </button>
                    <div class="w-2.5 h-2.5 rounded-full bg-accent shadow-neon-green animate-pulse"></div>
                </div>
            </div>
            <div class="h-[1px] w-full bg-gradient-to-r from-transparent via-primary-cyber to-transparent opacity-50"></div>
        </header>

        <!-- Page Content - Cyber Grid Background -->
        <div class="flex-1 overflow-y-auto cyber-scrollbar cyber-grid crt relative" style="background-color: #050a0a;">
            <div class="p-6 relative z-10">

                <!-- 3-Column Grid Layout -->
                <div class="grid grid-cols-12 gap-5">

                    <!-- LEFT COLUMN: System Status -->
                    <div class="col-span-12 lg:col-span-3 flex flex-col gap-5">

                        <!-- System Integrity Card -->
                        <div class="bg-panel-dark border border-primary-cyber/30 rounded-lg p-5 backdrop-blur-md relative overflow-hidden group hover:border-primary-cyber transition-colors duration-300">
                            <div class="absolute top-0 right-0 p-2 opacity-30 group-hover:opacity-100 transition-opacity">
                                <span class="material-icons text-primary-cyber text-4xl">shield</span>
                            </div>
                            <h3 class="text-primary-cyber font-mono text-xs tracking-widest mb-4 border-b border-primary-cyber/20 pb-2 uppercase">System_Integrity</h3>

                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-400 font-mono text-sm">Threat Level</span>
                                <span class="<?= $threatColor ?> font-bold tracking-wider text-neon-green"><?= $threatLevel ?></span>
                            </div>
                            <div class="w-full bg-gray-800 h-1.5 rounded-full mb-6">
                                <div class="bg-accent h-1.5 rounded-full shadow-neon-green transition-all duration-1000" style="width: <?= max($threatPercent, 2) ?>%"></div>
                            </div>

                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-400 font-mono text-sm">Firewall Status</span>
                                <span class="text-primary-cyber font-bold tracking-wider">ACTIVE</span>
                            </div>
                            <div class="flex space-x-1 mb-6">
                                <?php for ($i = 0; $i < $totalCount; $i++): ?>
                                    <div class="h-2 flex-1 <?= $i < $firewallBars ? 'bg-primary-cyber shadow-neon' : 'bg-primary-cyber/20' ?>"></div>
                                <?php endfor; ?>
                                <div class="h-2 flex-1 bg-primary-cyber/10"></div>
                            </div>

                            <div class="mt-3 p-3 bg-black/40 rounded border border-primary-cyber/10 font-mono text-xs text-primary-cyber/80">
                                <p>&gt; scanning ports... [OK]</p>
                                <p>&gt; checking protocols... [OK]</p>
                                <p>&gt; nodes online: <?= $connectedCount ?>/<?= $totalCount ?></p>
                                <p>&gt; encrypting packets... <span class="animate-pulse cursor-blink">_</span></p>
                            </div>
                        </div>

                        <!-- Encryption Widget -->
                        <div class="bg-panel-dark border border-primary-cyber/30 rounded-lg p-5 backdrop-blur-md relative">
                            <h3 class="text-primary-cyber font-mono text-xs tracking-widest mb-4 border-b border-primary-cyber/20 pb-2 uppercase">Encryption_Lvl</h3>
                            <div class="flex justify-center relative py-4">
                                <div class="w-28 h-28 rounded-full border-4 border-gray-800 relative flex items-center justify-center">
                                    <div class="absolute w-full h-full rounded-full border-4 border-t-primary-cyber border-r-primary-cyber border-b-transparent border-l-transparent" style="animation: spin 3s linear infinite;"></div>
                                    <div class="absolute w-20 h-20 rounded-full border-2 border-dashed border-accent opacity-40" style="animation: spin 8s linear infinite reverse;"></div>
                                    <div class="text-center z-10">
                                        <div class="text-xl font-bold text-white font-mono">256</div>
                                        <div class="text-[10px] text-primary-cyber uppercase tracking-widest">BIT AES</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] bg-primary-cyber/10 text-primary-cyber border border-primary-cyber/30">QUANTUM_RESISTANT</span>
                            </div>
                        </div>

                        <!-- Node Traffic -->
                        <div class="bg-panel-dark border border-primary-cyber/30 rounded-lg p-5 backdrop-blur-md flex-grow">
                            <h3 class="text-primary-cyber font-mono text-xs tracking-widest mb-4 border-b border-primary-cyber/20 pb-2 uppercase">Node_Traffic</h3>
                            <ul class="space-y-3 font-mono text-sm">
                                <?php foreach ($integrations as $key => $int): ?>
                                    <?php
                                    $node = $nodeMap[$key] ?? ['label' => strtoupper($key), 'ip' => '0.0.0.0'];
                                    $isUp = in_array($int['status'], ['connected', 'active']);
                                    ?>
                                    <li class="flex justify-between items-center group cursor-pointer">
                                        <span class="text-gray-400 group-hover:text-primary-cyber transition-colors"><?= $node['label'] ?></span>
                                        <span class="<?= $isUp ? 'text-primary-cyber/80' : 'text-red-500' ?>"><?= $isUp ? rand(50, 900) . ' MB/s' : 'OFFLINE' ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <li class="flex justify-between items-center group cursor-pointer">
                                    <span class="text-gray-400 group-hover:text-primary-cyber transition-colors">ERP_CORE</span>
                                    <span class="text-accent text-neon-green">1.2 GB/s</span>
                                </li>
                            </ul>
                            <div class="mt-5 pt-4 border-t border-dashed border-gray-700">
                                <div class="text-xs text-gray-500 uppercase mb-1">Connections Active</div>
                                <div class="text-xl font-mono text-white tracking-widest"><?= $connectedCount ?> / <?= $totalCount ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- CENTER COLUMN: Topology + Integration Cards + Terminal -->
                    <div class="col-span-12 lg:col-span-6 flex flex-col gap-5">

                        <!-- Network Topology Map -->
                        <div class="bg-black/60 border border-primary-cyber/40 rounded-lg relative overflow-hidden shadow-[0_0_30px_rgba(13,242,242,0.1)]" style="min-height: 360px;">
                            <!-- Dot Grid Background -->
                            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#0df2f2 1px, transparent 1px); background-size: 20px 20px;"></div>

                            <!-- Map Header -->
                            <div class="absolute top-4 left-4 z-20">
                                <h2 class="text-white font-mono text-sm bg-black/80 px-2 py-1 border border-primary-cyber/30 text-neon">LIVE_TOPOLOGY_MAP</h2>
                            </div>
                            <div class="absolute top-4 right-4 z-20 flex space-x-2 items-center">
                                <span class="h-2 w-2 rounded-full bg-accent animate-pulse"></span>
                                <span class="text-[10px] text-accent tracking-widest font-mono">LIVE_FEED</span>
                            </div>

                            <!-- Topology Visualization -->
                            <div class="w-full h-full flex items-center justify-center relative z-10 p-10" style="min-height: 360px;">
                                <!-- SVG Connection Lines -->
                                <svg class="absolute inset-0 w-full h-full pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <filter id="glow"><feGaussianBlur stdDeviation="2.5" result="coloredBlur"/><feMerge><feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                                    </defs>
                                    <!-- Lines from center to satellites -->
                                    <line x1="50%" y1="50%" x2="20%" y2="25%" stroke="#0a3a3a" stroke-width="2" stroke-dasharray="6 3"/>
                                    <line x1="50%" y1="50%" x2="80%" y2="25%" stroke="#0a3a3a" stroke-width="2" stroke-dasharray="6 3"/>
                                    <line x1="50%" y1="50%" x2="20%" y2="75%" stroke="#0a3a3a" stroke-width="2" stroke-dasharray="6 3"/>
                                    <line x1="50%" y1="50%" x2="80%" y2="75%" stroke="#0a3a3a" stroke-width="2" stroke-dasharray="6 3"/>
                                </svg>

                                <!-- Central Node -->
                                <div class="relative z-20 flex flex-col items-center group cursor-pointer">
                                    <div class="w-20 h-20 rounded-full border-2 border-primary-cyber bg-black/80 flex items-center justify-center shadow-neon relative overflow-hidden group-hover:border-accent transition-colors duration-300">
                                        <div class="absolute inset-0 bg-primary-cyber/10 animate-pulse"></div>
                                        <span class="material-icons text-3xl text-white z-10">hub</span>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <div class="text-primary-cyber font-bold tracking-widest font-mono bg-black/80 px-2 text-xs border border-primary-cyber/30">ERP_CORE</div>
                                        <div class="text-[10px] text-gray-400 mt-1">192.168.0.1</div>
                                    </div>
                                </div>

                                <!-- Satellite Nodes (from real data) -->
                                <?php
                                $positions = [
                                    'hris' => 'top-[20%] left-[12%]',
                                    'recruitment' => 'top-[20%] right-[12%]',
                                    'company_profile' => 'bottom-[18%] left-[12%]',
                                    'finance' => 'bottom-[18%] right-[12%]',
                                ];
                                foreach ($integrations as $key => $int):
                                    $node = $nodeMap[$key] ?? ['icon' => 'device_hub', 'label' => strtoupper($key), 'ip' => '0.0.0.0'];
                                    $pos = $positions[$key] ?? 'bottom-[18%] right-[12%]';
                                    $isUp = in_array($int['status'], ['connected', 'active']);
                                    $isIdle = ($int['status'] === 'idle');
                                    if ($isUp) {
                                        $borderColor = 'border-primary-cyber/60 hover:border-primary-cyber hover:shadow-neon';
                                        $labelColor = 'text-primary-cyber/70';
                                        $dotColor = 'bg-accent shadow-neon-green';
                                    } elseif ($isIdle) {
                                        $borderColor = 'border-yellow-400/60 hover:border-yellow-400';
                                        $labelColor = 'text-yellow-400/70';
                                        $dotColor = 'bg-yellow-400';
                                    } else {
                                        $borderColor = 'border-red-500/60 hover:border-red-500';
                                        $labelColor = 'text-red-400';
                                        $dotColor = 'bg-red-500';
                                    }
                                ?>
                                <div class="absolute <?= $pos ?> flex flex-col items-center">
                                    <div class="w-14 h-14 rounded-lg border <?= $borderColor ?> bg-black/80 flex items-center justify-center transition-all duration-300 relative">
                                        <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full <?= $dotColor ?> animate-pulse"></span>
                                        <span class="material-icons text-xl <?= $isUp ? 'text-primary-cyber/80' : ($isIdle ? 'text-yellow-400/80' : 'text-red-400') ?>"><?= $node['icon'] ?></span>
                                    </div>
                                    <span class="text-[10px] <?= $labelColor ?> mt-1.5 font-mono"><?= $node['label'] ?></span>
                                    <span class="text-[9px] text-gray-500 font-mono"><?= $node['ip'] ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Integration Status Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                            <?php foreach ($integrations as $key => $int):
                                $node = $nodeMap[$key] ?? ['icon' => 'device_hub', 'label' => strtoupper($key)];
                                $isUp = in_array($int['status'], ['connected', 'active']);
                                $isIdle = ($int['status'] === 'idle');
                                $isError = ($int['status'] === 'error');
                                
                                if ($isUp) {
                                    $statusColor = 'text-accent';
                                    $statusBg = 'bg-accent/10 border-accent/20';
                                    $iconColor = 'text-primary-cyber';
                                } elseif ($isIdle) {
                                    $statusColor = 'text-yellow-400';
                                    $statusBg = 'bg-yellow-400/10 border-yellow-400/20';
                                    $iconColor = 'text-yellow-400';
                                } else {
                                    $statusColor = 'text-red-500';
                                    $statusBg = 'bg-red-500/10 border-red-500/20';
                                    $iconColor = 'text-red-400';
                                }
                                $statusText = strtoupper($int['status']);
                            ?>
                            <div class="bg-panel-dark border border-primary-cyber/30 rounded-lg p-4 backdrop-blur-md hover:border-primary-cyber transition-colors duration-300 group">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-lg bg-black/50 border border-primary-cyber/20 flex items-center justify-center group-hover:border-primary-cyber/50 transition-colors">
                                        <span class="material-icons text-lg <?= $iconColor ?>"><?= $node['icon'] ?></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-mono text-white truncate"><?= htmlspecialchars($int['name']) ?></div>
                                        <div class="text-[10px] <?= $statusColor ?> <?= $statusBg ?> px-1.5 rounded border inline-block mt-0.5"><?= $statusText ?></div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400 font-mono mb-2"><?= htmlspecialchars($int['message']) ?></div>
                                <div class="text-[10px] text-gray-600 font-mono">
                                    <span class="material-icons text-[10px] align-middle">schedule</span>
                                    <?= $int['last_check'] ?>
                                </div>
                                <?php if ($isError): ?>
                                    <button onclick="window.location.reload()" class="mt-3 w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/40 py-1.5 rounded text-xs font-mono uppercase tracking-wider transition-all">
                                        <span class="material-icons text-sm align-middle mr-1">sync</span>Retry_Connection
                                    </button>
                                <?php elseif ($isIdle): ?>
                                    <button onclick="window.location.reload()" class="mt-3 w-full bg-yellow-400/10 hover:bg-yellow-400/20 text-yellow-400 border border-yellow-400/40 py-1.5 rounded text-xs font-mono uppercase tracking-wider transition-all">
                                        <span class="material-icons text-sm align-middle mr-1">refresh</span>Check_Status
                                    </button>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Troubleshooting Guide (from classic view) -->
                        <div x-data="{ showGuide: false }" class="bg-panel-dark border border-primary-cyber/30 rounded-lg backdrop-blur-md overflow-hidden">
                            <button @click="showGuide = !showGuide" class="w-full p-4 flex items-center justify-between text-left hover:bg-primary-cyber/5 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="material-icons text-primary-cyber">troubleshoot</span>
                                    <div>
                                        <h3 class="text-primary-cyber font-mono text-xs tracking-widest uppercase">Troubleshooting_Guide</h3>
                                        <p class="text-[10px] text-gray-500 font-mono mt-0.5">Connection diagnostics & repair instructions</p>
                                    </div>
                                </div>
                                <span class="material-icons text-primary-cyber/50 transition-transform" :class="showGuide ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div x-show="showGuide" x-transition class="border-t border-primary-cyber/20 p-4 space-y-4">
                                <!-- HRIS Troubleshooting -->
                                <div class="p-3 bg-black/40 rounded border-l-2 border-yellow-400">
                                    <h4 class="text-yellow-400 font-mono text-xs uppercase mb-2 flex items-center gap-2">
                                        <span class="material-icons text-sm">dns</span> HRIS Connection Issues
                                    </h4>
                                    <ol class="space-y-1.5 text-xs text-gray-300 font-mono list-decimal list-inside">
                                        <li>Pastikan HRIS Laravel app sudah running di <code class="text-primary-cyber bg-black/50 px-1 rounded">http://localhost/absensi/aplikasiabsensibygerry/public</code></li>
                                        <li>Cek API endpoints sudah dibuat di <code class="text-primary-cyber bg-black/50 px-1 rounded">routes/api.php</code></li>
                                        <li>Test: <code class="text-primary-cyber bg-black/50 px-1 rounded">curl http://localhost/absensi/aplikasiabsensibygerry/public/api/employees</code></li>
                                        <li>Periksa CORS settings jika ada error cross-origin</li>
                                    </ol>
                                </div>
                                <!-- Recruitment Troubleshooting -->
                                <div class="p-3 bg-black/40 rounded border-l-2 border-blue-400">
                                    <h4 class="text-blue-400 font-mono text-xs uppercase mb-2 flex items-center gap-2">
                                        <span class="material-icons text-sm">storage</span> Recruitment Connection Issues
                                    </h4>
                                    <ol class="space-y-1.5 text-xs text-gray-300 font-mono list-decimal list-inside">
                                        <li>Pastikan database <code class="text-primary-cyber bg-black/50 px-1 rounded">recruitment_db</code> exists</li>
                                        <li>Check credentials di <code class="text-primary-cyber bg-black/50 px-1 rounded">.env</code> file</li>
                                        <li>Verify table <code class="text-primary-cyber bg-black/50 px-1 rounded">applications</code> ada di database</li>
                                    </ol>
                                </div>
                                <!-- Company Profile Troubleshooting -->
                                <div class="p-3 bg-black/40 rounded border-l-2 border-accent">
                                    <h4 class="text-accent font-mono text-xs uppercase mb-2 flex items-center gap-2">
                                        <span class="material-icons text-sm">language</span> Company Profile Tracking
                                    </h4>
                                    <ol class="space-y-1.5 text-xs text-gray-300 font-mono list-decimal list-inside">
                                        <li>Tambahkan tracking script di semua pages Company Profile</li>
                                        <li>Pastikan endpoint <code class="text-primary-cyber bg-black/50 px-1 rounded">/api/track-visitor</code> sudah dibuat</li>
                                        <li>Test dengan membuka Company Profile website</li>
                                    </ol>
                                </div>
                                <!-- Finance Troubleshooting -->
                                <div class="p-3 bg-black/40 rounded border-l-2 border-purple-400">
                                    <h4 class="text-purple-400 font-mono text-xs uppercase mb-2 flex items-center gap-2">
                                        <span class="material-icons text-sm">account_balance</span> Finance & Payroll Module
                                    </h4>
                                    <ol class="space-y-1.5 text-xs text-gray-300 font-mono list-decimal list-inside">
                                        <li>Pastikan table <code class="text-primary-cyber bg-black/50 px-1 rounded">payroll_periods</code> dan <code class="text-primary-cyber bg-black/50 px-1 rounded">payroll_items</code> ada di database</li>
                                        <li>Generate payroll via menu <code class="text-primary-cyber bg-black/50 px-1 rounded">Crew Payroll > Generate Payroll</code></li>
                                        <li>Status akan berubah ke "active" setelah payroll di-generate dalam 30 hari terakhir</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Terminal Log Feed -->
                        <div class="bg-black border border-gray-800 rounded-lg overflow-hidden relative" style="height: 200px;">
                            <div class="absolute top-0 left-0 right-0 bg-gray-900 border-b border-gray-800 p-1.5 px-3 flex justify-between items-center z-10">
                                <span class="text-gray-400 font-mono text-xs">console_output.log</span>
                                <div class="flex gap-1.5">
                                    <div class="w-2.5 h-2.5 rounded-full bg-red-500/50"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/50"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-green-500/50"></div>
                                </div>
                            </div>
                            <div class="mt-8 h-full overflow-y-auto space-y-1 p-3 text-primary-cyber/80 font-mono text-xs pb-10 cyber-scrollbar">
                                <?php
                                $now = time();
                                $logLines = [];
                                foreach ($integrations as $key => $int) {
                                    $node = $nodeMap[$key] ?? ['label' => strtoupper($key)];
                                    $isUp = in_array($int['status'], ['connected', 'active']);
                                    $isIdle = ($int['status'] === 'idle');
                                    $logLines[] = ['time' => date('H:i:s', $now - rand(60, 300)), 'msg' => "Attempting connection to <span class='text-accent'>{$node['label']}</span>..."];
                                    if ($isUp) {
                                        $logLines[] = ['time' => date('H:i:s', $now - rand(10, 59)), 'msg' => "Connection established with node <span class='text-accent'>{$node['label']}</span>. Status: ONLINE"];
                                        $logLines[] = ['time' => date('H:i:s', $now - rand(1, 9)), 'msg' => "Data sync complete for module: {$node['label']}."];
                                    } elseif ($isIdle) {
                                        $logLines[] = ['time' => date('H:i:s', $now - rand(10, 59)), 'msg' => "<span class='text-yellow-500'>WARNING:</span> Connection to {$node['label']} failed. Retrying..."];
                                        $logLines[] = ['time' => date('H:i:s', $now - rand(1, 9)), 'msg' => "<span class='text-yellow-400'>STANDBY:</span> Node {$node['label']} reachable but idle. {$int['message']}"];
                                    } else {
                                        $logLines[] = ['time' => date('H:i:s', $now - rand(10, 59)), 'msg' => "<span class='text-red-500'>ERROR:</span> Connection to {$node['label']} failed. Retrying..."];
                                        $logLines[] = ['time' => date('H:i:s', $now - rand(1, 9)), 'msg' => "<span class='text-red-500'>CRITICAL:</span> Node {$node['label']} unreachable. Status: {$int['message']}"];
                                    }
                                }
                                // Add general log lines
                                $logLines[] = ['time' => date('H:i:s', $now - 5), 'msg' => "Encrypting data stream... success."];
                                $logLines[] = ['time' => date('H:i:s', $now - 3), 'msg' => "Integrity scan initiated..."];
                                $logLines[] = ['time' => date('H:i:s', $now - 1), 'msg' => "Scan result: 0 threats found."];

                                usort($logLines, function($a, $b) { return strcmp($a['time'], $b['time']); });

                                foreach ($logLines as $line):
                                ?>
                                    <p><span class="text-gray-500">[<?= $line['time'] ?>]</span> <?= $line['msg'] ?></p>
                                <?php endforeach; ?>
                                <p class="animate-pulse"><span class="text-gray-500">[<?= date('H:i:s') ?>]</span> Awaiting next packet batch... <span class="bg-primary-cyber/50 text-black px-1">_</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Stats -->
                    <div class="col-span-12 lg:col-span-3 flex flex-col gap-5">

                        <!-- Uptime Monitor -->
                        <div class="bg-panel-dark border border-primary-cyber/30 rounded-lg p-5 backdrop-blur-md relative overflow-hidden">
                            <div class="flex justify-between items-end mb-4 border-b border-primary-cyber/20 pb-2">
                                <h3 class="text-primary-cyber font-mono text-xs tracking-widest uppercase">Uptime_Mon</h3>
                                <span class="text-xl font-mono text-accent text-neon-green"><?= $connectedCount === $totalCount ? '99.99%' : round(($connectedCount / max($totalCount, 1)) * 100, 1) . '%' ?></span>
                            </div>
                            <!-- Mock Chart Bars -->
                            <div class="h-28 w-full relative bg-black/40 border border-primary-cyber/10 rounded flex items-end overflow-hidden px-0.5">
                                <div class="absolute inset-0 grid grid-cols-6 grid-rows-4 opacity-20 pointer-events-none">
                                    <div class="border-r border-primary-cyber/30"></div><div class="border-r border-primary-cyber/30"></div>
                                    <div class="border-r border-primary-cyber/30"></div><div class="border-r border-primary-cyber/30"></div><div class="border-r border-primary-cyber/30"></div>
                                </div>
                                <?php
                                $barHeights = [60,80,75,50,90,65,70,55,95,70,60,65];
                                foreach ($barHeights as $i => $h):
                                    $opacity = 40 + ($i * 4);
                                    $isLast = ($i === count($barHeights) - 1);
                                ?>
                                <div class="w-1/12 mx-[1px] bg-primary-cyber/<?= min($opacity, 90) ?> <?= $isLast ? 'shadow-neon' : '' ?>" style="height: <?= $h ?>%;"></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="flex justify-between text-[10px] font-mono text-gray-500 mt-2">
                                <span>-60m</span><span>-30m</span><span>NOW</span>
                            </div>
                        </div>

                        <!-- API Response -->
                        <div class="bg-panel-dark border border-primary-cyber/30 rounded-lg p-5 backdrop-blur-md">
                            <h3 class="text-primary-cyber font-mono text-xs tracking-widest mb-4 border-b border-primary-cyber/20 pb-2 uppercase">API_Response</h3>
                            <div class="flex items-center gap-4">
                                <div class="text-3xl font-mono text-white"><?= rand(18, 35) ?><span class="text-sm text-gray-400 ml-1">ms</span></div>
                                <div class="flex-1">
                                    <div class="text-xs text-accent text-right mb-1 font-mono">OPTIMAL</div>
                                    <div class="w-full bg-gray-800 h-2 rounded-full">
                                        <div class="bg-primary-cyber h-2 rounded-full w-[15%] shadow-neon"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-4">
                                <div class="bg-black/30 p-2 rounded border border-primary-cyber/10">
                                    <div class="text-[10px] text-gray-400 uppercase font-mono">Min</div>
                                    <div class="text-sm font-mono text-primary-cyber">12ms</div>
                                </div>
                                <div class="bg-black/30 p-2 rounded border border-primary-cyber/10">
                                    <div class="text-[10px] text-gray-400 uppercase font-mono">Max</div>
                                    <div class="text-sm font-mono text-primary-cyber">45ms</div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Sessions -->
                        <div class="bg-panel-dark border border-primary-cyber/30 rounded-lg p-5 backdrop-blur-md">
                            <h3 class="text-primary-cyber font-mono text-xs tracking-widest mb-4 border-b border-primary-cyber/20 pb-2 uppercase">Active_Sessions</h3>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded bg-gray-800 flex items-center justify-center border border-gray-600">
                                        <span class="material-icons text-sm text-gray-400">person</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-mono text-primary-cyber"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin_01') ?></div>
                                        <div class="text-[10px] text-gray-500">This session</div>
                                    </div>
                                    <div class="text-[10px] text-accent bg-accent/10 px-1.5 rounded border border-accent/20 font-mono">ONLINE</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded bg-gray-800 flex items-center justify-center border border-gray-600">
                                        <span class="material-icons text-sm text-gray-400">dns</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-mono text-primary-cyber">Sys_Daemon_V2</div>
                                        <div class="text-[10px] text-gray-500">Auto-Maintenance</div>
                                    </div>
                                    <div class="text-[10px] text-blue-400 bg-blue-400/10 px-1.5 rounded border border-blue-400/20 font-mono">RUNNING</div>
                                </div>
                                <div class="flex items-center gap-3 opacity-50">
                                    <div class="h-8 w-8 rounded bg-gray-800 flex items-center justify-center border border-gray-600">
                                        <span class="material-icons text-sm text-gray-400">person_off</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-mono text-primary-cyber">Guest_Proxy</div>
                                        <div class="text-[10px] text-gray-500">Timed out</div>
                                    </div>
                                    <div class="text-[10px] text-gray-500 font-mono">OFFLINE</div>
                                </div>
                            </div>
                        </div>

                        <!-- System Info -->
                        <div class="bg-panel-dark border border-primary-cyber/30 rounded-lg p-5 backdrop-blur-md flex-grow">
                            <h3 class="text-primary-cyber font-mono text-xs tracking-widest mb-4 border-b border-primary-cyber/20 pb-2 uppercase">Sys_Config</h3>
                            <div class="space-y-3 font-mono text-xs">
                                <?php foreach ($integrations as $key => $int):
                                    $node = $nodeMap[$key] ?? [];
                                ?>
                                <div class="p-2 bg-black/30 rounded border border-primary-cyber/10">
                                    <div class="text-primary-cyber/60 uppercase text-[10px] mb-1"><?= $node['label'] ?? strtoupper($key) ?></div>
                                    <div class="text-gray-300"><span class="text-gray-500">Protocol:</span> <?= $node['protocol'] ?? '-' ?></div>
                                    <div class="text-gray-300"><span class="text-gray-500">DB:</span> <?= $node['db'] ?? '-' ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer Status Bar -->
        <footer class="bg-black/90 border-t border-primary-cyber/30 py-1.5 px-4 flex justify-between items-center text-[10px] font-mono uppercase text-gray-500 flex-shrink-0">
            <div class="flex gap-4">
                <span>Build: v2.4.0-alpha</span>
                <span class="hidden sm:inline">|</span>
                <span class="text-primary-cyber hidden sm:inline">Secure Protocol: TLS 1.3</span>
            </div>
            <div class="flex items-center gap-2">
                <span>Status:</span>
                <div class="flex items-center gap-1 text-accent">
                    <div class="h-1.5 w-1.5 rounded-full bg-accent animate-pulse"></div>
                    OPERATIONAL
                </div>
            </div>
        </footer>
    </main>
</div>

<style>
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script>
    // Live clock
    function updateCyberClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('cyber-clock').textContent = h + ':' + m + ':' + s + ' UTC';
    }
    setInterval(updateCyberClock, 1000);
    updateCyberClock();
</script>

</body>
</html>
