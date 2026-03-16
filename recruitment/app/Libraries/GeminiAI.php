<?php
/**
 * Gemini AI Library
 * Handles communication with Google Gemini API for interview scoring
 */

class GeminiAI {
    
    private $apiKey;
    private $model = 'gemini-2.0-flash-lite';
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private $lastError = null;
    
    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey ?: $this->loadApiKey();
    }
    
    /**
     * Load API key from database settings or config
     */
    private function loadApiKey() {
        $db = getDB();
        if ($db) {
            try {
                $result = $db->query("SELECT setting_value FROM recruitment_settings WHERE setting_key = 'gemini_api_key' LIMIT 1");
                if ($result && $row = $result->fetch_assoc()) {
                    return $row['setting_value'];
                }
            } catch (\Throwable $e) {
                // Table may not exist yet, ignore
            }
        }
        return null;
    }
    
    /**
     * Check if API key is configured
     */
    public function isConfigured() {
        return !empty($this->apiKey);
    }
    
    /**
     * Get last error
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Score an essay answer using Gemini AI
     */
    public function scoreEssay($question, $answer, $expectedKeywords = [], $maxScore = 100) {
        if (!$this->isConfigured()) {
            $this->lastError = 'API key not configured';
            return null;
        }
        
        if (empty(trim($answer))) {
            return [
                'score' => 0,
                'relevance' => 0,
                'completeness' => 0,
                'keywords' => [],
                'feedback' => 'Jawaban kosong.'
            ];
        }
        
        $keywordsStr = !empty($expectedKeywords) ? implode(', ', $expectedKeywords) : 'tidak ada keyword khusus';
        
        $prompt = <<<PROMPT
Kamu adalah penilai interview profesional untuk perusahaan maritime/crewing.

PERTANYAAN INTERVIEW:
{$question}

JAWABAN PELAMAR:
{$answer}

KEYWORD YANG DIHARAPKAN:
{$keywordsStr}

Berikan penilaian dalam format JSON SAJA (tanpa markdown, tanpa teks lain):
{
  "score": <angka 0-100, skor keseluruhan>,
  "relevance": <angka 0-100, seberapa relevan jawaban dengan pertanyaan>,
  "completeness": <angka 0-100, seberapa lengkap jawaban>,
  "keyword_matches": [<daftar keyword yang ditemukan di jawaban>],
  "feedback": "<feedback singkat dalam Bahasa Indonesia, 1-2 kalimat, evaluasi jawaban>"
}

Kriteria penilaian:
- Relevansi: Apakah jawaban menjawab pertanyaan dengan tepat?
- Kelengkapan: Apakah jawaban mencakup poin-poin penting?
- Kedalaman: Apakah jawaban menunjukkan pemahaman yang baik?
- Keyword: Apakah keyword penting disebutkan?
- Profesionalisme: Apakah jawaban disampaikan dengan baik?

PENTING: Balas HANYA dengan JSON valid, tanpa teks tambahan.
PROMPT;

        $response = $this->generateContent($prompt);
        
        if ($response === null) {
            return null;
        }
        
        // Parse JSON response
        $parsed = $this->parseJsonResponse($response);
        
        if ($parsed === null) {
            $this->lastError = 'Failed to parse AI response';
            return null;
        }
        
        return [
            'score' => max(0, min(100, intval($parsed['score'] ?? 0))),
            'relevance' => max(0, min(100, intval($parsed['relevance'] ?? 0))),
            'completeness' => max(0, min(100, intval($parsed['completeness'] ?? 0))),
            'keywords' => $parsed['keyword_matches'] ?? [],
            'feedback' => $parsed['feedback'] ?? ''
        ];
    }
    
    /**
     * Call Gemini API to generate content
     */
    private function generateContent($prompt) {
        $url = $this->baseUrl . $this->model . ':generateContent?key=' . $this->apiKey;
        
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'maxOutputTokens' => 500,
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            $this->lastError = 'cURL error: ' . $curlError;
            error_log('[GeminiAI] cURL error: ' . $curlError);
            return null;
        }
        
        if ($httpCode !== 200) {
            $this->lastError = 'API returned HTTP ' . $httpCode;
            error_log('[GeminiAI] HTTP ' . $httpCode . ': ' . $response);
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (!$data || !isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $this->lastError = 'Invalid API response format';
            error_log('[GeminiAI] Invalid response: ' . $response);
            return null;
        }
        
        return $data['candidates'][0]['content']['parts'][0]['text'];
    }
    
    /**
     * Parse JSON from AI response (handles markdown code blocks)
     */
    private function parseJsonResponse($text) {
        // Remove markdown code blocks if present
        $text = trim($text);
        $text = preg_replace('/^```json?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);
        $text = trim($text);
        
        $parsed = json_decode($text, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $parsed;
        }
        
        // Try to extract JSON from text
        if (preg_match('/\{[^{}]*\}/s', $text, $matches)) {
            $parsed = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $parsed;
            }
        }
        
        error_log('[GeminiAI] JSON parse error: ' . json_last_error_msg() . ' | Text: ' . $text);
        return null;
    }
    
    /**
     * Save API key to database
     */
    public static function saveApiKey($apiKey) {
        $db = getDB();
        if (!$db) return false;
        
        // Ensure settings table exists
        $db->query("CREATE TABLE IF NOT EXISTS recruitment_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        $stmt = $db->prepare("INSERT INTO recruitment_settings (setting_key, setting_value) VALUES ('gemini_api_key', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param('ss', $apiKey, $apiKey);
        return $stmt->execute();
    }
    
    /**
     * Test API connection
     */
    public function testConnection() {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API key belum dikonfigurasi'];
        }
        
        $response = $this->generateContent('Balas dengan: {"status":"ok"}');
        
        if ($response !== null) {
            return ['success' => true, 'message' => 'Koneksi Gemini AI berhasil!'];
        }
        
        return ['success' => false, 'message' => 'Gagal: ' . ($this->lastError ?? 'Unknown error')];
    }
}
