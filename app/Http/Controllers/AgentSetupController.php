<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AgentSetupController extends Controller
{
    public function download(Request $request, School $school): Response
    {
        $script = $this->buildInstallerScript($request, $school);

        return response($script, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => sprintf(
                'attachment; filename="netmonitor-agent-setup-%d.ps1"',
                $school->id
            ),
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    public function downloadCmd(Request $request, School $school): Response
    {
        $script = $this->buildInstallerScript($request, $school);

        return response($this->buildCmdInstaller(base64_encode($script)), 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => sprintf(
                'attachment; filename="netmonitor-agent-setup-%d.cmd"',
                $school->id
            ),
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    public function file(string $file): Response
    {
        $allowed = [
            'qos-agent.ps1',
            'install-agent-task.ps1',
        ];

        abort_unless(in_array($file, $allowed, true), 404);

        $path = base_path('agent/' . $file);

        abort_unless(is_file($path), 404, 'File agent tidak ditemukan.');

        return response()->file($path, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    public function speedtest(): Response
    {
        $path = base_path('agent/speedtest.exe');

        if (! is_file($path)) {
            $path = storage_path('app/tools/speedtest.exe');
        }

        abort_unless(is_file($path), 404, 'Speedtest CLI tidak tersedia di server.');

        return response()->file($path, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="speedtest.exe"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    private function buildInstallerScript(Request $request, School $school): string
    {
        $user = $request->user();

        abort_unless(
            $user && (int) $user->school_id === (int) $school->id,
            403
        );

        $templatePath = base_path('agent/setup-template.ps1');

        abort_unless(is_file($templatePath), 500, 'Template installer agent tidak ditemukan.');

        $enrollmentToken = Str::random(32);

        $school->forceFill([
            'agent_enrollment_token' => $enrollmentToken,
            'agent_enrollment_expires_at' => now()->addMinutes(30),
        ])->save();

        $serverUrl = rtrim((string) config('app.url'), '/');

        if (
            $serverUrl === ''
            || preg_match('#^https?://(localhost|127\.0\.0\.1|\[::1\])#i', $serverUrl)
        ) {
            $serverUrl = rtrim($request->root(), '/');
        }

        $escapePowerShell = static fn (string $value): string => str_replace("'", "''", $value);

        $ownerId = User::query()
            ->where('school_id', $school->id)
            ->value('id');

        $interval = (int) UserSetting::getForUser(
            $ownerId,
            'monitoring_interval',
            '5'
        );

        if (! in_array($interval, [1, 5, 10, 15], true)) {
            $interval = 5;
        }

        $script = file_get_contents($templatePath);

        return str_replace(
            [
                '__SERVER_URL__',
                '__ENROLLMENT_TOKEN__',
                '__SCHOOL_NAME__',
                '__INTERVAL_MINUTES__',
                '__INTERVAL_SECONDS__',
            ],
            [
                $escapePowerShell($serverUrl),
                $escapePowerShell($enrollmentToken),
                $escapePowerShell($school->nama_sekolah),
                (string) $interval,
                (string) ($interval * 60),
            ],
            $script
        );
    }

    private function buildCmdInstaller(string $payload): string
    {
        $lines = [
            '@echo off',
            'setlocal EnableExtensions',
            'title NETMONITOR Agent Setup',
            '',
            'set "NETMONITOR_SELF=%~f0"',
            '',
            'net session >nul 2>&1',
            'if not "%errorlevel%"=="0" (',
            '    echo Meminta izin Administrator...',
            '    powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "Start-Process -FilePath \'%NETMONITOR_SELF%\' -Verb RunAs"',
            '    if errorlevel 1 (',
            '        echo.',
            '        echo Gagal meminta izin Administrator. Klik kanan file lalu pilih Run as administrator.',
            '        pause',
            '    )',
            '    exit /b',
            ')',
            '',
            'set "NETMONITOR_WORK=%TEMP%\\netmonitor-agent-%RANDOM%%RANDOM%"',
            'set "NETMONITOR_PS1=%NETMONITOR_WORK%\\netmonitor-agent-setup.ps1"',
            'mkdir "%NETMONITOR_WORK%" >nul 2>&1',
            '',
            'if exist "%~dp0speedtest.exe" copy /y "%~dp0speedtest.exe" "%NETMONITOR_WORK%\\speedtest.exe" >nul',
            '',
            'powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "$b=\'' . $payload . '\'; [IO.File]::WriteAllBytes(\'%NETMONITOR_PS1%\', [Convert]::FromBase64String($b))"',
            'if errorlevel 1 goto :failed',
            '',
            'powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%NETMONITOR_PS1%"',
            'set "NETMONITOR_EXIT=%ERRORLEVEL%"',
            '',
            'if not "%NETMONITOR_EXIT%"=="0" goto :failed',
            '',
            'rmdir /s /q "%NETMONITOR_WORK%" >nul 2>&1',
            'exit /b 0',
            '',
            ':failed',
            'echo.',
            'echo Instalasi agent gagal. Periksa pesan di atas.',
            'pause',
            'rmdir /s /q "%NETMONITOR_WORK%" >nul 2>&1',
            'exit /b 1',
        ];

        return implode("\r\n", $lines) . "\r\n";
    }
}
