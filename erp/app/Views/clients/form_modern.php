<?php
/**
 * Modern Client Form View (Create/Edit)
 * Consistent with modern.php layout
 */
$isEdit = !empty($client);
$currentPage = 'clients';
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit ' . htmlspecialchars($client['name']) : 'Add New Client' ?> - PT Indo Ocean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1"
        rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#EAB308",
                        "primary-hover": "#CA8A04",
                        "background-light": "#F1F5F9",
                        "surface-light": "#FFFFFF",
                        "border-light": "#E2E8F0",
                        "text-main-light": "#0F172A",
                        "text-sub-light": "#64748B",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
    </style>
</head>

<body class="bg-background-light font-sans text-text-main-light antialiased overflow-hidden h-screen flex">
    <div class="flex h-screen w-full">
        <!-- Modern Sidebar -->
        <?php
        include APPPATH . 'Views/partials/modern_sidebar.php';
        ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 flex flex-col min-w-0 bg-background-light">
            <!-- Header -->
            <header class="h-16 bg-surface-light border-b border-border-light flex items-center justify-between px-6 z-10 shadow-sm">
                <div class="flex items-center gap-3">
                    <a href="<?= BASE_URL ?>clients"
                        class="p-2 hover:bg-slate-100 rounded-lg text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="ph-bold ph-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-base font-bold text-text-main-light flex items-center gap-2">
                        <span class="text-slate-400 font-normal">Client /</span>
                        <?= $isEdit ? 'Edit Client' : 'Tambah Client Baru' ?>
                    </h1>
                </div>
            </header>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-3xl mx-auto">
                    <form method="POST" action="<?= BASE_URL ?>clients/<?= $isEdit ? 'update/' . $client['id'] : 'store' ?>">

                        <!-- Company Information -->
                        <div class="bg-white rounded-xl border border-border-light shadow-sm p-6 mb-6">
                            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <div class="p-1.5 bg-blue-50 rounded-lg">
                                    <i class="ph-fill ph-buildings text-blue-600"></i>
                                </div>
                                Informasi Perusahaan
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Nama Perusahaan <span class="text-red-500">*</span></label>
                                    <input type="text" name="name"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['name'] ?? '') ?>" required
                                        placeholder="Contoh: PT Indo Ocean">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Kode / Nama Singkat</label>
                                    <input type="text" name="short_name"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['short_name'] ?? '') ?>"
                                        placeholder="Contoh: IOC">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Negara</label>
                                    <input type="text" name="country"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['country'] ?? '') ?>"
                                        placeholder="Contoh: Indonesia">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Kota</label>
                                    <input type="text" name="city"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['city'] ?? '') ?>"
                                        placeholder="Contoh: Jakarta">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Alamat</label>
                                    <textarea name="address" rows="2"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                                        placeholder="Alamat lengkap perusahaan"><?= htmlspecialchars($client['address'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details -->
                        <div class="bg-white rounded-xl border border-border-light shadow-sm p-6 mb-6">
                            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <div class="p-1.5 bg-green-50 rounded-lg">
                                    <i class="ph-fill ph-phone text-green-600"></i>
                                </div>
                                Detail Kontak
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Email</label>
                                    <input type="email" name="email"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['email'] ?? '') ?>"
                                        placeholder="email@perusahaan.com">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Telepon</label>
                                    <input type="text" name="phone"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['phone'] ?? '') ?>"
                                        placeholder="+62 812 3456 7890">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Website</label>
                                    <input type="url" name="website"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['website'] ?? '') ?>"
                                        placeholder="https://www.perusahaan.com">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Person -->
                        <div class="bg-white rounded-xl border border-border-light shadow-sm p-6 mb-6">
                            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <div class="p-1.5 bg-purple-50 rounded-lg">
                                    <i class="ph-fill ph-user text-purple-600"></i>
                                </div>
                                Kontak Person
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Nama</label>
                                    <input type="text" name="contact_person"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['contact_person'] ?? '') ?>"
                                        placeholder="Nama lengkap PIC">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Email</label>
                                    <input type="email" name="contact_email"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['contact_email'] ?? '') ?>"
                                        placeholder="email@pic.com">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Telepon</label>
                                    <input type="text" name="contact_phone"
                                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                        value="<?= htmlspecialchars($client['contact_phone'] ?? '') ?>"
                                        placeholder="+62 812 3456 7890">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pb-8">
                            <a href="<?= BASE_URL ?>clients"
                                class="px-5 py-2.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white rounded-lg text-sm font-bold shadow-md shadow-primary/20 transition-all flex items-center gap-2">
                                <i class="ph-bold ph-floppy-disk"></i>
                                <?= $isEdit ? 'Update Client' : 'Simpan Client' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
