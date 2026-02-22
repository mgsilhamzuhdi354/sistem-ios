<?php
/**
 * Email Configuration for PT Indo Ocean ERP
 * SMTP via Gmail - indooceancrewservice@gmail.com
 * File ini berisi kredensial SMTP - JANGAN commit ke Git!
 */

return [
    // SMTP Server Gmail
    'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
    'smtp_port' => getenv('SMTP_PORT') ?: 587,
    'smtp_secure' => 'tls',
    
    // Kredensial Gmail - indooceancrewservice@gmail.com
    'smtp_user' => getenv('SMTP_USER') ?: 'indooceancrewservice@gmail.com',
    'smtp_pass' => getenv('SMTP_PASS') ?: 'kabpgxlpzqkznnla',
    
    // Sender Info
    'from_email' => getenv('SMTP_USER') ?: 'indooceancrewservice@gmail.com',
    'from_name'  => 'PT Indo Ocean ERP',
    
    // Debug mode - set true untuk melihat error SMTP di log
    'debug' => false,
];
