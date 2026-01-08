@echo off
REM =====================================================
REM PT Indo Ocean - Auto Payroll Cron Script
REM Run this via Windows Task Scheduler on payday
REM =====================================================

REM Set your cron key (must match the key in CronController.php)
SET CRON_KEY=indoocean_cron_2024

REM ERP System URL
SET BASE_URL=http://localhost/PT_indoocean/erp%%20sistem

REM Run auto-payroll
echo Running Auto-Payroll...
curl -s "%BASE_URL%/cron/auto-payroll?key=%CRON_KEY%"

REM Run contract alerts
echo.
echo Running Contract Alerts...
curl -s "%BASE_URL%/cron/contract-alerts?key=%CRON_KEY%"

echo.
echo Done!
pause
