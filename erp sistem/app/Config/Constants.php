<?php
/**
 * PT Indo Ocean - ERP System
 * Constants Configuration
 */

// =====================================================
// CONTRACT STATUS
// =====================================================
define('CONTRACT_STATUS_DRAFT', 'draft');
define('CONTRACT_STATUS_PENDING', 'pending_approval');
define('CONTRACT_STATUS_ACTIVE', 'active');
define('CONTRACT_STATUS_ONBOARD', 'onboard');
define('CONTRACT_STATUS_COMPLETED', 'completed');
define('CONTRACT_STATUS_TERMINATED', 'terminated');
define('CONTRACT_STATUS_CANCELLED', 'cancelled');

define('CONTRACT_STATUSES', [
    CONTRACT_STATUS_DRAFT => 'Draft',
    CONTRACT_STATUS_PENDING => 'Pending Approval',
    CONTRACT_STATUS_ACTIVE => 'Active',
    CONTRACT_STATUS_ONBOARD => 'Onboard',
    CONTRACT_STATUS_COMPLETED => 'Completed',
    CONTRACT_STATUS_TERMINATED => 'Terminated',
    CONTRACT_STATUS_CANCELLED => 'Cancelled',
]);

define('CONTRACT_STATUS_COLORS', [
    CONTRACT_STATUS_DRAFT => 'secondary',
    CONTRACT_STATUS_PENDING => 'warning',
    CONTRACT_STATUS_ACTIVE => 'success',
    CONTRACT_STATUS_ONBOARD => 'info',
    CONTRACT_STATUS_COMPLETED => 'primary',
    CONTRACT_STATUS_TERMINATED => 'danger',
    CONTRACT_STATUS_CANCELLED => 'dark',
]);

// =====================================================
// CONTRACT TYPE
// =====================================================
define('CONTRACT_TYPE_TEMPORARY', 'temporary');
define('CONTRACT_TYPE_FIXED', 'fixed');
define('CONTRACT_TYPE_PERMANENT', 'permanent');

define('CONTRACT_TYPES', [
    CONTRACT_TYPE_TEMPORARY => 'Temporary / Voyage Contract',
    CONTRACT_TYPE_FIXED => 'Fixed Term Contract',
    CONTRACT_TYPE_PERMANENT => 'Permanent (Office Staff)',
]);

// =====================================================
// TAX TYPES
// =====================================================
define('TAX_TYPE_PPH21', 'pph21');
define('TAX_TYPE_PPH21_NON_NPWP', 'pph21_non_npwp');
define('TAX_TYPE_EXEMPT', 'exempt');
define('TAX_TYPE_FOREIGN', 'foreign');

define('TAX_TYPES', [
    TAX_TYPE_PPH21 => 'PPh 21 (With NPWP)',
    TAX_TYPE_PPH21_NON_NPWP => 'PPh 21 (Non-NPWP) +20%',
    TAX_TYPE_EXEMPT => 'Tax Exempt',
    TAX_TYPE_FOREIGN => 'Foreign Tax Treaty',
]);

define('TAX_RATES', [
    TAX_TYPE_PPH21 => 5.00,
    TAX_TYPE_PPH21_NON_NPWP => 6.00,
    TAX_TYPE_EXEMPT => 0,
    TAX_TYPE_FOREIGN => 0,
]);

// =====================================================
// DEDUCTION TYPES
// =====================================================
define('DEDUCTION_INSURANCE', 'insurance');
define('DEDUCTION_MEDICAL', 'medical');
define('DEDUCTION_TRAINING', 'training');
define('DEDUCTION_ADVANCE', 'advance');
define('DEDUCTION_LOAN', 'loan');
define('DEDUCTION_OTHER', 'other');

define('DEDUCTION_TYPES', [
    DEDUCTION_INSURANCE => 'Insurance',
    DEDUCTION_MEDICAL => 'Medical Cost',
    DEDUCTION_TRAINING => 'Training Cost',
    DEDUCTION_ADVANCE => 'Advance Payment',
    DEDUCTION_LOAN => 'Loan Repayment',
    DEDUCTION_OTHER => 'Other',
]);

// =====================================================
// APPROVAL LEVELS
// =====================================================
define('APPROVAL_CREWING', 'crewing');
define('APPROVAL_HR', 'hr');
define('APPROVAL_DIRECTOR', 'director');

define('APPROVAL_LEVELS', [
    APPROVAL_CREWING => 'Crewing Officer',
    APPROVAL_HR => 'HR Manager',
    APPROVAL_DIRECTOR => 'Director',
]);

// =====================================================
// ALERT THRESHOLDS (days before contract expiry)
// =====================================================
define('ALERT_DANGER_DAYS', 7);
define('ALERT_WARNING_DAYS', 30);
define('ALERT_INFO_DAYS', 60);

// =====================================================
// PAYROLL STATUS
// =====================================================
define('PAYROLL_DRAFT', 'draft');
define('PAYROLL_PROCESSING', 'processing');
define('PAYROLL_COMPLETED', 'completed');
define('PAYROLL_LOCKED', 'locked');

define('PAYROLL_STATUSES', [
    PAYROLL_DRAFT => 'Draft',
    PAYROLL_PROCESSING => 'Processing',
    PAYROLL_COMPLETED => 'Completed',
    PAYROLL_LOCKED => 'Locked',
]);

// =====================================================
// USER ROLES (for ERP access)
// =====================================================
define('ROLE_SUPER_ADMIN', 1);
define('ROLE_HR_MANAGER', 2);
define('ROLE_CREWING_OFFICER', 3);
define('ROLE_FINANCE', 4);
define('ROLE_VIEWER', 5);

define('USER_ROLES', [
    ROLE_SUPER_ADMIN => 'Super Admin',
    ROLE_HR_MANAGER => 'HR Manager',
    ROLE_CREWING_OFFICER => 'Crewing Officer',
    ROLE_FINANCE => 'Finance',
    ROLE_VIEWER => 'Viewer',
]);

// =====================================================
// DOCUMENT LANGUAGES
// =====================================================
define('LANG_ID', 'id');
define('LANG_EN', 'en');

define('DOCUMENT_LANGUAGES', [
    LANG_ID => 'Bahasa Indonesia',
    LANG_EN => 'English',
]);

// =====================================================
// DATE FORMATS
// =====================================================
define('DATE_FORMAT_DISPLAY', 'd M Y');
define('DATE_FORMAT_DB', 'Y-m-d');
define('DATETIME_FORMAT_DISPLAY', 'd M Y H:i');

// =====================================================
// PAGINATION
// =====================================================
define('ITEMS_PER_PAGE', 20);

// =====================================================
// FILE UPLOAD
// =====================================================
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);
