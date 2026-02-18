<?php

namespace App\Services;

/**
 * HRIS Integration Service
 * Connects to HRIS Absensi via REST API
 */
class HRISService
{
    private $baseUrl;
    private $apiToken;

    public function __construct()
    {
        // Get from .env
        $this->baseUrl = $_ENV['HRIS_API_URL'] ?? 'http://localhost/absensi/aplikasiabsensibygerry/api';
        $this->apiToken = $_ENV['HRIS_API_TOKEN'] ?? '';
    }

    /**
     * Make HTTP request to HRIS API
     */
    private function request($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        if ($this->apiToken) {
            $headers[] = 'Authorization: Bearer ' . $this->apiToken;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("HRIS API Error: " . $error);
            return ['success' => false, 'error' => $error];
        }

        $result = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'data' => $result];
        } else {
            return ['success' => false, 'error' => $result['message'] ?? 'Unknown error', 'code' => $httpCode];
        }
    }

    /**
     * Get all employees from HRIS
     */
    public function getEmployees($filters = [])
    {
        $query = http_build_query($filters);
        $endpoint = '/karyawan' . ($query ? '?' . $query : '');
        return $this->request($endpoint);
    }

    /**
     * Get employee by ID
     */
    public function getEmployee($id)
    {
        return $this->request('/karyawan/' . $id);
    }

    /**
     * Get attendance data
     */
    public function getAttendance($filters = [])
    {
        $query = http_build_query($filters);
        $endpoint = '/absensi' . ($query ? '?' . $query : '');
        return $this->request($endpoint);
    }

    /**
     * Get payroll data
     */
    public function getPayroll($filters = [])
    {
        $query = http_build_query($filters);
        $endpoint = '/payroll' . ($query ? '?' . $query : '');
        return $this->request($endpoint);
    }

    /**
     * Get employee performance
     */
    public function getPerformance($employeeId = null, $period = null)
    {
        $filters = [];
        if ($employeeId)
            $filters['employee_id'] = $employeeId;
        if ($period)
            $filters['period'] = $period;

        $query = http_build_query($filters);
        $endpoint = '/kinerja' . ($query ? '?' . $query : '');
        return $this->request($endpoint);
    }

    /**
     * Sync employee data to ERP local database
     */
    public function syncEmployees($db)
    {
        $result = $this->getEmployees();

        if (!$result['success']) {
            return $result;
        }

        $employees = $result['data']['data'] ?? $result['data'] ?? [];
        $synced = 0;
        $errors = [];

        foreach ($employees as $emp) {
            try {
                // Check if employee exists
                $stmt = $db->prepare("SELECT id FROM hris_karyawan WHERE hris_id = ?");
                $stmt->bind_param('i', $emp['id']);
                $stmt->execute();
                $exists = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($exists) {
                    // Update
                    $stmt = $db->prepare("
                        UPDATE hris_karyawan SET 
                            nik = ?, nama_lengkap = ?, email = ?, 
                            jabatan = ?, departemen = ?, status_karyawan = ?,
                            tanggal_bergabung = ?, synced_at = NOW()
                        WHERE hris_id = ?
                    ");
                    $stmt->bind_param(
                        'sssssssi',
                        $emp['nik'],
                        $emp['nama_lengkap'],
                        $emp['email'],
                        $emp['jabatan'],
                        $emp['departemen'],
                        $emp['status'],
                        $emp['tanggal_bergabung'],
                        $emp['id']
                    );
                } else {
                    // Insert
                    $stmt = $db->prepare("
                        INSERT INTO hris_karyawan 
                        (hris_id, nik, nama_lengkap, email, jabatan, departemen, status_karyawan, tanggal_bergabung, synced_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->bind_param(
                        'isssssss',
                        $emp['id'],
                        $emp['nik'],
                        $emp['nama_lengkap'],
                        $emp['email'],
                        $emp['jabatan'],
                        $emp['departemen'],
                        $emp['status'],
                        $emp['tanggal_bergabung']
                    );
                }

                $stmt->execute();
                $stmt->close();
                $synced++;

            } catch (\Exception $e) {
                $errors[] = "Employee ID {$emp['id']}: " . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'synced' => $synced,
            'total' => count($employees),
            'errors' => $errors
        ];
    }
}
