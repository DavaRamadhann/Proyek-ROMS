@echo off
echo Installing Laravel Reverb...
call composer require laravel/reverb

echo Installing Reverb Configuration...
call php artisan reverb:install

echo Starting Reverb Server...
echo Please keep this window open!
call php artisan reverb:start
