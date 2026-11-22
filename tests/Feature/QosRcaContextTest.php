<?php

namespace Tests\Feature;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Services\QosRcaContextService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QosRcaContextTest extends TestCase
{
    use DatabaseTransactions;

    public function test_context_reads_agent_ping_key_and_speedtest_status(): void
    {
        $school = School::create([
            'nama_sekolah' => 'TEMP RCA Test',
            'ip_target' => '192.168.1.1',
            'bandwidth' => 100,
            'paket_isp' => null,
            'status' => true,
        ]);

        $this->measurement($school, [
            'ping' => ['success' => false, 'packet_loss' => 0, 'ping' => null],
            'speedtest' => ['success' => true],
        ]);

        $current = $this->measurement($school, [
            'ping' => ['success' => false, 'packet_loss' => 0, 'ping' => null],
            'speedtest' => ['success' => true],
        ]);

        $context = app(QosRcaContextService::class)->build($current);

        $this->assertSame('TIMEOUT', $context['ping_status']);
        $this->assertSame(0.0, $context['target_packet_loss']);
        $this->assertTrue($context['speedtest_succeeded']);
        $this->assertTrue($context['ping_timeout_repeated']);
    }

    public function test_context_still_reads_legacy_target_ping_key(): void
    {
        $school = School::create([
            'nama_sekolah' => 'TEMP RCA Legacy Test',
            'ip_target' => '192.168.1.1',
            'bandwidth' => 100,
            'paket_isp' => null,
            'status' => true,
        ]);

        $current = $this->measurement($school, [
            'target_ping' => ['success' => true, 'packet_loss' => 0, 'ping' => 8],
            'speedtest' => ['success' => true],
        ]);

        $context = app(QosRcaContextService::class)->build($current);

        $this->assertSame('TERHUBUNG', $context['ping_status']);
        $this->assertFalse($context['ping_timeout']);
    }

    private function measurement(School $school, array $rawOutput): QosMeasurement
    {
        return QosMeasurement::create([
            'school_id' => $school->id,
            'throughput' => 50,
            'delay' => 20,
            'jitter' => 2,
            'packet_loss' => 0,
            'ping' => 10,
            'success' => true,
            'raw_output' => json_encode($rawOutput),
            'measured_at' => now(),
        ]);
    }
}
