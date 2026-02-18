<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * API Onboarding Controller
 * Provides REST API endpoints for onboarding tracking
 */
class ApiOnboardingController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        // Set JSON header
        header('Content-Type: application/json');

        // CORS headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /**
     * Get onboarding progress for a candidate
     * GET /api/onboarding/{applicationId}
     */
    public function getProgress($applicationId)
    {
        try {
            // Get application details
            $appStmt = $this->db->prepare("
                SELECT a.*, u.full_name, u.email, v.title as vacancy_title
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                WHERE a.id = ?
            ");
            $appStmt->bind_param('i', $applicationId);
            $appStmt->execute();
            $application = $appStmt->get_result()->fetch_assoc();

            if (!$application) {
                $this->jsonResponse([
                    'code' => 404,
                    'message' => 'Application not found',
                    'data' => null
                ], 404);
                return;
            }

            // Get onboarding checklist (from onboarding_tasks table if exists)
            // For now, return a structured response
            $onboardingSteps = [
                [
                    'step' => 'document_verification',
                    'name' => 'Document Verification',
                    'status' => 'completed',
                    'completed_at' => $application['reviewed_at']
                ],
                [
                    'step' => 'medical_checkup',
                    'name' => 'Medical Checkup',
                    'status' => $this->getMedicalStatus($applicationId),
                    'completed_at' => null
                ],
                [
                    'step' => 'contract_signing',
                    'name' => 'Contract Signing',
                    'status' => 'pending',
                    'completed_at' => null
                ],
                [
                    'step' => 'system_access',
                    'name' => 'System Access Setup',
                    'status' => $application['is_synced_to_erp'] ? 'completed' : 'pending',
                    'completed_at' => $application['synced_at']
                ],
                [
                    'step' => 'orientation',
                    'name' => 'Company Orientation',
                    'status' => 'pending',
                    'completed_at' => null
                ]
            ];

            // Calculate progress percentage
            $completedSteps = count(array_filter($onboardingSteps, function ($step) {
                return $step['status'] === 'completed';
            }));
            $progress = ($completedSteps / count($onboardingSteps)) * 100;

            $this->jsonResponse([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'application' => $application,
                    'steps' => $onboardingSteps,
                    'progress_percentage' => round($progress, 2),
                    'completed_steps' => $completedSteps,
                    'total_steps' => count($onboardingSteps)
                ]
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
     * Mark onboarding step as complete
     * POST /api/onboarding/{applicationId}/complete-step
     */
    public function completeStep($applicationId)
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

            $step = $this->input('step');
            $notes = $this->input('notes') ?: '';

            if (!$step) {
                $this->jsonResponse([
                    'code' => 400,
                    'message' => 'Step name is required',
                    'data' => null
                ], 400);
                return;
            }

            // Log completion (you can create onboarding_progress table later)
            // For now, just return success

            $this->jsonResponse([
                'code' => 200,
                'message' => 'Step marked as complete',
                'data' => [
                    'application_id' => $applicationId,
                    'step' => $step,
                    'completed_at' => date('Y-m-d H:i:s')
                ]
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
     * Get medical checkup status
     */
    private function getMedicalStatus($applicationId)
    {
        $stmt = $this->db->prepare("
            SELECT status FROM medical_checkups 
            WHERE application_id = ? 
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return 'pending';
        }

        return $result['status'] === 'completed' ? 'completed' : 'in_progress';
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
