<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\QosNotificationService;
use Illuminate\Console\Command;

class CheckAgentOffline extends Command
{
    protected $signature = 'agents:check-offline
        {--minutes=10 : Batas waktu heartbeat sebelum agent dianggap offline}';

    protected $description =
        'Memeriksa agent yang offline dan mengirim notifikasi WhatsApp';

    public function handle(QosNotificationService $notifications): int
    {
        $minutes = max(1, (int) $this->option('minutes'));
        $threshold = now()->subMinutes($minutes);

        $schools = School::query()
            ->where('status', true)
            ->whereNotNull('agent_last_seen_at')
            ->get();

        if ($schools->isEmpty()) {
            $this->info('Tidak ada sekolah dengan agent untuk diperiksa.');

            return self::SUCCESS;
        }

        $offlineCount = 0;
        $recoveryCount = 0;

        foreach ($schools as $school) {
            if ($school->agent_last_seen_at->lt($threshold)) {
                if ($school->agent_offline_notified_at !== null) {
                    continue;
                }

                if ($notifications->notifyAgentOffline($school)) {
                    $school->forceFill([
                        'agent_offline_notified_at' => now(),
                    ])->save();

                    $offlineCount++;

                    $this->warn(
                        'Agent offline: ' . $school->nama_sekolah
                    );
                }

                continue;
            }

            if ($school->agent_offline_notified_at !== null) {
                if ($notifications->notifyAgentRecovered($school)) {
                    $school->forceFill([
                        'agent_offline_notified_at' => null,
                    ])->save();

                    $recoveryCount++;

                    $this->info(
                        'Agent kembali online: ' . $school->nama_sekolah
                    );
                }
            }
        }

        $this->info(
            sprintf(
                'Pemeriksaan agent selesai. Offline: %d, recovery: %d.',
                $offlineCount,
                $recoveryCount
            )
        );

        return self::SUCCESS;
    }
}
