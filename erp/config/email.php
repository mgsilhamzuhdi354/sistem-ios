<?php
/**
 * Email Configuration for PT Indo Ocean ERP
 * File ini berisi kredensial SMTP - JANGAN commit ke Git!
 */

return [
    // SMTP Server Gmail
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    
    // Kredensial Gmail - indooceancrewservice@gmail.com
    'smtp_user' => 'indooceancrewservice@gmail.com',   // Email SMTP
    'smtp_pass' => 'kabpgxlpzqkznnla',                  // App Password Gmail (tanpa spasi)
    
    // Sender Info
    'from_email' => 'indooceancrewservice@gmail.com',  // Email pengirim
    'from_name'  => 'PT Indo Ocean ERP',
    
    // Debug mode - set true untuk melihat error SMTP
    'debug' => true,
];
