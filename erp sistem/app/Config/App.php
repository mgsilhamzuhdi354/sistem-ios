<?php
/**
 * PT Indo Ocean - ERP System
 * Application Configuration
 */

return [
    'baseURL' => 'http://localhost/PT_indoocean/erp%20sistem/public/',
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
