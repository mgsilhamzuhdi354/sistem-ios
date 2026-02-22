<?php
/**
 * Email Configuration for PT Indo Ocean ERP
 * SMTP via Domainesia - ios@indooceancrew.co.id
 * File ini berisi kredensial SMTP - JANGAN commit ke Git!
 */

return [
    // SMTP Server Domainesia
    'smtp_host' => 'indooceancrew.co.id',
    'smtp_port' => 465,
    'smtp_secure' => 'ssl',
    
    // Kredensial Domainesia
    'smtp_user' => 'ios@indooceancrew.co.id',
    'smtp_pass' => 'Oceancrew1!',
    
    // Sender Info
    'from_email' => 'ios@indooceancrew.co.id',
    'from_name'  => 'PT Indo Oceancrew Services',
    
    // Debug mode - set true untuk melihat error SMTP di log
    'debug' => false,
];
