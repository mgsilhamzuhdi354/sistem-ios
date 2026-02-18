<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vessel | IndoOcean ERP</title>
    
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
                    <span>Vessels</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-800 font-medium">Edit Vessel</span>
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
                <div class="max-w-5xl mx-auto mb-8">
                    <div class="flex flex-col">
                        <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Edit Vessel</h1>
                        <p class="text-slate-500">Update vessel details and technical specifications.</p>
                    </div>
                </div>

                <!-- Form -->
                <form action="<?= BASE_URL ?>vessels/update/<?= $vessel['id'] ?>" method="POST" enctype="multipart/form-data" class="max-w-5xl mx-auto space-y-6 pb-20">
                    
                    <!-- Vessel Information Card -->
                    <div class="bg-white rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 card-header-gradient flex items-center gap-3">
                            <div class="p-2 bg-amber-50 rounded-lg text-secondary">
                                <span class="material-icons-round text-xl">sailing</span>
                            </div>
                            <h2 class="text-lg font-bold text-slate-800">Vessel Information</h2>
                        </div>
                        
                        <div class="p-8 flex flex-col xl:flex-row gap-8">
                            <!-- Photo Upload Section -->
                            <div class="w-full xl:w-72 flex-shrink-0">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Upload Vessel Photo</label>
                                
                                <div x-data="imageUpload()" class="relative">
                                    <input type="file" 
                                           name="vessel_photo" 
                                           id="vessel_photo"
                                           accept="image/jpeg,image/png,image/jpg"
                                           @change="handleFileSelect"
                                           class="hidden">
                                    
                                    <label for="vessel_photo" 
                                           class="group relative w-full aspect-[4/3] bg-slate-50 rounded-2xl border-2 border-dashed border-slate-300 hover:border-secondary transition-all cursor-pointer overflow-hidden flex flex-col items-center justify-center">
                                        
                                        <!-- Current/Preview Image -->
                                        <img :src="preview" 
                                             x-show="preview"
                                             alt="Vessel Preview" 
                                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 group-hover:opacity-40">
                                        
                                        <!-- Placeholder when no image -->
                                        <div x-show="!preview" class="flex flex-col items-center text-slate-400">
                                            <span class="material-icons-round text-6xl mb-3">directions_boat</span>
                                            <p class="text-sm font-medium">No photo uploaded</p>
                                        </div>
                                        
                                        <!-- Hover Overlay -->
                                        <div class="relative z-10 flex flex-col items-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <div class="h-12 w-12 bg-white rounded-full shadow-lg flex items-center justify-center text-secondary mb-3">
                                                <span class="material-icons-round text-2xl">add_a_photo</span>
                                            </div>
                                            <p class="text-sm font-bold text-slate-700" x-text="preview ? 'Change Photo' : 'Upload Photo'"></p>
                                            <p class="text-xs text-slate-500 mt-1">Drag & Drop or Click</p>
                                        </div>
                                    </label>
                                    
                                    <!-- Current photo path (hidden) -->
                                    <input type="hidden" name="current_photo" value="<?= $vessel['image_url'] ?? '' ?>">
                                </div>
                                
                                <div class="mt-3 text-center space-y-1">
                                    <p class="text-xs font-medium text-slate-500">Recommended: 800x600px</p>
                                    <p class="text-xs text-slate-400">JPG, PNG (Max 5MB)</p>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Vessel Name -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700">
                                        Vessel Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           value="<?= htmlspecialchars($vessel['name'] ?? '') ?>"
                                           required
                                           class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 placeholder-slate-400 focus:ring-0 border"
                                           placeholder="Enter vessel name">
                                </div>

                                <!-- IMO Number -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700">IMO Number</label>
                                    <input type="text" 
                                           name="imo_number" 
                                           value="<?= htmlspecialchars($vessel['imo_number'] ?? '') ?>"
                                           class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 placeholder-slate-400 focus:ring-0 border"
                                           placeholder="Enter IMO number">
                                </div>

                                <!-- Vessel Type -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700">Vessel Type</label>
                                    <div class="relative">
                                        <select name="vessel_type_id" 
                                                class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 focus:ring-0 border appearance-none pr-10">
                                            <?php foreach ($vesselTypes as $type): ?>
                                                <option value="<?= $type['id'] ?>" 
                                                    <?= ($vessel['vessel_type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
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
                                    <label class="block text-sm font-semibold text-slate-700">Flag State</label>
                                    <div class="relative">
                                        <select name="flag_state_id" 
                                                class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 focus:ring-0 border appearance-none pr-10">
                                            <?php foreach ($flagStates as $flag): ?>
                                                <option value="<?= $flag['id'] ?>" 
                                                    <?= ($vessel['flag_state_id'] ?? '') == $flag['id'] ? 'selected' : '' ?>>
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
                                    <label class="block text-sm font-semibold text-slate-700">Client / Ship Owner</label>
                                    <div class="relative">
                                        <select name="client_id" 
                                                class="w-full rounded-xl custom-input pl-10 pr-10 py-3 text-slate-700 focus:ring-0 border appearance-none">
                                            <?php foreach ($clients as $client): ?>
                                                <option value="<?= $client['id'] ?>" 
                                                    <?= ($vessel['client_id'] ?? '') == $client['id'] ? 'selected' : '' ?>>
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
                    <div class="bg-white rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 card-header-gradient flex items-center gap-3">
                            <div class="p-2 bg-blue-50 rounded-lg text-primary">
                                <span class="material-icons-round text-xl">info</span>
                            </div>
                            <h2 class="text-lg font-bold text-slate-800">Technical Details</h2>
                        </div>
                        
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Gross Tonnage -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Gross Tonnage (GT)</label>
                                <input type="text" 
                                       name="gross_tonnage" 
                                       value="<?= htmlspecialchars($vessel['gross_tonnage'] ?? '') ?>"
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 border focus:ring-0">
                            </div>

                            <!-- Deadweight -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Deadweight (DWT)</label>
                                <input type="text" 
                                       name="dwt" 
                                       value="<?= htmlspecialchars($vessel['dwt'] ?? '') ?>"
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 border focus:ring-0">
                            </div>

                            <!-- Year Built -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Year Built</label>
                                <input type="number" 
                                       name="year_built" 
                                       value="<?= htmlspecialchars($vessel['year_built'] ?? '') ?>"
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 border focus:ring-0">
                            </div>

                            <!-- Call Sign -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Call Sign</label>
                                <input type="text" 
                                       name="call_sign" 
                                       value="<?= htmlspecialchars($vessel['call_sign'] ?? '') ?>"
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 border focus:ring-0">
                            </div>

                            <!-- Crew Capacity -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Crew Capacity</label>
                                <input type="number" 
                                       name="crew_capacity" 
                                       value="<?= htmlspecialchars($vessel['crew_capacity'] ?? 25) ?>"
                                       class="w-full rounded-xl custom-input px-4 py-3 text-slate-700 border focus:ring-0">
                            </div>

                            <!-- Status -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700">Status</label>
                                <div class="relative">
                                    <select name="status" 
                                            class="w-full rounded-xl custom-input pl-8 pr-10 py-3 text-slate-700 focus:ring-0 border appearance-none">
                                        <option value="active" <?= ($vessel['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="maintenance" <?= ($vessel['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                        <option value="inactive" <?= ($vessel['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    </div>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                        <span class="material-icons-round">expand_more</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="<?= BASE_URL ?>vessels" 
                           class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 rounded-xl bg-secondary hover:bg-amber-600 text-white font-bold shadow-lg shadow-amber-500/30 hover:shadow-amber-500/40 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span class="material-icons-round text-xl">save</span>
                            Update Vessel
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function imageUpload() {
            return {
                preview: '<?= !empty($vessel['image_url']) ? htmlspecialchars($vessel['image_url']) : '' ?>',
                
                handleFileSelect(e) {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.preview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        }
    </script>
</body>
</html>
