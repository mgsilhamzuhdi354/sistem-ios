<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Archive Management Controller
 * Manage archived applications (approved/rejected applicants)
 */
class Archive extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect(url('/login'));
        }
    }
    
    /**
     * Archive Dashboard - List all archived applications
     */
    public function index() {
        // Get filters
        $year = $_GET['year'] ?? date('Y');
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        // Build query
        $where = "WHERE YEAR(aa.archived_at) = ?";
        $params = [$year];
        $types = 'i';
        
        if ($status !== 'all') {
            $where .= " AND aa.final_status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        if ($search) {
            $where .= " AND (aa.applicant_name LIKE ? OR aa.position_title LIKE ? OR aa.applicant_email LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'sss';
        }
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM archived_applications aa $where";
        $stmt = $this->db->prepare($countQuery);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $totalCount = $stmt->get_result()->fetch_assoc()['total'];
        
        // Get archived applications
        $query = "
            SELECT aa.*, u.full_name as archived_by_name
            FROM archived_applications aa
            LEFT JOIN users u ON aa.archived_by = u.id
            $where
            ORDER BY aa.archived_at DESC
            LIMIT ? OFFSET ?
        ";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $archives = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get stats
        $stats = $this->getStats($year);
        
        // Get available years
        $yearsResult = $this->db->query("SELECT DISTINCT YEAR(archived_at) as year FROM archived_applications ORDER BY year DESC");
        $years = $yearsResult ? $yearsResult->fetch_all(MYSQLI_ASSOC) : [];
        if (empty($years)) {
            $years = [['year' => date('Y')]];
        }
        
        $this->view('master_admin/archive/index', [
            'pageTitle' => 'Archive Management',
            'archives' => $archives,
            'stats' => $stats,
            'years' => $years,
            'currentYear' => $year,
            'currentStatus' => $status,
            'search' => $search,
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'totalPages' => ceil($totalCount / $perPage)
        ]);
    }
    
    /**
     * Get archive statistics
     */
    private function getStats($year) {
        $stats = [
            'total' => 0,
            'approved' => 0,
            'rejected' => 0,
            'this_month' => 0
        ];
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN final_status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN final_status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN MONTH(archived_at) = MONTH(CURRENT_DATE()) AND YEAR(archived_at) = YEAR(CURRENT_DATE()) THEN 1 ELSE 0 END) as this_month
            FROM archived_applications
            WHERE YEAR(archived_at) = ?
        ");
        $stmt->bind_param('i', $year);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result) {
            $stats = $result->fetch_assoc();
        }
        
        return $stats;
    }
    
    /**
     * View archived application details
     */
    public function detail($id) {
        $stmt = $this->db->prepare("
            SELECT aa.*, u.full_name as archived_by_name
            FROM archived_applications aa
            LEFT JOIN users u ON aa.archived_by = u.id
            WHERE aa.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $archive = $stmt->get_result()->fetch_assoc();
        
        if (!$archive) {
            flash('error', 'Archive not found');
            redirect(url('/master-admin/archive'));
        }
        
        $this->view('master_admin/archive/view', [
            'pageTitle' => 'Archived Application',
            'archive' => $archive
        ]);
    }
    
    /**
     * Archive an application
     */
    public function archive($appId) {
        // Get application data
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   u.full_name as applicant_name, u.email as applicant_email, u.phone as applicant_phone,
                   jv.title as position_title, d.name as department_name,
                   s.name as status_name,
                   h.full_name as handler_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies jv ON a.vacancy_id = jv.id
            LEFT JOIN departments d ON jv.department_id = d.id
            JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN users h ON a.current_crewing_id = h.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $appId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            return $this->json(['success' => false, 'message' => 'Application not found']);
        }
        
        // Determine final status
        $finalStatus = 'rejected';
        if ($app['status_id'] == 6) { // Approved
            $finalStatus = 'approved';
        }
        
        // Insert into archive
        $stmt = $this->db->prepare("
            INSERT INTO archived_applications 
            (original_application_id, user_id, vacancy_id, final_status,
             applicant_name, applicant_email, applicant_phone, position_title, department_name,
             document_score, interview_score, overall_score, admin_notes, rejection_reason,
             handler_name, applied_at, completed_at, archived_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
        ");
        
        $stmt->bind_param(
            'iiissssssiiissssi',
            $appId,
            $app['user_id'],
            $app['vacancy_id'],
            $finalStatus,
            $app['applicant_name'],
            $app['applicant_email'],
            $app['applicant_phone'],
            $app['position_title'],
            $app['department_name'],
            $app['document_score'],
            $app['interview_score'],
            $app['overall_score'],
            $app['admin_notes'],
            $app['rejection_reason'],
            $app['handler_name'],
            $app['submitted_at'],
            $_SESSION['user_id']
        );
        
        if ($stmt->execute()) {
            // Delete from active applications
            $delStmt = $this->db->prepare("DELETE FROM applications WHERE id = ?");
            $delStmt->bind_param('i', $appId);
            $delStmt->execute();
            
            return $this->json(['success' => true, 'message' => 'Application archived successfully']);
        }
        
        return $this->json(['success' => false, 'message' => 'Failed to archive application']);
    }
    
    /**
     * Restore application from archive
     */
    public function restore($id) {
        $stmt = $this->db->prepare("SELECT * FROM archived_applications WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $archive = $stmt->get_result()->fetch_assoc();
        
        if (!$archive) {
            return $this->json(['success' => false, 'message' => 'Archive not found']);
        }
        
        // Check if original user still exists
        $userCheckStmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
        $userCheckStmt->bind_param('i', $archive['user_id']);
        $userCheckStmt->execute();
        $userCheck = $userCheckStmt->get_result()->fetch_assoc();
        if (!$userCheck) {
            return $this->json(['success' => false, 'message' => 'Original user no longer exists']);
        }
        
        // Determine status to restore to
        $statusId = $archive['final_status'] == 'approved' ? 6 : 7;
        
        // Re-insert into applications
        $stmt = $this->db->prepare("
            INSERT INTO applications 
            (user_id, vacancy_id, status_id, document_score, interview_score, overall_score, 
             admin_notes, rejection_reason, submitted_at, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->bind_param(
            'iiiiissss',
            $archive['user_id'],
            $archive['vacancy_id'],
            $statusId,
            $archive['document_score'],
            $archive['interview_score'],
            $archive['overall_score'],
            $archive['admin_notes'],
            $archive['rejection_reason'],
            $archive['applied_at']
        );
        
        if ($stmt->execute()) {
            // Delete from archive
            $delStmt = $this->db->prepare("DELETE FROM archived_applications WHERE id = ?");
            $delStmt->bind_param('i', $id);
            $delStmt->execute();
            
            return $this->json(['success' => true, 'message' => 'Application restored successfully']);
        }
        
        return $this->json(['success' => false, 'message' => 'Failed to restore application']);
    }
    
    /**
     * Bulk archive applications
     */
    public function bulkArchive() {
        $ids = $_POST['ids'] ?? [];
        
        if (empty($ids)) {
            return $this->json(['success' => false, 'message' => 'No applications selected']);
        }
        
        $success = 0;
        foreach ($ids as $id) {
            $result = $this->archive($id);
            if (json_decode($result, true)['success'] ?? false) {
                $success++;
            }
        }
        
        return $this->json([
            'success' => true, 
            'message' => "$success applications archived successfully"
        ]);
    }
    
    /**
     * Export archives to CSV
     */
    public function export() {
        $year = $_GET['year'] ?? date('Y');
        $status = $_GET['status'] ?? 'all';
        
        $where = "WHERE YEAR(archived_at) = ?";
        $params = [$year];
        $types = 'i';
        if ($status !== 'all') {
            $where .= " AND final_status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                applicant_name as 'Name',
                applicant_email as 'Email',
                applicant_phone as 'Phone',
                position_title as 'Position',
                department_name as 'Department',
                final_status as 'Status',
                document_score as 'Doc Score',
                interview_score as 'Interview Score',
                overall_score as 'Overall Score',
                handler_name as 'Handler',
                applied_at as 'Applied Date',
                archived_at as 'Archived Date'
            FROM archived_applications
            $where
            ORDER BY archived_at DESC
        ");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        // Generate CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="archive_' . $year . '_' . $status . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Permanently delete archived application
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM archived_applications WHERE id = ?");
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            return $this->json(['success' => true, 'message' => 'Archive deleted permanently']);
        }
        
        return $this->json(['success' => false, 'message' => 'Failed to delete archive']);
    }
}
