<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * API Candidate Controller
 * Provides REST API endpoints for candidate data
 */
class ApiCandidateController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        // Set JSON header
        header('Content-Type: application/json');

        // CORS headers for API access
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /**
     * Get all candidates with filters
     * GET /api/candidates?status=&department=&search=
     */
    public function index()
    {
        try {
            $status = $this->input('status');
            $department = $this->input('department');
            $search = $this->input('search');
            $limit = $this->input('limit') ?: 100;

            $query = "
                SELECT a.*, u.full_name, u.email, u.phone, u.avatar,
                       v.title as vacancy_title,
                       d.name as department_name,
                       s.name as status_name, s.color as status_color,
                       ap.profile_completion, ap.date_of_birth, ap.address,
                       ap.passport_no, ap.seaman_book_no, ap.total_sea_service_months
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                LEFT JOIN departments d ON v.department_id = d.id
                JOIN application_statuses s ON a.status_id = s.id
                LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
                WHERE 1=1
            ";

            if ($status) {
                $query .= " AND a.status_id = " . intval($status);
            }
            if ($department) {
                $query .= " AND v.department_id = " . intval($department);
            }
            if ($search) {
                $searchEsc = $this->db->real_escape_string($search);
                $query .= " AND (u.full_name LIKE '%$searchEsc%' OR u.email LIKE '%$searchEsc%')";
            }

            $query .= " ORDER BY a.submitted_at DESC LIMIT " . intval($limit);

            $candidates = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);

            $this->jsonResponse([
                'code' => 200,
                'message' => 'Success',
                'data' => $candidates
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'code' => 500,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get approved candidates ready for ERP import
     * GET /api/candidates/approved
     */
    public function approved()
    {
        try {
            // Status 6 = Approved (adjust based on your database)
            $query = "
                SELECT a.*, u.full_name, u.email, u.phone, u.avatar,
                       v.title as vacancy_title,
                       d.name as department_name,
                       ap.date_of_birth, ap.place_of_birth, ap.address, ap.city, ap.country,
                       ap.passport_no, ap.seaman_book_no, ap.emergency_name,
                       ap.emergency_phone, ap.total_sea_service_months,
                       a.is_synced_to_erp, a.synced_at
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                LEFT JOIN departments d ON v.department_id = d.id
                LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
                WHERE a.status_id IN (SELECT id FROM application_statuses WHERE name IN ('Approved', 'Hired'))
                ORDER BY a.reviewed_at DESC
            ";

            $approved = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);

            // Get documents for each candidate
            foreach ($approved as &$candidate) {
                $docStmt = $this->db->prepare("
                    SELECT d.*, dt.name as type_name
                    FROM documents d
                    JOIN document_types dt ON d.document_type_id = dt.id
                    WHERE d.user_id = ?
                    ORDER BY dt.sort_order
                ");
                $docStmt->bind_param('i', $candidate['user_id']);
                $docStmt->execute();
                $candidate['documents'] = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);

                // Get medical checkup status
                $medStmt = $this->db->prepare("
                    SELECT * FROM medical_checkups 
                    WHERE application_id = ? 
                    ORDER BY created_at DESC LIMIT 1
                ");
                $medStmt->bind_param('i', $candidate['id']);
                $medStmt->execute();
                $candidate['medical_checkup'] = $medStmt->get_result()->fetch_assoc();
            }

            $this->jsonResponse([
                'code' => 200,
                'message' => 'Success',
                'data' => $approved
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'code' => 500,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get candidate detail by ID
     * GET /api/candidates/{id}
     */
    public function show($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, u.full_name, u.email, u.phone, u.avatar, u.created_at as user_created,
                       v.title as vacancy_title, v.description as vacancy_description,
                       v.salary_min, v.salary_max,
                       d.name as department_name, s.name as status_name, s.color as status_color,
                       ap.*
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                LEFT JOIN departments d ON v.department_id = d.id
                JOIN application_statuses s ON a.status_id = s.id
                LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
                WHERE a.id = ?
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $candidate = $stmt->get_result()->fetch_assoc();

            if (!$candidate) {
                $this->jsonResponse([
                    'code' => 404,
                    'message' => 'Candidate not found',
                    'data' => null
                ], 404);
                return;
            }

            // Get documents
            $docStmt = $this->db->prepare("
                SELECT d.*, dt.name as type_name
                FROM documents d
                JOIN document_types dt ON d.document_type_id = dt.id
                WHERE d.user_id = ?
                ORDER BY dt.sort_order
            ");
            $docStmt->bind_param('i', $candidate['user_id']);
            $docStmt->execute();
            $candidate['documents'] = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Get interview history
            $intStmt = $this->db->prepare("
                SELECT is2.*, qb.name as question_bank_name
                FROM interview_sessions is2
                JOIN interview_question_banks qb ON is2.question_bank_id = qb.id
                WHERE is2.application_id = ?
            ");
            $intStmt->bind_param('i', $id);
            $intStmt->execute();
            $candidate['interviews'] = $intStmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Get medical checkups
            $medStmt = $this->db->prepare("SELECT * FROM medical_checkups WHERE application_id = ?");
            $medStmt->bind_param('i', $id);
            $medStmt->execute();
            $candidate['medical_checkups'] = $medStmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $this->jsonResponse([
                'code' => 200,
                'message' => 'Success',
                'data' => $candidate
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'code' => 500,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Mark candidate as synced to ERP
     * POST /api/candidates/{id}/mark-synced
     */
    public function markSynced($id)
    {
        try {
            if (!$this->isPost()) {
                $this->jsonResponse([
                    'code' => 405,
                    'message' => 'Method not allowed',
                    'data' => null
                ], 405);
                return;
            }

            $erpEmployeeId = $this->input('erp_employee_id');

            $stmt = $this->db->prepare("
                UPDATE applications 
                SET is_synced_to_erp = 1, 
                    synced_at = NOW(),
                    erp_employee_id = ?
                WHERE id = ?
            ");
            $stmt->bind_param('ii', $erpEmployeeId, $id);

            if ($stmt->execute()) {
                $this->jsonResponse([
                    'code' => 200,
                    'message' => 'Candidate marked as synced to ERP',
                    'data' => ['application_id' => $id, 'erp_employee_id' => $erpEmployeeId]
                ]);
            } else {
                throw new Exception('Failed to update database');
            }

        } catch (Exception $e) {
            $this->jsonResponse([
                'code' => 500,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Helper function to send JSON response
     */
    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}
