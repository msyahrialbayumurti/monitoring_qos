[CmdletBinding()]
param(
    [string]$OutputPath = '',
    [string]$MysqldumpPath = ''
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$envPath = Join-Path $root '.env'

if (-not (Test-Path -LiteralPath $envPath)) {
    throw '.env tidak ditemukan. Jalankan script dari repository project.'
}

$settings = @{}

foreach ($line in Get-Content -LiteralPath $envPath) {
    if ($line -match '^\s*([A-Za-z0-9_]+)\s*=\s*(.*)\s*$') {
        $key = $matches[1]
        $value = $matches[2].Trim()

        if (
            ($value.StartsWith('"') -and $value.EndsWith('"')) -or
            ($value.StartsWith("'") -and $value.EndsWith("'"))
        ) {
            $value = $value.Substring(1, $value.Length - 2)
        }

        $settings[$key] = $value
    }
}

foreach ($key in @('DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME')) {
    if ([string]::IsNullOrWhiteSpace([string]$settings[$key])) {
        throw "Nilai $key tidak ditemukan pada .env."
    }
}

if ([string]::IsNullOrWhiteSpace($MysqldumpPath)) {
    $command = Get-Command mysqldump -ErrorAction SilentlyContinue

    if ($command) {
        $MysqldumpPath = $command.Source
    }
}

if (
    [string]::IsNullOrWhiteSpace($MysqldumpPath) -or
    -not (Test-Path -LiteralPath $MysqldumpPath)
) {
    throw 'mysqldump tidak ditemukan. Isi parameter -MysqldumpPath.'
}

$dumpDirectory = Join-Path $root 'database\dumps'
New-Item -ItemType Directory -Force -Path $dumpDirectory | Out-Null

if ([string]::IsNullOrWhiteSpace($OutputPath)) {
    $OutputPath = Join-Path $dumpDirectory (
        'monitoring-qos-data-{0}.sql' -f (Get-Date -Format 'yyyyMMdd-HHmmss')
    )
} elseif (-not [System.IO.Path]::IsPathRooted($OutputPath)) {
    $OutputPath = Join-Path $root $OutputPath
}

$database = [string]$settings['DB_DATABASE']

$arguments = @(
    ('--host=' + [string]$settings['DB_HOST']),
    ('--port=' + [string]$settings['DB_PORT']),
    ('--user=' + [string]$settings['DB_USERNAME']),
    '--no-create-info',
    '--complete-insert',
    '--skip-triggers',
    '--single-transaction',
    '--default-character-set=utf8mb4',
    ('--ignore-table=' + $database + '.migrations'),
    ('--ignore-table=' + $database + '.password_reset_tokens'),
    ('--ignore-table=' + $database + '.personal_access_tokens'),
    ('--ignore-table=' + $database + '.failed_jobs'),
    $database
)

$previousPassword = $env:MYSQL_PWD

if (-not [string]::IsNullOrWhiteSpace([string]$settings['DB_PASSWORD'])) {
    $env:MYSQL_PWD = [string]$settings['DB_PASSWORD']
}

try {
    $output = & $MysqldumpPath @arguments
    $exitCode = $LASTEXITCODE
} finally {
    if ($null -eq $previousPassword) {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    } else {
        $env:MYSQL_PWD = $previousPassword
    }
}

if ($exitCode -ne 0) {
    throw ('mysqldump gagal dengan exit code {0}.' -f $exitCode)
}

$content = "SET FOREIGN_KEY_CHECKS=0;`r`n" +
    ($output -join "`r`n") +
    "`r`nSET FOREIGN_KEY_CHECKS=1;`r`n"

$utf8 = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText($OutputPath, $content, $utf8)

$insertCount = ([regex]::Matches($content, 'INSERT INTO')).Count
$size = (Get-Item -LiteralPath $OutputPath).Length

Write-Host ('Dump data dibuat: {0}' -f $OutputPath) -ForegroundColor Green
Write-Host ('Ukuran: {0} bytes, INSERT: {1}' -f $size, $insertCount)
Write-Host 'Dump ini berisi data sekolah/akun/pengukuran dan tidak boleh di-commit.'
