<?php
/**
 * PT Indo Ocean - ERP System
 * Employee Controller (Karyawan from HRIS)
 */

namespace App\Controllers;

require_once APPPATH . 'Libraries/HrisApi.php';

use App\Libraries\HrisApi;

class Employee extends BaseController
{
    private $hrisApi;

    public function __construct()
    {
        parent::__construct();
        $this->hrisApi = new HrisApi();
    }

    /**
     * Employee list (from HRIS)
     */
    public function index()
    {
        $status = $this->input('status');

        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }

        $result = $this->hrisApi->getEmployees($filters);

        $data = [
            'title' => 'Data Karyawan',
            'currentPage' => 'employees',
            'employees' => $result['data'] ?? [],
            'success' => $result['success'],
            'error' => $result['error'] ?? null,
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'employees/index_modern' : 'employees/index';

        return $this->view($view, $data);
    }

    /**
     * Employee detail
     */
    public function show($id)
    {
        $result = $this->hrisApi->getEmployee($id);

        if (!$result['success']) {
            $this->setFlash('error', 'Failed to load employee data');
            $this->redirect('employees');
        }

        $employee = $result['data'];

        $data = [
            'title' => $employee['name'] ?? 'Employee Detail',
            'currentPage' => 'employees',
            'employee' => $employee,
            'flash' => $this->getFlash()
        ];

        return $this->view('employees/view', $data);
    }

    /**
     * Employee attendance view - show one row per employee per day
     */
    public function attendance()
    {
        $month = $this->input('month', date('m'));
        $year = $this->input('year', date('Y'));
        $employeeId = $this->input('employee_id');

        // Date filter - default to today
        $startDate = $this->input('start_date', date('Y-m-d'));
        $endDate = $this->input('end_date', date('Y-m-d'));

        // Build API filter params
        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        // Add employee filter if specified
        if ($employeeId) {
            $filters['user_id'] = $employeeId;
        }

        // Get attendance data from API with date filter
        $result = $this->hrisApi->getAttendance($filters);

        // Get employee list for filter dropdown
        $employeesResult = $this->hrisApi->getEmployees([]);

        $data = [
            'title' => 'Data Absen',
            'currentPage' => 'attendance',
            'month' => $month,
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedEmployee' => $employeeId,
            'attendanceData' => $result['data'] ?? [],
            'employees' => $employeesResult['data'] ?? [],
            'success' => $result['success'],
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
    $uiMode = $_SESSION['ui_mode'] ?? 'modern';
    $view = $uiMode === 'modern' ? 'employees/attendance_modern' : 'employees/attendance';

    return $this->view($view, $data);
    }

    /**
     * Employee attendance detail
     */
    public function attendanceDetail($employeeId)
    {
        $month = $this->input('month', date('m'));
        $year = $this->input('year', date('Y'));

        $result = $this->hrisApi->getEmployeeAttendance($employeeId, $month, $year);

        if (!$result['success']) {
            $this->setFlash('error', 'Failed to load attendance data');
            $this->redirect('employees/attendance');
        }

        $data = [
            'title' => 'Detail Absensi',
            'currentPage' => 'employees',
            'month' => $month,
            'year' => $year,
            'attendanceData' => $result['data'],
            'flash' => $this->getFlash()
        ];

        return $this->view('employees/attendance-detail', $data);
    }

    /**
     * Employee payroll
     */
    public function payroll()
    {
        $month = $this->input('bulan', date('m'));
        $year = $this->input('tahun', date('Y'));

        $result = $this->hrisApi->getPayrollSummary($month, $year);

        $data = [
            'title' => 'Payroll Karyawan',
            'currentPage' => 'employees',
            'month' => $month,
            'year' => $year,
            'payrollData' => $result['data'] ?? [],
            'success' => $result['success'],
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
    $uiMode = $_SESSION['ui_mode'] ?? 'modern';
    $view = $uiMode === 'modern' ? 'employees/payroll_modern' : 'employees/payroll';

    return $this->view($view, $data);
    }

    /**
     * Employee performance tracking
     */
    public function performance()
    {
        $employeeId = $this->input('employee_id');
        $month = $this->input('month', date('m'));
        $year = $this->input('year', date('Y'));

        // Get performance data from HRIS
        $result = $this->hrisApi->getPerformanceData($employeeId, $month, $year);

        // Get employee list for filter
        $employeesResult = $this->hrisApi->getEmployees(['status' => 'aktif']);

        $data = [
            'title' => 'Performa Karyawan',
            'currentPage' => 'employee-performance',
            'month' => $month,
            'year' => $year,
            'performanceData' => $result['data'] ?? [],
            'employees' => $employeesResult['data'] ?? [],
            'selectedEmployee' => $employeeId,
            'success' => $result['success'],
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
    $uiMode = $_SESSION['ui_mode'] ?? 'modern';
    $view = $uiMode === 'modern' ? 'employees/performance_modern' : 'employees/performance';

    return $this->view($view, $data);
    }
}
