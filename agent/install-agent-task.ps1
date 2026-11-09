[CmdletBinding()]
param(
    [string]$ConfigPath = (Join-Path $PSScriptRoot 'config.json'),
    [string]$TaskName = 'NETMONITOR QoS Agent',
    [int]$IntervalMinutes = 5
)

$agentPath = Join-Path $PSScriptRoot 'qos-agent.ps1'

if (-not (Test-Path -LiteralPath $agentPath)) {
    throw ('Agent tidak ditemukan: {0}' -f $agentPath)
}

if (-not (Test-Path -LiteralPath $ConfigPath)) {
    throw ('Config tidak ditemukan: {0}' -f $ConfigPath)
}

$action = New-ScheduledTaskAction `
    -Execute 'powershell.exe' `
    -Argument ('-NoProfile -ExecutionPolicy Bypass -File "{0}" -ConfigPath "{1}" -Once' -f $agentPath, $ConfigPath)

$trigger = New-ScheduledTaskTrigger `
    -Once `
    -At (Get-Date).AddMinutes(1) `
    -RepetitionInterval (New-TimeSpan -Minutes 1) `
    -RepetitionDuration (New-TimeSpan -Days 3650)

$settings = New-ScheduledTaskSettingsSet `
    -StartWhenAvailable `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -MultipleInstances IgnoreNew `
    -ExecutionTimeLimit (New-TimeSpan -Minutes 10)

$principal = New-ScheduledTaskPrincipal `
    -UserId 'SYSTEM' `
    -LogonType ServiceAccount `
    -RunLevel Highest

Register-ScheduledTask `
    -TaskName $TaskName `
    -Action $action `
    -Trigger $trigger `
    -Settings $settings `
    -Principal $principal `
    -Description 'Menjalankan agent monitoring QoS NETMONITOR.' `
    -Force | Out-Null

Write-Host ('Task "{0}" berhasil dibuat. Agent memeriksa perintah setiap 1 menit; pengukuran mengikuti interval {1} menit.' -f $TaskName, $IntervalMinutes)
