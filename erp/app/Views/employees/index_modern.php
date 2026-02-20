<?php
/**
 * Modern Employee Database View
 * PT Indo Ocean - ERP System
 */
$currentPage = $currentPage ?? 'employees';

// Calculate stats
$totalEmployees = count($employees ?? []);
$aktif = count(array_filter($employees ?? [], fn($e) => strtolower($e['status'] ?? $e['status_karyawan'] ?? '') === 'aktif'));
$probation = count(array_filter($employees ?? [], fn($e) => strtolower($e['status'] ?? $e['status_karyawan'] ?? '') === 'probation'));
$resign = count(array_filter($employees ?? [], fn($e) => in_array(strtolower($e['status'] ?? $e['status_karyawan'] ?? ''), ['resign', 'nonaktif'])));

// Avatar color palette
$avatarColors = [
    ['bg-indigo-100 dark:bg-indigo-900/50', 'text-indigo-600 dark:text-indigo-300'],
    ['bg-pink-100 dark:bg-pink-900/50', 'text-pink-600 dark:text-pink-300'],
    ['bg-purple-100 dark:bg-purple-900/50', 'text-purple-600 dark:text-purple-300'],
    ['bg-blue-100 dark:bg-blue-900/50', 'text-blue-600 dark:text-blue-300'],
    ['bg-orange-100 dark:bg-orange-900/50', 'text-orange-600 dark:text-orange-300'],
    ['bg-teal-100 dark:bg-teal-900/50', 'text-teal-600 dark:text-teal-300'],
    ['bg-rose-100 dark:bg-rose-900/50', 'text-rose-600 dark:text-rose-300'],
    ['bg-emerald-100 dark:bg-emerald-900/50', 'text-emerald-600 dark:text-emerald-300'],
];
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('employees.title') ?> | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366F1',
                        secondary: '#D4AF37',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-fade-in-delay-1 { animation: fadeInUp 0.4s ease-out 0.1s forwards; opacity: 0; }
        .animate-fade-in-delay-2 { animation: fadeInUp 0.4s ease-out 0.2s forwards; opacity: 0; }

        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center; z-index: 10000;
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .modal-content {
            background: white; border-radius: 16px; width: 90%; max-width: 560px; max-height: 85vh;
            overflow: hidden; transform: translateY(20px); transition: transform 0.3s ease;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        }
        .modal-overlay.active .modal-content { transform: translateY(0); }

        /* KPI Score Display */
        .kpi-score-circle {
            width: 140px; height: 140px; border-radius: 50%;
            background: conic-gradient(from 0deg, #10b981 0%, #059669 var(--score-percent), #e5e7eb var(--score-percent));
            display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; position: relative;
        }
        .kpi-score-circle::before {
            content: ''; width: 100px; height: 100px; background: white; border-radius: 50%; position: absolute;
        }
        .kpi-score-value {
            position: relative; z-index: 1; font-size: 2rem; font-weight: 700;
            background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        /* Spinner */
        .spinner { width: 36px; height: 36px; border: 3px solid #e5e7eb; border-top-color: #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>

<body class="bg-gray-100 text-slate-800 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 overflow-y-auto bg-gray-100 p-4 md:p-8 custom-scrollbar">
            <!-- Header -->
            <header class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center animate-fade-in">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900"><?= __('employees.title') ?></h2>
                    <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                        <span><?= __('employees.subtitle') ?></span>
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            LIVE
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <select id="statusFilter" onchange="filterByStatus(this.value)"
                            class="appearance-none cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value=""><?= __('common.all') ?> <?= __('common.status') ?></option>
                            <option value="Aktif" <?= ($statusFilter ?? '') === 'Aktif' ? 'selected' : '' ?>><?= __('common.active') ?></option>
                            <option value="Probation" <?= ($statusFilter ?? '') === 'Probation' ? 'selected' : '' ?>><?= __('employees.probation') ?></option>
                            <option value="Resign" <?= ($statusFilter ?? '') === 'Resign' ? 'selected' : '' ?>><?= __('employees.resign') ?></option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <span class="material-icons-outlined text-sm">expand_more</span>
                        </div>
                    </div>
                </div>
            </header>

            <?php if (!$success): ?>
                <!-- Error State -->
                <div class="bg-red-50 border border-red-200 rounded-xl p-6 flex items-start gap-4 animate-fade-in">
                    <div class="rounded-lg bg-red-100 p-3 text-red-600">
                        <span class="material-icons-outlined">error</span>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-red-800"><?= __('employees.connection_error') ?></h3>
                        <p class="text-sm text-red-600 mt-1"><?= __('employees.cannot_connect_hris') ?> <?= htmlspecialchars($error ?? 'Unknown error') ?></p>
                    </div>
                </div>
            <?php else: ?>

            <!-- Stats Cards -->
            <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 animate-fade-in">
                <!-- Total -->
                <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-shadow hover:shadow-lg border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500"><?= __('employees.total_employees') ?></p>
                            <h3 class="mt-2 text-3xl font-bold text-gray-900"><?= $totalEmployees ?></h3>
                        </div>
                        <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                            <span class="material-icons-outlined">groups</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full rounded-full bg-gray-100">
                        <div class="h-1 rounded-full bg-indigo-500" style="width: 100%"></div>
                    </div>
                </div>
                <!-- Aktif -->
                <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-shadow hover:shadow-lg border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500"><?= __('employees.active_employees') ?></p>
                            <h3 class="mt-2 text-3xl font-bold text-gray-900"><?= $aktif ?></h3>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3 text-green-600">
                            <span class="material-icons-outlined">check_circle</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full rounded-full bg-gray-100">
                        <div class="h-1 rounded-full bg-green-500" style="width: <?= $totalEmployees > 0 ? round($aktif/$totalEmployees*100) : 0 ?>%"></div>
                    </div>
                </div>
                <!-- Probation -->
                <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-shadow hover:shadow-lg border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500"><?= __('employees.probation') ?></p>
                            <h3 class="mt-2 text-3xl font-bold text-gray-900"><?= $probation ?></h3>
                        </div>
                        <div class="rounded-lg bg-yellow-50 p-3 text-yellow-600">
                            <span class="material-icons-outlined">hourglass_top</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full rounded-full bg-gray-100">
                        <div class="h-1 rounded-full bg-yellow-500" style="width: <?= $totalEmployees > 0 ? round($probation/$totalEmployees*100) : 0 ?>%"></div>
                    </div>
                </div>
                <!-- Resign -->
                <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-shadow hover:shadow-lg border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500"><?= __('employees.resign') ?> / <?= __('common.inactive') ?></p>
                            <h3 class="mt-2 text-3xl font-bold text-gray-900"><?= $resign ?></h3>
                        </div>
                        <div class="rounded-lg bg-red-50 p-3 text-red-600">
                            <span class="material-icons-outlined">block</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full rounded-full bg-gray-100">
                        <div class="h-1 rounded-full bg-red-500" style="width: <?= $totalEmployees > 0 ? round($resign/$totalEmployees*100) : 0 ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Employee Table -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm animate-fade-in-delay-1">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-4 font-semibold tracking-wider" scope="col"><?= __('employees.employee') ?></th>
                                <th class="px-6 py-4 font-semibold tracking-wider" scope="col"><?= __('employees.position') ?></th>
                                <th class="px-6 py-4 font-semibold tracking-wider" scope="col"><?= __('employees.department') ?></th>
                                <th class="px-6 py-4 font-semibold tracking-wider" scope="col"><?= __('common.status') ?></th>
                                <th class="px-6 py-4 text-right font-semibold tracking-wider" scope="col"><?= __('common.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($employees)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-16">
                                        <span class="material-icons-outlined text-5xl text-gray-300 block mb-4">people_outline</span>
                                        <span class="text-gray-400"><?= __('employees.no_employees') ?></span>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($employees as $i => $employee): ?>
                                    <?php
                                    $empId = $employee['id'] ?? 0;
                                    $empName = $employee['nama'] ?? $employee['name'] ?? $employee['nama_lengkap'] ?? '-';
                                    $empEmail = $employee['email'] ?? '-';
                                    $empInitials = '';
                                    $nameParts = explode(' ', $empName);
                                    $empInitials = strtoupper(substr($nameParts[0], 0, 1));
                                    if (count($nameParts) > 1) {
                                        $empInitials .= strtoupper(substr($nameParts[1], 0, 1));
                                    }

                                    // Jabatan
                                    $empJabatan = '-';
                                    if (isset($employee['jabatan']) && is_array($employee['jabatan'])) {
                                        $empJabatan = $employee['jabatan']['nama_jabatan'] ?? $employee['jabatan']['nama'] ?? '-';
                                    } elseif (isset($employee['jabatan']) && is_string($employee['jabatan'])) {
                                        $empJabatan = $employee['jabatan'];
                                    }

                                    // Departemen/Lokasi
                                    $empDept = '-';
                                    if (isset($employee['lokasi']) && is_array($employee['lokasi'])) {
                                        $empDept = $employee['lokasi']['nama_lokasi'] ?? $employee['lokasi']['nama'] ?? '-';
                                    } elseif (isset($employee['departemen'])) {
                                        $empDept = $employee['departemen'];
                                    }

                                    // Status
                                    $empStatus = $employee['status_karyawan'] ?? $employee['status'] ?? 'Aktif';
                                    if (empty($empStatus) || $empStatus === 'Unknown') {
                                        $empStatus = 'Aktif';
                                    }
                                    $isActive = strtolower($empStatus) === 'aktif';

                                    // Avatar color
                                    $colorPair = $avatarColors[$i % count($avatarColors)];
                                    ?>
                                    <tr class="group hover:bg-gray-50 transition-colors">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full <?= $colorPair[0] ?> text-sm font-bold <?= $colorPair[1] ?>">
                                                    <?= $empInitials ?>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900"><?= htmlspecialchars($empName) ?></div>
                                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($empEmail) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-gray-700"><?= htmlspecialchars($empJabatan) ?></td>
                                        <td class="whitespace-nowrap px-6 py-4 text-gray-700"><?= htmlspecialchars($empDept) ?></td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <?php if ($isActive): ?>
                                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">
                                                    AKTIF
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">
                                                    <?= strtoupper(htmlspecialchars($empStatus)) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <button onclick="showAttendance(<?= $empId ?>, '<?= htmlspecialchars(addslashes($empName)) ?>')"
                                                    class="rounded p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="<?= __('employees.attendance_title') ?>">
                                                    <span class="material-icons-outlined text-xl">schedule</span>
                                                </button>
                                                <button onclick="showKPI(<?= $empId ?>, '<?= htmlspecialchars(addslashes($empName)) ?>')"
                                                    class="rounded p-1.5 text-gray-400 hover:bg-green-50 hover:text-green-600 transition-colors" title="<?= __('employees.kpi_score') ?>">
                                                    <span class="material-icons-outlined text-xl">pie_chart</span>
                                                </button>
                                                <button onclick="showPerformance(<?= $empId ?>, '<?= htmlspecialchars(addslashes($empName)) ?>')"
                                                    class="rounded p-1.5 text-gray-400 hover:bg-purple-50 hover:text-purple-600 transition-colors" title="<?= __('employees.performance') ?>">
                                                    <span class="material-icons-outlined text-xl">trending_up</span>
                                                </button>
                                                <a href="<?= BASE_URL ?>employees/<?= $empId ?>"
                                                    class="rounded p-1.5 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="<?= __('common.details') ?>">
                                                    <span class="material-icons-outlined text-xl">visibility</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4">
                    <p class="text-sm text-gray-500">
                        <?= __('common.showing') ?> <span class="font-medium">1</span> <?= __('common.to') ?> <span class="font-medium"><?= min($totalEmployees, 20) ?></span> <?= __('common.of') ?> <span class="font-medium"><?= $totalEmployees ?></span> <?= __('common.results') ?>
                    </p>
                </div>
            </div>

            <div class="h-8"></div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Modal Template -->
    <div id="dataModal" class="modal-overlay" onclick="closeModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="bg-gradient-to-r from-primary to-indigo-700 px-6 py-4 flex justify-between items-center">
                <h3 id="modalTitle" class="text-white font-semibold text-lg flex items-center gap-2">
                    <span class="material-icons-outlined">hourglass_empty</span> Loading...
                </h3>
                <button onclick="closeModal()" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div id="modalBody" class="p-6 overflow-y-auto max-h-[70vh] custom-scrollbar">
                <div class="flex flex-col items-center justify-center py-8 gap-3">
                    <div class="spinner"></div>
                    <p class="text-gray-500 text-sm">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '<?= $_ENV['HRIS_API_URL'] ?? 'http://localhost/absensi/aplikasiabsensibygerry/public/api' ?>';
        const currentMonth = new Date().getMonth() + 1;
        const currentYear = new Date().getFullYear();

        function openModal(title, icon) {
            document.getElementById('modalTitle').innerHTML = `<span class="material-icons-outlined">${icon}</span> ${title}`;
            document.getElementById('modalBody').innerHTML = `
                <div class="flex flex-col items-center justify-center py-8 gap-3">
                    <div class="spinner"></div>
                    <p class="text-gray-500 text-sm">Memuat data...</p>
                </div>`;
            document.getElementById('dataModal').classList.add('active');
        }

        function closeModal(e) {
            if (e && e.target !== e.currentTarget) return;
            document.getElementById('dataModal').classList.remove('active');
        }

        async function fetchAPI(endpoint) {
            try {
                const response = await fetch(`${API_BASE}${endpoint}`);
                return await response.json();
            } catch (error) {
                console.error('API Error:', error);
                return { code: 500, message: error.message, data: null };
            }
        }

        async function showAttendance(employeeId, employeeName) {
            openModal(`Absensi - ${employeeName}`, 'schedule');
            const result = await fetchAPI(`/attendance/employee/${employeeId}?month=${currentMonth}&year=${currentYear}`);

            if (result.code === 200 && result.data) {
                const stats = result.data.stats || { hadir: 0, izin: 0, sakit: 0, alpha: 0, telat: 0 };
                const records = result.data.records || [];
                document.getElementById('modalBody').innerHTML = `
                    <div class="text-center text-sm text-gray-500 mb-4">
                        <span class="material-icons-outlined text-sm align-middle">calendar_month</span>
                        ${getMonthName(currentMonth)} ${currentYear}
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-green-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">${stats.hadir || 0}</div>
                            <div class="text-xs text-gray-500 mt-1">Hadir</div>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">${stats.izin || 0}</div>
                            <div class="text-xs text-gray-500 mt-1">Izin</div>
                        </div>
                        <div class="bg-yellow-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">${stats.sakit || 0}</div>
                            <div class="text-xs text-gray-500 mt-1">Sakit</div>
                        </div>
                        <div class="bg-red-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-red-600">${stats.alpha || 0}</div>
                            <div class="text-xs text-gray-500 mt-1">Alpha</div>
                        </div>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-3 text-center text-sm">
                        <strong class="text-emerald-700">Telat ${stats.telat || 0} kali</strong> bulan ini
                    </div>
                    ${records.length > 0 ? `
                        <h4 class="mt-5 mb-3 font-semibold text-gray-800 text-sm">Riwayat Terakhir</h4>
                        <div class="max-h-48 overflow-y-auto custom-scrollbar divide-y divide-gray-100">
                            ${records.slice(0, 10).map(r => `
                                <div class="flex justify-between items-center py-2.5 px-1">
                                    <span class="text-sm text-gray-600">${formatDate(r.tanggal)}</span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full ${r.status_absen === 'Masuk' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}">${r.status_absen}</span>
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}`;
            } else {
                showModalError('schedule', 'Data absensi tidak tersedia', result.message);
            }
        }

        async function showKPI(employeeId, employeeName) {
            openModal(`KPI Score - ${employeeName}`, 'pie_chart');
            const result = await fetchAPI(`/performance/employee/${employeeId}`);

            if (result.code === 200 && result.data) {
                const score = result.data.current_score || 0;
                const totalScore = result.data.total_score || 0;
                const breakdown = result.data.breakdown || [];
                const scorePercent = Math.min(100, score);
                document.getElementById('modalBody').innerHTML = `
                    <div class="text-center py-4">
                        <div class="kpi-score-circle" style="--score-percent: ${scorePercent}%;">
                            <span class="kpi-score-value">${score}</span>
                        </div>
                        <p class="text-gray-500 text-sm mb-6">Penilaian Berjalan</p>
                        <div class="flex justify-center gap-8 mb-6">
                            <div class="text-center">
                                <div class="text-xl font-bold text-emerald-600">${totalScore}</div>
                                <div class="text-xs text-gray-500">Total Skor</div>
                            </div>
                        </div>
                    </div>
                    ${breakdown.length > 0 ? `
                        <h4 class="mb-3 font-semibold text-sm text-gray-800">Breakdown Kinerja</h4>
                        <div class="space-y-3">
                            ${breakdown.map(b => `
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600 min-w-[100px]">${b.jenis}</span>
                                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-emerald-500 to-green-400 rounded-full" style="width: ${Math.min(100, b.total)}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-emerald-600 min-w-[40px] text-right">${b.total}</span>
                                </div>
                            `).join('')}
                        </div>
                    ` : `
                        <div class="text-center py-4 bg-gray-50 rounded-xl text-sm text-gray-400">
                            <span class="material-icons-outlined block mb-2">info</span> Breakdown detail tidak tersedia
                        </div>
                    `}`;
            } else {
                showModalError('pie_chart', 'Data KPI tidak tersedia', result.message);
            }
        }

        async function showPerformance(employeeId, employeeName) {
            openModal(`Kinerja - ${employeeName}`, 'trending_up');
            const result = await fetchAPI(`/performance/employee/${employeeId}`);

            if (result.code === 200 && result.data) {
                const records = result.data.records || [];
                const currentScore = result.data.current_score || 0;
                const totalScore = result.data.total_score || 0;
                document.getElementById('modalBody').innerHTML = `
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-5 rounded-xl text-center">
                            <div class="text-2xl font-bold text-emerald-600">${currentScore}</div>
                            <div class="text-xs text-gray-500 mt-1">Skor Berjalan</div>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-5 rounded-xl text-center">
                            <div class="text-2xl font-bold text-indigo-600">${totalScore}</div>
                            <div class="text-xs text-gray-500 mt-1">Total Nilai</div>
                        </div>
                    </div>
                    ${records.length > 0 ? `
                        <h4 class="mb-3 font-semibold text-sm text-gray-800">Riwayat Penilaian</h4>
                        <div class="max-h-64 overflow-y-auto custom-scrollbar divide-y divide-gray-100">
                            ${records.slice(0, 15).map(r => `
                                <div class="flex justify-between items-center py-3 px-1">
                                    <div>
                                        <div class="text-sm font-medium text-gray-800">${r.jenis?.nama || 'Penilaian'}</div>
                                        <div class="text-xs text-gray-400">${formatDate(r.tanggal)}</div>
                                    </div>
                                    <div class="text-sm font-bold ${r.nilai >= 0 ? 'text-emerald-600' : 'text-red-500'}">
                                        ${r.nilai >= 0 ? '+' : ''}${r.nilai}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : `
                        <div class="text-center py-8 bg-gray-50 rounded-xl text-sm text-gray-400">
                            <span class="material-icons-outlined block mb-2 text-3xl">assignment</span>
                            Belum ada riwayat penilaian
                        </div>
                    `}`;
            } else {
                showModalError('trending_up', 'Data kinerja tidak tersedia', result.message);
            }
        }

        function showModalError(icon, title, message) {
            document.getElementById('modalBody').innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <span class="material-icons-outlined text-5xl block mb-3">${icon}</span>
                    <p class="font-medium">${title}</p>
                    <p class="text-sm mt-1">${message || 'Tidak dapat memuat data'}</p>
                </div>`;
        }

        function filterByStatus(status) {
            window.location.href = status
                ? '<?= BASE_URL ?>employees?status=' + status
                : '<?= BASE_URL ?>employees';
        }

        function getMonthName(month) {
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return months[month - 1];
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
    </script>
</body>
</html>
