<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class AgentEnrollmentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_cmd_installer_embeds_a_working_setup_script(): void
    {
        $school = $this->makeSchool('TEMP Agent CMD Test');
        $user = $this->makeUser($school->id);

        try {
            $response = $this->actingAs($user)->get(route('agent.setup.cmd', $school));

            $response->assertOk();

            $body = $response->getContent();

            $this->assertStringContainsString('@echo off', $body);
            $this->assertStringContainsString('Start-Process', $body);
            $this->assertStringContainsString('FromBase64String', $body);
            $this->assertStringNotContainsString('__SERVER_URL__', $body);

            preg_match("/\\\$b='([^']+)'/", $body, $matches);

            $decoded = base64_decode($matches[1] ?? '', true);

            $this->assertNotFalse($decoded);
            $this->assertStringContainsString('EnrollmentToken', $decoded);
            $this->assertStringNotContainsString('__INTERVAL_MINUTES__', $decoded);
            $this->assertStringNotContainsString('__INTERVAL_SECONDS__', $decoded);

            preg_match("/\\\$enrollmentToken = '([^']+)'/", $decoded, $tokenMatch);

            $this->assertNotEmpty($tokenMatch[1] ?? null);

            $school->refresh();

            $this->assertSame($tokenMatch[1], $school->agent_enrollment_token);
        } finally {
            $school->delete();
        }
    }

    public function test_setup_download_and_enrollment_flow(): void
    {
        $school = $this->makeSchool('TEMP Agent Enrollment Test');
        $user = $this->makeUser($school->id);

        try {
            $response = $this->actingAs($user)->get(route('agent.setup', $school));

            $response->assertOk();
            $script = $response->getContent();

            $this->assertStringContainsString('EnrollmentToken', $script);
            $this->assertStringContainsString('/agent/files/speedtest.exe', $script);
            $this->assertStringNotContainsString('__SERVER_URL__', $script);
            $this->assertStringNotContainsString('__ENROLLMENT_TOKEN__', $script);
            $this->assertStringNotContainsString('__INTERVAL_MINUTES__', $script);
            $this->assertStringNotContainsString('__INTERVAL_SECONDS__', $script);

            preg_match("/\\\$enrollmentToken = '([^']+)'/", $script, $matches);

            $this->assertNotEmpty($matches[1] ?? null);

            $school->refresh();

            $this->assertSame($matches[1], $school->agent_enrollment_token);
            $this->assertNotNull($school->agent_enrollment_expires_at);

            $enroll = $this->postJson('/api/agent/enroll', [
                'enrollment_token' => $matches[1],
                'agent_version' => 'test-1.0',
            ]);

            $enroll->assertStatus(201);
            $enroll->assertJsonStructure(['agent_token', 'school' => ['id', 'nama_sekolah']]);

            $agentToken = $enroll->json('agent_token');

            $school->refresh();

            $this->assertSame($agentToken, $school->agent_token);
            $this->assertNull($school->agent_enrollment_token);
            $this->assertNull($school->agent_enrollment_expires_at);

            $this->postJson('/api/agent/enroll', [
                'enrollment_token' => $matches[1],
                'agent_version' => 'test-1.0',
            ])->assertStatus(422);

            $this->withHeader('X-Agent-Token', $agentToken)
                ->getJson('/api/agent/config')
                ->assertOk()
                ->assertJsonPath('school.id', $school->id);

            $this->withHeader('X-Agent-Token', $agentToken)
                ->postJson('/api/agent/heartbeat', [
                    'gateway' => '192.168.1.1',
                    'public_ip' => '192.168.1.1',
                    'agent_version' => 'test-1.0',
                ])
                ->assertOk();

            $school->refresh();

            $this->assertSame('192.168.1.1', $school->ip_target);
            $this->assertSame('192.168.1.1', $school->agent_gateway);

            $this->get('/agent/files/qos-agent.ps1')->assertOk();
            $this->get('/agent/files/not-allowed.ps1')->assertNotFound();
        } finally {
            $school->delete();
        }
    }

    public function test_speedtest_binary_is_served_to_agents(): void
    {
        if (! is_file(storage_path('app/tools/speedtest.exe'))) {
            $this->markTestSkipped('Speedtest CLI tidak tersedia di server.');
        }

        $this->get('/agent/files/speedtest.exe')
            ->assertOk()
            ->assertHeader('content-type', 'application/octet-stream');
    }

    private function makeSchool(string $name): School
    {
        return School::create([
            'nama_sekolah' => $name,
            'ip_target' => null,
            'bandwidth' => null,
            'paket_isp' => null,
            'status' => true,
        ]);
    }

    private function makeUser(int $schoolId): User
    {
        return User::create([
            'school_id' => $schoolId,
            'role' => 'user',
            'name' => 'Agent Test ' . Str::random(5),
            'email' => 'agent-test-' . Str::random(10) . '@example.test',
            'password' => 'password',
        ]);
    }
}
