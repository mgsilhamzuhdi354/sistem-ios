# Script untuk update PHP upload limit di Laragon
# Jalankan dari Laragon Terminal dengan: powershell -ExecutionPolicy Bypass -File update_php_upload_limit.ps1

Write-Host "=== Update PHP Upload Limit to 10MB ===" -ForegroundColor Cyan

# Cari php.ini di Laragon
$phpIniPath = "C:\laragon\bin\php\php-8.1.10\php.ini"  # Sesuaikan versi PHP kamu

# Cek apakah file ada
if (-Not (Test-Path $phpIniPath)) {
    # Coba cari versi PHP lain
    $phpDirs = Get-ChildItem "C:\laragon\bin\php\" -Directory | Sort-Object Name -Descending
    if ($phpDirs) {
        $phpIniPath = "$($phpDirs[0].FullName)\php.ini"
        Write-Host "PHP version detected: $($phpDirs[0].Name)" -ForegroundColor Yellow
    } else {
        Write-Host "ERROR: php.ini not found!" -ForegroundColor Red
        exit 1
    }
}

Write-Host "Using php.ini: $phpIniPath" -ForegroundColor Green

# Backup php.ini
$backupPath = "$phpIniPath.backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
Copy-Item $phpIniPath $backupPath
Write-Host "Backup created: $backupPath" -ForegroundColor Green

# Baca isi file
$content = Get-Content $phpIniPath

# Update upload_max_filesize
$content = $content -replace '^upload_max_filesize\s*=\s*\d+M', 'upload_max_filesize = 10M'

# Update post_max_size
$content = $content -replace '^post_max_size\s*=\s*\d+M', 'post_max_size = 10M'

# Simpan perubahan
$content | Set-Content $phpIniPath -Encoding UTF8

Write-Host "`nPHP.ini updated successfully!" -ForegroundColor Green
Write-Host "- upload_max_filesize = 10M" -ForegroundColor Cyan
Write-Host "- post_max_size = 10M" -ForegroundColor Cyan

# Restart Apache
Write-Host "`nRestarting Apache..." -ForegroundColor Yellow
$apachePath = "C:\laragon\bin\apache\httpd-2.4.54-win64-VS16\bin\httpd.exe"

# Coba cari Apache
if (-Not (Test-Path $apachePath)) {
    $apacheDirs = Get-ChildItem "C:\laragon\bin\apache\" -Directory | Sort-Object Name -Descending
    if ($apacheDirs) {
        $apachePath = "$($apacheDirs[0].FullName)\bin\httpd.exe"
    }
}

if (Test-Path $apachePath) {
    # Stop Apache
    Stop-Process -Name "httpd" -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
    
    # Start Apache
    Start-Process $apachePath -WindowStyle Hidden
    Write-Host "Apache restarted!" -ForegroundColor Green
} else {
    Write-Host "Could not find Apache. Please restart manually via Laragon." -ForegroundColor Yellow
}

Write-Host "`n=== DONE! ===" -ForegroundColor Green
Write-Host "Upload limit is now 10MB. You can verify by checking phpinfo()." -ForegroundColor Cyan
