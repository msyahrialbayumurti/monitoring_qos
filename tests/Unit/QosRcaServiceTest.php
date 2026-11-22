<?php

namespace Tests\Unit;

use App\Services\QosRcaService;
use PHPUnit\Framework\TestCase;

class QosRcaServiceTest extends TestCase
{
    private QosRcaService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new QosRcaService();
    }

    public function test_rule_1_still_matches_original_table(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 3,
            delay: 2,
            jitter: 1,
            packetLoss: 4
        ));

        $this->assertSame(1, $result['causes'][0]['rule_number']);
        $this->assertSame('Jitter + Delay', $result['causes'][0]['parameter']);
    }

    public function test_rule_2_still_matches_original_table(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 2,
            delay: 4,
            jitter: 4,
            packetLoss: 1
        ));

        $this->assertSame(2, $result['causes'][0]['rule_number']);
        $this->assertSame('Packet Loss + Throughput', $result['causes'][0]['parameter']);
    }

    public function test_rule_3_still_matches_original_table(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 4,
            delay: 1,
            jitter: 3,
            packetLoss: 4
        ));

        $this->assertSame(3, $result['causes'][0]['rule_number']);
        $this->assertSame('Delay', $result['causes'][0]['parameter']);
    }

    public function test_rule_4_triggers_for_high_target_packet_loss(): void
    {
        $result = $this->service->analyze(
            $this->classification(
                throughput: 3,
                delay: 3,
                jitter: 3,
                packetLoss: 4
            ),
            targetPacketLoss: 95
        );

        $this->assertSame(4, $result['causes'][0]['rule_number']);
    }

    public function test_rule_4_triggers_for_repeated_ping_timeout_when_speedtest_fails(): void
    {
        $result = $this->service->analyze(
            $this->classification(
                throughput: 3,
                delay: 3,
                jitter: 3,
                packetLoss: 4
            ),
            targetPacketLoss: 0,
            pingTimeoutRepeated: true,
            speedtestSucceeded: false
        );

        $this->assertSame(4, $result['causes'][0]['rule_number']);
    }

    public function test_rule_4_does_not_trigger_for_unreachable_ping_when_speedtest_succeeds(): void
    {
        $result = $this->service->analyze(
            $this->classification(
                throughput: 1,
                delay: 4,
                jitter: 3,
                packetLoss: 4
            ),
            targetPacketLoss: 0,
            pingTimeoutRepeated: true,
            speedtestSucceeded: true
        );

        $this->assertSame(6, $result['causes'][0]['rule_number']);
    }

    public function test_rule_6_handles_throughput_only_degradation(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 1,
            delay: 4,
            jitter: 3,
            packetLoss: 4
        ));

        $cause = $result['causes'][0];

        $this->assertSame(6, $cause['rule_number']);
        $this->assertSame('Throughput', $cause['parameter']);
        $this->assertStringContainsString('Bandwidth', $cause['cause']);
        $this->assertStringContainsString('speedtest', $cause['recommendation']);
        $this->assertStringContainsString('Rule 6', $result['summary']);
    }

    public function test_rule_7_handles_throughput_and_delay_degradation(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 1,
            delay: 2,
            jitter: 3,
            packetLoss: 4
        ));

        $this->assertSame(7, $result['causes'][0]['rule_number']);
    }

    public function test_rule_8_handles_jitter_and_throughput_degradation(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 1,
            delay: 3,
            jitter: 1,
            packetLoss: 3
        ));

        $this->assertSame(8, $result['causes'][0]['rule_number']);
    }

    public function test_rule_9_handles_moderate_packet_loss_and_throughput_degradation(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 1,
            delay: 4,
            jitter: 3,
            packetLoss: 2
        ));

        $this->assertSame(9, $result['causes'][0]['rule_number']);
    }

    public function test_rule_10_handles_jitter_only_degradation(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 4,
            delay: 4,
            jitter: 1,
            packetLoss: 4
        ));

        $this->assertSame(10, $result['causes'][0]['rule_number']);
    }

    public function test_rule_11_handles_packet_loss_only_degradation(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 4,
            delay: 4,
            jitter: 3,
            packetLoss: 2
        ));

        $this->assertSame(11, $result['causes'][0]['rule_number']);
    }

    public function test_rule_12_handles_uncovered_combination(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 4,
            delay: 2,
            jitter: 3,
            packetLoss: 2
        ));

        $cause = $result['causes'][0];

        $this->assertSame(12, $cause['rule_number']);
        $this->assertTrue($cause['is_fallback']);
        $this->assertSame('Delay + Packet Loss', $cause['parameter']);
    }

    public function test_rule_5_returns_good_condition_without_causes(): void
    {
        $result = $this->service->analyze($this->classification(
            throughput: 4,
            delay: 4,
            jitter: 4,
            packetLoss: 4
        ));

        $this->assertFalse($result['required']);
        $this->assertSame([], $result['causes']);
    }

    private function classification(
        int $throughput,
        int $delay,
        int $jitter,
        int $packetLoss
    ): array {
        $index = ($throughput + $delay + $jitter + $packetLoss) / 4;

        return [
            'throughput_index' => $throughput,
            'delay_index' => $delay,
            'jitter_index' => $jitter,
            'packet_loss_index' => $packetLoss,
            'qos_index' => $index,
            'condition' => $index >= 3.5 ? 'BAIK' : ($index >= 2.5 ? 'SEDANG' : 'BURUK'),
        ];
    }
}
