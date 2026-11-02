<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\QosMeasurement;
use App\Services\QoSMeasurementService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class MonitorQoS extends Command
{
    protected $signature = 'qos:monitor';

    protected $description =
        'Melakukan monitoring QoS otomatis pada seluruh sekolah aktif';

    public function handle(QoSMeasurementService $qos): int
    {
        $schools = School::where('status', true)->get();

        if ($schools->isEmpty()) {
            $this->warn(
                'Tidak ada sekolah aktif yang dapat dimonitor.'
            );

            return self::SUCCESS;
        }

        $this->info('Memulai monitoring QoS...');

        $failedCount = 0;

        foreach ($schools as $school) {
            $this->newLine();

            $this->line(
                'Memeriksa: ' .
                $school->nama_sekolah .
                ' [' .
                $school->ip_target .
                ']'
            );

            /*
            |--------------------------------------------------------------------------
            | Jalankan pengukuran
            |--------------------------------------------------------------------------
            |
            | Jika service mengalami exception, percobaan tetap dicatat
            | sebagai gagal selama database masih dapat menyimpan data.
            |
            */

            try {
                $result = $qos->measure($school->ip_target);

                $success = ($result['success'] ?? false) === true;

                $throughput = $result['throughput'] ?? null;
                $delay = $result['delay'] ?? null;
                $jitter = $result['jitter'] ?? null;
                $packetLoss = $result['packet_loss'] ?? null;
                $ping = $result['ping'] ?? null;
                $rawOutput = $result['raw_output'] ?? null;

                if (!$success) {
                    $failedCount++;

                    Log::warning('Pengukuran QoS gagal', [
                        'school_id' => $school->id,
                        'school' => $school->nama_sekolah,
                        'ip' => $school->ip_target,
                        'raw_output' => $rawOutput,
                    ]);
                }
            } catch (Throwable $e) {
                $success = false;

                $throughput = null;
                $delay = null;
                $jitter = null;
                $packetLoss = null;
                $ping = null;

                $failedCount++;

                $rawOutput = json_encode([
                    'status' => 'GAGAL',
                    'message' => $e->getMessage(),
                    'target_ip' => $school->ip_target,
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                Log::error('Exception saat monitoring QoS', [
                    'school_id' => $school->id,
                    'school' => $school->nama_sekolah,
                    'ip' => $school->ip_target,
                    'error' => $e->getMessage(),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan setiap percobaan ke database
            |--------------------------------------------------------------------------
            |
            | Percobaan gagal disimpan dengan success = false.
            | Nilai parameter yang tidak berhasil diukur tetap null,
            | bukan diisi dengan angka buatan.
            |
            */

            try {
                QosMeasurement::create([
                    'school_id' => $school->id,
                    'throughput' => $throughput,
                    'delay' => $delay,
                    'jitter' => $jitter,
                    'packet_loss' => $packetLoss,
                    'ping' => $ping,
                    'success' => $success,
                    'raw_output' => $rawOutput,
                    'measured_at' => now(),
                ]);
            } catch (Throwable $e) {
                $failedCount++;

                Log::error('Gagal menyimpan riwayat monitoring QoS', [
                    'school_id' => $school->id,
                    'school' => $school->nama_sekolah,
                    'ip' => $school->ip_target,
                    'error' => $e->getMessage(),
                ]);

                $this->error(
                    '  ✗ Hasil tidak dapat disimpan ke database.'
                );

                // Lanjutkan ke sekolah berikutnya.
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Tampilkan hasil di terminal
            |--------------------------------------------------------------------------
            */

            if ($success) {
                $this->info('  ✓ Pengukuran berhasil');

                $this->line(
                    '    Throughput : ' .
                    ($throughput ?? '-') .
                    ' Mbps'
                );

                $this->line(
                    '    Ping       : ' .
                    ($ping ?? '-') .
                    ' ms'
                );

                $this->line(
                    '    Delay      : ' .
                    ($delay ?? '-') .
                    ' ms'
                );

                $this->line(
                    '    Jitter     : ' .
                    ($jitter ?? '-') .
                    ' ms'
                );

                $this->line(
                    '    Packet Loss: ' .
                    ($packetLoss ?? '-') .
                    ' %'
                );
            } else {
                $this->error(
                    '  ✗ Pengukuran gagal; percobaan dicatat di riwayat.'
                );
            }
        }

        $this->newLine();

        $this->info('Monitoring QoS selesai.');

        if ($failedCount > 0) {
            $this->warn(
                'Terdapat ' .
                $failedCount .
                ' kegagalan pengukuran atau penyimpanan. Periksa log jika diperlukan.'
            );
        }

        return self::SUCCESS;
    }
}
