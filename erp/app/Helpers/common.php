<?php
/**
 * PT Indo Ocean - ERP System
 * Common Helper Functions
 */

/**
 * Get base URL
 */
function base_url($path = '')
{
    return BASE_URL . ltrim($path, '/');
}

/**
 * Redirect to URL
 */
function redirect($url)
{
    header('Location: ' . base_url($url));
    exit;
}

/**
 * Set flash message
 */
function flash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function get_flash()
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Check if user is authenticated
 */
function is_auth()
{
    return isset($_SESSION['user_id']);
}

/**
 * Get current user
 */
function current_user()
{
    return $_SESSION['user'] ?? null;
}

/**
 * Escape HTML
 */
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Get input value
 */
function input($key, $default = null)
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

/**
 * Check if POST request
 */
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Format currency
 */
function money($amount, $currency = 'IDR')
{
    if ($currency === 'USD') {
        return '$' . number_format($amount, 2);
    }
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date Indonesian style
 */
function tgl($date, $format = 'd M Y')
{
    if (!$date)
        return '-';
    return date($format, strtotime($date));
}
