[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)][string]$DbHost,
    [int]$Port = 3306,
    [Parameter(Mandatory = $true)][string]$Database,
    [Parameter(Mandatory = $true)][string]$Username,
    [string]$Password = '',
    [Parameter(Mandatory = $true)][string]$DumpPath,
    [string]$MysqlPath = ''
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot

if (-not [System.IO.Path]::IsPathRooted($DumpPath)) {
    $DumpPath = Join-Path $root $DumpPath
}

if (-not (Test-Path -LiteralPath $DumpPath)) {
    throw ('File dump tidak ditemukan: {0}' -f $DumpPath)
}

if ([string]::IsNullOrWhiteSpace($MysqlPath)) {
    $command = Get-Command mysql -ErrorAction SilentlyContinue

    if ($command) {
        $MysqlPath = $command.Source
    }
}

if (
    [string]::IsNullOrWhiteSpace($MysqlPath) -or
    -not (Test-Path -LiteralPath $MysqlPath)
) {
    throw 'mysql client tidak ditemukan. Isi parameter -MysqlPath.'
}

$arguments = @(
    ('--host=' + $DbHost),
    ('--port=' + $Port),
    ('--user=' + $Username),
    '--default-character-set=utf8mb4',
    $Database
)

$previousPassword = $env:MYSQL_PWD

if (-not [string]::IsNullOrWhiteSpace($Password)) {
    $env:MYSQL_PWD = $Password
}

try {
    $process = Start-Process `
        -FilePath $MysqlPath `
        -ArgumentList $arguments `
        -RedirectStandardInput $DumpPath `
        -NoNewWindow `
        -Wait `
        -PassThru
} finally {
    if ($null -eq $previousPassword) {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    } else {
        $env:MYSQL_PWD = $previousPassword
    }
}

if ($process.ExitCode -ne 0) {
    throw ('Import gagal dengan exit code {0}.' -f $process.ExitCode)
}

Write-Host ('Import selesai: {0}' -f $DumpPath) -ForegroundColor Green
