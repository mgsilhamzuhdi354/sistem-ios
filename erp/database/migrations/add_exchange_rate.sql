-- Migration: Add exchange_rate to contract_salaries
-- Run this SQL in phpMyAdmin or MySQL CLI

ALTER TABLE contract_salaries 
ADD COLUMN exchange_rate DECIMAL(15,6) DEFAULT NULL 
COMMENT 'Exchange rate to USD, set by owner. NULL = use system default' 
AFTER currency_id;

-- Example: If currency is IDR and rate is 15800, it means 1 USD = 15800 IDR
-- To convert IDR to USD: amount_idr / exchange_rate = amount_usd
