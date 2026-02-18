<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Routes Configuration
 */

// Define base path
define('BASEPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

$routes = [];

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

// Home & Landing
$routes['GET']['/'] = 'Home::index';
$routes['GET']['/jobs'] = 'Jobs::index';
$routes['GET']['/jobs/(:num)'] = 'Jobs::detail/$1';

// Authentication
$routes['GET']['/login'] = 'Auth/Login::index';
$routes['POST']['/login'] = 'Auth/Login::authenticate';
$routes['GET']['/register'] = 'Auth/Register::index';
$routes['POST']['/register'] = 'Auth/Register::store';
$routes['GET']['/logout'] = 'Auth/Login::logout';
$routes['GET']['/forgot-password'] = 'Auth/ForgotPassword::index';
$routes['POST']['/forgot-password'] = 'Auth/ForgotPassword::send';
$routes['GET']['/reset-password/(:any)'] = 'Auth/ForgotPassword::reset/$1';
$routes['POST']['/reset-password'] = 'Auth/ForgotPassword::update';

// ============================================
// APPLICANT ROUTES (Authentication Required)
// ============================================

$routes['GET']['/applicant/dashboard'] = 'Applicant/Dashboard::index';
$routes['GET']['/applicant/profile'] = 'Applicant/Profile::index';
$routes['POST']['/applicant/profile/update'] = 'Applicant/Profile::update';

// Recruiter Selection (NEW)
$routes['GET']['/applicant/select-recruiter/(:num)'] = 'Applicant/SelectRecruiter::index/$1';
$routes['POST']['/applicant/select-recruiter/(:num)/(:num)'] = 'Applicant/SelectRecruiter::select/$1/$2';
$routes['GET']['/applicant/random-recruiter/(:num)'] = 'Applicant/SelectRecruiter::random/$1';

$routes['GET']['/applicant/applications'] = 'Applicant/Applications::index';
$routes['GET']['/applicant/applications/(:num)'] = 'Applicant/Applications::detail/$1';
$routes['GET']['/applicant/applications/apply/(:num)'] = 'Applicant/Applications::applyForm/$1';
$routes['POST']['/applicant/applications/apply/(:num)'] = 'Applicant/Applications::apply/$1';
$routes['GET']['/applicant/documents'] = 'Applicant/Documents::index';
$routes['POST']['/applicant/documents/upload'] = 'Applicant/Documents::upload';
$routes['DELETE']['/applicant/documents/(:num)'] = 'Applicant/Documents::delete/$1';
$routes['GET']['/applicant/interview'] = 'Applicant/Interview::index';
$routes['GET']['/applicant/interview/start/(:num)'] = 'Applicant/Interview::start/$1';
$routes['POST']['/applicant/interview/submit'] = 'Applicant/Interview::submit';
$routes['GET']['/applicant/notifications'] = 'Applicant/Notifications::index';

// ============================================
// ADMIN ROUTES (Admin Authentication Required)
// ============================================

$routes['GET']['/admin'] = 'Admin/Dashboard::index';
$routes['GET']['/admin/dashboard'] = 'Admin/Dashboard::index';

// Admin - Vacancy Management
$routes['GET']['/admin/vacancies'] = 'Admin/Vacancies::index';
$routes['GET']['/admin/vacancies/create'] = 'Admin/Vacancies::create';
$routes['POST']['/admin/vacancies/store'] = 'Admin/Vacancies::store';
$routes['GET']['/admin/vacancies/edit/(:num)'] = 'Admin/Vacancies::edit/$1';
$routes['POST']['/admin/vacancies/update/(:num)'] = 'Admin/Vacancies::update/$1';
$routes['POST']['/admin/vacancies/delete/(:num)'] = 'Admin/Vacancies::delete/$1';
$routes['DELETE']['/admin/vacancies/(:num)'] = 'Admin/Vacancies::delete/$1';

// Admin - Applicant Management
$routes['GET']['/admin/applicants'] = 'Admin/Applicants::index';
$routes['GET']['/admin/applicants/(:num)'] = 'Admin/Applicants::detail/$1';
$routes['POST']['/admin/applicants/status/(:num)'] = 'Admin/Applicants::updateStatus/$1';
$routes['GET']['/admin/applicants/pipeline'] = 'Admin/Applicants::pipeline';

// Admin - Document Verification
$routes['GET']['/admin/documents'] = 'Admin/Documents::index';
$routes['GET']['/admin/documents/applicant/(:num)'] = 'Admin/Documents::applicant/$1';
$routes['POST']['/admin/documents/verify/(:num)'] = 'Admin/Documents::verify/$1';
$routes['POST']['/admin/documents/bulk-verify/(:num)'] = 'Admin/Documents::bulkVerify/$1';
$routes['POST']['/admin/documents/reject/(:num)'] = 'Admin/Documents::reject/$1';

// Admin - Interview Management
$routes['GET']['/admin/interviews'] = 'Admin/Interviews::index';
$routes['GET']['/admin/interviews/questions'] = 'Admin/Interviews::questions';
$routes['GET']['/admin/interviews/questions/create'] = 'Admin/Interviews::createQuestionBank';
$routes['POST']['/admin/interviews/questions/store-bank'] = 'Admin/Interviews::storeQuestionBank';
$routes['GET']['/admin/interviews/questions/(:num)'] = 'Admin/Interviews::editQuestionBank/$1';
$routes['POST']['/admin/interviews/questions/store'] = 'Admin/Interviews::storeQuestion';
$routes['POST']['/admin/interviews/questions/delete/(:num)'] = 'Admin/Interviews::deleteQuestion/$1';
$routes['GET']['/admin/interviews/review/(:num)'] = 'Admin/Interviews::review/$1';
$routes['POST']['/admin/interviews/score/(:num)'] = 'Admin/Interviews::score/$1';
$routes['POST']['/admin/interviews/reset/(:num)'] = 'Admin/Interviews::resetInterview/$1';
$routes['POST']['/admin/interviews/assign'] = 'Admin/Interviews::assignInterview';

// Admin - Medical Management
$routes['GET']['/admin/medical'] = 'Admin/Medical::index';
$routes['POST']['/admin/medical/schedule/(:num)'] = 'Admin/Medical::schedule/$1';
$routes['POST']['/admin/medical/result/(:num)'] = 'Admin/Medical::result/$1';

// Admin - Reports
$routes['GET']['/admin/reports'] = 'Admin/Reports::index';
$routes['GET']['/admin/reports/export'] = 'Admin/Reports::export';

// Admin - User Management
$routes['GET']['/admin/users'] = 'Admin/Users::index';
$routes['POST']['/admin/users/store'] = 'Admin/Users::store';
$routes['DELETE']['/admin/users/(:num)'] = 'Admin/Users::delete/$1';

// Admin - Settings
$routes['GET']['/admin/settings'] = 'Admin/Settings::index';
$routes['POST']['/admin/settings/update'] = 'Admin/Settings::update';

// ============================================
// CREWING ROUTES (Crewing Authentication Required)
// ============================================

$routes['GET']['/crewing'] = 'Crewing/Dashboard::index';
$routes['GET']['/crewing/dashboard'] = 'Crewing/Dashboard::index';

// Crewing - Applications
$routes['GET']['/crewing/applications'] = 'Crewing/Applications::index';
$routes['GET']['/crewing/applications/(:num)'] = 'Crewing/Applications::detail/$1';
$routes['POST']['/crewing/applications/assign/(:num)'] = 'Crewing/Applications::assign/$1';
$routes['POST']['/crewing/applications/status/(:num)'] = 'Crewing/Applications::updateStatus/$1';
$routes['POST']['/crewing/applications/complete/(:num)'] = 'Crewing/Applications::markComplete/$1';

// Crewing - Pipeline
$routes['GET']['/crewing/pipeline'] = 'Crewing/Pipeline::index';
$routes['POST']['/crewing/pipeline/update-status'] = 'Crewing/Pipeline::updateStatusAjax';
$routes['POST']['/crewing/pipeline/request-status'] = 'Crewing/Pipeline::requestStatus'; // Keep potential legacy
$routes['POST']['/crewing/pipeline/request-status-change'] = 'Crewing/Pipeline::requestStatusChange'; // New endpoint
$routes['POST']['/crewing/pipeline/request-claim'] = 'Crewing/Pipeline::requestClaim';
$routes['GET']['/crewing/pipeline/detail'] = 'Crewing/Pipeline::getDetail';

// Pipeline Archive
$routes['POST']['/crewing/pipeline/archive'] = 'Crewing/Pipeline::archive';
$routes['GET']['/crewing/pipeline/archived'] = 'Crewing/Pipeline::getArchivedApplications';
$routes['POST']['/crewing/pipeline/restore'] = 'Crewing/Pipeline::restore';
$routes['POST']['/crewing/pipeline/delete-permanent'] = 'Crewing/Pipeline::permanentDelete';
$routes['POST']['/crewing/pipeline/dismiss-alert'] = 'Crewing/Pipeline::dismissAlert';


// Crewing - Team
$routes['GET']['/crewing/team'] = 'Crewing/Team::index';
$routes['POST']['/crewing/team/bulk-assign'] = 'Crewing/Team::bulkAssign';
$routes['POST']['/crewing/team/auto-assign-all'] = 'Crewing/Team::autoAssignAll';

// Crewing - Profile
$routes['GET']['/crewing/profile'] = 'Crewing/Profile::index';
$routes['POST']['/crewing/profile/update'] = 'Crewing/Profile::update';
$routes['POST']['/crewing/profile/avatar'] = 'Crewing/Profile::uploadAvatar';
$routes['POST']['/crewing/profile/photo'] = 'Crewing/Profile::uploadPhoto'; // NEW: Recruiter photo
$routes['POST']['/crewing/profile/change-password'] = 'Crewing/Profile::changePassword';

// Crewing - Manual Entry (NEW)
$routes['GET']['/crewing/manual-entry'] = 'Crewing/ManualEntry::form';
$routes['GET']['/crewing/manual-entry/vacancy/(:num)'] = 'Crewing/ManualEntry::form/$1';
$routes['POST']['/crewing/manual-entry/submit'] = 'Crewing/ManualEntry::submit';
$routes['GET']['/crewing/manual-entries'] = 'Crewing/ManualEntry::list';
$routes['GET']['/crewing/manual-entries/detail/(:num)'] = 'Crewing/ManualEntry::detail/$1';
$routes['GET']['/crewing/manual-entries/edit/(:num)'] = 'Crewing/ManualEntry::editForm/$1';
$routes['POST']['/crewing/manual-entries/update/(:num)'] = 'Crewing/ManualEntry::update/$1';
$routes['POST']['/crewing/manual-entries/delete/(:num)'] = 'Crewing/ManualEntry::deleteEntry/$1';
$routes['POST']['/crewing/manual-entries/push-erp/(:num)'] = 'Crewing/ManualEntry::pushToErp/$1';

// Crewing - Email Center
$routes['GET']['/crewing/email'] = 'Crewing/Email::index';
$routes['POST']['/crewing/email/send'] = 'Crewing/Email::send';
$routes['GET']['/crewing/email/preview'] = 'Crewing/Email::preview';
$routes['POST']['/crewing/email/delete'] = 'Crewing/Email::delete';

// Crewing - Settings
$routes['GET']['/crewing/settings'] = 'Crewing/Settings::index';
$routes['POST']['/crewing/settings/save-smtp'] = 'Crewing/Settings::saveSmtp';
$routes['POST']['/crewing/settings/test-smtp'] = 'Crewing/Settings::testSmtp';
$routes['POST']['/crewing/settings/update-profile'] = 'Crewing/Settings::updateProfile';
$routes['POST']['/crewing/settings/delete-photo'] = 'Crewing/Settings::deletePhoto';
$routes['POST']['/crewing/settings/backup-database'] = 'Crewing/Settings::backupDatabase';
$routes['POST']['/crewing/settings/import-database'] = 'Crewing/Settings::importDatabase';
$routes['POST']['/crewing/settings/delete-all-data'] = 'Crewing/Settings::deleteAllData';

// Crewing - Personal SMTP Settings (Per-User)
$routes['GET']['/crewing/settings/smtp-personal'] = 'Crewing/Settings::smtpPersonal';
$routes['POST']['/crewing/settings/smtp-personal/save'] = 'Crewing/Settings::smtpPersonalSave';
$routes['POST']['/crewing/settings/smtp-personal/test'] = 'Crewing/Settings::smtpPersonalTest';
$routes['POST']['/crewing/settings/smtp-personal/delete'] = 'Crewing/Settings::smtpPersonalDelete';
$routes['POST']['/crewing/settings/save-ui-scale'] = 'Crewing/Settings::saveUiScale';
$routes['POST']['/crewing/settings/save-language'] = 'Crewing/Settings::saveLanguage';


// Crewing - ERP Integration
$routes['POST']['/crewing/erp/send'] = 'Crewing/ErpIntegration::sendToErp';
$routes['POST']['/crewing/erp/get-ranks'] = 'Crewing/ErpIntegration::getRanks';
$routes['POST']['/crewing/erp/check-status'] = 'Crewing/ErpIntegration::checkStatus';

// Crewing - Daily Report
$routes['GET']['/crewing/daily-report'] = 'Crewing/DailyReport::index';
$routes['GET']['/crewing/daily-report/export-pdf'] = 'Crewing/DailyReport::exportPdf';
$routes['GET']['/crewing/daily-report/export-excel'] = 'Crewing/DailyReport::exportExcel';
$routes['GET']['/crewing/daily-report/export-pdf-combined'] = 'Crewing/DailyReport::exportPdfCombined';
$routes['GET']['/crewing/daily-report/export-pdf-daily'] = 'Crewing/DailyReport::exportPdfDaily';

// Crewing - Vacancies (View + Share + Create + Edit)
$routes['GET']['/crewing/vacancies'] = 'Crewing/Vacancies::index';
$routes['GET']['/crewing/vacancies/create'] = 'Crewing/Vacancies::create';
$routes['POST']['/crewing/vacancies/store'] = 'Crewing/Vacancies::store';
$routes['GET']['/crewing/vacancies/edit/(:num)'] = 'Crewing/Vacancies::edit/$1';
$routes['POST']['/crewing/vacancies/update/(:num)'] = 'Crewing/Vacancies::update/$1';
$routes['GET']['/crewing/vacancies/detail/(:num)'] = 'Crewing/Vacancies::detail/$1';
$routes['POST']['/crewing/vacancies/share/(:num)'] = 'Crewing/Vacancies::generateShareLink/$1';

// Crewing - AI Interviews
$routes['GET']['/crewing/interviews'] = 'Crewing/Interviews::index';
$routes['GET']['/crewing/interviews/review/(:num)'] = 'Crewing/Interviews::review/$1';
$routes['POST']['/crewing/interviews/assign'] = 'Crewing/Interviews::assignInterview';
$routes['POST']['/crewing/interviews/score/(:num)'] = 'Crewing/Interviews::score/$1';
$routes['POST']['/crewing/interviews/reset/(:num)'] = 'Crewing/Interviews::resetInterview/$1';

// Crewing - Question Bank Management
$routes['GET']['/crewing/interviews/questions'] = 'Crewing/Interviews::questions';
$routes['POST']['/crewing/interviews/storeQuestion'] = 'Crewing/Interviews::storeQuestion';
$routes['POST']['/crewing/interviews/deleteQuestion/(:num)'] = 'Crewing/Interviews::deleteQuestion/$1';
$routes['POST']['/crewing/interviews/deleteBank/(:num)'] = 'Crewing/Interviews::deleteBank/$1';


// ============================================
// ADMIN - CREWING MANAGEMENT
// ============================================

$routes['GET']['/admin/crewing'] = 'Admin/CrewingManagement::index';
$routes['GET']['/admin/crewing/create'] = 'Admin/CrewingManagement::create';
$routes['POST']['/admin/crewing/store'] = 'Admin/CrewingManagement::store';
$routes['GET']['/admin/crewing/edit/(:num)'] = 'Admin/CrewingManagement::edit/$1';
$routes['POST']['/admin/crewing/update/(:num)'] = 'Admin/CrewingManagement::update/$1';
$routes['POST']['/admin/crewing/delete/(:num)'] = 'Admin/CrewingManagement::delete/$1';
$routes['GET']['/admin/crewing/workload'] = 'Admin/CrewingManagement::workload';

// Admin - Email Settings
$routes['GET']['/admin/email-settings'] = 'Admin/EmailSettings::index';
$routes['POST']['/admin/email-settings/save'] = 'Admin/EmailSettings::save';
$routes['POST']['/admin/email-settings/test'] = 'Admin/EmailSettings::test';
$routes['POST']['/admin/email-settings/toggle-template'] = 'Admin/EmailSettings::toggleTemplate';

// ============================================
// LEADER ROUTES (Leader Authentication Required)
// ============================================

$routes['GET']['/leader'] = 'Leader/Dashboard::index';
$routes['GET']['/leader/dashboard'] = 'Leader/Dashboard::index';

// Leader - Requests
$routes['GET']['/leader/requests'] = 'Leader/Requests::index';
$routes['POST']['/leader/requests/approve/(:num)'] = 'Leader/Requests::approve/$1';
$routes['POST']['/leader/requests/reject/(:num)'] = 'Leader/Requests::reject/$1';

// Leader - Pipeline
$routes['GET']['/leader/pipeline'] = 'Leader/Pipeline::index';
$routes['POST']['/leader/pipeline/update-status'] = 'Leader/Pipeline::updateStatusAjax';
$routes['POST']['/leader/pipeline/transfer'] = 'Leader/Pipeline::transferResponsibility';
$routes['GET']['/leader/pipeline/transfer-history'] = 'Leader/Pipeline::getTransferHistory';
$routes['GET']['/leader/pipeline/application-detail'] = 'Leader/Pipeline::getApplicationDetail';
$routes['GET']['/leader/pipeline/crewing-staff'] = 'Leader/Pipeline::getCrewingStaffAjax';

// Leader - Team
$routes['GET']['/leader/team'] = 'Leader/Team::index';
$routes['POST']['/leader/team/transfer'] = 'Leader/Team::transfer';
$routes['GET']['/leader/team/crewing/(:num)'] = 'Leader/Team::getCrewingDetails/$1';

// Leader - Applications
$routes['GET']['/leader/applications'] = 'Leader/Applications::index';
$routes['GET']['/leader/applications/(:num)'] = 'Leader/Applications::detail/$1';

// Leader - Profile
$routes['GET']['/leader/profile'] = 'Leader/Profile::index';
$routes['POST']['/leader/profile/update'] = 'Leader/Profile::update';
$routes['POST']['/leader/profile/avatar'] = 'Leader/Profile::uploadAvatar';
$routes['POST']['/leader/profile/change-password'] = 'Leader/Profile::changePassword';

// ============================================
// MASTER ADMIN ROUTES (Master Admin Only)
// ============================================

$routes['GET']['/master-admin'] = 'MasterAdmin/Dashboard::index';
$routes['GET']['/master-admin/dashboard'] = 'MasterAdmin/Dashboard::index';

// Master Admin - User Management
$routes['GET']['/master-admin/users'] = 'MasterAdmin/Users::index';
$routes['GET']['/master-admin/users/create'] = 'MasterAdmin/Users::create';
$routes['POST']['/master-admin/users/store'] = 'MasterAdmin/Users::store';
$routes['GET']['/master-admin/users/edit/(:num)'] = 'MasterAdmin/Users::edit/$1';
$routes['POST']['/master-admin/users/update/(:num)'] = 'MasterAdmin/Users::update/$1';
$routes['POST']['/master-admin/users/delete/(:num)'] = 'MasterAdmin/Users::delete/$1';
$routes['GET']['/master-admin/users/online'] = 'MasterAdmin/Users::online';

// Master Admin - Leaders Management
$routes['GET']['/master-admin/leaders'] = 'MasterAdmin/Leaders::index';
$routes['GET']['/master-admin/leaders/create'] = 'MasterAdmin/Leaders::create';
$routes['POST']['/master-admin/leaders/store'] = 'MasterAdmin/Leaders::store';

// Master Admin - Pipeline (Full Access)
$routes['GET']['/master-admin/pipeline'] = 'MasterAdmin/Pipeline::index';
$routes['POST']['/master-admin/pipeline/update-status'] = 'MasterAdmin/Pipeline::updateStatus';
$routes['POST']['/master-admin/pipeline/assign'] = 'MasterAdmin/Pipeline::assignApplication';
$routes['POST']['/master-admin/pipeline/bulk-assign'] = 'MasterAdmin/Pipeline::bulkAssign';
$routes['GET']['/master-admin/pipeline/crewing-staff'] = 'MasterAdmin/Pipeline::getCrewingStaffAjax';
$routes['POST']['/master-admin/pipeline/transfer'] = 'MasterAdmin/Pipeline::transferResponsibility';
$routes['GET']['/master-admin/pipeline/transfer-history'] = 'MasterAdmin/Pipeline::getTransferHistory';
$routes['GET']['/master-admin/pipeline/application-detail'] = 'MasterAdmin/Pipeline::getApplicationDetail';

// Master Admin - Reports
$routes['GET']['/master-admin/reports'] = 'MasterAdmin/Reports::index';

// Master Admin - Recruitment Daily Report
$routes['GET']['/master-admin/recruitment-report'] = 'MasterAdmin/RecruitmentReport::index';
$routes['GET']['/master-admin/recruitment-report/export-pdf'] = 'MasterAdmin/RecruitmentReport::exportPdf';

// Master Admin - Settings
$routes['GET']['/master-admin/settings'] = 'MasterAdmin/Settings::index';
$routes['POST']['/master-admin/settings/update'] = 'MasterAdmin/Settings::update';

// Master Admin - Components Management
$routes['GET']['/master-admin/departments'] = 'MasterAdmin/Departments::index';
$routes['POST']['/master-admin/departments/store'] = 'MasterAdmin/Departments::store';
$routes['POST']['/master-admin/departments/update/(:num)'] = 'MasterAdmin/Departments::update/$1';
$routes['POST']['/master-admin/departments/delete/(:num)'] = 'MasterAdmin/Departments::delete/$1';

$routes['GET']['/master-admin/vessel-types'] = 'MasterAdmin/VesselTypes::index';
$routes['POST']['/master-admin/vessel-types/store'] = 'MasterAdmin/VesselTypes::store';
$routes['POST']['/master-admin/vessel-types/update/(:num)'] = 'MasterAdmin/VesselTypes::update/$1';
$routes['POST']['/master-admin/vessel-types/delete/(:num)'] = 'MasterAdmin/VesselTypes::delete/$1';


// Master Admin - Profile
$routes['GET']['/master-admin/profile'] = 'MasterAdmin/Profile::index';
$routes['POST']['/master-admin/profile/update'] = 'MasterAdmin/Profile::update';
$routes['POST']['/master-admin/profile/avatar'] = 'MasterAdmin/Profile::uploadAvatar';
$routes['POST']['/master-admin/profile/change-password'] = 'MasterAdmin/Profile::changePassword';

// Master Admin - Vacancies
$routes['GET']['/master-admin/vacancies'] = 'MasterAdmin/Vacancies::index';
$routes['POST']['/master-admin/vacancies/toggle/(:num)'] = 'MasterAdmin/Vacancies::toggleStatus/$1';

// Master Admin - Requests (Pipeline Approvals)
$routes['GET']['/master-admin/requests'] = 'MasterAdmin/Requests::index';
$routes['POST']['/master-admin/requests/approve/(:num)'] = 'MasterAdmin/Requests::approve/$1';
$routes['POST']['/master-admin/requests/reject/(:num)'] = 'MasterAdmin/Requests::reject/$1';
$routes['POST']['/master-admin/requests/approve-claim/(:num)'] = 'MasterAdmin/Requests::approveClaim/$1';
$routes['POST']['/master-admin/requests/reject-claim/(:num)'] = 'MasterAdmin/Requests::rejectClaim/$1';

// Master Admin - Archive Management
$routes['GET']['/master-admin/archive'] = 'MasterAdmin/Archive::index';
$routes['GET']['/master-admin/archive/view/(:num)'] = 'MasterAdmin/Archive::detail/$1';
$routes['POST']['/master-admin/archive/archive/(:num)'] = 'MasterAdmin/Archive::archive/$1';
$routes['POST']['/master-admin/archive/restore/(:num)'] = 'MasterAdmin/Archive::restore/$1';
$routes['POST']['/master-admin/archive/bulk-archive'] = 'MasterAdmin/Archive::bulkArchive';
$routes['GET']['/master-admin/archive/export'] = 'MasterAdmin/Archive::export';
$routes['POST']['/master-admin/archive/delete/(:num)'] = 'MasterAdmin/Archive::delete/$1';

// Master Admin - Email Settings
$routes['GET']['/master-admin/email-settings'] = 'MasterAdmin/EmailSettings::index';
$routes['GET']['/master-admin/email-settings/edit/(:num)'] = 'MasterAdmin/EmailSettings::editTemplate/$1';
$routes['POST']['/master-admin/email-settings/edit/(:num)'] = 'MasterAdmin/EmailSettings::editTemplate/$1';
$routes['POST']['/master-admin/email-settings/send-test'] = 'MasterAdmin/EmailSettings::sendTest';
$routes['GET']['/master-admin/email-settings/logs'] = 'MasterAdmin/EmailSettings::logs';
$routes['POST']['/master-admin/email-settings/save'] = 'MasterAdmin/EmailSettings::saveSettings';
$routes['POST']['/master-admin/email-settings/send-to-applicant'] = 'MasterAdmin/EmailSettings::sendToApplicant';

// Master Admin - Database Migration
$routes['GET']['/master-admin/db-migration'] = 'MasterAdmin/DbMigration::index';
$routes['GET']['/master-admin/db-migration/check-connection'] = 'MasterAdmin/DbMigration::checkConnection';
$routes['GET']['/master-admin/db-migration/run-migrations'] = 'MasterAdmin/DbMigration::runMigrations';

// Master Admin - Permissions Management
$routes['GET']['/master-admin/permissions'] = 'MasterAdmin/Permissions::index';
$routes['POST']['/master-admin/permissions/update/(:num)'] = 'MasterAdmin/Permissions::updateRolePermissions/$1';

// Master Admin - ERP Transfer
$routes['GET']['/master-admin/transfer'] = 'MasterAdmin/Transfer::index';
$routes['POST']['/master-admin/transfer/(:num)'] = 'MasterAdmin/Transfer::transfer/$1';
$routes['POST']['/master-admin/transfer/bulk'] = 'MasterAdmin/Transfer::bulkTransfer';
$routes['GET']['/master-admin/transfer/preview/(:num)'] = 'MasterAdmin/Transfer::preview/$1';

// ============================================
// CREWING PIC ROUTES - DEPRECATED (Merged to /crewing/*)
// These routes redirect to Crewing controllers for backward compatibility
// ============================================

// Redirect to Crewing Dashboard
$routes['GET']['/crewing-pic'] = 'Crewing/Dashboard::index';
$routes['GET']['/crewing-pic/dashboard'] = 'Crewing/Dashboard::index';

// Note: /crewing-pic/requests functionality merged into Leader/Requests

// ============================================
// API ROUTES (For ERP Integration)
// ============================================

// Candidate API
$routes['GET']['/api/candidates'] = 'API/ApiCandidateController::index';
$routes['GET']['/api/candidates/approved'] = 'API/ApiCandidateController::approved';
$routes['GET']['/api/candidates/(:num)'] = 'API/ApiCandidateController::show/$1';
$routes['POST']['/api/candidates/(:num)/mark-synced'] = 'API/ApiCandidateController::markSynced/$1';

// Onboarding API
$routes['GET']['/api/onboarding/(:num)'] = 'API/ApiOnboardingController::getProgress/$1';
$routes['POST']['/api/onboarding/(:num)/complete-step'] = 'API/ApiOnboardingController::completeStep/$1';

// Analytics API (Phase 4: Monitoring Center)
$routes['GET']['/api/analytics/visitors'] = 'API/ApiAnalyticsController::visitors';
$routes['GET']['/api/analytics/recruitment/funnel'] = 'API/ApiAnalyticsController::recruitmentFunnel';
$routes['GET']['/api/analytics/recruitment/timeline'] = 'API/ApiAnalyticsController::recruitmentTimeline';
$routes['GET']['/api/analytics/recent-visitors'] = 'API/ApiAnalyticsController::recentVisitors';

return $routes;
