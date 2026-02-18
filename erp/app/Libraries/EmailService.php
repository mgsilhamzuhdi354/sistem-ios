<?php
namespace App\Libraries;

use Config\Services;

/**
 * Email Service
 * Handles email sending for medical checkup requests and other notifications
 */
class EmailService
{

    private $config;

    public function __construct()
    {
        // Load email configuration
        $this->config = [
            'medical_vendors' => [
                'indosehat' => [
                    'email' => 'booking@indosehat.com',
                    'name' => 'Indosehat Medical Center',
                    'default' => true
                ],
                'prodia' => [
                    'email' => 'cs@prodia.co.id',
                    'name' => 'Prodia Medical Clinic'
                ]
            ],
            'from_email' => 'hr@indoocean.com',
            'from_name' => 'PT Indo Ocean - HR Department'
        ];
    }

    /**
     * Send medical checkup request to clinic vendor
     *
     * @param array $employeeData
     * @param string $clinicVendor (default: indosehat)
     * @return array
     */
    public function sendMedicalCheckupRequest($employeeData, $clinicVendor = 'indosehat')
    {
        try {
            // Get clinic configuration
            if (!isset($this->config['medical_vendors'][$clinicVendor])) {
                return [
                    'success' => false,
                    'message' => 'Clinic vendor not found'
                ];
            }

            $clinic = $this->config['medical_vendors'][$clinicVendor];

            // Prepare email
            $email = Services::email();

            $email->setFrom($this->config['from_email'], $this->config['from_name']);
            $email->set

            ($clinic['email']);
            $email->setBCC('hr@indoocean.com'); // Copy to HR
            $email->setSubject('Medical Checkup Request - ' . $employeeData['full_name']);

            $message = $this->getMedicalCheckupTemplate($employeeData, $clinic['name']);
            $email->setMessage($message);

            // Send email
            if ($email->send()) {
                // Log successful email
                $this->logEmail($employeeData, $clinic, 'sent');

                return [
                    'success' => true,
                    'message' => 'Medical checkup request sent to ' . $clinic['name']
                ];
            } else {
                // Log failed email
                $this->logEmail($employeeData, $clinic, 'failed');

                return [
                    'success' => false,
                    'message' => 'Failed to send email: ' . $email->printDebugger()
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get medical checkup email template
     *
     * @param array $employee
     * @param string $clinicName
     * @return string
     */
    private function getMedicalCheckupTemplate($employee, $clinicName)
    {
        $rank = $employee['rank'] ?? $employee['vacancy_title'] ?? 'N/A';
        $dob = isset($employee['date_of_birth']) ? date('d F Y', strtotime($employee['date_of_birth'])) : 'N/A';

        $html = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0056b3; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; display: inline-block; width: 150px; }
        .section-title { color: #0056b3; font-weight: bold; margin-top: 20px; margin-bottom: 10px; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
        ul { margin: 10px 0; }
        li { margin: 5px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Medical Checkup Request</h2>
            <p>PT Indo Ocean Crew Services</p>
        </div>
        
        <div class='content'>
            <p>Dear {$clinicName} Team,</p>
            
            <p>We kindly request your assistance to schedule a comprehensive medical checkup for our crew candidate:</p>
            
            <div class='section-title'>Candidate Information:</div>
            <div class='info-row'><span class='label'>Full Name:</span> {$employee['full_name']}</div>
            <div class='info-row'><span class='label'>Position/Rank:</span> {$rank}</div>
            <div class='info-row'><span class='label'>Date of Birth:</span> {$dob}</div>
            <div class='info-row'><span class='label'>ID Number:</span> {$employee['ktp_number']}</div>
            <div class='info-row'><span class='label'>Phone Number:</span> {$employee['phone']}</div>
            <div class='info-row'><span class='label'>Email:</span> {$employee['email']}</div>
            
            <div class='section-title'>Required Medical Examinations:</div>
            <ul>
                <li>General Health Check-up</li>
                <li>Vision Test (Eye Examination)</li>
                <li>Complete Blood Test</li>
                <li>Drug Screening Test</li>
                <li>Chest X-Ray</li>
                <li>ECG (Electrocardiogram)</li>
                <li>Hearing Test</li>
                <li>Physical Fitness Assessment</li>
            </ul>
            
            <p><strong>Please confirm the available schedule at your earliest convenience.</strong></p>
            
            <p>Feel free to contact us if you need any additional information.</p>
            
            <p>Best regards,<br>
            <strong>HR Department</strong><br>
            PT Indo Ocean Crew Services<br>
            Phone: +62 21 XXXXXXX<br>
            Email: hr@indoocean.com</p>
        </div>
        
        <div class='footer'>
            <p>This is an automated email from PT Indo Ocean ERP System</p>
        </div>
    </div>
</body>
</html>
        ";

        return $html;
    }

    /**
     * Log email sending
     *
     * @param array $employeeData
     * @param array $clinic
     * @param string $status
     */
    private function logEmail($employeeData, $clinic, $status)
    {
        // Log to database or file
        log_message('info', 'Medical Email ' . $status . ': ' . $employeeData['full_name'] . ' to ' . $clinic['name']);
    }

    /**
     * Send welcome email to new employee
     *
     * @param array $employeeData
     * @return array
     */
    public function sendWelcomeEmail($employeeData)
    {
        try {
            $email = Services::email();

            $email->setFrom($this->config['from_email'], $this->config['from_name']);
            $email->setTo($employeeData['email']);
            $email->setSubject('Welcome to PT Indo Ocean!');

            $message = $this->getWelcomeTemplate($employeeData);
            $email->setMessage($message);

            if ($email->send()) {
                return ['success' => true, 'message' => 'Welcome email sent'];
            } else {
                return ['success' => false, 'message' => 'Failed to send welcome email'];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get welcome email template
     *
     * @param array $employee
     * @return string
     */
    private function getWelcomeTemplate($employee)
    {
        return "
        <h2>Welcome to PT Indo Ocean!</h2>
        <p>Dear {$employee['full_name']},</p>
        <p>We are pleased to welcome you to PT Indo Ocean Crew Services.</p>
        <p>Your onboarding process has begun. Please check your email regularly for further instructions.</p>
        <p>Best regards,<br>HR Team</p>
        ";
    }
}
