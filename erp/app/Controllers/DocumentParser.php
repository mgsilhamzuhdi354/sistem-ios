<?php
/**
 * PT Indo Ocean - ERP System
 * AI Document Parser Controller
 * Supports Gemini & Claude APIs - scans EVERY page
 */

namespace App\Controllers;

class DocumentParser extends BaseController
{
    public function index()
    {
        $csrfToken = $this->generateCsrfToken();
        $scanHistory = $_SESSION['scan_history'] ?? [];
        $provider = $_ENV['AI_PROVIDER'] ?? 'gemini';
        
        return $this->view('document_parser/index', [
            'title' => __('document_parser.title'),
            'currentPage' => 'document-parser',
            'csrf_token' => $csrfToken,
            'scanHistory' => $scanHistory,
            'aiProvider' => $provider,
        ]);
    }

    public function process()
    {
        set_time_limit(300);
        ini_set('memory_limit', '256M');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 400);
        }

        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $this->json(['success' => false, 'message' => 'Token CSRF tidak valid.'], 403);
        }

        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'Upload file gagal.'], 400);
        }

        $file = $_FILES['document'];
        if ($file['size'] > 10 * 1024 * 1024) {
            $this->json(['success' => false, 'message' => 'File melebihi 10MB.'], 400);
        }

        $allowedMimes = ['application/pdf' => 'application/pdf', 'image/jpeg' => 'image/jpeg', 'image/jpg' => 'image/jpeg', 'image/png' => 'image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowedMimes[$detectedMime])) {
            $this->json(['success' => false, 'message' => 'Format tidak didukung. Hanya PDF, JPG, PNG.'], 400);
        }

        $mimeType = $allowedMimes[$detectedMime];
        $base64Data = base64_encode(file_get_contents($file['tmp_name']));

        $provider = $_ENV['AI_PROVIDER'] ?? 'gemini';

        if ($provider === 'claude') {
            $apiResult = $this->callClaudeApi($base64Data, $mimeType);
            $modelLabel = $_ENV['CLAUDE_MODEL'] ?? 'claude-sonnet-4-20250514';
        } else {
            $apiResult = $this->callGeminiApi($base64Data, $mimeType);
            $modelLabel = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.5-flash';
        }

        if (!$apiResult['success']) {
            $this->json($apiResult, 500);
        }

        $this->saveScanHistory($apiResult['data'], $file['name']);
        $this->json([
            'success' => true,
            'data' => $apiResult['data'],
            'model' => $modelLabel,
            'provider' => $provider,
            'message' => 'OK'
        ]);
    }

    private function buildPrompt()
    {
        $today = date('Y-m-d');
        $p = array();
        $p[] = 'Anda adalah AI ekstraktor dokumen maritim profesional.';
        $p[] = 'BACA SETIAP HALAMAN dari halaman 1 sampai halaman terakhir. TIDAK BOLEH ada halaman yang dilewati.';
        $p[] = '';
        $p[] = 'TANGGAL HARI INI: ' . $today;
        $p[] = 'Tanggal sebelum hari ini = masa lalu, BUKAN masa depan.';
        $p[] = '';
        $p[] = 'TUGAS: Buat 1 entry untuk SETIAP HALAMAN di array certificates.';
        $p[] = 'Jika dokumen 44 halaman, maka array certificates harus punya 44 entry.';
        $p[] = '';
        $p[] = 'ATURAN OUTPUT:';
        $p[] = '1. HANYA JSON murni tanpa markdown/backtick.';
        $p[] = '2. String value SATU BARIS (tanpa line break).';
        $p[] = '3. Format COMPACT.';
        $p[] = '4. JSON harus LENGKAP dan VALID.';
        $p[] = '';
        $p[] = 'STRUKTUR:';
        $p[] = '- seafarer_info: {name, nationality, date_of_birth, place_of_birth, seaman_book_number} masing-masing {value, confidence}';
        $p[] = '- certificates: array, 1 entry per halaman:';
        $p[] = '  {page_number, document_type:{value,confidence}, document_number:{value,confidence}, rank_capacity:{value,confidence}, issue_date:{value,confidence}, expiry_date:{value,confidence}, issuing_authority:{value,confidence}, limitations:{gross_tonnage:{value,confidence}, engine_kw:{value,confidence}, voyage_area:{value,confidence}}, is_valid:bool, status_notes:string, page_content_summary:string}';
        $p[] = '- total_certificates_found: jumlah sertifikat unik';
        $p[] = '- total_pages_scanned: total halaman';
        $p[] = '- is_document_clear: bool';
        $p[] = '- anomaly_notes: string (kosong jika normal)';
        $p[] = '';
        $p[] = 'TIPE HALAMAN untuk document_type:';
        $p[] = '- Sertifikat: COC, COP, Endorsement, BST, GMDSS, Medical, Passport, Seaman Book, STCW, Vaccination, GOC, dll';
        $p[] = '- Non-sertifikat: CV/Resume, Cover Page, Blank Page, Back Page, Photo Page, Stamp Page, dll';
        $p[] = '';
        $p[] = 'ATURAN PER HALAMAN:';
        $p[] = '- Halaman CV/resume: document_type "CV/Resume", isi ringkasan di page_content_summary';
        $p[] = '- Halaman belakang sertifikat: document_type "Back Page - [nama sertifikat]"';
        $p[] = '- Halaman kosong: document_type "Blank Page"';
        $p[] = '- Halaman foto: document_type "Photo Page"';
        $p[] = '- Halaman stempel/cap: document_type "Stamp/Endorsement Page"';
        $p[] = '- Field kosong = value null, confidence 0';
        $p[] = '- Tanggal YYYY-MM-DD';
        $p[] = '- is_valid false jika expiry_date < ' . $today;
        $p[] = '- page_content_summary: ringkasan 1 kalimat isi halaman';
        $p[] = '- anomaly_notes kosongkan jika normal';
        $p[] = '- PASTIKAN JSON LENGKAP';
        return implode("\n", $p);
    }

    // ========================================
    // CLAUDE API
    // ========================================
    private function callClaudeApi($base64Data, $mimeType)
    {
        $apiKey = $_ENV['CLAUDE_API_KEY'] ?? '';
        if (empty($apiKey)) {
            return ['success' => false, 'message' => 'CLAUDE_API_KEY belum dikonfigurasi.'];
        }

        $model = $_ENV['CLAUDE_MODEL'] ?? 'claude-sonnet-4-20250514';
        $url = 'https://api.anthropic.com/v1/messages';
        $prompt = $this->buildPrompt();

        // Claude API format
        $mediaType = $mimeType;
        if ($mimeType === 'application/pdf') {
            $sourceType = 'base64';
            $contentBlock = [
                'type' => 'document',
                'source' => [
                    'type' => 'base64',
                    'media_type' => 'application/pdf',
                    'data' => $base64Data,
                ]
            ];
        } else {
            $contentBlock = [
                'type' => 'image',
                'source' => [
                    'type' => 'base64',
                    'media_type' => $mediaType,
                    'data' => $base64Data,
                ]
            ];
        }

        $payload = [
            'model' => $model,
            'max_tokens' => 65536,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        $contentBlock,
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $apiKey,
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'message' => 'Gagal terhubung ke Claude API: ' . $curlError];
        }

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            $errorMsg = $responseData['error']['message'] ?? ('HTTP Error ' . $httpCode);
            return ['success' => false, 'message' => 'Claude API Error: ' . $errorMsg];
        }

        // Extract text from Claude response
        $textContent = '';
        if (isset($responseData['content'])) {
            foreach ($responseData['content'] as $block) {
                if ($block['type'] === 'text') {
                    $textContent .= $block['text'];
                }
            }
        }

        $stopReason = $responseData['stop_reason'] ?? 'unknown';

        if (empty($textContent)) {
            return ['success' => false, 'message' => 'Claude tidak mengembalikan hasil.'];
        }

        // Debug
        $debugDir = APPPATH . '../storage';
        if (!is_dir($debugDir)) mkdir($debugDir, 0777, true);
        file_put_contents($debugDir . '/claude_debug_latest.txt', $textContent);
        file_put_contents($debugDir . '/claude_debug_latest_meta.txt', 'stop_reason=' . $stopReason . "\ntext_length=" . strlen($textContent) . "\nmodel=" . $model);

        $parsedData = $this->cleanAndParseJson($textContent, $stopReason);

        if ($parsedData === null) {
            return [
                'success' => false,
                'message' => 'Claude response tidak valid (JSON ' . json_last_error_msg() . '). Stop: ' . $stopReason,
                'raw_response' => mb_substr($textContent, 0, 500, 'UTF-8')
            ];
        }

        return ['success' => true, 'data' => $parsedData];
    }

    // ========================================
    // GEMINI API
    // ========================================
    private function callGeminiApi($base64Data, $mimeType)
    {
        $apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
        if (empty($apiKey)) {
            return ['success' => false, 'message' => 'GEMINI_API_KEY belum dikonfigurasi.'];
        }

        $model = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.5-flash';
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey;
        $prompt = $this->buildPrompt();

        $payload = [
            'contents' => [['parts' => [
                ['text' => $prompt],
                ['inline_data' => ['mime_type' => $mimeType, 'data' => $base64Data]]
            ]]],
            'generationConfig' => [
                'temperature' => 0.1,
                'topP' => 0.8,
                'maxOutputTokens' => 65536,
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'message' => 'Gagal terhubung ke Gemini API: ' . $curlError];
        }
        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? ('HTTP Error ' . $httpCode);
            return ['success' => false, 'message' => 'Gemini API Error: ' . $errorMsg];
        }

        $responseData = json_decode($response, true);
        $textContent = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $finishReason = $responseData['candidates'][0]['finishReason'] ?? 'UNKNOWN';

        if (empty($textContent)) {
            return ['success' => false, 'message' => 'AI tidak mengembalikan hasil.'];
        }

        // Debug
        $debugDir = APPPATH . '../storage';
        if (!is_dir($debugDir)) mkdir($debugDir, 0777, true);
        file_put_contents($debugDir . '/gemini_debug_latest.txt', $textContent);
        file_put_contents($debugDir . '/gemini_debug_latest_meta.txt', 'finish_reason=' . $finishReason . "\ntext_length=" . strlen($textContent));

        $parsedData = $this->cleanAndParseJson($textContent, $finishReason);

        if ($parsedData === null) {
            return [
                'success' => false,
                'message' => 'AI response tidak valid (JSON ' . json_last_error_msg() . '). Finish: ' . $finishReason,
                'raw_response' => mb_substr($textContent, 0, 500, 'UTF-8')
            ];
        }

        return ['success' => true, 'data' => $parsedData];
    }

    // ========================================
    // JSON CLEANING & REPAIR
    // ========================================
    private function cleanAndParseJson($text, $finishReason = 'STOP')
    {
        if (function_exists('mb_convert_encoding')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }
        $text = trim($text);

        $firstBrace = strpos($text, '{');
        $lastBrace = strrpos($text, '}');
        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $text = substr($text, $firstBrace, $lastBrace - $firstBrace + 1);
        } elseif ($firstBrace !== false) {
            $text = substr($text, $firstBrace);
        }

        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // ATTEMPT 1: Direct
        $data = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && $data !== null) return $data;

        // ATTEMPT 2: Clean newlines in strings
        $cleaned = $this->removeNewlinesInStrings($text);
        $data = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE && $data !== null) return $data;

        // ATTEMPT 3: Repair truncated
        $repaired = $this->repairTruncatedJson($cleaned);
        $data = json_decode($repaired, true);
        if (json_last_error() === JSON_ERROR_NONE && $data !== null) return $data;

        // ATTEMPT 4: Repair original
        $repaired2 = $this->repairTruncatedJson($this->removeNewlinesInStrings($text));
        $data = json_decode($repaired2, true);
        if (json_last_error() === JSON_ERROR_NONE && $data !== null) return $data;

        return null;
    }

    private function removeNewlinesInStrings($text)
    {
        $result = '';
        $inString = false;
        $escaped = false;
        $len = strlen($text);

        for ($i = 0; $i < $len; $i++) {
            $ch = $text[$i];
            $ord = ord($ch);

            if ($ord >= 128) { $result .= $ch; continue; }
            if ($escaped) { $result .= $ch; $escaped = false; continue; }
            if ($ch === '\\' && $inString) { $result .= $ch; $escaped = true; continue; }
            if ($ch === '"') { $inString = !$inString; $result .= $ch; continue; }

            if ($inString) {
                $result .= ($ord < 32) ? ' ' : $ch;
            } else {
                $result .= $ch;
            }
        }
        return $result;
    }

    private function repairTruncatedJson($text)
    {
        $text = rtrim($text);
        $stack = [];
        $inString = false;
        $escaped = false;
        $len = strlen($text);

        for ($i = 0; $i < $len; $i++) {
            $ch = $text[$i];
            $ord = ord($ch);
            if ($ord >= 128) continue;
            if ($escaped) { $escaped = false; continue; }
            if ($ch === '\\' && $inString) { $escaped = true; continue; }
            if ($ch === '"') { $inString = !$inString; continue; }
            if (!$inString) {
                if ($ch === '{') $stack[] = '}';
                elseif ($ch === '[') $stack[] = ']';
                elseif ($ch === '}' || $ch === ']') {
                    if (!empty($stack)) array_pop($stack);
                }
            }
        }

        if ($inString) {
            $text = rtrim($text, '\\');
            $text .= '"';
        }

        $text = preg_replace('/,\s*$/', '', $text);
        $lastChar = substr(rtrim($text), -1);
        if ($lastChar === ':') $text .= 'null';
        if ($lastChar === ',') $text = substr(rtrim($text), 0, -1);

        $closers = array_reverse($stack);
        foreach ($closers as $closer) {
            $text .= $closer;
        }
        return $text;
    }

    // ========================================
    // SCAN HISTORY
    // ========================================
    private function saveScanHistory($data, $fileName)
    {
        $history = $_SESSION['scan_history'] ?? [];

        $seafarerName = '-';
        if (isset($data['seafarer_info']['name'])) {
            $n = $data['seafarer_info']['name'];
            $seafarerName = is_array($n) ? ($n['value'] ?? '-') : ($n ?? '-');
        }

        $totalCerts = $data['total_certificates_found'] ?? count($data['certificates'] ?? []);
        $totalPages = $data['total_pages_scanned'] ?? count($data['certificates'] ?? []);
        $firstType = '-';
        if (!empty($data['certificates'][0]['document_type'])) {
            $dt = $data['certificates'][0]['document_type'];
            $firstType = is_array($dt) ? ($dt['value'] ?? '-') : ($dt ?? '-');
        }

        $suffix = $totalPages > 1 ? (' (' . $totalPages . ' hal)') : '';

        array_unshift($history, [
            'timestamp' => date('Y-m-d H:i:s'),
            'file_name' => $fileName,
            'document_type' => $firstType . $suffix,
            'seafarer_name' => $seafarerName,
            'total_certs' => $totalCerts,
            'total_pages' => $totalPages,
            'data' => $data,
        ]);

        $_SESSION['scan_history'] = array_slice($history, 0, 5);
    }
}
