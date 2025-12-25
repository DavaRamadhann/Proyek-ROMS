# ============================================================
# SETUP WINDOWS TASK SCHEDULER UNTUK LARAVEL BROADCAST
# ============================================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  SETUP LARAVEL SCHEDULER - SOMEAH" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Deteksi PHP Path
Write-Host "[1/5] Mencari PHP..." -ForegroundColor Yellow
$phpPath = (Get-Command php -ErrorAction SilentlyContinue).Source

if (-not $phpPath) {
    Write-Host "ERROR: PHP tidak ditemukan di PATH!" -ForegroundColor Red
    Write-Host "Menggunakan path default..." -ForegroundColor Yellow
    $phpPath = "C:\php-8.4.14-nts-Win32-vs17-x64\php.exe"
    
    if (-not (Test-Path $phpPath)) {
        Write-Host "ERROR: PHP tidak ditemukan di $phpPath" -ForegroundColor Red
        pause
        exit 1
    }
}

Write-Host "   PHP: $phpPath" -ForegroundColor Green
Write-Host ""

# 2. Get Project Path
Write-Host "[2/5] Mendeteksi project path..." -ForegroundColor Yellow
$projectPath = $PSScriptRoot

if (-not (Test-Path "$projectPath\artisan")) {
    Write-Host "ERROR: File artisan tidak ditemukan di: $projectPath" -ForegroundColor Red
    pause
    exit 1
}

Write-Host "   Project: $projectPath" -ForegroundColor Green
Write-Host ""

# 3. Konfigurasi Task
Write-Host "[3/5] Konfigurasi scheduled task..." -ForegroundColor Yellow
$taskName = "Laravel Scheduler - SOMEAH"
$taskDescription = "Menjalankan Laravel scheduler setiap menit untuk memproses broadcast dan reminder otomatis"

Write-Host "   Task Name: $taskName" -ForegroundColor Green
Write-Host ""

# 4. Cek apakah task sudah ada
Write-Host "[4/5] Cek existing task..." -ForegroundColor Yellow
$existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue

if ($existingTask) {
    Write-Host "   Task sudah ada, menghapus..." -ForegroundColor Yellow
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
    Write-Host "   Task lama dihapus" -ForegroundColor Green
}
Write-Host ""

# 5. Buat Task Baru
Write-Host "[5/5] Membuat scheduled task..." -ForegroundColor Yellow

try {
    $taskAction = New-ScheduledTaskAction -Execute $phpPath -Argument "artisan schedule:run" -WorkingDirectory $projectPath
    
    $taskTrigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 1)
    
    $taskSettings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable:$false -MultipleInstances IgnoreNew
    
    $task = Register-ScheduledTask -TaskName $taskName -Description $taskDescription -Action $taskAction -Trigger $taskTrigger -Settings $taskSettings -User $env:USERNAME -RunLevel Highest -Force

    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "  SETUP BERHASIL!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Task telah dibuat dan akan berjalan:" -ForegroundColor White
    Write-Host "  - Setiap 1 menit" -ForegroundColor Cyan
    Write-Host "  - Saat Windows login" -ForegroundColor Cyan
    Write-Host "  - Bahkan saat menggunakan baterai" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "BROADCAST SEKARANG AKAN OTOMATIS TERKIRIM!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Cara Test:" -ForegroundColor Yellow
    Write-Host "1. Buat broadcast dengan jadwal 2-3 menit dari sekarang" -ForegroundColor White
    Write-Host "2. Tunggu waktu jadwal" -ForegroundColor White
    Write-Host "3. Broadcast otomatis terkirim tanpa command manual" -ForegroundColor White
    Write-Host ""
    Write-Host "Untuk melihat task: Win + R -> taskschd.msc" -ForegroundColor Cyan
    Write-Host ""
    
} catch {
    Write-Host ""
    Write-Host "ERROR: Gagal membuat scheduled task!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
    Write-Host "Solusi:" -ForegroundColor Yellow
    Write-Host "Jalankan PowerShell sebagai Administrator" -ForegroundColor White
    Write-Host ""
    pause
    exit 1
}

pause
