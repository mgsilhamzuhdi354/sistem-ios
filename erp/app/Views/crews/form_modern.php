<?php
/**
 * Modern Crew Form View - Create/Edit
 * White modern theme with Tailwind + Alpine.js
 */
$currentPage = 'crews';
$isEdit = !empty($crew);
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit Crew' : 'Tambah Crew Baru' ?> - IndoOcean ERP</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#0F172A",
                        "background-light": "#F8FAFC",
                        "accent-gold": "#B59410",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                }
            }
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-background-light text-slate-900 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto ml-0 lg:ml-64">
            <!-- Top Header -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex items-center space-x-4">
                    <a href="<?= BASE_URL ?>crews" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-icons text-xl">arrow_back</span>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-primary">
                            <?= $isEdit ? 'Edit Crew' : 'Tambah Crew Baru' ?>
                        </h1>
                        <p class="text-xs text-slate-400"><?= $isEdit ? 'Edit informasi crew' : 'Tambahkan crew baru ke database' ?></p>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>crews"
                   class="flex items-center space-x-2 px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-200 transition-all">
                    <span class="material-icons text-lg">arrow_back</span>
                    <span>Kembali</span>
                </a>
            </header>

            <!-- Form Content -->
            <div class="p-8 max-w-7xl mx-auto">
                <form method="POST" action="<?= BASE_URL ?>crews/<?= $isEdit ? 'update/' . $crew['id'] : 'store' ?>"
                      enctype="multipart/form-data" x-data="crewForm()">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        <!-- Personal Information Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                                <div class="flex items-center space-x-2">
                                    <span class="material-icons text-blue-600 text-xl">person</span>
                                    <h3 class="font-bold text-sm text-primary">Informasi Pribadi</h3>
                                </div>
                            </div>
                            <div class="p-6 space-y-5">
                                <!-- Employee ID -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Employee ID</label>
                                    <input type="text" value="<?= htmlspecialchars($employeeId ?? '') ?>" disabled
                                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-500 font-mono cursor-not-allowed">
                                </div>

                                <!-- Nama Lengkap -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="full_name" required
                                           value="<?= htmlspecialchars($crew['full_name'] ?? '') ?>"
                                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                           placeholder="Masukkan nama lengkap">
                                </div>

                                <!-- Nama Panggilan & Jenis Kelamin -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Panggilan</label>
                                        <input type="text" name="nickname"
                                               value="<?= htmlspecialchars($crew['nickname'] ?? '') ?>"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Kelamin</label>
                                        <select name="gender"
                                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                                            <option value="male" <?= ($crew['gender'] ?? 'male') === 'male' ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="female" <?= ($crew['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Tanggal & Tempat Lahir -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Lahir</label>
                                        <input type="date" name="birth_date" value="<?= $crew['birth_date'] ?? '' ?>"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tempat Lahir</label>
                                        <input type="text" name="birth_place"
                                               value="<?= htmlspecialchars($crew['birth_place'] ?? '') ?>"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                    </div>
                                </div>

                                <!-- Kewarganegaraan & Agama -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Kewarganegaraan</label>
                                        <input type="text" name="nationality"
                                               value="<?= htmlspecialchars($crew['nationality'] ?? 'Indonesia') ?>"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Agama</label>
                                        <select name="religion"
                                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                                            <option value="">Pilih</option>
                                            <option value="Islam" <?= ($crew['religion'] ?? '') === 'Islam' ? 'selected' : '' ?>>Islam</option>
                                            <option value="Kristen" <?= ($crew['religion'] ?? '') === 'Kristen' ? 'selected' : '' ?>>Kristen</option>
                                            <option value="Katolik" <?= ($crew['religion'] ?? '') === 'Katolik' ? 'selected' : '' ?>>Katolik</option>
                                            <option value="Hindu" <?= ($crew['religion'] ?? '') === 'Hindu' ? 'selected' : '' ?>>Hindu</option>
                                            <option value="Buddha" <?= ($crew['religion'] ?? '') === 'Buddha' ? 'selected' : '' ?>>Buddha</option>
                                            <option value="Konghucu" <?= ($crew['religion'] ?? '') === 'Konghucu' ? 'selected' : '' ?>>Konghucu</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Status Pernikahan -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Status Pernikahan</label>
                                    <select name="marital_status"
                                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                                        <option value="single" <?= ($crew['marital_status'] ?? 'single') === 'single' ? 'selected' : '' ?>>Belum Menikah</option>
                                        <option value="married" <?= ($crew['marital_status'] ?? '') === 'married' ? 'selected' : '' ?>>Menikah</option>
                                        <option value="divorced" <?= ($crew['marital_status'] ?? '') === 'divorced' ? 'selected' : '' ?>>Cerai</option>
                                        <option value="widowed" <?= ($crew['marital_status'] ?? '') === 'widowed' ? 'selected' : '' ?>>Duda/Janda</option>
                                    </select>
                                </div>

                                <!-- Foto -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Foto</label>
                                    <div class="flex items-center gap-4">
                                        <?php if (!empty($crew['photo'])): ?>
                                            <img src="<?= BASE_URL . $crew['photo'] ?>" alt="Current Photo"
                                                 class="w-16 h-16 rounded-xl object-cover border-2 border-slate-100 shadow-sm">
                                        <?php else: ?>
                                            <div class="w-16 h-16 rounded-xl bg-blue-600 flex items-center justify-center text-white text-xl font-bold border-2 border-blue-100 shadow-sm">
                                                <?= strtoupper(substr($crew['full_name'] ?? 'N', 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-1">
                                            <input type="file" name="photo" accept="image/*"
                                                   class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                                            <p class="text-[10px] text-slate-400 mt-1">Max 2MB. Format: JPG, PNG, GIF</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact & Address Card -->
                        <div class="space-y-6">
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                                    <div class="flex items-center space-x-2">
                                        <span class="material-icons text-blue-600 text-xl">contact_phone</span>
                                        <h3 class="font-bold text-sm text-primary">Kontak & Alamat</h3>
                                    </div>
                                </div>
                                <div class="p-6 space-y-5">
                                    <!-- Email -->
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Email</label>
                                        <input type="email" name="email"
                                               value="<?= htmlspecialchars($crew['email'] ?? '') ?>"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                               placeholder="email@example.com">
                                    </div>

                                    <!-- No. Telepon & WhatsApp -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">No. Telepon</label>
                                            <input type="tel" name="phone"
                                                   value="<?= htmlspecialchars($crew['phone'] ?? '') ?>"
                                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">No. WhatsApp</label>
                                            <input type="tel" name="whatsapp"
                                                   value="<?= htmlspecialchars($crew['whatsapp'] ?? '') ?>"
                                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                        </div>
                                    </div>

                                    <!-- Alamat -->
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Alamat</label>
                                        <textarea name="address" rows="2"
                                                  class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none"><?= htmlspecialchars($crew['address'] ?? '') ?></textarea>
                                    </div>

                                    <!-- Kota, Provinsi, Kode Pos -->
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Kota</label>
                                            <input type="text" name="city"
                                                   value="<?= htmlspecialchars($crew['city'] ?? '') ?>"
                                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Provinsi</label>
                                            <input type="text" name="province"
                                                   value="<?= htmlspecialchars($crew['province'] ?? '') ?>"
                                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Kode Pos</label>
                                            <input type="text" name="postal_code"
                                                   value="<?= htmlspecialchars($crew['postal_code'] ?? '') ?>"
                                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                        </div>
                                    </div>

                                    <!-- Emergency Contact -->
                                    <div class="pt-4 border-t border-slate-100">
                                        <div class="flex items-center space-x-2 mb-4">
                                            <span class="material-icons text-amber-500 text-lg">warning</span>
                                            <h4 class="font-bold text-sm text-primary">Kontak Darurat</h4>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama</label>
                                                <input type="text" name="emergency_name"
                                                       value="<?= htmlspecialchars($crew['emergency_name'] ?? '') ?>"
                                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Hubungan</label>
                                                    <input type="text" name="emergency_relation"
                                                           value="<?= htmlspecialchars($crew['emergency_relation'] ?? '') ?>"
                                                           placeholder="Ayah, Ibu, Istri, dll"
                                                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">No. Telepon</label>
                                                    <input type="tel" name="emergency_phone"
                                                           value="<?= htmlspecialchars($crew['emergency_phone'] ?? '') ?>"
                                                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Information Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                                <div class="flex items-center space-x-2">
                                    <span class="material-icons text-blue-600 text-xl">account_balance</span>
                                    <h3 class="font-bold text-sm text-primary">Informasi Bank</h3>
                                </div>
                            </div>
                            <div class="p-6 space-y-5">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Bank</label>
                                    <input type="text" name="bank_name"
                                           value="<?= htmlspecialchars($crew['bank_name'] ?? '') ?>"
                                           placeholder="BCA, Mandiri, BNI, dll"
                                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">No. Rekening</label>
                                    <input type="text" name="bank_account"
                                           value="<?= htmlspecialchars($crew['bank_account'] ?? '') ?>"
                                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Pemilik Rekening</label>
                                    <input type="text" name="bank_holder"
                                           value="<?= htmlspecialchars($crew['bank_holder'] ?? '') ?>"
                                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                                <div class="flex items-center space-x-2">
                                    <span class="material-icons text-blue-600 text-xl">sailing</span>
                                    <h3 class="font-bold text-sm text-primary">Informasi Profesional</h3>
                                </div>
                            </div>
                            <div class="p-6 space-y-5">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pengalaman (Tahun)</label>
                                    <input type="number" name="years_experience"
                                           value="<?= $crew['years_experience'] ?? 0 ?>" min="0"
                                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Status</label>
                                    <select name="status"
                                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                                        <option value="available" <?= ($crew['status'] ?? 'available') === 'available' ? 'selected' : '' ?>>Available</option>
                                        <option value="onboard" <?= ($crew['status'] ?? '') === 'onboard' ? 'selected' : '' ?>>On Board</option>
                                        <option value="standby" <?= ($crew['status'] ?? '') === 'standby' ? 'selected' : '' ?>>Standby</option>
                                        <option value="terminated" <?= ($crew['status'] ?? '') === 'terminated' ? 'selected' : '' ?>>Terminated</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Catatan</label>
                                    <textarea name="notes" rows="3"
                                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none"><?= htmlspecialchars($crew['notes'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex items-center justify-end gap-3">
                        <a href="<?= BASE_URL ?>crews"
                           class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all flex items-center gap-2">
                            <span class="material-icons text-lg">close</span>
                            Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
                            <span class="material-icons text-lg">save</span>
                            <?= $isEdit ? 'Update Crew' : 'Simpan Crew' ?>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
    function crewForm() {
        return {
            // Add any reactive form behavior here if needed
        }
    }
    </script>
</body>
</html>
