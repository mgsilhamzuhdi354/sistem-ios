<?php
/**
 * PT Indo Ocean - ERP System
 * Routes Configuration
 */

// Define base path
$basePath = 'erp sistem';

return [
    // Default route
    'default' => 'Dashboard',
    
    // Routes mapping
    'routes' => [
        // Dashboard
        '/' => 'Dashboard::index',
        'dashboard' => 'Dashboard::index',
        
        // Contracts
        'contracts' => 'Contract::index',
        'contracts/create' => 'Contract::create',
        'contracts/store' => 'Contract::store',
        'contracts/(:num)' => 'Contract::show/$1',
        'contracts/edit/(:num)' => 'Contract::edit/$1',
        'contracts/update/(:num)' => 'Contract::update/$1',
        'contracts/delete/(:num)' => 'Contract::delete/$1',
        'contracts/renew/(:num)' => 'Contract::renew/$1',
        'contracts/terminate/(:num)' => 'Contract::terminate/$1',
        'contracts/approve/(:num)' => 'Contract::approve/$1',
        'contracts/reject/(:num)' => 'Contract::reject/$1',
        
        // Vessels
        'vessels' => 'Vessel::index',
        'vessels/create' => 'Vessel::create',
        'vessels/store' => 'Vessel::store',
        'vessels/(:num)' => 'Vessel::show/$1',
        'vessels/edit/(:num)' => 'Vessel::edit/$1',
        'vessels/update/(:num)' => 'Vessel::update/$1',
        'vessels/delete/(:num)' => 'Vessel::delete/$1',
        'vessels/crew/(:num)' => 'Vessel::crewList/$1',
        
        // Clients
        'clients' => 'Client::index',
        'clients/create' => 'Client::create',
        'clients/store' => 'Client::store',
        'clients/(:num)' => 'Client::show/$1',
        'clients/edit/(:num)' => 'Client::edit/$1',
        'clients/update/(:num)' => 'Client::update/$1',
        'clients/delete/(:num)' => 'Client::delete/$1',
        
        // Payroll
        'payroll' => 'Payroll::index',
        'payroll/process' => 'Payroll::process',
        'payroll/(:num)' => 'Payroll::show/$1',
        'payroll/export/(:num)' => 'Payroll::export/$1',
        
        // Reports
        'reports' => 'Report::index',
        'reports/contracts' => 'Report::contracts',
        'reports/payroll' => 'Report::payroll',
        'reports/crew' => 'Report::crew',
        'reports/export/(:any)' => 'Report::export/$1',
        
        // Settings
        'settings' => 'Settings::index',
        'settings/save' => 'Settings::save',
        'settings/init' => 'Settings::init',
        'settings/delete-data' => 'Settings::deleteData',
        'settings/export' => 'Settings::exportData',
        'settings/import' => 'Settings::importData',
        
        // Notifications
        'notifications' => 'Notification::index',
        'notifications/unread' => 'Notification::getUnread',
        'notifications/mark-read/(:num)' => 'Notification::markRead/$1',
        'notifications/mark-all-read' => 'Notification::markAllRead',
        'notifications/generate' => 'Notification::generate',
        
        // Contracts extended
        'contracts/expiring' => 'Contract::expiring',
        
        // API endpoints
        'api/contracts' => 'Api\Contract::list',
        'api/contracts/(:num)' => 'Api\Contract::get/$1',
        'api/crew/search' => 'Api\Crew::search',
        'api/dashboard/stats' => 'Api\Dashboard::stats',
        'api/notifications/unread' => 'Notification::getUnread',
    ],
];
