<?php
/**
 * PT Indo Ocean - ERP System
 * Language Helper
 * 
 * Provides multi-language support (English & Indonesian)
 */

// Static cache for loaded translations
$_LANG_STRINGS = null;
$_LANG_CURRENT = null;

/**
 * Get current language from session or default
 */
function getLanguage() {
    global $_LANG_CURRENT;
    if ($_LANG_CURRENT !== null) return $_LANG_CURRENT;
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_LANG_CURRENT = $_SESSION['app_language'] ?? 'en';
    return $_LANG_CURRENT;
}

/**
 * Set the active language
 */
function setLanguage($lang) {
    global $_LANG_CURRENT, $_LANG_STRINGS;
    
    if (!in_array($lang, ['en', 'id'])) {
        $lang = 'en';
    }
    
    $_LANG_CURRENT = $lang;
    $_LANG_STRINGS = null; // Force reload
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['app_language'] = $lang;
}

/**
 * Load language strings
 */
function loadLanguageStrings() {
    global $_LANG_STRINGS;
    if ($_LANG_STRINGS !== null) return $_LANG_STRINGS;
    
    $lang = getLanguage();
    $langFile = APPPATH . 'Language/' . $lang . '.php';
    
    if (file_exists($langFile)) {
        $_LANG_STRINGS = require $langFile;
    } else {
        // Fallback to English
        $_LANG_STRINGS = require APPPATH . 'Language/en.php';
    }
    
    return $_LANG_STRINGS;
}

/**
 * Translate a key
 * 
 * @param string $key Translation key (dot notation supported: 'sidebar.dashboard')
 * @param array $replacements Optional replacements for :placeholder syntax
 * @return string Translated string or key if not found
 */
function __($key, $replacements = []) {
    $strings = loadLanguageStrings();
    
    // Support dot notation
    $parts = explode('.', $key);
    $value = $strings;
    
    foreach ($parts as $part) {
        if (is_array($value) && isset($value[$part])) {
            $value = $value[$part];
        } else {
            return $key; // Key not found, return key itself
        }
    }
    
    if (!is_string($value)) {
        return $key;
    }
    
    // Apply replacements
    foreach ($replacements as $placeholder => $replacement) {
        $value = str_replace(':' . $placeholder, $replacement, $value);
    }
    
    return $value;
}
