<?php

namespace App\Services;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Models\User;
use App\Models\UserSetting;
use App\Models\WhatsAppNotification;
use Illuminate\Support\Facades\Log;
use Throwable;

class QosNotificationService
{
    public const EVENT_CONDITION_CHANGE = 'condition_change';

    public const EVENT_RECOVERY = 'recovery';

    public const EVENT_FAILURE = 'failure';

    public const EVENT_AGENT_OFFLINE = 'agent_offline';

    public const EVENT_AGENT_RECOVERY = 'agent_recovery';

    public function __construct(
        private readonly FonnteService $fonnte,
        private readonly QosClassificationService $classification
    ) {
    }

    /**
     * Kirim notifikasi WhatsApp saat kondisi QoS berubah atau pengukuran gagal.
     */
    public function handle(QosMeasurement $measurement): void
    {
        try {
            $this->dispatch($measurement);
        } catch (Throwable $e) {
            Log::warning('Notifikasi WhatsApp QoS gagal diproses.', [
                'measurement_id' => $measurement->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function notifyAgentOffline(School $school): bool
    {
        $lastSeen = $school->agent_last_seen_at?->format('d/m/Y H:i') ?? '-';

        $message = implode("\n", [
            '[NETMONITOR] ' . $school->nama_sekolah,
            'Agent sekolah tidak terhubung sejak ' . $lastSeen . '.',
            'Periksa koneksi internet dan PC agent sekolah.',
            route('dashboard', ['school_id' => $school->id]),
        ]);

        return $this->sendAgentNotification(
            $school,
            self::EVENT_AGENT_OFFLINE,
            $message
        );
    }

    public function notifyAgentRecovered(School $school): bool
    {
        $time = now()->format('d/m/Y H:i');

        $message = implode("\n", [
            '[NETMONITOR] ' . $school->nama_sekolah,
            'Agent sekolah kembali terhubung pada ' . $time . '.',
            'Monitoring QoS sekolah sudah aktif kembali.',
            route('dashboard', ['school_id' => $school->id]),
        ]);

        return $this->sendAgentNotification(
            $school,
            self::EVENT_AGENT_RECOVERY,
            $message
        );
    }

    private function dispatch(QosMeasurement $measurement): void
    {
        $school = $measurement->school;

        if (! $school) {
            return;
        }

        $recipient = $this->recipient($school);

        if (! $recipient) {
            return;
        }

        [$user, $phone] = $recipient;

        if (! $this->fonnte->isConfigured()) {
            Log::warning('Notifikasi WhatsApp aktif tetapi token Fonnte belum diatur.');

            return;
        }

        if (WhatsAppNotification::query()
            ->where('measurement_id', $measurement->id)
            ->where('user_id', $user->id)
            ->exists()) {
            return;
        }

        $current = $this->classify($measurement, $school->bandwidth);
        $previous = $this->previousMeasurement($measurement);
        $previousCondition = $previous
            ? $this->conditionOf($previous, $school->bandwidth)
            : null;

        $event = $this->resolveEvent(
            $measurement,
            $current['condition'],
            $previous,
            $previousCondition
        );

        if ($event === null) {
            return;
        }

        $message = $this->buildMessage(
            $event,
            $measurement,
            $current,
            $previousCondition
        );

        $result = $this->fonnte->send($phone, $message);

        WhatsAppNotification::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'measurement_id' => $measurement->id,
            'phone' => $phone,
            'event' => $event,
            'condition' => $current['condition'],
            'message' => $message,
            'status' => $result['success'] ? 'sent' : 'failed',
            'response' => $result['reason']
                ?? json_encode($result['response'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'sent_at' => $result['success'] ? now() : null,
        ]);
    }

    private function recipient(School $school): ?array
    {
        $user = User::query()
            ->where('school_id', $school->id)
            ->orderBy('id')
            ->first();

        if (! $user) {
            return null;
        }

        $phone = UserSetting::query()
            ->where('user_id', $user->id)
            ->where('key', 'whatsapp_number')
            ->value('value');

        $enabled = UserSetting::query()
            ->where('user_id', $user->id)
            ->where('key', 'whatsapp_notifications')
            ->value('value') === '1';

        if (! $enabled || blank($phone)) {
            return null;
        }

        return [$user, $phone];
    }

    private function sendAgentNotification(
        School $school,
        string $event,
        string $message
    ): bool {
        $recipient = $this->recipient($school);

        if (! $recipient) {
            return true;
        }

        [$user, $phone] = $recipient;

        if (! $this->fonnte->isConfigured()) {
            Log::warning('Notifikasi WhatsApp aktif tetapi token Fonnte belum diatur.');

            return false;
        }

        $result = $this->fonnte->send($phone, $message);

        WhatsAppNotification::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'measurement_id' => null,
            'phone' => $phone,
            'event' => $event,
            'condition' => null,
            'message' => $message,
            'status' => $result['success'] ? 'sent' : 'failed',
            'response' => $result['reason']
                ?? json_encode($result['response'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'sent_at' => $result['success'] ? now() : null,
        ]);

        return (bool) $result['success'];
    }

    private function resolveEvent(
        QosMeasurement $measurement,
        ?string $currentCondition,
        ?QosMeasurement $previous,
        ?string $previousCondition
    ): ?string {
        if (! $measurement->success) {
            if ($previous && ! $previous->success) {
                return null;
            }

            return self::EVENT_FAILURE;
        }

        if ($currentCondition === 'BAIK') {
            if (in_array($previousCondition, ['SEDANG', 'BURUK'], true)) {
                return self::EVENT_RECOVERY;
            }

            if ($previous && ! $previous->success) {
                return self::EVENT_RECOVERY;
            }

            return null;
        }

        if (in_array($currentCondition, ['SEDANG', 'BURUK'], true)) {
            return $previousCondition === $currentCondition
                ? null
                : self::EVENT_CONDITION_CHANGE;
        }

        return null;
    }

    private function buildMessage(
        string $event,
        QosMeasurement $measurement,
        array $classification,
        ?string $previousCondition
    ): string {
        $schoolName = $measurement->school?->nama_sekolah ?? 'Sekolah';
        $time = $measurement->measured_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');
        $dashboardUrl = route('dashboard', [
            'school_id' => $measurement->school_id,
        ]);

        if ($event === self::EVENT_FAILURE) {
            return implode("\n", [
                '[NETMONITOR] ' . $schoolName,
                'Pengukuran QoS gagal pada ' . $time . '.',
                'Periksa koneksi internet dan status agent sekolah.',
                $dashboardUrl,
            ]);
        }

        $condition = $classification['condition'] ?? '-';
        $index = $classification['qos_index'];

        if ($event === self::EVENT_RECOVERY) {
            return implode("\n", [
                '[NETMONITOR] ' . $schoolName,
                'Kondisi QoS membaik ke ' . $condition
                    . ($index !== null ? ' (indeks ' . number_format((float) $index, 2) . ')' : '')
                    . '.',
                $this->parameterLines($measurement),
                'Waktu: ' . $time,
                $dashboardUrl,
            ]);
        }

        $from = $previousCondition ?? 'AWAL';

        return implode("\n", [
            '[NETMONITOR] ' . $schoolName,
            'Kondisi QoS berubah dari ' . $from . ' ke ' . $condition
                . ($index !== null ? ' (indeks ' . number_format((float) $index, 2) . ')' : '')
                . '.',
            $this->parameterLines($measurement),
            'Waktu: ' . $time,
            $dashboardUrl,
        ]);
    }

    private function parameterLines(QosMeasurement $measurement): string
    {
        return implode("\n", [
            'Throughput: ' . $this->formatValue($measurement->throughput) . ' Mbps',
            'Delay: ' . $this->formatValue($measurement->delay) . ' ms',
            'Jitter: ' . $this->formatValue($measurement->jitter) . ' ms',
            'Packet loss: ' . $this->formatValue($measurement->packet_loss) . '%',
            'Ping: ' . $this->formatValue($measurement->ping) . ' ms',
        ]);
    }

    private function formatValue(mixed $value): string
    {
        return $value === null
            ? '-'
            : number_format((float) $value, 2);
    }

    private function classify(QosMeasurement $measurement, mixed $bandwidth): array
    {
        return $this->classification->analyze(
            $measurement->throughput !== null ? (float) $measurement->throughput : null,
            $bandwidth !== null ? (float) $bandwidth : null,
            $measurement->delay !== null ? (float) $measurement->delay : null,
            $measurement->jitter !== null ? (float) $measurement->jitter : null,
            $measurement->packet_loss !== null ? (float) $measurement->packet_loss : null
        );
    }

    private function conditionOf(QosMeasurement $measurement, mixed $bandwidth): ?string
    {
        return $this->classify($measurement, $bandwidth)['condition'] ?? null;
    }

    private function previousMeasurement(QosMeasurement $measurement): ?QosMeasurement
    {
        return QosMeasurement::query()
            ->where('school_id', $measurement->school_id)
            ->where('id', '<', $measurement->id)
            ->latest('id')
            ->first();
    }
}
