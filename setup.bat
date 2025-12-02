@echo off
echo ==========================================
echo   ROMS AUTOMATED SETUP (Windows)
echo ==========================================
echo.

echo [1/5] Copying .env...
if not exist .env (
    copy .env.example .env
    echo .env created.
) else (
    echo .env already exists. Skipped.
)

echo.
echo [2/5] Installing PHP Dependencies (Composer)...
call composer install

echo.
echo [3/5] Generating App Key...
call php artisan key:generate

echo.
echo [4/5] Installing Node Dependencies (NPM)...
call npm install
call npm run build

echo.
echo [5/5] Setting up Database...
echo Pastikan XAMPP/PostgreSQL sudah berjalan!
call php artisan migrate

echo.
set /p seed="Apakah ingin mengisi database dengan data awal (Seed)? (y/n): "
if /i "%seed%"=="y" (
    echo Seeding database...
    call php artisan db:seed
)

echo.
echo ==========================================
echo   SETUP COMPLETE! SIAP DIGUNAKAN 🚀
echo   Jalankan: php artisan serve
echo ==========================================
pause
