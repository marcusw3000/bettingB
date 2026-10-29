@echo off
setlocal
cd /d "%~dp0"

set "PROMEX_PHP_DIR=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe"
if not exist "%PROMEX_PHP_DIR%\php.exe" (
  echo PHP 8.3 was not found. Reinstall PHP.PHP.8.3 with winget.
  pause
  exit /b 1
)
if not exist ".env.supabase" (
  echo Missing .env.supabase.
  pause
  exit /b 1
)

for /f "usebackq tokens=1,* delims==" %%A in (".env.supabase") do set "%%A=%%B"
if not defined SUPABASE_DB_PASSWORD (
  echo SUPABASE_DB_PASSWORD is empty in .env.supabase.
  pause
  exit /b 1
)

set "APP_ENV=local"
set "APP_DEBUG=false"
set "APP_LOCALE=pt-br"
set "APP_URL=%SUPABASE_APP_URL%"
set "DB_CONNECTION=pgsql"
set "DB_HOST=%SUPABASE_DB_HOST%"
set "DB_PORT=%SUPABASE_DB_PORT%"
set "DB_DATABASE=%SUPABASE_DB_DATABASE%"
set "DB_USERNAME=%SUPABASE_DB_USERNAME%"
set "DB_PASSWORD=%SUPABASE_DB_PASSWORD%"
set "DB_SCHEMA=promex"
set "DB_PREFIX=w_"
set "DB_SSLMODE=require"
set "DB_PERSISTENT=true"
rem Keep request-local state off the remote Supabase database.
set "CACHE_DRIVER=file"
set "SESSION_DRIVER=file"
set "SETTINGS_CACHE=true"
set "SETTINGS_CACHE_TTL=3600"
set "LEGACY_GAMES_ENABLED=false"
set "STRIPE_ENABLED=false"
set "PAYPAL_ENABLED=false"

rem Start a background request first: it primes the local file caches for
rem settings and the progressive game catalogue before the browser opens.
start "PROMEX cache warmup" /b powershell.exe -NoProfile -WindowStyle Hidden -Command "$limit=(Get-Date).AddSeconds(45); $opened=$false; do { try { Invoke-WebRequest -UseBasicParsing 'http://127.0.0.1:8081/catalog.json?category=hot&page=1&per_page=15' -TimeoutSec 10 | Out-Null; Start-Process 'http://127.0.0.1:8081/'; $opened=$true; break } catch { Start-Sleep -Milliseconds 500 } } while ((Get-Date) -lt $limit); if (-not $opened) { Start-Process 'http://127.0.0.1:8081/' }"
"%PROMEX_PHP_DIR%\php.exe" -S 127.0.0.1:8081 router.local.php
