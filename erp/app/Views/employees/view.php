<?php
/**
 * Employee Detail View
 * PT Indo Ocean - ERP System
 */
$currentPage = $currentPage ?? 'employees';
$emp = $employee ?? [];

// Extract fields with fallbacks
$empName    = $emp['nama'] ?? $emp['name'] ?? $emp['nama_lengkap'] ?? '-';
$empEmail   = $emp['email'] ?? '-';
$empPhone   = $emp['no_hp'] ?? $emp['phone'] ?? $emp['telepon'] ?? '-';
$empNik     = $emp['nik'] ?? $emp['employee_id'] ?? '-';
$empGender  = $emp['jenis_kelamin'] ?? $emp['gender'] ?? '-';
$empAddress = $emp['alamat'] ?? $emp['address'] ?? '-';

// Jabatan
$empJabatan = '-';
if (isset($emp['jabatan']) && is_array($emp['jabatan'])) {
    $empJabatan = $emp['jabatan']['nama_jabatan'] ?? $emp['jabatan']['nama'] ?? '-';
} elseif (isset($emp['jabatan']) && is_string($emp['jabatan'])) {
    $empJabatan = $emp['jabatan'];
}

// Departemen/Lokasi
$empDept = '-';
if (isset($emp['lokasi']) && is_array($emp['lokasi'])) {
    $empDept = $emp['lokasi']['nama_lokasi'] ?? $emp['lokasi']['nama'] ?? '-';
} elseif (isset($emp['departemen'])) {
    $empDept = $emp['departemen'];
}

// Status
$empStatus = $emp['status_karyawan'] ?? $emp['status'] ?? 'Aktif';
if (empty($empStatus) || $empStatus === 'Unknown') $empStatus = 'Aktif';
$isActive = strtolower($empStatus) === 'aktif';

// Tanggal masuk
$empJoinDate = $emp['tanggal_masuk'] ?? $emp['join_date'] ?? $emp['created_at'] ?? null;
$empJoinFormatted = $empJoinDate ? date('d M Y', strtotime($empJoinDate)) : '-';

// Initials
$nameParts = explode(' ', $empName);
$empInitials = strtoupper(substr($nameParts[0], 0, 1));
if (count($nameParts) > 1) $empInitials .= strtoupper(substr($nameParts[1], 0, 1));

// Avatar
$empAvatar = $emp['avatar'] ?? $emp['foto'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($empName) ?> | Detail Karyawan</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#6366F1', secondary: '#D4AF37' },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                },
            },
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-fade-in-delay-1 { animation: fadeInUp 0.4s ease-out 0.1s forwards; opacity: 0; }
        .animate-fade-in-delay-2 { animation: fadeInUp 0.4s ease-out 0.2s forwards; opacity: 0; }
    </style>
</head>

<body class="bg-gray-100 text-slate-800 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 overflow-y-auto bg-gray-100 custom-scrollbar">

            <!-- Hero Header -->
            <div class="relative bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 px-8 py-10 animate-fade-in">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0di0xaDEwdi0xSDM2di0xMmgxMFYxOUgzNnYtMWgxMHYtMUgzNlYwaDJ2MTdoMTB2MUgzOHYxMmgxMHYxSDM4djEyaDEwdjFIMzh2MWgtMnoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-30"></div>
                <div class="relative flex items-center gap-4">
                    <a href="<?= BASE_URL ?>employees" class="rounded-lg bg-white/10 p-2 text-white/80 hover:bg-white/20 hover:text-white transition-colors">
                        <span class="material-icons-outlined">arrow_back</span>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Detail Karyawan</h1>
                        <p class="text-indigo-200 text-sm mt-0.5">Informasi lengkap karyawan</p>
                    </div>
                </div>
            </div>

            <div class="px-8 -mt-8 pb-8">
                <!-- Profile Card -->
                <div class="rounded-xl bg-white shadow-lg border border-gray-100 p-8 mb-6 animate-fade-in">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                        <!-- Avatar -->
                        <?php if ($empAvatar && file_exists($empAvatar)): ?>
                            <img src="<?= BASE_URL . $empAvatar ?>" alt="<?= htmlspecialchars($empName) ?>" class="w-20 h-20 rounded-2xl object-cover shadow-md">
                        <?php else: ?>
                            <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-2xl font-bold text-white shadow-md">
                                <?= $empInitials ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-2">
                                <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($empName) ?></h2>
                                <?php if ($isActive): ?>
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                        AKTIF
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">
                                        <?= strtoupper(htmlspecialchars($empStatus)) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($empJabatan) ?> · <?= htmlspecialchars($empDept) ?></p>
                            <div class="flex items-center gap-4 mt-3 text-sm text-gray-400">
                                <span class="flex items-center gap-1">
                                    <span class="material-icons-outlined text-base">badge</span>
                                    <?= htmlspecialchars($empNik) ?>
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="material-icons-outlined text-base">calendar_today</span>
                                    Bergabung <?= $empJoinFormatted ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-delay-1">
                    <!-- Personal Information -->
                    <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <span class="material-icons-outlined text-indigo-500">person</span>
                            <h3 class="font-semibold text-gray-900">Informasi Pribadi</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">Nama Lengkap</span>
                                <span class="text-sm font-medium text-gray-900 text-right"><?= htmlspecialchars($empName) ?></span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">NIK</span>
                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($empNik) ?></span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">Jenis Kelamin</span>
                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars(ucfirst($empGender)) ?></span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">Alamat</span>
                                <span class="text-sm font-medium text-gray-900 text-right max-w-[60%]"><?= htmlspecialchars($empAddress) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <span class="material-icons-outlined text-indigo-500">contact_mail</span>
                            <h3 class="font-semibold text-gray-900">Kontak</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">Email</span>
                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($empEmail) ?></span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">Telepon</span>
                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($empPhone) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Information -->
                    <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <span class="material-icons-outlined text-indigo-500">work</span>
                            <h3 class="font-semibold text-gray-900">Informasi Pekerjaan</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">Jabatan</span>
                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($empJabatan) ?></span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">Departemen / Lokasi</span>
                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($empDept) ?></span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">Status</span>
                                <?php if ($isActive): ?>
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">AKTIF</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700"><?= strtoupper(htmlspecialchars($empStatus)) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500">Tanggal Bergabung</span>
                                <span class="text-sm font-medium text-gray-900"><?= $empJoinFormatted ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <span class="material-icons-outlined text-indigo-500">flash_on</span>
                            <h3 class="font-semibold text-gray-900">Aksi Cepat</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 gap-3">
                            <a href="<?= BASE_URL ?>employees/attendance?employee_id=<?= $emp['id'] ?? 0 ?>"
                               class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-700 transition-all">
                                <span class="material-icons-outlined text-xl text-indigo-400">schedule</span>
                                Lihat Absensi
                            </a>
                            <a href="<?= BASE_URL ?>employees/payroll"
                               class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-green-50 hover:border-green-200 hover:text-green-700 transition-all">
                                <span class="material-icons-outlined text-xl text-green-400">payments</span>
                                Lihat Payroll
                            </a>
                            <a href="<?= BASE_URL ?>employees/performance?employee_id=<?= $emp['id'] ?? 0 ?>"
                               class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-purple-50 hover:border-purple-200 hover:text-purple-700 transition-all">
                                <span class="material-icons-outlined text-xl text-purple-400">trending_up</span>
                                Lihat Performance
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Additional Data from API -->
                <?php
                // Show any extra data that might be available
                $extraFields = [];
                $skipKeys = ['id','nama','name','nama_lengkap','email','no_hp','phone','telepon','nik','employee_id',
                    'jenis_kelamin','gender','alamat','address','jabatan','lokasi','departemen','status_karyawan','status',
                    'tanggal_masuk','join_date','created_at','avatar','foto','password','remember_token','updated_at'];
                foreach ($emp as $key => $value) {
                    if (!in_array($key, $skipKeys) && !is_array($value) && !is_null($value) && $value !== '') {
                        $extraFields[$key] = $value;
                    }
                }
                ?>
                <?php if (!empty($extraFields)): ?>
                <div class="mt-6 rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden animate-fade-in-delay-2">
                    <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <span class="material-icons-outlined text-indigo-500">info</span>
                        <h3 class="font-semibold text-gray-900">Informasi Tambahan</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                            <?php foreach ($extraFields as $key => $value): ?>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500"><?= ucwords(str_replace('_', ' ', htmlspecialchars($key))) ?></span>
                                <span class="text-sm font-medium text-gray-900 text-right"><?= htmlspecialchars((string)$value) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="h-8"></div>
            </div>
        </main>
    </div>
</body>
</html>
