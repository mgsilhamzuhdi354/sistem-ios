<?php
/**
 * Email Configuration for PT Indo Ocean ERP
 * SMTP via ios@indooceancrew.co.id (SSL Port 465)
 * File ini berisi kredensial SMTP - JANGAN commit ke Git!
 */

return [
    // SMTP Server - indooceancrew.co.id
    'smtp_host' => getenv('SMTP_HOST') ?: 'mail.indooceancrew.co.id',
    'smtp_port' => getenv('SMTP_PORT') ?: 465,
    'smtp_secure' => 'ssl',
    
    // Kredensial - ios@indooceancrew.co.id
    'smtp_user' => getenv('SMTP_USER') ?: 'ios@indooceancrew.co.id',
    'smtp_pass' => getenv('SMTP_PASS') ?: '(Oceancrew1!)',
    
    // Sender Info
    'from_email' => getenv('SMTP_USER') ?: 'ios@indooceancrew.co.id',
    'from_name'  => 'PT Indo Ocean ERP',
    
    // Debug mode - set true untuk melihat error SMTP di log
    'debug' => false,
];
