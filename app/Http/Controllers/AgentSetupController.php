<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AgentSetupController extends Controller
{
    public function download(Request $request, School $school): Response
    {
        $user = $request->user();

        abort_unless(
            $user && ($user->isAdmin() || (int) $user->school_id === (int) $school->id),
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

        $script = file_get_contents($templatePath);

        $script = str_replace(
            ['__SERVER_URL__', '__ENROLLMENT_TOKEN__', '__SCHOOL_NAME__'],
            [
                $escapePowerShell($serverUrl),
                $escapePowerShell($enrollmentToken),
                $escapePowerShell($school->nama_sekolah),
            ],
            $script
        );

        return response($script, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => sprintf(
                'attachment; filename="netmonitor-agent-setup-%d.ps1"',
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
}
