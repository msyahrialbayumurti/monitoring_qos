[CmdletBinding()]
param(
    [string]$ConfigPath = (Join-Path $PSScriptRoot 'config.json'),
    [switch]$Once,
    [switch]$SkipSpeedtest,
    [switch]$VerboseOutput
)

$ErrorActionPreference = 'Stop'

try {
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
} catch {}

$script:AgentVersion = '1.0.0'
$script:Config = $null

$defaults = [ordered]@{
    ServerUrl = 'https://your-app.up.railway.app'
    AgentToken = ''
    EnrollmentToken = ''
    IntervalSeconds = 300
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

function Resolve-AgentPath([string]$Path) {
    if ([string]::IsNullOrWhiteSpace($Path)) {
        return $Path
    }

    if ([System.IO.Path]::IsPathRooted($Path)) {
        return $Path
    }

    return (Join-Path $PSScriptRoot $Path)
}

function Get-AgentState {
    try {
        $statePath = [string]$script:Config.StatePath

        if ([string]::IsNullOrWhiteSpace($statePath) -or -not (Test-Path -LiteralPath $statePath)) {
            return $null
        }

        $state = Get-Content -LiteralPath $statePath -Raw | ConvertFrom-Json

        if ($state -and -not [string]::IsNullOrWhiteSpace([string]$state.AgentToken)) {
            return $state
        }
    } catch {
        Write-AgentLog ('Gagal membaca state agent: {0}' -f $_.Exception.Message)
    }

    return $null
}

function Save-AgentState([string]$Token) {
    $state = [ordered]@{
        AgentToken = $Token
        EnrolledAt = (Get-Date).ToString('o')
    }

    $state | ConvertTo-Json | Set-Content -LiteralPath $script:Config.StatePath -Encoding UTF8
}

function Invoke-AgentEnrollment {
    $enrollmentToken = [string]$script:Config.EnrollmentToken

    if ([string]::IsNullOrWhiteSpace($enrollmentToken)) {
        throw 'AgentToken kosong dan EnrollmentToken tidak tersedia pada config agent.'
    }

    $body = [ordered]@{
        enrollment_token = $enrollmentToken
        agent_version = $script:AgentVersion
    } | ConvertTo-Json -Compress

    $params = @{
        Uri = (Join-AgentUrl 'enroll')
        Method = 'POST'
        Headers = @{ 'Accept' = 'application/json' }
        ContentType = 'application/json'
        Body = $body
        TimeoutSec = [int]$script:Config.RequestTimeoutSeconds
    }

    try {
        $response = Invoke-RestMethod @params
    } catch {
        throw ('Enrollment gagal: {0}' -f $_.Exception.Message)
    }

    if (-not $response.agent_token) {
        throw 'Enrollment gagal: server tidak mengembalikan token agent.'
    }

    $script:Config.AgentToken = [string]$response.agent_token
    Save-AgentState $script:Config.AgentToken
    Write-AgentLog 'Enrollment berhasil. Token agent disimpan otomatis.'
}

function Write-AgentLog([string]$Message) {
    $line = '[{0}] {1}' -f (Get-Date).ToString('yyyy-MM-dd HH:mm:ss'), $Message

    if ($VerboseOutput) {
        Write-Host $line
    }

    try {
        $logPath = $script:Config.LogPath

        if ((Test-Path -LiteralPath $logPath) -and ((Get-Item -LiteralPath $logPath).Length -gt 5MB)) {
            Move-Item -LiteralPath $logPath -Destination ($logPath + '.1') -Force
        }

        Add-Content -LiteralPath $logPath -Value $line -Encoding UTF8
    } catch {}
}

function Join-AgentUrl([string]$Path) {
    return ($script:Config.ServerUrl.TrimEnd('/') + '/api/agent/' + $Path.TrimStart('/'))
}

function Invoke-AgentApi {
    param(
        [Parameter(Mandatory = $true)][string]$Method,
        [Parameter(Mandatory = $true)][string]$Path,
        $Body = $null
    )

    $headers = @{
        'X-Agent-Token' = $script:Config.AgentToken
        'Accept' = 'application/json'
    }

    $params = @{
        Uri = (Join-AgentUrl $Path)
        Method = $Method
        Headers = $headers
        TimeoutSec = [int]$script:Config.RequestTimeoutSeconds
    }

    if ($null -ne $Body) {
        $params['ContentType'] = 'application/json'
        $params['Body'] = ($Body | ConvertTo-Json -Depth 8 -Compress)
    }

    return Invoke-RestMethod @params
}

function Add-AgentBuffer {
    param($Payload)

    try {
        $line = $Payload | ConvertTo-Json -Depth 8 -Compress
        Add-Content -LiteralPath $script:Config.BufferPath -Value $line -Encoding UTF8

        $lines = @(Get-Content -LiteralPath $script:Config.BufferPath -ErrorAction Stop)
        $max = [int]$script:Config.MaxBufferItems

        if ($lines.Count -gt $max) {
            $lines | Select-Object -Last $max | Set-Content -LiteralPath $script:Config.BufferPath -Encoding UTF8
        }

        Write-AgentLog ('Pengukuran disimpan ke buffer lokal. Total: {0}' -f [Math]::Min($lines.Count, $max))
    } catch {
        Write-AgentLog ('Gagal menyimpan buffer: {0}' -f $_.Exception.Message)
    }
}

function Send-AgentBuffer {
    $bufferPath = $script:Config.BufferPath

    if (-not (Test-Path -LiteralPath $bufferPath)) {
        return
    }

    $lines = @(Get-Content -LiteralPath $bufferPath -ErrorAction SilentlyContinue)

    if ($lines.Count -eq 0) {
        Remove-Item -LiteralPath $bufferPath -Force -ErrorAction SilentlyContinue
        return
    }

    $remaining = New-Object System.Collections.Generic.List[string]
    $sendFailed = $false

    foreach ($line in $lines) {
        if ([string]::IsNullOrWhiteSpace($line)) {
            continue
        }

        if ($sendFailed) {
            $remaining.Add($line)
            continue
        }

        try {
            $payload = $line | ConvertFrom-Json
            Invoke-AgentApi -Method 'POST' -Path 'measurements' -Body $payload | Out-Null
            Write-AgentLog 'Buffer pengukuran berhasil dikirim.'
        } catch {
            $sendFailed = $true
            $remaining.Add($line)
            Write-AgentLog ('Buffer gagal dikirim: {0}' -f $_.Exception.Message)
        }
    }

    if ($remaining.Count -eq 0) {
        Remove-Item -LiteralPath $bufferPath -Force -ErrorAction SilentlyContinue
    } else {
        $remaining | Set-Content -LiteralPath $bufferPath -Encoding UTF8
    }
}

function Get-DefaultGateway {
    try {
        $route = Get-NetRoute -DestinationPrefix '0.0.0.0/0' -ErrorAction Stop |
            Sort-Object RouteMetric, IfMetric |
            Select-Object -First 1

        if ($route -and $route.NextHop -ne '0.0.0.0') {
            return [string]$route.NextHop
        }
    } catch {}

    try {
        foreach ($line in @(ipconfig)) {
            $text = [string]$line

            if ($text -match '(?:Default Gateway|Gerbang Default|Gateway)[^:]*:\s*([0-9a-fA-F:.]+)') {
                return $matches[1]
            }
        }
    } catch {}

    return $null
}

function Get-PublicIp {
    foreach ($url in @('https://api.ipify.org?format=json', 'https://ifconfig.me/ip')) {
        try {
            $response = Invoke-RestMethod -Uri $url -TimeoutSec 10

            if ($response -is [string]) {
                $value = $response.Trim()

                if ($value) {
                    return $value
                }
            } elseif ($response.ip) {
                return [string]$response.ip
            }
        } catch {}
    }

    return $null
}

function Measure-AgentPing {
    param(
        [string]$Target,
        [int]$Count = 4
    )

    $result = [ordered]@{
        success = $false
        ping = $null
        packet_loss = $null
        jitter = $null
        raw_output = ''
    }

    if ([string]::IsNullOrWhiteSpace($Target)) {
        $result.raw_output = 'Target ping tidak tersedia.'
        return $result
    }

    $lines = @(& ping.exe -n $Count -w 2000 $Target 2>&1)
    $text = (($lines | ForEach-Object { [string]$_ }) -join "`n")
    $result.raw_output = $text

    $delays = New-Object System.Collections.Generic.List[double]

    foreach ($line in $lines) {
        $match = [regex]::Match([string]$line, '(?:time|waktu)[=<]\s*(\d+)\s*ms', 'IgnoreCase')

        if ($match.Success) {
            $delays.Add([double]$match.Groups[1].Value)
        }
    }

    $loss = $null
    $match = [regex]::Match($text, '\((\d+)%\)', 'IgnoreCase')

    if ($match.Success) {
        $loss = [double]$match.Groups[1].Value
    } else {
        $match = [regex]::Match($text, '(\d+)%', 'IgnoreCase')

        if ($match.Success) {
            $loss = [double]$match.Groups[1].Value
        }
    }

    if ($null -eq $loss -and $Count -gt 0) {
        $loss = [Math]::Round((($Count - $delays.Count) / $Count) * 100, 2)
    }

    $average = $null
    $match = [regex]::Match($text, '(?:Average|Rata-rata)\s*=\s*(\d+)\s*ms', 'IgnoreCase')

    if ($match.Success) {
        $average = [double]$match.Groups[1].Value
    }

    if ($null -eq $average -and $delays.Count -gt 0) {
        $average = [Math]::Round((($delays | Measure-Object -Average).Average), 2)
    }

    $jitter = $null

    if ($delays.Count -ge 2) {
        $differences = @()

        for ($i = 1; $i -lt $delays.Count; $i++) {
            $differences += [Math]::Abs($delays[$i] - $delays[$i - 1])
        }

        if ($differences.Count -gt 0) {
            $jitter = [Math]::Round((($differences | Measure-Object -Average).Average), 2)
        }
    }

    $result.ping = $average
    $result.packet_loss = $loss
    $result.jitter = $jitter
    $result.success = ($null -ne $average -and ($null -eq $loss -or $loss -lt 100))

    return $result
}

function Resolve-SpeedtestPath {
    if (-not [string]::IsNullOrWhiteSpace($script:Config.SpeedtestPath)) {
        $configured = Resolve-AgentPath ([string]$script:Config.SpeedtestPath)

        if (Test-Path -LiteralPath $configured) {
            return $configured
        }

        Write-AgentLog ('Speedtest tidak ditemukan di path konfigurasi: {0}' -f $configured)
    }

    $local = Join-Path $PSScriptRoot 'speedtest.exe'

    if (Test-Path -LiteralPath $local) {
        return $local
    }

    $command = Get-Command speedtest -ErrorAction SilentlyContinue

    if ($command) {
        return $command.Source
    }

    return $null
}

function Measure-AgentSpeedtest {
    $result = [ordered]@{
        success = $false
        throughput = $null
        delay = $null
        jitter = $null
        packet_loss = $null
        raw_output = ''
    }

    $path = Resolve-SpeedtestPath

    if (-not $path) {
        $result.raw_output = 'Speedtest CLI tidak ditemukan.'
        return $result
    }

    $arguments = @('--accept-license', '--accept-gdpr', '--format=json')

    if (-not [string]::IsNullOrWhiteSpace([string]$script:Config.SpeedtestServerId)) {
        $arguments = @('--server-id=' + [string]$script:Config.SpeedtestServerId) + $arguments
    }

    $lines = @(& $path @arguments 2>&1)
    $text = (($lines | ForEach-Object { [string]$_ }) -join "`n")
    $result.raw_output = $text
    $data = $null

    foreach ($line in $lines) {
        $candidate = ([string]$line).Trim()

        if (-not $candidate) {
            continue
        }

        try {
            $decoded = $candidate | ConvertFrom-Json

            if ($decoded.type -eq 'result') {
                $data = $decoded
                break
            }
        } catch {}
    }

    if ($null -eq $data) {
        return $result
    }

    if ($data.download.bandwidth) {
        $result.throughput = [Math]::Round((([double]$data.download.bandwidth * 8) / 1000000), 2)
    }

    if ($null -ne $data.ping.latency) {
        $result.delay = [Math]::Round([double]$data.ping.latency, 2)
    }

    if ($null -ne $data.ping.jitter) {
        $result.jitter = [Math]::Round([double]$data.ping.jitter, 2)
    }

    if ($null -ne $data.packetLoss) {
        $result.packet_loss = [Math]::Round([double]$data.packetLoss, 2)
    }

    $result.success = ($null -ne $result.throughput)

    return $result
}

function Get-AgentServerConfig {
    try {
        return Invoke-AgentApi -Method 'GET' -Path 'config'
    } catch {
        Write-AgentLog ('Gagal mengambil konfigurasi server: {0}' -f $_.Exception.Message)
        return $null
    }
}

function Invoke-AgentCycle {
    $gateway = Get-DefaultGateway
    $publicIp = Get-PublicIp
    $serverConfig = Get-AgentServerConfig

    try {
        Invoke-AgentApi -Method 'POST' -Path 'heartbeat' -Body ([ordered]@{
            gateway = $gateway
            public_ip = $publicIp
            agent_version = $script:AgentVersion
        }) | Out-Null
    } catch {
        Write-AgentLog ('Heartbeat gagal: {0}' -f $_.Exception.Message)
    }

    $measureNow = $false
    $due = $true

    if ($serverConfig) {
        $measureNow = [bool]$serverConfig.measure_now

        if (-not [string]::IsNullOrWhiteSpace([string]$serverConfig.last_measurement_at)) {
            try {
                $lastMeasurement = [datetimeoffset]::Parse([string]$serverConfig.last_measurement_at)
                $elapsed = ((Get-Date).ToUniversalTime() - $lastMeasurement.UtcDateTime).TotalSeconds
                $due = $elapsed -ge [int][Math]::Max(30, [int]$script:Config.IntervalSeconds)
            } catch {
                $due = $true
            }
        }
    }

    if (-not $measureNow -and -not $due) {
        Write-AgentLog 'Belum waktunya mengukur. Heartbeat tetap dikirim.'
        return
    }

    if ($measureNow) {
        Write-AgentLog 'Permintaan Tes QoS dari dashboard diterima.'
    }

    $target = [string]$script:Config.PingTarget

    if ([string]::IsNullOrWhiteSpace($target) -and $serverConfig) {
        $target = [string]$serverConfig.school.ip_target
    }

    if ([string]::IsNullOrWhiteSpace($target)) {
        $target = $gateway
    }

    $ping = Measure-AgentPing -Target $target -Count ([int]$script:Config.PingCount)

    if ($SkipSpeedtest -or [bool]$script:Config.SkipSpeedtest) {
        $speedtest = [ordered]@{
            success = $true
            throughput = $null
            delay = $null
            jitter = $null
            packet_loss = $null
            raw_output = 'Speedtest dilewati.'
        }
    } else {
        $speedtest = Measure-AgentSpeedtest
    }

    $packetLoss = $ping.packet_loss

    if ($null -ne $speedtest.packet_loss) {
        $packetLoss = $speedtest.packet_loss
    }

    $delay = $speedtest.delay

    if ($null -eq $delay) {
        $delay = $ping.ping
    }

    $jitterValue = $speedtest.jitter

    if ($null -eq $jitterValue) {
        $jitterValue = $ping.jitter
    }

    $success = [bool]($ping.success -and $speedtest.success)

    $rawOutput = [ordered]@{
        agent_version = $script:AgentVersion
        target = $target
        gateway = $gateway
        public_ip = $publicIp
        ping = $ping
        speedtest = $speedtest
    } | ConvertTo-Json -Depth 8

    $payload = [ordered]@{
        throughput = $speedtest.throughput
        delay = $delay
        jitter = $jitterValue
        packet_loss = $packetLoss
        ping = $ping.ping
        success = $success
        raw_output = $rawOutput
        gateway = $gateway
        public_ip = $publicIp
        agent_version = $script:AgentVersion
    }

    try {
        $response = Invoke-AgentApi -Method 'POST' -Path 'measurements' -Body $payload
        Write-AgentLog ('Pengukuran terkirim. ID: {0}, target: {1}, throughput: {2} Mbps' -f $response.measurement_id, $target, $speedtest.throughput)
    } catch {
        Write-AgentLog ('Pengukuran gagal dikirim: {0}' -f $_.Exception.Message)
        Add-AgentBuffer -Payload $payload
    }
}

if (-not (Test-Path -LiteralPath $ConfigPath)) {
    throw ('Config agent tidak ditemukan: {0}' -f $ConfigPath)
}

$configJson = Get-Content -LiteralPath $ConfigPath -Raw | ConvertFrom-Json
$config = [ordered]@{}

foreach ($key in $defaults.Keys) {
    $config[$key] = $defaults[$key]
}

foreach ($property in $configJson.PSObject.Properties) {
    if ($config.Contains($property.Name)) {
        $config[$property.Name] = $property.Value
    }
}

$config.BufferPath = Resolve-AgentPath ([string]$config.BufferPath)
$config.LogPath = Resolve-AgentPath ([string]$config.LogPath)
$config.StatePath = Resolve-AgentPath ([string]$config.StatePath)
$script:Config = $config

if ([string]::IsNullOrWhiteSpace([string]$script:Config.ServerUrl)) {
    throw 'ServerUrl belum diisi pada config agent.'
}

$configuredToken = [string]$script:Config.AgentToken

if ([string]::IsNullOrWhiteSpace($configuredToken) -or $configuredToken -eq 'PASTE_TOKEN') {
    $state = Get-AgentState

    if ($state) {
        $script:Config.AgentToken = [string]$state.AgentToken
    }
}

if ([string]::IsNullOrWhiteSpace([string]$script:Config.AgentToken) -or
    [string]$script:Config.AgentToken -eq 'PASTE_TOKEN') {
    Invoke-AgentEnrollment
}

if ([string]::IsNullOrWhiteSpace([string]$script:Config.AgentToken)) {
    throw 'AgentToken belum tersedia. Jalankan installer ulang untuk enrollment.'
}

Write-AgentLog ('Agent v{0} mulai. Server: {1}' -f $script:AgentVersion, $script:Config.ServerUrl)
Send-AgentBuffer

do {
    try {
        Invoke-AgentCycle
    } catch {
        Write-AgentLog ('Siklus agent gagal: {0}' -f $_.Exception.Message)
    }

    if ($Once) {
        break
    }

    $sleep = [int][Math]::Max(30, [int]$script:Config.IntervalSeconds)
    Start-Sleep -Seconds $sleep
} while ($true)
