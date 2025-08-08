@echo off
echo Starting Laravel Reverb Server...
cd payroll
php artisan reverb:start --host=0.0.0.0 --port=8080
pause
