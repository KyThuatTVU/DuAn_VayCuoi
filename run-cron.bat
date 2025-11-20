@echo off
REM Script tự động chạy cron job mỗi phút
REM Chạy file này để bắt đầu

:loop
echo [%date% %time%] Checking expired orders...
php cron-update-expired-orders.php
echo.
timeout /t 60 /nobreak > nul
goto loop
