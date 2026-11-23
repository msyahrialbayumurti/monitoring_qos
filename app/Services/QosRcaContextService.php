<?php

namespace App\Services;

use App\Models\QosMeasurement;

class QosRcaContextService
{
    /**
     * @return array{
     *     target_packet_loss: ?float,
     *     target_ping: ?float,
     *     target_success: ?bool,
     *     ping_timeout: bool,
     *     ping_timeout_repeated: bool,
     *     ping_status: string,
     *     gateway_ping: ?float,
     *     gateway_jitter: ?float,
     *     gateway_packet_loss: ?float,
     *     gateway_success: ?bool,
     *     gateway_timeout: bool,
     *     gateway_status: string,
     *     speedtest_succeeded: bool
     * }
     */
    public function build(QosMeasurement $measurement): array
    {
        $current = $this->state($measurement);
        $speedtestSucceeded = $this->speedtestSucceeded($measurement);

        $previousMeasurement = QosMeasurement::query()
            ->where('school_id', $measurement->school_id)
            ->where('id', '<', $measurement->id)
            ->latest('id')
            ->first();

        $previousTimeout = false;

        if ($previousMeasurement) {
            $previous = $this->state($previousMeasurement);
            $previousTimeout = $previous['target_success'] === false;
        }

        $targetPacketLoss = $current['target_packet_loss'];

        if ($current['target_success'] === false && $targetPacketLoss === null) {
            $targetPacketLoss = 100.0;
        }

        return [
            'target_packet_loss' => $targetPacketLoss,
            'target_ping' => $current['target_ping'],
            'target_success' => $current['target_success'],
            'ping_timeout' => $current['target_success'] === false,
            'ping_timeout_repeated' => $current['target_success'] === false && $previousTimeout,
            'ping_status' => $this->status(
                $current['target_success'],
                $current['target_ping']
            ),
            'gateway_ping' => $current['gateway_ping'],
            'gateway_jitter' => $current['gateway_jitter'],
            'gateway_packet_loss' => $current['gateway_packet_loss'],
            'gateway_success' => $current['gateway_success'],
            'gateway_timeout' => $current['gateway_success'] === false,
            'gateway_status' => $this->status(
                $current['gateway_success'],
                $current['gateway_ping']
            ),
            'speedtest_succeeded' => $speedtestSucceeded,
        ];
    }

    /**
     * @return array{
     *     target_ping: ?float,
     *     target_packet_loss: ?float,
     *     target_success: ?bool,
     *     gateway_ping: ?float,
     *     gateway_jitter: ?float,
     *     gateway_packet_loss: ?float,
     *     gateway_success: ?bool
     * }
     */
    private function state(QosMeasurement $measurement): array
    {
        $rawOutput = $this->decode($measurement->raw_output);
        $targetPing = is_array($rawOutput)
            ? $this->targetPayload($rawOutput)
            : null;
        $gatewayPing = is_array($rawOutput)
            ? $this->gatewayPayload($rawOutput)
            : null;

        return [
            'target_ping' => $measurement->target_ping !== null
                ? (float) $measurement->target_ping
                : $this->numeric($targetPing['ping'] ?? null),
            'target_packet_loss' => $measurement->target_packet_loss !== null
                ? (float) $measurement->target_packet_loss
                : $this->numeric($targetPing['packet_loss'] ?? null),
            'target_success' => $measurement->target_success !== null
                ? (bool) $measurement->target_success
                : $this->boolean($targetPing['success'] ?? null),
            'gateway_ping' => $measurement->gateway_ping !== null
                ? (float) $measurement->gateway_ping
                : $this->numeric($gatewayPing['ping'] ?? null),
            'gateway_jitter' => $measurement->gateway_jitter !== null
                ? (float) $measurement->gateway_jitter
                : $this->numeric($gatewayPing['jitter'] ?? null),
            'gateway_packet_loss' => $measurement->gateway_packet_loss !== null
                ? (float) $measurement->gateway_packet_loss
                : $this->numeric($gatewayPing['packet_loss'] ?? null),
            'gateway_success' => $measurement->gateway_success !== null
                ? (bool) $measurement->gateway_success
                : $this->boolean($gatewayPing['success'] ?? null),
        ];
    }

    private function speedtestSucceeded(QosMeasurement $measurement): bool
    {
        $rawOutput = $this->decode($measurement->raw_output);

        if (
            is_array($rawOutput) &&
            isset($rawOutput['speedtest']) &&
            is_array($rawOutput['speedtest']) &&
            array_key_exists('success', $rawOutput['speedtest'])
        ) {
            return (bool) $rawOutput['speedtest']['success'];
        }

        return (bool) $measurement->success;
    }

    private function status(?bool $success, ?float $ping): string
    {
        if ($success === false) {
            return 'TIMEOUT';
        }

        if ($success === true && $ping !== null) {
            return 'TERHUBUNG';
        }

        return 'DATA';
    }

    private function decode(mixed $rawOutput): ?array
    {
        if (is_array($rawOutput)) {
            return $rawOutput;
        }

        if (is_string($rawOutput) && $rawOutput !== '') {
            $decoded = json_decode($rawOutput, true);

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    private function targetPayload(array $rawOutput): ?array
    {
        $ping = $rawOutput['target_ping'] ?? $rawOutput['ping'] ?? null;

        return is_array($ping) ? $ping : null;
    }

    private function gatewayPayload(array $rawOutput): ?array
    {
        $ping = $rawOutput['gateway_ping'] ?? null;

        return is_array($ping) ? $ping : null;
    }

    private function numeric(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function boolean(mixed $value): ?bool
    {
        return $value === null ? null : (bool) $value;
    }
}
