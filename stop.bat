@echo off
setlocal
set "PROMEX_PORT=8081"

echo Encerrando o servidor local do Promex na porta %PROMEX_PORT%...
powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "$connections = @(Get-NetTCPConnection -State Listen -LocalPort %PROMEX_PORT% -ErrorAction SilentlyContinue); if ($connections.Count -eq 0) { Write-Host 'Nenhum servidor Promex esta em execucao.'; exit 0 }; $processIds = $connections | Select-Object -ExpandProperty OwningProcess -Unique; foreach ($processId in $processIds) { $process = Get-Process -Id $processId -ErrorAction SilentlyContinue; if ($process) { Stop-Process -Id $processId -Force; Write-Host ('Encerrado: ' + $process.ProcessName + ' (PID ' + $processId + ')') } }"

if "%~1"=="" pause
endlocal
