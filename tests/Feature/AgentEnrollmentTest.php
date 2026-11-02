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

    public function test_setup_download_and_enrollment_flow(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            $admin = User::create([
                'name' => 'Agent Test Admin',
                'email' => 'agent-test-' . Str::random(8) . '@example.test',
                'password' => 'password',
                'role' => 'admin',
            ]);
        }

        $school = School::create([
            'nama_sekolah' => 'TEMP Agent Enrollment Test',
            'ip_target' => null,
            'bandwidth' => null,
            'paket_isp' => null,
            'status' => true,
        ]);

        try {
            $response = $this->actingAs($admin)->get(route('agent.setup', $school));

            $response->assertOk();
            $script = $response->getContent();

            $this->assertStringContainsString('EnrollmentToken', $script);
            $this->assertStringNotContainsString('__SERVER_URL__', $script);
            $this->assertStringNotContainsString('__ENROLLMENT_TOKEN__', $script);

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
}
