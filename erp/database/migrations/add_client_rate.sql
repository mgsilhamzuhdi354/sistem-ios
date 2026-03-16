-- Migration: Add client_rate to contract_salaries for profit calculation
-- Run this SQL in phpMyAdmin on database 'erp_db'

ALTER TABLE contract_salaries 
ADD COLUMN client_rate DECIMAL(12,2) DEFAULT NULL 
COMMENT 'Rate paid by client to company for this crew (in same currency as salary)' 
AFTER exchange_rate;

-- client_rate = what client pays to company
-- total_monthly = what company pays to crew
-- profit = client_rate - total_monthly
