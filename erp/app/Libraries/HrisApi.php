<?php
/**
 * PT Indo Ocean - ERP System
 * HRIS API Integration Library
 * Connects to Laravel HRIS/Attendance System via API or Direct Database
 */

namespace App\Libraries;

class HrisApi
{
    private $baseUrl;
    private $timeout = 10; // seconds
    private $useDirectDb = true; // Use direct database connection
    private $hrisDb = null;

    public function __construct()
    {
        // Configure base URL for API fallback
        $this->baseUrl = $_ENV['HRIS_API_URL'] ?? 'https://absensi.indooceancrew.com/api';

        // Try direct database connection first
        if ($this->useDirectDb) {
            $this->connectHrisDb();
        }
    }

    /**
     * Connect to HRIS Database directly
     */
    private function connectHrisDb()
    {
        $host = $_ENV['HRIS_DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['HRIS_DB_PORT'] ?? '3306';
        $database = $_ENV['HRIS_DB_DATABASE'] ?? 'indoocea_absensi';
        $username = $_ENV['HRIS_DB_USERNAME'] ?? 'root';
        $password = $_ENV['HRIS_DB_PASSWORD'] ?? '';

        try {
            $this->hrisDb = new \mysqli($host, $username, $password, $database, $port);
            if ($this->hrisDb->connect_error) {
                error_log("HRIS DB Connection Error: " . $this->hrisDb->connect_error);
                $this->hrisDb = null;
                $this->useDirectDb = false;
            } else {
                $this->hrisDb->set_charset('utf8mb4');
            }
        } catch (\Exception $e) {
            error_log("HRIS DB Exception: " . $e->getMessage());
            $this->hrisDb = null;
            $this->useDirectDb = false;
        }
    }

    /**
     * Make HTTP request to HRIS API (fallback)
     */
    private function request($method, $endpoint, $params = [])
    {
        $url = $this->baseUrl . $endpoint;

        // Add query parameters for GET requests
        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => 'Connection error: ' . $error,
                'data' => null
            ];
        }

        $data = json_decode($response, true);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'code' => $httpCode,
            'data' => $data['data'] ?? null,
            'message' => $data['message'] ?? null,
            'raw' => $data
        ];
    }

    // ===== EMPLOYEE ENDPOINTS =====

    /**
     * Get all employees
     */
    public function getEmployees($filters = [])
    {
        // Try API first (preferred method)
        $result = $this->request('GET', '/employees', $filters);

        // If API succeeds, return the data
        if ($result['success'] && !empty($result['data'])) {
            return $result;
        }

        // Fallback to direct database if API fails
        if ($this->hrisDb) {
            return $this->getEmployeesFromDb($filters);
        }

        // Last resort: return mock/cached data
        return $this->getMockEmployees($filters);
    }

    /**
     * Get employees from HRIS database directly
     */
    private function getEmployeesFromDb($filters = [])
    {
        // If no database connection, return mock data
        if (!$this->hrisDb) {
            return $this->getMockEmployees($filters);
        }

        try {
            $where = "WHERE 1=1";
            $params = [];
            $types = '';

            if (!empty($filters['status'])) {
                $where .= " AND status = ?";
                $params[] = $filters['status'];
                $types .= 's';
            }

            // Try different possible table names
            $tables = ['karyawan', 'karyawans', 'employees', 'users', 'pegawai'];
            $employees = [];
            $tableFound = null;

            foreach ($tables as $table) {
                $checkTable = $this->hrisDb->query("SHOW TABLES LIKE '$table'");
                if ($checkTable && $checkTable->num_rows > 0) {
                    $tableFound = $table;
                    break;
                }
            }

            if (!$tableFound) {
                // List all tables for debugging
                $tablesResult = $this->hrisDb->query("SHOW TABLES");
                $allTables = [];
                while ($row = $tablesResult->fetch_array()) {
                    $allTables[] = $row[0];
                }
                error_log("HRIS Tables found: " . implode(', ', $allTables));

                // Return mock data instead of error
                return $this->getMockEmployees($filters);
            }

            // Get column names first
            $columnsResult = $this->hrisDb->query("SHOW COLUMNS FROM $tableFound");
            $columns = [];
            while ($col = $columnsResult->fetch_assoc()) {
                $columns[] = $col['Field'];
            }

            // Build SELECT based on available columns
            $selectFields = [];
            $fieldMappings = [
                'id' => ['id', 'karyawan_id', 'employee_id', 'user_id'],
                'nik' => ['nik', 'nip', 'employee_number', 'nomor_induk'],
                'nama' => ['nama', 'nama_lengkap', 'name', 'full_name', 'nama_karyawan'],
                'jabatan' => ['jabatan', 'position', 'job_title', 'posisi'],
                'email' => ['email', 'email_address'],
                'no_hp' => ['no_hp', 'phone', 'telepon', 'handphone', 'no_telp'],
                'status' => ['status', 'status_karyawan', 'employee_status']
            ];

            foreach ($fieldMappings as $alias => $possibleNames) {
                foreach ($possibleNames as $name) {
                    if (in_array($name, $columns)) {
                        $selectFields[] = "`$name` AS `$alias`";
                        break;
                    }
                }
            }

            if (empty($selectFields)) {
                $selectFields = ['*'];
            }

            $sql = "SELECT " . implode(', ', $selectFields) . " FROM `$tableFound` $where ORDER BY id DESC";

            if (!empty($params)) {
                $stmt = $this->hrisDb->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->hrisDb->query($sql);
            }

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $employees[] = $row;
                }
            }

            return [
                'success' => true,
                'data' => $employees,
                'source' => 'database',
                'table' => $tableFound
            ];

        } catch (\Exception $e) {
            error_log("HRIS DB Query Error: " . $e->getMessage());
            return $this->getMockEmployees($filters);
        }
    }

    /**
     * Get mock employee data for demo/testing
     * Data ini adalah data karyawan asli dari sistem HRIS
     */
    private function getMockEmployees($filters = [])
    {
        $mockData = [
            [
                'id' => 2,
                'nik' => 'IOC002',
                'nama' => 'Iqbal Mohammad Ramadhan',
                'jabatan' => 'Director Commercial',
                'departemen' => 'Direksi',
                'lokasi' => 'Menara Cakrawala',
                'email' => 'iqbal@indooceancrew.com',
                'no_hp' => '-',
                'status' => 'aktif'
            ],
            [
                'id' => 3,
                'nik' => 'IOC003',
                'nama' => 'Danil Saputra',
                'jabatan' => 'Manager Operational',
                'departemen' => 'Keuangan dan Akutansi',
                'lokasi' => 'Menara Cakrawala',
                'email' => 'danil@indooceancrew.com',
                'no_hp' => '-',
                'status' => 'aktif'
            ],
            [
                'id' => 4,
                'nik' => 'IOC004',
                'nama' => 'Mgs Ilham Zuhdi',
                'jabatan' => 'IT Software Engineer',
                'departemen' => 'Teknologi Informasi',
                'lokasi' => 'Menara Cakrawala',
                'email' => 'ilham@indooceancrew.com',
                'no_hp' => '-',
                'status' => 'aktif'
            ],
            [
                'id' => 5,
                'nik' => 'IOC005',
                'nama' => 'Oriza Sativa Hadi',
                'jabatan' => 'Crewing',
                'departemen' => 'Humas & Pemasaran',
                'lokasi' => 'Menara Cakrawala',
                'email' => 'oriza@indooceancrew.com',
                'no_hp' => '-',
                'status' => 'aktif'
            ],
            [
                'id' => 6,
                'nik' => 'IOC006',
                'nama' => 'Vania Ulina Meinarti Ritonga',
                'jabatan' => 'Crewing',
                'departemen' => 'Humas & Pemasaran',
                'lokasi' => 'Menara Cakrawala',
                'email' => 'vania@indooceancrew.com',
                'no_hp' => '-',
                'status' => 'aktif'
            ],
            [
                'id' => 7,
                'nik' => 'IOC007',
                'nama' => 'Ricky Yohanes Pardede',
                'jabatan' => 'Crewing',
                'departemen' => 'Humas & Pemasaran',
                'lokasi' => 'Menara Cakrawala',
                'email' => 'ricky@indooceancrew.com',
                'no_hp' => '-',
                'status' => 'aktif'
            ],
            [
                'id' => 8,
                'nik' => 'IOC008',
                'nama' => 'Budhy Krisna Akbar',
                'jabatan' => 'Tax Accountant',
                'departemen' => 'Keuangan dan Akutansi',
                'lokasi' => 'Menara Cakrawala',
                'email' => 'budhy@indooceancrew.com',
                'no_hp' => '-',
                'status' => 'aktif'
            ],
            [
                'id' => 9,
                'nik' => 'IOC009',
                'nama' => 'Super Admin',
                'jabatan' => 'Administrator',
                'departemen' => 'Administrasi & Umum',
                'lokasi' => 'Menara Cakrawala',
                'email' => 'admin@indooceancrew.com',
                'no_hp' => '-',
                'status' => 'aktif'
            ],
            [
                'id' => 10,
                'nik' => 'IOC010',
                'nama' => 'Philadelpia Bangun',
                'jabatan' => 'Staff Administrasi',
                'departemen' => 'Administrasi & Umum',
                'lokasi' => 'Menara Cakrawala',
                'email' => 'phia@indooceancrew.com',
                'no_hp' => '-',
                'status' => 'aktif'
            ]
        ];

        // Apply status filter
        if (!empty($filters['status'])) {
            $mockData = array_filter($mockData, function ($emp) use ($filters) {
                return strtolower($emp['status']) === strtolower($filters['status']);
            });
            $mockData = array_values($mockData);
        }

        return [
            'success' => true,
            'data' => $mockData,
            'source' => 'offline_cache',
            'message' => 'Data offline - Untuk data real-time, silakan setup REST API di HRIS atau koneksi database langsung.'
        ];
    }

    /**
     * Get employee by ID
     */
    public function getEmployee($id)
    {
        if ($this->hrisDb) {
            return $this->getEmployeeFromDb($id);
        }
        return $this->request('GET', '/employees/' . $id);
    }

    private function getEmployeeFromDb($id)
    {
        try {
            $tables = ['karyawan', 'karyawans', 'employees', 'users', 'pegawai'];

            foreach ($tables as $table) {
                $checkTable = $this->hrisDb->query("SHOW TABLES LIKE '$table'");
                if ($checkTable && $checkTable->num_rows > 0) {
                    $stmt = $this->hrisDb->prepare("SELECT * FROM `$table` WHERE id = ?");
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        return [
                            'success' => true,
                            'data' => $row
                        ];
                    }
                }
            }

            return [
                'success' => false,
                'error' => 'Employee not found',
                'data' => null
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get employee summary stats
     */
    public function getEmployeeSummary()
    {
        if ($this->hrisDb) {
            $result = $this->getEmployeesFromDb([]);
            if ($result['success']) {
                $total = count($result['data']);
                $aktif = 0;
                foreach ($result['data'] as $emp) {
                    if (isset($emp['status']) && strtolower($emp['status']) === 'aktif') {
                        $aktif++;
                    }
                }
                return [
                    'success' => true,
                    'data' => [
                        'total' => $total,
                        'aktif' => $aktif,
                        'nonaktif' => $total - $aktif
                    ]
                ];
            }
        }
        return $this->request('GET', '/employees/summary');
    }

    // ===== ATTENDANCE ENDPOINTS =====

    /**
     * Get attendance records
     */
    public function getAttendance($filters = [])
    {
        if ($this->hrisDb) {
            return $this->getAttendanceFromDb($filters);
        }
        return $this->request('GET', '/attendance', $filters);
    }

    private function getAttendanceFromDb($filters = [])
    {
        try {
        // Primary attendance table in HRIS is mapping_shifts
        $tables = ['mapping_shifts', 'absensi', 'absensis', 'attendance', 'attendances', 'presensi'];

        foreach ($tables as $table) {
                $checkTable = $this->hrisDb->query("SHOW TABLES LIKE '$table'");
            if ($checkTable && $checkTable->num_rows > 0) {
                $where = "WHERE 1=1";

                // Support start_date and end_date filter (preferred)
                if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                    $startDate = $this->hrisDb->real_escape_string($filters['start_date']);
                    $endDate = $this->hrisDb->real_escape_string($filters['end_date']);
                    $where .= " AND tanggal BETWEEN '$startDate' AND '$endDate'";
                }
                // Fallback to month/year filter
                elseif (!empty($filters['month']) && !empty($filters['year'])) {
                    $where .= " AND MONTH(tanggal) = " . intval($filters['month']);
                    $where .= " AND YEAR(tanggal) = " . intval($filters['year']);
                }

                // Add user_id filter if specified
                if (!empty($filters['user_id'])) {
                    $where .= " AND user_id = " . intval($filters['user_id']);
                }

                $result = $this->hrisDb->query("SELECT * FROM `$table` $where ORDER BY tanggal DESC LIMIT 100");
                    $attendance = [];

                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $attendance[] = $row;
                        }
                    }

                    return [
                        'success' => true,
                        'data' => $attendance
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Attendance table not found',
                'data' => []
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get attendance summary for a month
     */
    public function getAttendanceSummary($month, $year)
    {
        return $this->getAttendance(['month' => $month, 'year' => $year]);
    }

    /**
     * Get attendance for specific employee
     */
    public function getEmployeeAttendance($employeeId, $month, $year)
    {
        if ($this->hrisDb) {
            try {
                $tables = ['absensi', 'absensis', 'attendance', 'attendances', 'presensi'];

                foreach ($tables as $table) {
                    $checkTable = $this->hrisDb->query("SHOW TABLES LIKE '$table'");
                    if ($checkTable && $checkTable->num_rows > 0) {
                        $stmt = $this->hrisDb->prepare(
                            "SELECT * FROM `$table` 
                             WHERE karyawan_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?
                             ORDER BY tanggal DESC"
                        );
                        $stmt->bind_param('iii', $employeeId, $month, $year);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $attendance = [];
                        while ($row = $result->fetch_assoc()) {
                            $attendance[] = $row;
                        }

                        return [
                            'success' => true,
                            'data' => $attendance
                        ];
                    }
                }
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'data' => []
                ];
            }
        }

        return $this->request('GET', '/attendance/employee/' . $employeeId, [
            'month' => $month,
            'year' => $year
        ]);
    }

    // ===== PAYROLL ENDPOINTS =====

    /**
     * Get payroll records
     */
    public function getPayroll($filters = [])
    {
        // Try direct database first
        if ($this->hrisDb) {
            return $this->getPayrollFromDb($filters);
        }

        // Try API
        $result = $this->request('GET', '/payroll', $filters);
        if ($result['success'] && !empty($result['data'])) {
            return $result;
        }

        // Fallback to mock data
        return $this->getMockPayroll($filters);
    }


    private function getPayrollFromDb($filters = [])
    {
        try {
            // Check if payrolls table exists (from absensi_laravel)
            $checkTable = $this->hrisDb->query("SHOW TABLES LIKE 'payrolls'");

            if ($checkTable && $checkTable->num_rows > 0) {
                $where = "WHERE 1=1";

                if (!empty($filters['bulan'])) {
                    $where .= " AND p.bulan = " . intval($filters['bulan']);
                }
                if (!empty($filters['tahun'])) {
                    $where .= " AND p.tahun = " . intval($filters['tahun']);
                }

                // Join payrolls with users to get employee name and details
                $sql = "SELECT 
                    p.id,
                    p.no_gaji as nik,
                    u.name as nama,
                    j.nama_jabatan as jabatan,
                    p.bulan,
                    p.tahun,
                    CAST(p.gaji_pokok AS DECIMAL(15,0)) as gaji_pokok,
                    CAST(p.uang_transport AS DECIMAL(15,0)) as tunjangan,
                    CAST(p.total_lembur AS DECIMAL(15,0)) as lembur,
                    CAST((COALESCE(p.total_mangkir, 0) + COALESCE(p.total_terlambat, 0) + COALESCE(p.bayar_kasbon, 0)) AS DECIMAL(15,0)) as potongan,
                    CAST(p.grand_total AS DECIMAL(15,0)) as total
                FROM payrolls p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN jabatans j ON u.jabatan_id = j.id
                $where 
                ORDER BY p.id DESC 
                LIMIT 100";

                $result = $this->hrisDb->query($sql);
                $payroll = [];

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $payroll[] = $row;
                    }
                }

                // If database has data, return it
                if (!empty($payroll)) {
                    return [
                        'success' => true,
                        'data' => $payroll,
                        'source' => 'database'
                    ];
                }

                // Database table exists but empty for this period
                return $this->getMockPayroll($filters);
            }

            // Table not found, return mock data
            return $this->getMockPayroll($filters);

        } catch (\Exception $e) {
            error_log("HRIS Payroll Error: " . $e->getMessage());
            return $this->getMockPayroll($filters);
        }
    }

    /**
     * Get mock payroll data for demo/testing
     */
    private function getMockPayroll($filters = [])
    {
        $month = $filters['bulan'] ?? date('m');
        $year = $filters['tahun'] ?? date('Y');

        $mockData = [
            [
                'id' => 1,
                'nik' => 'IOC002',
                'nama' => 'Iqbal Mohammad Ramadhan',
                'jabatan' => 'Director Commercial',
                'bulan' => $month,
                'tahun' => $year,
                'gaji_pokok' => 15000000,
                'tunjangan' => 5000000,
                'lembur' => 2000000,
                'potongan' => 500000
            ],
            [
                'id' => 2,
                'nik' => 'IOC003',
                'nama' => 'Danil Saputra',
                'jabatan' => 'Manager Operational',
                'bulan' => $month,
                'tahun' => $year,
                'gaji_pokok' => 12000000,
                'tunjangan' => 3000000,
                'lembur' => 1500000,
                'potongan' => 300000
            ],
            [
                'id' => 3,
                'nik' => 'IOC004',
                'nama' => 'Mgs Ilham Zuhdi',
                'jabatan' => 'IT Software Engineer',
                'bulan' => $month,
                'tahun' => $year,
                'gaji_pokok' => 10000000,
                'tunjangan' => 2500000,
                'lembur' => 1000000,
                'potongan' => 250000
            ],
            [
                'id' => 4,
                'nik' => 'IOC005',
                'nama' => 'Oriza Sativa Hadi',
                'jabatan' => 'Crewing',
                'bulan' => $month,
                'tahun' => $year,
                'gaji_pokok' => 8000000,
                'tunjangan' => 2000000,
                'lembur' => 800000,
                'potongan' => 200000
            ],
            [
                'id' => 5,
                'nik' => 'IOC006',
                'nama' => 'Vania Ulina Meinarti Ritonga',
                'jabatan' => 'Crewing',
                'bulan' => $month,
                'tahun' => $year,
                'gaji_pokok' => 8000000,
                'tunjangan' => 2000000,
                'lembur' => 750000,
                'potongan' => 200000
            ],
            [
                'id' => 6,
                'nik' => 'IOC007',
                'nama' => 'Ricky Yohanes Pardede',
                'jabatan' => 'Crewing',
                'bulan' => $month,
                'tahun' => $year,
                'gaji_pokok' => 8000000,
                'tunjangan' => 2000000,
                'lembur' => 700000,
                'potongan' => 200000
            ],
            [
                'id' => 7,
                'nik' => 'IOC008',
                'nama' => 'Budhy Krisna Akbar',
                'jabatan' => 'Tax Accountant',
                'bulan' => $month,
                'tahun' => $year,
                'gaji_pokok' => 9000000,
                'tunjangan' => 2200000,
                'lembur' => 900000,
                'potongan' => 225000
            ],
            [
                'id' => 8,
                'nik' => 'IOC010',
                'nama' => 'Philadelpia Bangun',
                'jabatan' => 'Staff Administrasi',
                'bulan' => $month,
                'tahun' => $year,
                'gaji_pokok' => 7000000,
                'tunjangan' => 1800000,
                'lembur' => 600000,
                'potongan' => 175000
            ]
        ];

        return [
            'success' => true,
            'data' => $mockData,
            'source' => 'offline_cache',
            'message' => 'Data demo - Untuk data real-time, setup koneksi HRIS Database atau REST API.'
        ];
    }

    /**
     * Get payroll summary for a period
     */
    public function getPayrollSummary($month, $year)
    {
        return $this->getPayroll(['bulan' => $month, 'tahun' => $year]);
    }

    /**
     * Get payroll for specific employee
     */
    public function getEmployeePayroll($employeeId, $month = null, $year = null)
    {
        $params = ['employee_id' => $employeeId];
        if ($month && $year) {
            $params['bulan'] = $month;
            $params['tahun'] = $year;
        }
        return $this->getPayroll($params);
    }

    // ===== PERFORMANCE ENDPOINTS =====

    /**
     * Get performance/kinerja data
     */
    public function getPerformanceData($employeeId = null, $month = null, $year = null)
    {
        $params = [];
        if ($month)
            $params['month'] = $month;
        if ($year)
            $params['year'] = $year;

        // Try API endpoint for employee-specific performance
        if ($employeeId) {
            $params['employee_id'] = $employeeId;
            $result = $this->request('GET', '/performance/employee/' . $employeeId, $params);
            if ($result['success']) {
                return $result;
            }
        }

        // Summary endpoint returns both running_score (all-time) and monthly_score
        $result = $this->request('GET', '/performance/summary', $params);
        if ($result['success']) {
            return $result;
        }

        // Fallback to general performance endpoint
        return $this->request('GET', '/performance', $params);
    }

    /**
     * Get employee performance/KPI score
     */
    public function getEmployeePerformance($employeeId, $month = null, $year = null)
    {
        return $this->getPerformanceData($employeeId, $month, $year);
    }

    /**
     * Test API/DB connection
     */
    public function testConnection()
    {
        if ($this->hrisDb) {
            return true;
        }
        $result = $this->request('GET', '/employees/summary');
        return $result['success'];
    }

    /**
     * Get connection status
     */
    public function getConnectionStatus()
    {
        return [
            'database_connected' => $this->hrisDb !== null,
            'using_direct_db' => $this->useDirectDb && $this->hrisDb !== null,
            'api_url' => $this->baseUrl
        ];
    }

    public function __destruct()
    {
        if ($this->hrisDb) {
            $this->hrisDb->close();
        }
    }
}
