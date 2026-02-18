<?php
/**
 * Applicant - Select Recruiter Controller
 * Allows applicants to choose their preferred recruiter
 */

require_once APPPATH . 'Controllers/BaseController.php';

class SelectRecruiter extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn()) {
            flash('error', 'Please login to continue');
            redirect(url('/login'));
        }
    }
    
    /**
     * Display recruiter selection page with photos
     * Shows ALL active crewing staff (not just online ones)
     */
    public function index($vacancyId) {
        // Validate vacancy
        $stmt = $this->db->prepare("SELECT v.*, d.name as department_name FROM job_vacancies v LEFT JOIN departments d ON v.department_id = d.id WHERE v.id = ? AND v.status = 'published'");
        $stmt->bind_param('i', $vacancyId);
        $stmt->execute();
        $vacancy = $stmt->get_result()->fetch_assoc();
        
        if (!$vacancy) {
            flash('error', 'Vacancy not found or no longer active');
            redirect(url('/jobs'));
        }
        
        // Get ALL active crewing staff with photos and workload
        // Removed: is_online = 1 filter (show all active staff, not just online)
        // Changed: INNER JOIN to LEFT JOIN on crewing_profiles (staff without profiles still appear)
        $query = "
            SELECT 
                u.id, 
                u.full_name, 
                u.email,
                u.avatar,
                u.is_online,
                u.last_activity,
                cp.photo,
                cp.bio,
                cp.specialization,
                cp.max_applications,
                cp.employee_id,
                COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_count,
                COALESCE(AVG(cr.rating), 0) as avg_rating,
                COUNT(DISTINCT cr.id) as total_ratings
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            LEFT JOIN crewing_ratings cr ON u.id = cr.crewing_id
            WHERE u.role_id = 5 
              AND u.is_active = 1
            GROUP BY u.id
            ORDER BY u.is_online DESC, active_count ASC, avg_rating DESC
        ";
        
        $result = $this->db->query($query);
        $recruiters = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        // Calculate workload percentage for each recruiter
        foreach ($recruiters as &$recruiter) {
            $max = $recruiter['max_applications'] ?: 50;
            $recruiter['workload_percent'] = ($recruiter['active_count'] / $max) * 100;
        }
        
        $this->view('applicant/recruiters/select', [
            'pageTitle' => 'Select Your Recruiter',
            'vacancy' => $vacancy,
            'recruiters' => $recruiters
        ]);
    }
    
    /**
     * Random recruiter selection (based on lowest workload for fairness)
     * Shows ALL active crewing staff, not just online ones
     */
    public function random($vacancyId) {
        // Validate vacancy first
        $stmtV = $this->db->prepare("SELECT id FROM job_vacancies WHERE id = ? AND status = 'published'");
        $stmtV->bind_param('i', $vacancyId);
        $stmtV->execute();
        if (!$stmtV->get_result()->fetch_assoc()) {
            flash('error', 'Vacancy not found or no longer active');
            redirect(url('/jobs'));
            return;
        }

        // Get recruiter with lowest workload (removed is_online filter)
        $query = "
            SELECT u.id
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to AND aa.status = 'active'
            WHERE u.role_id = 5 
              AND u.is_active = 1
            GROUP BY u.id
            HAVING COUNT(aa.id) < COALESCE(MAX(cp.max_applications), 50)
            ORDER BY COUNT(aa.id) ASC, RAND()
            LIMIT 1
        ";
        
        $result = $this->db->query($query);
        $recruiter = $result ? $result->fetch_assoc() : null;
        
        if (!$recruiter) {
            // Redirect to select-recruiter page (NOT back to /jobs/ to avoid loop)
            flash('warning', 'Semua rekruter sedang penuh saat ini. Silakan pilih secara manual.');
            redirect(url('/applicant/select-recruiter/' . $vacancyId));
            return;
        }
        
        // Store in session
        $_SESSION['preferred_recruiter'] = $recruiter['id'];
        $_SESSION['recruiter_assignment_type'] = 'random';
        
        flash('success', 'Great! We\'ve randomly assigned a recruiter for you.');
        redirect(url('/applicant/applications/apply/' . $vacancyId));
    }
    
    /**
     * Manual recruiter selection
     */
    public function select($vacancyId, $recruiterId) {
        // Validate recruiter
        $stmt = $this->db->prepare("
            SELECT u.id, u.full_name 
            FROM users u
            WHERE u.id = ? AND u.role_id = 5 AND u.is_active = 1
        ");
        $stmt->bind_param('i', $recruiterId);
        $stmt->execute();
        $recruiter = $stmt->get_result()->fetch_assoc();
        
        if (!$recruiter) {
            flash('error', 'Selected recruiter is not available');
            redirect(url('/applicant/select-recruiter/' . $vacancyId));
        }
        
        // Store in session
        $_SESSION['preferred_recruiter'] = $recruiterId;
        $_SESSION['recruiter_assignment_type'] = 'preferred';
        
        flash('success', '✓ You selected ' . $recruiter['full_name'] . ' as your recruiter');
        redirect(url('/applicant/applications/apply/' . $vacancyId));
    }
}
