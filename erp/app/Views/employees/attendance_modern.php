<?php
/**
 * Modern Attendance Dashboard View
 * PT Indo Ocean - ERP System
 */
$currentPage = $currentPage ?? 'attendance';

// Calculate stats
$records = [];
if (is_array($attendanceData)) {
    if (isset($attendanceData['data']) && is_array($attendanceData['data'])) {
        $records = $attendanceData['data'];
    } elseif (isset($attendanceData['records'])) {
        $records = $attendanceData['records'];
    } else {
        $records = $attendanceData;
    }
}

$totalPresent = count(array_filter($records, fn($r) => !empty($r['jam_masuk'])));
$lateArrivals = count(array_filter($records, fn($r) => ($r['telat'] ?? 0) > 0));
$absent = count(array_filter($records, fn($r) => empty($r['jam_masuk'])));
$onLeave = 0; // Would need additional API data
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records | IndoOcean ERP</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#6366F1",
                        "primary-dark": "#4F46E5",
                        "gold": "#D97706",
                        "navy-header": "#1E293B",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                        'card': '0 0 20px rgba(0,0,0,0.03)',
                    }
                },
            },
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
    </style>
</head>

<body class="bg-gray-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 p-4 md:p-8 flex flex-col gap-8 overflow-y-auto">
            <!-- Header -->
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 animate-fade-in">
                <div>
                    <nav class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
                        <ol class="flex items-center space-x-2">
                            <li><a class="hover:text-slate-800 dark:hover:text-slate-200" href="<?= BASE_URL ?>">ERP</a></li>
                            <li><span class="text-slate-300">/</span></li>
                            <li><a class="hover:text-slate-800 dark:hover:text-slate-200" href="<?= BASE_URL ?>employees">Employees</a></li>
                            <li><span class="text-slate-300">/</span></li>
                            <li class="text-slate-800 dark:text-white font-medium">Attendance</li>
                        </ol>
                    </nav>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Attendance Records</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                        Today: <span class="font-medium text-slate-700 dark:text-slate-300"><?= date('M d, Y') ?></span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                            Live
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                        onclick="document.documentElement.classList.toggle('dark')">
                        <span class="material-icons-outlined">dark_mode</span>
                    </button>
                    <button onclick="exportData()" class="flex items-center gap-2 px-4 py-2 bg-gold hover:bg-yellow-600 text-white text-sm font-medium rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold">
                        <span class="material-icons-outlined text-[18px]">download</span>
                        Export Data
                    </button>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-card border border-slate-200 dark:border-slate-700 flex flex-col justify-between h-32 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-icons-outlined text-6xl text-emerald-500">check_circle</span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Present</p>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-bold text-slate-900 dark:text-white"><?= $totalPresent ?></span>
                        <span class="text-sm font-medium text-emerald-500 mb-1">Today</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-card border border-slate-200 dark:border-slate-700 flex flex-col justify-between h-32 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-icons-outlined text-6xl text-orange-500">schedule</span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Late Arrivals</p>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-bold text-slate-900 dark:text-white"><?= $lateArrivals ?></span>
                        <span class="text-sm font-medium text-orange-500 mb-1">Alert</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-card border border-slate-200 dark:border-slate-700 flex flex-col justify-between h-32 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-icons-outlined text-6xl text-rose-500">person_off</span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Absent</p>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-bold text-slate-900 dark:text-white"><?= $absent ?></span>
                        <span class="text-sm font-medium text-rose-500 mb-1">Alert</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-card border border-slate-200 dark:border-slate-700 flex flex-col justify-between h-32 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-icons-outlined text-6xl text-blue-500">flight_takeoff</span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">On Leave</p>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-bold text-slate-900 dark:text-white"><?= $onLeave ?></span>
                        <span class="text-sm font-medium text-slate-400 mb-1">Planned</span>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-icons-outlined text-slate-400">search</span>
                        </span>
                        <input id="searchInput" class="w-full pl-10 pr-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-gray-50 dark:bg-slate-900 text-sm focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white"
                            placeholder="Search employee..." type="text" onkeyup="filterTable()">
                    </div>
                    <div class="relative w-full md:w-56">
                        <select id="employeeFilter" onchange="applyFilter()" class="w-full pl-4 pr-10 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-gray-50 dark:bg-slate-900 text-sm focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white appearance-none cursor-pointer">
                            <option value="">All Employees</option>
                            <?php if (isset($employees) && !empty($employees)): ?>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>" <?= (isset($selectedEmployee) && $selectedEmployee == $emp['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($emp['nama'] ?? $emp['name'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="material-icons-outlined text-slate-400">expand_more</span>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto bg-gray-50 dark:bg-slate-900 p-1 rounded-lg border border-slate-300 dark:border-slate-600">
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-slate-400">
                            <span class="material-icons-outlined text-sm">calendar_today</span>
                        </span>
                        <input id="startDate" class="pl-9 pr-2 py-1.5 w-32 bg-transparent border-none text-sm font-medium text-slate-700 dark:text-slate-300 focus:ring-0 cursor-pointer"
                            type="date" value="<?= $startDate ?? date('Y-m-d') ?>" onchange="applyFilter()">
                    </div>
                    <span class="text-slate-300 text-sm">to</span>
                    <div class="relative flex items-center">
                        <input id="endDate" class="pl-3 pr-2 py-1.5 w-28 bg-transparent border-none text-sm font-medium text-slate-700 dark:text-slate-300 focus:ring-0 cursor-pointer"
                            type="date" value="<?= $endDate ?? date('Y-m-d') ?>" onchange="applyFilter()">
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col min-h-[400px]">
                <div class="grid grid-cols-12 gap-4 bg-navy-header p-4 text-xs font-semibold text-white uppercase tracking-wider items-center">
                    <div class="col-span-1 text-center">No.</div>
                    <div class="col-span-2">Employee Name</div>
                    <div class="col-span-1">Shift</div>
                    <div class="col-span-2">Date</div>
                    <div class="col-span-1">Check In</div>
                    <div class="col-span-1">Late</div>
                    <div class="col-span-2">Location</div>
                    <div class="col-span-1">Status</div>
                    <div class="col-span-1 text-right">Action</div>
                </div>
                <div class="flex-1 overflow-x-auto">
                    <?php if (!$success || empty($records)): ?>
                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center p-12 text-center bg-white dark:bg-slate-900 min-h-[400px]">
                            <div class="bg-indigo-50 dark:bg-indigo-900/30 p-6 rounded-full mb-6">
                                <span class="material-icons-outlined text-5xl text-primary">cloud_off</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Attendance Data Found</h3>
                            <p class="text-slate-500 dark:text-slate-400 max-w-md mb-8 leading-relaxed">
                                We couldn't retrieve the attendance records from the HRIS system. This might be due to a synchronization delay or connection issue.
                            </p>
                            <div class="flex gap-4">
                                <button class="px-6 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium">
                                    View Logs
                                </button>
                                <button onclick="location.reload()" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary-dark text-white shadow-lg shadow-indigo-500/30 transition-all text-sm font-medium flex items-center gap-2">
                                    <span class="material-icons-outlined text-sm">sync</span>
                                    Sync with HRIS
                                </button>
                            </div>
                            <div class="mt-12 opacity-30 w-full max-w-2xl">
                                <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full w-full mb-3"></div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full w-3/4 mx-auto mb-3"></div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full w-1/2 mx-auto"></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Table Data -->
                        <table class="w-full text-sm" id="attendanceTable">
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                <?php $no = 1; foreach ($records as $record): ?>
                                    <?php
                                    $empName = $record['user']['name'] ?? $record['nama'] ?? '-';
                                    $shiftName = $record['shift']['nama_shift'] ?? $record['shift']['nama'] ?? '-';
                                    $tanggal = $record['tanggal'] ?? '-';
                                    $jamMasuk = $record['jam_masuk'] ?? null;
                                    $telat = $record['telat'] ?? 0;
                                    $lokasiMasuk = $record['lokasi_masuk'] ?? '-';
                                    ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-4 py-3 text-center text-slate-600 dark:text-slate-400"><?= $no++ ?></td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-xs font-bold text-indigo-600 dark:text-indigo-300">
                                                    <?= strtoupper(substr($empName, 0, 1)) ?>
                                                </div>
                                                <span class="font-medium text-slate-900 dark:text-white"><?= htmlspecialchars($empName) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?= htmlspecialchars($shiftName) ?></td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?= $tanggal !== '-' ? date('M d, Y', strtotime($tanggal)) : '-' ?></td>
                                        <td class="px-4 py-3">
                                            <?php if ($jamMasuk): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                    <?= $jamMasuk ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                    Not Yet
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php if ($telat > 0): ?>
                                                <span class="text-orange-600 dark:text-orange-400 font-medium"><?= $telat ?> min</span>
                                            <?php else: ?>
                                                <span class="text-emerald-600 dark:text-emerald-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400 text-xs">
                                            <?= htmlspecialchars(substr($lokasiMasuk, 0, 25)) ?><?= strlen($lokasiMasuk) > 25 ? '...' : '' ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                Present
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button class="p-1.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors" title="View Details">
                                                <span class="material-icons-outlined text-lg">visibility</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function applyFilter() {
            const employee = document.getElementById('employeeFilter').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            let url = '<?= BASE_URL ?>employees/attendance?';
            const params = [];
            if (employee) params.push('employee_id=' + employee);
            if (startDate) params.push('start_date=' + startDate);
            if (endDate) params.push('end_date=' + endDate);

            window.location.href = url + params.join('&');
        }

        function exportData() {
            alert('Export functionality will be available soon');
        }

        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('attendanceTable');
            if (!table) return;
            
            const rows = table.getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[1];
                if (nameCell) {
                    const txtValue = nameCell.textContent || nameCell.innerText;
                    rows[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
                }
            }
        }
    </script>
</body>
</html>
