<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Recruitment Report Controller
 * Daily report of all applicant data - sent and unsent to ERP
 */
class RecruitmentReport extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect('/login');
        }
    }
    
    /**
     * Main report page with filters
     */
    public function index() {
        // Get filter parameters
        $dateFrom = $_GET['date_from'] ?? date('Y-m-d');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');
        $status = $_GET['status'] ?? 'all'; // all, sent, not_sent, rejected
        $search = $_GET['search'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        
        // Build WHERE clause
        $where = "WHERE DATE(a.created_at) BETWEEN ? AND ?";
        $params = [$dateFrom, $dateTo];
        $types = 'ss';
        
        if ($status === 'sent') {
            $where .= " AND a.sent_to_erp_at IS NOT NULL";
        } elseif ($status === 'not_sent') {
            $where .= " AND a.sent_to_erp_at IS NULL AND a.status_id != 7"; // not rejected
        } elseif ($status === 'rejected') {
            $where .= " AND a.status_id = 7";
        }
        
        if ($search) {
            $where .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'sss';
        }
        
        // Get total count
        $countQuery = "
            SELECT COUNT(*) as total 
            FROM applications a
            JOIN users u ON a.user_id = u.id
            $where
        ";
        $stmt = $this->db->prepare($countQuery);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $totalCount = $stmt->get_result()->fetch_assoc()['total'];
        
        // Get report data
        $query = "
            SELECT 
                a.id,
                a.user_id,
                a.vacancy_id,
                a.status_id,
                a.submitted_at,
                a.created_at,
                a.sent_to_erp_at,
                a.erp_crew_id,
                a.interview_score,
                a.overall_score,
                u.full_name,
                u.email,
                u.phone,
                u.avatar,
                jv.title as vacancy_title,
                d.name as department_name,
                s.name as status_name,
                s.color as status_color
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
            LEFT JOIN departments d ON jv.department_id = d.id
            LEFT JOIN application_statuses s ON a.status_id = s.id
            $where
            ORDER BY a.created_at DESC
            LIMIT ? OFFSET ?
        ";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get daily stats
        $stats = $this->getDailyStats($dateFrom, $dateTo);
        
        $this->view('master_admin/recruitment_report/index', [
            'pageTitle' => 'Laporan Harian Rekrutmen',
            'applications' => $applications,
            'stats' => $stats,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'currentStatus' => $status,
            'search' => $search,
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'totalPages' => ceil($totalCount / $perPage),
            'perPage' => $perPage
        ]);
    }
    
    /**
     * Get statistics for date range
     */
    private function getDailyStats($dateFrom, $dateTo) {
        $stats = [
            'total' => 0,
            'sent_to_erp' => 0,
            'not_sent' => 0,
            'rejected' => 0,
            'pending' => 0,
            'approved' => 0
        ];
        
        // Total applications in date range
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM applications 
            WHERE DATE(created_at) BETWEEN ? AND ?
        ");
        $stmt->bind_param('ss', $dateFrom, $dateTo);
        $stmt->execute();
        $stats['total'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Sent to ERP
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM applications 
            WHERE DATE(created_at) BETWEEN ? AND ? 
            AND sent_to_erp_at IS NOT NULL
        ");
        $stmt->bind_param('ss', $dateFrom, $dateTo);
        $stmt->execute();
        $stats['sent_to_erp'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Not sent (excluding rejected)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM applications 
            WHERE DATE(created_at) BETWEEN ? AND ? 
            AND sent_to_erp_at IS NULL 
            AND status_id != 7
        ");
        $stmt->bind_param('ss', $dateFrom, $dateTo);
        $stmt->execute();
        $stats['not_sent'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Rejected (status_id = 7)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM applications 
            WHERE DATE(created_at) BETWEEN ? AND ? 
            AND status_id = 7
        ");
        $stmt->bind_param('ss', $dateFrom, $dateTo);
        $stmt->execute();
        $stats['rejected'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Approved / Hired (status_id = 6)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM applications 
            WHERE DATE(created_at) BETWEEN ? AND ? 
            AND status_id = 6
        ");
        $stmt->bind_param('ss', $dateFrom, $dateTo);
        $stmt->execute();
        $stats['approved'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Pending (status_id IN 1,2,3)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM applications 
            WHERE DATE(created_at) BETWEEN ? AND ? 
            AND status_id IN (1,2,3)
        ");
        $stmt->bind_param('ss', $dateFrom, $dateTo);
        $stmt->execute();
        $stats['pending'] = $stmt->get_result()->fetch_assoc()['total'];
        
        return $stats;
    }
    
    /**
     * Export PDF - returns print-friendly HTML page
     */
    public function exportPdf() {
        $dateFrom = $_GET['date_from'] ?? date('Y-m-d');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');
        $status = $_GET['status'] ?? 'all';
        
        // Build WHERE clause
        $where = "WHERE DATE(a.created_at) BETWEEN ? AND ?";
        $params = [$dateFrom, $dateTo];
        $types = 'ss';
        
        if ($status === 'sent') {
            $where .= " AND a.sent_to_erp_at IS NOT NULL";
        } elseif ($status === 'not_sent') {
            $where .= " AND a.sent_to_erp_at IS NULL AND a.status_id != 7";
        } elseif ($status === 'rejected') {
            $where .= " AND a.status_id = 7";
        }
        
        // Get ALL data (no pagination for PDF)
        $query = "
            SELECT 
                a.id,
                a.created_at,
                a.sent_to_erp_at,
                a.erp_crew_id,
                a.overall_score,
                u.full_name,
                u.email,
                u.phone,
                jv.title as vacancy_title,
                d.name as department_name,
                s.name as status_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
            LEFT JOIN departments d ON jv.department_id = d.id
            LEFT JOIN application_statuses s ON a.status_id = s.id
            $where
            ORDER BY a.created_at DESC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $stats = $this->getDailyStats($dateFrom, $dateTo);
        
        $this->view('master_admin/recruitment_report/print', [
            'applications' => $applications,
            'stats' => $stats,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'currentStatus' => $status,
            'totalCount' => count($applications)
        ]);
    }
}
