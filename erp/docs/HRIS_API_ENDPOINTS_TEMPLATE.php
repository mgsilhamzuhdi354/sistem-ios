<?php
/**
 * HRIS API Endpoints Template
 * This file provides example endpoints to create in HRIS Laravel application
 * 
 * Location: c:\laragon\www\absensi\aplikasiabsensibygerry\routes\api.php
 */

// ===== ADD TO routes/api.php =====

Route::prefix('api')->group(function () {

    // EMPLOYEES
    Route::get('/employees', 'Api\EmployeeApiController@index');
    Route::get('/employees/{id}', 'Api\EmployeeApiController@show');
    Route::get('/employees/summary', 'Api\EmployeeApiController@summary');

    // ATTENDANCE
    Route::get('/attendance', 'Api\AttendanceApiController@index');
    Route::get('/attendance/summary', 'Api\AttendanceApiController@summary');
    Route::get('/attendance/employee/{id}', 'Api\AttendanceApiController@employee');

    // PAYROLL
    Route::get('/payroll', 'Api\PayrollApiController@index');
    Route::get('/payroll/summary', 'Api\PayrollApiController@summary');
    Route::get('/payroll/employee/{id}', 'Api\PayrollApiController@employee');

    // PERFORMANCE
    Route::get('/performance', 'Api\PerformanceApiController@index');
    Route::get('/performance/employee/{id}', 'Api\PerformanceApiController@employee');
});

/*
 * ===== CONTROLLER EXAMPLES =====
 * 
 * Create these controllers in:
 * app/Http/Controllers/Api/
 */

/**
 * Example: EmployeeApiController.php
 *
 * namespace App\Http\Controllers\Api;
 * 
 * use App\Http\Controllers\Controller;
 * use App\Models\Employee;
 * 
 * class EmployeeApiController extends Controller
 * {
 *     public function index(Request $request)
 *     {
 *         $query = Employee::query();
 *         
 *         if ($request->has('status')) {
 *             $query->where('status', $request->status);
 *         }
 *         
 *         $employees = $query->get();
 *         
 *         return response()->json([
 *             'success' => true,
 *             'data' => $employees
 *         ]);
 *     }
 *     
 *     public function show($id)
 *     {
 *         $employee = Employee::find($id);
 *         
 *         if (!$employee) {
 *             return response()->json(['success' => false, 'message' => 'Not found'], 404);
 *         }
 *         
 *         return response()->json([
 *             'success' => true,
 *             'data' => $employee
 *         ]);
 *     }
 *     
 *     public function summary()
 *     {
 *         return response()->json([
 *             'success' => true,
 *             'data' => [
 *                 'total' => Employee::count(),
 *                 'active' => Employee::where('status', 'aktif')->count(),
 *                 'inactive' => Employee::where('status', 'resign')->count()
 *             ]
 *         ]);
 *     }
 * }
 */

/**
 * Example: AttendanceApiController.php
 *
 * class AttendanceApiController extends Controller
 * {
 *     public function index(Request $request)
 *     {
 *         $query = Attendance::query();
 *         
 *         if ($request->has('month') && $request->has('year')) {
 *             $query->whereMonth('date', $request->month)
 *                   ->whereYear('date', $request->year);
 *         }
 *         
 *         return response()->json([
 *             'success' => true,
 *             'data' => $query->get()
 *         ]);
 *     }
 *     
 *     public function summary(Request $request)
 *     {
 *         $month = $request->input('month');
 *         $year = $request->input('year');
 *         
 *         $data = Attendance::whereMonth('date', $month)
 *                     ->whereYear('date', $year)
 *                     ->selectRaw('employee_id, COUNT(*) as total_hadir')
 *                     ->groupBy('employee_id')
 *                     ->get();
 *         
 *         return response()->json(['success' => true, 'data' => $data]);
 *     }
 *     
 *     public function employee($id, Request $request)
 *     {
 *         $month = $request->input('month');
 *         $year = $request->input('year');
 *         
 *         $attendance = Attendance::where('employee_id', $id)
 *                         ->whereMonth('date', $month)
 *                         ->whereYear('date', $year)
 *                         ->get();
 *         
 *         return response()->json(['success' => true, 'data' => $attendance]);
 *     }
 * }
 */

/**
 * Example: PayrollApiController.php
 *
 * class PayrollApiController extends Controller
 * {
 *     public function index(Request $request)
 *     {
 *         $query = Payroll::with('employee');
 *         
 *         return response()->json([
 *             'success' => true,
 *             'data' => $query->get()
 *         ]);
 *     }
 *     
 *     public function summary(Request $request)
 *     {
 *         $bulan = $request->input('bulan');
 *         $tahun = $request->input('tahun');
 *         
 *         $payrolls = Payroll::with('employee')
 *                       ->where('bulan', $bulan)
 *                       ->where('tahun', $tahun)
 *                       ->get();
 *         
 *         return response()->json(['success' => true, 'data' => $payrolls]);
 *     }
 *     
 *     public function employee($id, Request $request)
 *     {
 *         $bulan = $request->input('bulan');
 *         $tahun = $request->input('tahun');
 *         
 *         $query = Payroll::where('employee_id', $id);
 *         
 *         if ($bulan && $tahun) {
 *             $query->where('bulan', $bulan)->where('tahun', $tahun);
 *         }
 *         
 *         return response()->json(['success' => true, 'data' => $query->get()]);
 *     }
 * }
 */

/**
 * Example: PerformanceApiController.php
 *
 * class PerformanceApiController extends Controller
 * {
 *     public function index(Request $request)
 *     {
 *         $query = Performance::with('employee');
 *         
 *         if ($request->has('month') && $request->has('year')) {
 *             $query->where('month', $request->month)
 *                   ->where('year', $request->year);
 *         }
 *         
 *         return response()->json([
 *             'success' => true,
 *             'data' => $query->get()
 *         ]);
 *     }
 *     
 *     public function employee($id, Request $request)
 *     {
 *         $month = $request->input('month');
 *         $year = $request->input('year');
 *         
 *         $performance = Performance::where('employee_id', $id)
 *                         ->where('month', $month)
 *                         ->where('year', $year)
 *                         ->first();
 *         
 *         return response()->json(['success' => true, 'data' => $performance]);
 *     }
 * }
 */
