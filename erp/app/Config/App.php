<?php
/**
 * PT Indo Ocean - ERP System
 * Application Configuration (Laragon)
 */

// Dynamic BASE_URL for Laragon
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseURL = $protocol . '://' . $host . '/PT_indoocean/erp/';

return [
    'baseURL' => $baseURL,
    'indexPage' => '',
    'uriProtocol' => 'REQUEST_URI',
    'defaultLocale' => 'id',
    'negotiateLocale' => false,
    'supportedLocales' => ['id', 'en'],
    'appTimezone' => 'Asia/Jakarta',
    'charset' => 'UTF-8',
    'forceGlobalSecureRequests' => false,
    'sessionDriver' => 'CodeIgniter\Session\Handlers\FileHandler',
    'sessionCookieName' => 'erp_session',
    'sessionExpiration' => 7200,
    'sessionSavePath' => WRITEPATH . 'session',
    'sessionMatchIP' => false,
    'sessionTimeToUpdate' => 300,
    'sessionRegenerateDestroy' => false,
    'CSPEnabled' => false,
];
