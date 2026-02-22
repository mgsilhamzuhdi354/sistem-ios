-- ============================================================
-- 006_payment_integration.sql
-- Add MYR currency and payment fields to payroll_items
-- ============================================================

-- 1. Ensure MYR (Malaysian Ringgit) exists in currencies table
INSERT IGNORE INTO currencies (code, name, symbol, is_active) 
VALUES ('MYR', 'Malaysian Ringgit', 'RM', 1);

-- 2. Add payment method and bank detail columns to payroll_items
-- Using stored procedure to safely check if columns exist first

DELIMITER //
DROP PROCEDURE IF EXISTS add_payment_columns//
CREATE PROCEDURE add_payment_columns()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_items' AND COLUMN_NAME = 'payment_method') THEN
        ALTER TABLE payroll_items ADD COLUMN payment_method VARCHAR(30) DEFAULT 'bank_transfer' AFTER exchange_rate;
    END IF;
    
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_items' AND COLUMN_NAME = 'bank_name') THEN
        ALTER TABLE payroll_items ADD COLUMN bank_name VARCHAR(100) DEFAULT NULL AFTER payment_method;
    END IF;
    
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_items' AND COLUMN_NAME = 'bank_account') THEN
        ALTER TABLE payroll_items ADD COLUMN bank_account VARCHAR(50) DEFAULT NULL AFTER bank_name;
    END IF;
    
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payroll_items' AND COLUMN_NAME = 'bank_holder') THEN
        ALTER TABLE payroll_items ADD COLUMN bank_holder VARCHAR(100) DEFAULT NULL AFTER bank_account;
    END IF;
END//
DELIMITER ;

CALL add_payment_columns();
DROP PROCEDURE IF EXISTS add_payment_columns;
-- Note: MYR exchange rate (0.21 USD) is handled by PayrollModel::getExchangeRate() defaults
