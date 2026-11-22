<?php

namespace App\Services;

use App\Models\QosMeasurement;

class QosRcaContextService
{
    /**
     * @return array{
     *     target_packet_loss: ?float,
     *     ping_timeout: bool,
     *     ping_timeout_repeated: bool,
     *     ping_status: string,
     *     speedtest_succeeded: bool
     * }
     */
    public function build(QosMeasurement $measurement): array
    {
        $targetPacketLoss = null;
        $pingTimeout = false;
        $pingStatus = 'DATA';
        $speedtestSucceeded = (bool) $measurement->success;

        $rawOutput = $this->decode($measurement->raw_output);

        if (is_array($rawOutput)) {
            $targetPing = $this->pingPayload($rawOutput);

            if ($targetPing !== null) {
                if (
                    isset($targetPing['packet_loss']) &&
                    is_numeric($targetPing['packet_loss'])
                ) {
                    $targetPacketLoss = (float) $targetPing['packet_loss'];
                }

                if (array_key_exists('success', $targetPing)) {
                    $pingTimeout = $targetPing['success'] === false;

                    if ($pingTimeout) {
                        $pingStatus = 'TIMEOUT';
                    } elseif (($targetPing['ping'] ?? null) !== null) {
                        $pingStatus = 'TERHUBUNG';
                    }
                }
            }

            if (
                isset($rawOutput['speedtest']) &&
                is_array($rawOutput['speedtest']) &&
                array_key_exists('success', $rawOutput['speedtest'])
            ) {
                $speedtestSucceeded = (bool) $rawOutput['speedtest']['success'];
            }
        }

        if ($pingTimeout && $targetPacketLoss === null) {
            $targetPacketLoss = 100.0;
        }

        $previousPingTimeout = false;
        $previousMeasurement = QosMeasurement::query()
            ->where('school_id', $measurement->school_id)
            ->where('id', '<', $measurement->id)
            ->latest('id')
            ->first();

        if ($previousMeasurement) {
            $previousRawOutput = $this->decode($previousMeasurement->raw_output);

            if (is_array($previousRawOutput)) {
                $previousPing = $this->pingPayload($previousRawOutput);

                if ($previousPing !== null) {
                    $previousPingTimeout =
                        isset($previousPing['success']) &&
                        $previousPing['success'] === false;
                }
            }
        }

        return [
            'target_packet_loss' => $targetPacketLoss,
            'ping_timeout' => $pingTimeout,
            'ping_timeout_repeated' => $pingTimeout && $previousPingTimeout,
            'ping_status' => $pingStatus,
            'speedtest_succeeded' => $speedtestSucceeded,
        ];
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

    private function pingPayload(array $rawOutput): ?array
    {
        $ping = $rawOutput['target_ping'] ?? $rawOutput['ping'] ?? null;

        return is_array($ping) ? $ping : null;
    }
}
