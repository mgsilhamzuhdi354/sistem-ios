<?php
/**
 * Translation Helper Function
 * Loads and returns translated strings based on user's language preference
 */

if (!function_exists('t')) {
    /**
     * Get translated string
     * 
     * @param string $key Translation key (e.g. 'nav.dashboard')
     * @param string|null $fallback Fallback text if key not found
     * @return string Translated string or fallback
     */
    function t($key, $fallback = null) {
        static $lang = null;
        static $currentLang = null;
        
        // Get user's language from session
        $userLang = $_SESSION['user_language'] ?? $_SESSION['language'] ?? 'id';
        
        // Reload language file if language changed
        if ($lang === null || $currentLang !== $userLang) {
            $langFile = APPPATH . "Languages/{$userLang}.php";
            
            // Fallback to Indonesian if file doesn't exist
            if (!file_exists($langFile)) {
                $langFile = APPPATH . "Languages/id.php";
                $userLang = 'id';
            }
            
            $lang = include $langFile;
            $currentLang = $userLang;
        }
        
        // Return translated string, fallback, or key itself
        return $lang[$key] ?? $fallback ?? $key;
    }
}

if (!function_exists('setLanguage')) {
    /**
     * Set user's language preference
     * 
     * @param string $langCode Language code ('id' or 'en')
     * @return void
     */
    function setLanguage($langCode) {
        $_SESSION['user_language'] = $langCode;
        $_SESSION['language'] = $langCode;
        
        // Update in database if user is logged in
        if (!empty($_SESSION['user_id'])) {
            global $db;
            if ($db) {
                $stmt = $db->prepare("UPDATE users SET language = ? WHERE id = ?");
                $stmt->bind_param('si', $langCode, $_SESSION['user_id']);
                $stmt->execute();
            }
        }
    }
}

if (!function_exists('getCurrentLanguage')) {
    /**
     * Get current language code
     * 
     * @return string Language code ('id' or 'en')
     */
    function getCurrentLanguage() {
        return $_SESSION['user_language'] ?? $_SESSION['language'] ?? 'id';
    }
}
