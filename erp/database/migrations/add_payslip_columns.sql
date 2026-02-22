-- =============================================
-- Payslip Dual-Currency Columns Migration
-- Adds columns for original currency display
-- =============================================

-- Add original currency tracking columns
ALTER TABLE payroll_items 
    ADD COLUMN IF NOT EXISTS original_currency VARCHAR(10) DEFAULT NULL AFTER currency_code,
    ADD COLUMN IF NOT EXISTS original_basic DECIMAL(12,2) DEFAULT 0 AFTER original_currency,
    ADD COLUMN IF NOT EXISTS original_overtime DECIMAL(12,2) DEFAULT 0 AFTER original_basic,
    ADD COLUMN IF NOT EXISTS original_leave_pay DECIMAL(12,2) DEFAULT 0 AFTER original_overtime,
    ADD COLUMN IF NOT EXISTS exchange_rate DECIMAL(12,4) DEFAULT 0 AFTER original_leave_pay,
    ADD COLUMN IF NOT EXISTS tax_rate DECIMAL(5,2) DEFAULT 2.50 AFTER tax_type,
    ADD COLUMN IF NOT EXISTS admin_bank_fee DECIMAL(12,2) DEFAULT 0 AFTER other_deductions,
    ADD COLUMN IF NOT EXISTS reimbursement DECIMAL(12,2) DEFAULT 0 AFTER admin_bank_fee,
    ADD COLUMN IF NOT EXISTS loans DECIMAL(12,2) DEFAULT 0 AFTER reimbursement,
    ADD COLUMN IF NOT EXISTS email_sent_at DATETIME DEFAULT NULL AFTER notes,
    ADD COLUMN IF NOT EXISTS email_status VARCHAR(20) DEFAULT NULL AFTER email_sent_at,
    ADD COLUMN IF NOT EXISTS email_failure_reason TEXT DEFAULT NULL AFTER email_status;
