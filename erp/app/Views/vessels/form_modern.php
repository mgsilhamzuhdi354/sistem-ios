<!DOCTYPE html>
<html lang="<?= session()->get('lang') ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('vessels.add_vessel') ?> | IndoOcean ERP</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
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
        body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .custom-input {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            transition: all 0.2s;
        }
        
        .custom-input:focus {
            background-color: #ffffff;
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }
        
        .card-header-gradient {
            background: linear-gradient(to right, rgba(30, 58, 138, 0.03), transparent);
        }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.5s ease-out forwards; }
        .animate-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .animate-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .animate-delay-3 { animation-delay: 0.3s; opacity: 0; }
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
            <header class="h-20 flex items-center justify-between px-8 z-10 bg-white/50 backdrop-blur-sm border-b border-slate-200/50 flex-shrink-0">
                <div class="flex items-center text-slate-500 text-sm">
                    <span class="material-icons-round text-lg mr-2 text-slate-400">home</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <a href="<?= BASE_URL ?>vessels" class="hover:text-primary transition-colors"><?= __('vessels.title') ?></a>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-800 font-medium"><?= __('vessels.add_vessel') ?></span>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 bg-white rounded-lg border border-slate-200 p-1 pr-3 shadow-sm">
                        <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded">ID</span>
                        <span class="text-xs font-semibold text-slate-600">EN</span>
                    </div>
                    
                    <button class="h-10 w-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition-colors relative shadow-sm">
                        <span class="material-icons-round">notifications</span>
                        <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-red-500 border border-white"></span>
                    </button>
                </div>
            </header>

            <!-- Form Content -->
            <div class="flex-1 overflow-y-auto px-8 py-8 bg-slate-50/50 custom-scrollbar">
                <!-- Page Header -->
                <div class="max-w-5xl mx-auto mb-8 animate-fade-in">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-2"><?= __('vessels.add_vessel') ?></h1>
                            <p class="text-slate-500"><?= __('vessels.add_vessel_subtitle') ?></p>
                        </div>
                        <a href="<?= BASE_URL ?>vessels" 
                           class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 font-medium hover:bg-white hover:shadow-sm transition-all text-sm">
                            <span class="material-icons-round text-lg">arrow_back</span>
                            Kembali
                        </a>
                    </div>
                </div>

                <!-- Form -->
                <form action="<?= BASE_URL ?>vessels/store" method="POST" class="max-w-5xl mx-auto space-y-6 pb-20">
                    
                    <!-- Vessel Information Card -->
                    <div class="bg-white rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-slate-100 overflow-hidden animate-fade-in animate-delay-1">
                        <div class="px-6 py-4 border-b border-slate-100 card-header-gradient flex items-center gap-3">
                            <div class="p-2 bg-amber-50 rounded-lg text-secondary">
                                <span class="material-icons-round text-xl">sailing</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-800"><?= __('vessels.vessel_info') ?></h2>
                                <p class="text-xs text-slate-400"><?= __('vessels.vessel_info_subtitle') ?></p>
                            </div>
                        </div>
                        
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Vessel Name -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        <?= __('vessels.vessel_name') ?> <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           required
                                           class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 placeholder-slate-400 focus:ring-0 border"
                                           placeholder="Masukkan nama kapal">
                                </div>

                                <!-- IMO Number -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700">IMO Number</label>
                                    <input type="text" 
                                           name="imo_number" 
                                           class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 placeholder-slate-400 focus:ring-0 border"
                                           placeholder="9xxxxxx">
                                </div>

                                <!-- Vessel Type -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700"><?= __('vessels.vessel_type') ?></label>
                                    <div class="relative">
                                        <select name="vessel_type_id" 
                                                class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 focus:ring-0 border appearance-none pr-10">
                                            <option value=""><?= __('vessels.select_type') ?></option>
                                            <?php foreach ($vesselTypes ?? [] as $type): ?>
                                                <option value="<?= $type['id'] ?>">
                                                    <?= htmlspecialchars($type['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                            <span class="material-icons-round">expand_more</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Flag State -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700"><?= __('vessels.flag_state') ?></label>
                                    <div class="relative">
                                        <select name="flag_state_id" 
                                                class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 focus:ring-0 border appearance-none pr-10">
                                            <option value=""><?= __('vessels.select_flag') ?></option>
                                            <?php foreach ($flagStates ?? [] as $flag): ?>
                                                <option value="<?= $flag['id'] ?>">
                                                    <?= htmlspecialchars($flag['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                            <span class="material-icons-round">expand_more</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Client / Owner -->
                                <div class="col-span-1 md:col-span-2 space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700"><?= __('vessels.client_owner') ?></label>
                                    <div class="relative">
                                        <select name="client_id" 
                                                class="w-full rounded-xl custom-input pl-10 pr-10 py-3 text-slate-700 focus:ring-0 border appearance-none">
                                            <option value=""><?= __('vessels.select_client') ?></option>
                                            <?php foreach ($clients ?? [] as $client): ?>
                                                <option value="<?= $client['id'] ?>">
                                                    <?= htmlspecialchars($client['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                            <span class="material-icons-round text-lg">business</span>
                                        </div>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                            <span class="material-icons-round">expand_more</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Details Card -->
                    <div class="bg-white rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-slate-100 overflow-hidden animate-fade-in animate-delay-2">
                        <div class="px-6 py-4 border-b border-slate-100 card-header-gradient flex items-center gap-3">
                            <div class="p-2 bg-blue-50 rounded-lg text-primary">
                                <span class="material-icons-round text-xl">info</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-800"><?= __('vessels.technical_details') ?></h2>
                                <p class="text-xs text-slate-400"><?= __('vessels.technical_details_subtitle') ?></p>
                            </div>
                        </div>
                        
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Gross Tonnage -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Gross Tonnage (GT)</label>
                                <input type="number" 
                                       name="gross_tonnage" 
                                       step="0.01"
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 placeholder-slate-400 border focus:ring-0"
                                       placeholder="Masukkan gross tonnage">
                            </div>

                            <!-- Deadweight -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Deadweight (DWT)</label>
                                <input type="number" 
                                       name="dwt" 
                                       step="0.01"
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 placeholder-slate-400 border focus:ring-0"
                                       placeholder="Masukkan deadweight">
                            </div>

                            <!-- Year Built -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Year Built</label>
                                <input type="number" 
                                       name="year_built" 
                                       min="1900" 
                                       max="<?= date('Y') ?>"
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 placeholder-slate-400 border focus:ring-0"
                                       placeholder="Tahun pembuatan">
                            </div>

                            <!-- Call Sign -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Call Sign</label>
                                <input type="text" 
                                       name="call_sign" 
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 placeholder-slate-400 border focus:ring-0"
                                       placeholder="Masukkan call sign">
                            </div>

                            <!-- Crew Capacity -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Crew Capacity</label>
                                <input type="number" 
                                       name="crew_capacity" 
                                       value="25"
                                       min="1"
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 border focus:ring-0">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-4 animate-fade-in animate-delay-3">
                        <a href="<?= BASE_URL ?>vessels" 
                           class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-white hover:shadow-sm transition-all">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 rounded-xl bg-secondary hover:bg-amber-600 text-white font-bold shadow-lg shadow-amber-500/30 hover:shadow-amber-500/40 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span class="material-icons-round text-xl">save</span>
                            Tambah Kapal
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
