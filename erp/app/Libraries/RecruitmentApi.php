<?php
namespace App\Libraries;

/**
 * Recruitment API Library
 * Handles communication with Recruitment System API
 */
class RecruitmentApi
{

    private $baseUrl;

    public function __construct()
    {
        // Configure base URL
        $this->baseUrl = 'http://localhost/PT_indoocean/recruitment/public/api';
    }

    /**
     * Get all candidates with optional filters
     *
     * @param array $filters (status, department, search, limit)
     * @return array
     */
    public function getCandidates($filters = [])
    {
        $queryParams = http_build_query($filters);
        $url = $this->baseUrl . '/candidates' . ($queryParams ? '?' . $queryParams : '');

        return $this->request('GET', $url);
    }

    /**
     * Get approved candidates ready for ERP import
     *
     * @return array
     */
    public function getApprovedCandidates()
    {
        $url = $this->baseUrl . '/candidates/approved';
        return $this->request('GET', $url);
    }

    /**
     * Get single candidate detail by ID
     *
     * @param int $id
     * @return array
     */
    public function getCandidateDetails($id)
    {
        $url = $this->baseUrl . '/candidates/' . $id;
        return $this->request('GET', $url);
    }

    /**
     * Mark candidate as synced to ERP
     *
     * @param int $applicationId
     * @param int $erpEmployeeId
     * @return array
     */
    public function markAsSynced($applicationId, $erpEmployeeId)
    {
        $url = $this->baseUrl . '/candidates/' . $applicationId . '/mark-synced';
        return $this->request('POST', $url, [
            'erp_employee_id' => $erpEmployeeId
        ]);
    }

    /**
     * Get onboarding progress for candidate
     *
     * @param int $applicationId
     * @return array
     */
    public function getOnboardingProgress($applicationId)
    {
        $url = $this->baseUrl . '/onboarding/' . $applicationId;
        return $this->request('GET', $url);
    }

    /**
     * Mark onboarding step as complete
     *
     * @param int $applicationId
     * @param string $step
     * @param string $notes
     * @return array
     */
    public function completeOnboardingStep($applicationId, $step, $notes = '')
    {
        $url = $this->baseUrl . '/onboarding/' . $applicationId . '/complete-step';
        return $this->request('POST', $url, [
            'step' => $step,
            'notes' => $notes
        ]);
    }

    /**
     * Make HTTP request to recruitment API
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @return array
     */
    private function request($method, $url, $data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => 'cURL Error: ' . $error,
                'data' => null
            ];
        }

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => 'JSON Decode Error: ' . json_last_error_msg(),
                'data' => null
            ];
        }

        return [
            'success' => ($httpCode >= 200 && $httpCode < 300),
            'code' => $httpCode,
            'data' => $result['data'] ?? null,
            'message' => $result['message'] ?? ''
        ];
    }
}
