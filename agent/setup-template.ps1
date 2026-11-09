# NETMONITOR Agent Setup
# Sekolah: __SCHOOL_NAME__
# File ini berisi enrollment code sekali pakai. Jangan dibagikan.

[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'

try {
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
} catch {}

$serverUrl = '__SERVER_URL__'.TrimEnd('/')
$enrollmentToken = '__ENROLLMENT_TOKEN__'

$identity = [Security.Principal.WindowsIdentity]::GetCurrent()
$principal = New-Object Security.Principal.WindowsPrincipal($identity)
$isAdmin = $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    try {
        $arguments = @(
            '-NoProfile',
            '-ExecutionPolicy', 'Bypass',
            '-File', ('"{0}"' -f $PSCommandPath)
        )

        Start-Process -FilePath 'powershell.exe' -Verb RunAs -ArgumentList $arguments
        exit
    } catch {
        Write-Host 'Installer harus dijalankan sebagai Administrator.' -ForegroundColor Red
        Write-Host 'Klik kanan file installer, lalu pilih Run with PowerShell sebagai Administrator.'
        Read-Host 'Tekan Enter untuk keluar'
        exit 1
    }
}

$installDir = Join-Path $env:ProgramData 'NETMONITOR\Agent'
$configPath = Join-Path $installDir 'config.json'
$statePath = Join-Path $installDir 'state.json'
$agentPath = Join-Path $installDir 'qos-agent.ps1'
$taskInstallerPath = Join-Path $installDir 'install-agent-task.ps1'

New-Item -ItemType Directory -Force -Path $installDir | Out-Null

Write-Host 'Mengunduh agent NETMONITOR...' -ForegroundColor Cyan

Invoke-WebRequest -Uri ($serverUrl + '/agent/files/qos-agent.ps1') -OutFile $agentPath -UseBasicParsing
Invoke-WebRequest -Uri ($serverUrl + '/agent/files/install-agent-task.ps1') -OutFile $taskInstallerPath -UseBasicParsing

$speedtestSource = Join-Path $PSScriptRoot 'speedtest.exe'
$speedtestTarget = Join-Path $installDir 'speedtest.exe'

if (Test-Path -LiteralPath $speedtestSource) {
    Copy-Item -LiteralPath $speedtestSource -Destination $speedtestTarget -Force
    Write-Host 'Speedtest CLI disalin ke folder agent.' -ForegroundColor Green
} elseif (Test-Path -LiteralPath $speedtestTarget) {
    Write-Host 'Speedtest CLI sudah tersedia di folder agent.' -ForegroundColor Green
} else {
    Write-Host 'Mengunduh Speedtest CLI dari server...' -ForegroundColor Cyan

    try {
        Invoke-WebRequest -Uri ($serverUrl + '/agent/files/speedtest.exe') -OutFile $speedtestTarget -UseBasicParsing
        Write-Host 'Speedtest CLI berhasil diunduh.' -ForegroundColor Green
    } catch {
        Remove-Item -LiteralPath $speedtestTarget -Force -ErrorAction SilentlyContinue
        Write-Host 'Speedtest CLI gagal diunduh. Agent tetap berjalan untuk ping; letakkan speedtest.exe di folder agent untuk data throughput.' -ForegroundColor Yellow
    }
}

Remove-Item -LiteralPath $statePath -Force -ErrorAction SilentlyContinue

$config = [ordered]@{
    ServerUrl = $serverUrl
    AgentToken = ''
    EnrollmentToken = $enrollmentToken
    IntervalSeconds = __INTERVAL_SECONDS__
    PingCount = 4
    PingTarget = ''
    SpeedtestPath = ''
    SpeedtestServerId = ''
    SkipSpeedtest = $false
    RequestTimeoutSeconds = 90
    BufferPath = 'buffer.jsonl'
    LogPath = 'agent.log'
    MaxBufferItems = 200
    StatePath = 'state.json'
}

$config | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath $configPath -Encoding UTF8

Stop-ScheduledTask -TaskName 'NETMONITOR QoS Agent' -ErrorAction SilentlyContinue

Write-Host 'Melakukan enrollment dan pengukuran pertama...' -ForegroundColor Cyan

& powershell.exe -NoProfile -ExecutionPolicy Bypass -File $agentPath -ConfigPath $configPath -Once -VerboseOutput

if ($LASTEXITCODE -ne 0) {
    throw 'Agent gagal melakukan enrollment. Periksa koneksi internet dan URL server.'
}

Write-Host ('Memasang pengecekan otomatis setiap 1 menit (pengukuran setiap {0} menit)...' -f __INTERVAL_MINUTES__) -ForegroundColor Cyan

& powershell.exe -NoProfile -ExecutionPolicy Bypass -File $taskInstallerPath -ConfigPath $configPath -IntervalMinutes __INTERVAL_MINUTES__

if ($LASTEXITCODE -ne 0) {
    throw 'Gagal memasang Task Scheduler agent.'
}

Write-Host ''
Write-Host 'Agent NETMONITOR berhasil dipasang.' -ForegroundColor Green
Write-Host ('Folder agent : {0}' -f $installDir)
Write-Host ('Log agent    : {0}' -f (Join-Path $installDir 'agent.log'))
Write-Host 'Data akan muncul di dashboard setelah pengukuran pertama selesai.'
Write-Host ''
Read-Host 'Tekan Enter untuk menutup'
