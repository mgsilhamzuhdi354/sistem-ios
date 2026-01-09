<?php
/**
 * PT Indo Ocean - ERP System
 * OTP Model for Two-Factor Authentication
 */

namespace App\Models;

require_once APPPATH . 'Models/BaseModel.php';

class OtpModel extends BaseModel
{
    protected $table = 'otp_codes';
    protected $allowedFields = ['user_id', 'code', 'type', 'expires_at', 'attempts', 'used'];
    
    /**
     * Generate and store new OTP code
     */
    public function generate($userId, $type = 'login', $expiresMinutes = 5)
    {
        // Invalidate any existing OTP for this user/type
        $this->invalidateExisting($userId, $type);
        
        // Generate 6-digit OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiresMinutes} minutes"));
        
        $this->create([
            'user_id' => $userId,
            'code' => $code,
            'type' => $type,
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'used' => 0
        ]);
        
        return $code;
    }
    
    /**
     * Verify OTP code
     */
    public function verify($userId, $code, $type = 'login')
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? AND type = ? AND used = 0 
                ORDER BY created_at DESC LIMIT 1";
        $result = $this->query($sql, [$userId, $type], 'is');
        
        if (empty($result)) {
            return ['success' => false, 'message' => 'Kode OTP tidak ditemukan'];
        }
        
        $otp = $result[0];
        
        // Check if expired
        if (strtotime($otp['expires_at']) < time()) {
            return ['success' => false, 'message' => 'Kode OTP sudah kedaluwarsa'];
        }
        
        // Check max attempts (3)
        if ($otp['attempts'] >= 3) {
            return ['success' => false, 'message' => 'Terlalu banyak percobaan. Minta kode baru.'];
        }
        
        // Check code match
        if ($otp['code'] !== $code) {
            // Increment attempts
            $this->update($otp['id'], ['attempts' => $otp['attempts'] + 1]);
            $remaining = 2 - $otp['attempts'];
            return ['success' => false, 'message' => "Kode OTP salah. {$remaining} percobaan tersisa."];
        }
        
        // Success - mark as used
        $this->update($otp['id'], ['used' => 1]);
        
        return ['success' => true, 'message' => 'OTP verified'];
    }
    
    /**
     * Invalidate existing OTP for user
     */
    public function invalidateExisting($userId, $type = 'login')
    {
        $sql = "UPDATE {$this->table} SET used = 1 WHERE user_id = ? AND type = ? AND used = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('is', $userId, $type);
        return $stmt->execute();
    }
    
    /**
     * Check if user has pending OTP
     */
    public function hasPendingOtp($userId, $type = 'login')
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? AND type = ? AND used = 0 AND expires_at > NOW()
                ORDER BY created_at DESC LIMIT 1";
        $result = $this->query($sql, [$userId, $type], 'is');
        return !empty($result);
    }
    
    /**
     * Get time remaining for current OTP
     */
    public function getTimeRemaining($userId, $type = 'login')
    {
        $sql = "SELECT expires_at FROM {$this->table} 
                WHERE user_id = ? AND type = ? AND used = 0 
                ORDER BY created_at DESC LIMIT 1";
        $result = $this->query($sql, [$userId, $type], 'is');
        
        if (empty($result)) return 0;
        
        $remaining = strtotime($result[0]['expires_at']) - time();
        return max(0, $remaining);
    }
}
