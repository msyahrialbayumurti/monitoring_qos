<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\QosMeasurement;
use App\Models\UserSetting;
use App\Services\QosClassificationService;
use App\Services\QosRcaService;
use App\Services\QosDssService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        QosClassificationService $classification,
        QosRcaService $rcaService,
        QosDssService $dssService
    ) {
        /*
        |--------------------------------------------------------------------------
        | Batasi data sesuai akun yang login
        |--------------------------------------------------------------------------
        */
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Setiap akun hanya memiliki satu sekolah (datanya sendiri)
        |--------------------------------------------------------------------------
        */
        $schools = $user && $user->school_id
            ? School::whereKey($user->school_id)->get()
            : collect();

        $selectedSchool = $schools->first();
        $selectedSchoolId = $selectedSchool?->id;

        /*
        |--------------------------------------------------------------------------
        | Variabel hasil
        |--------------------------------------------------------------------------
        */
        $latestMeasurement = null;
        $latestClassification = null;
        $latestRca = null;
        $latestDss = null;

        /*
        |--------------------------------------------------------------------------
        | Ambil pengukuran terbaru dari database
        |--------------------------------------------------------------------------
        | Mengambil pengukuran terbaru untuk sekolah yang dipilih,
        | baik hasil Tes QoS manual maupun scheduler otomatis.
        |--------------------------------------------------------------------------
        */
        if ($selectedSchool) {
            $latestMeasurement = QosMeasurement::where(
                'school_id',
                $selectedSchool->id
            )
                ->latest('measured_at')
                ->latest('id')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | TIPHON, RCA, dan DSS
        |--------------------------------------------------------------------------
        */
        if ($latestMeasurement) {

            /*
            |--------------------------------------------------------------------------
            | TIPHON
            |--------------------------------------------------------------------------
            */
            $latestClassification = $classification->analyze(
                $latestMeasurement->throughput !== null
                    ? (float) $latestMeasurement->throughput
                    : null,
                $selectedSchool->bandwidth !== null
                    ? (float) $selectedSchool->bandwidth
                    : null,
                $latestMeasurement->delay !== null
                    ? (float) $latestMeasurement->delay
                    : null,
                $latestMeasurement->jitter !== null
                    ? (float) $latestMeasurement->jitter
                    : null,
                $latestMeasurement->packet_loss !== null
                    ? (float) $latestMeasurement->packet_loss
                    : null
            );

            /*
            |--------------------------------------------------------------------------
            | RCA dan DSS
            |--------------------------------------------------------------------------
            | RCA dijalankan untuk kondisi SEDANG atau BURUK.
            |--------------------------------------------------------------------------
            */
            if (
                isset($latestClassification['condition']) &&
                in_array(
                    $latestClassification['condition'],
                    ['SEDANG', 'BURUK']
                )
            ) {
                /*
                |--------------------------------------------------------------------------
                | RCA
                |--------------------------------------------------------------------------
                |
                | Konteks target ping (packet loss target dan timeout berulang)
                | diambil dari pengukuran saat ini dan pengukuran sebelumnya.
                |
                */
                $rcaContext = $this->buildRcaContext(
                    $latestMeasurement
                );

                $latestRca = $rcaService->analyze(
                    $latestClassification,
                    $rcaContext['target_packet_loss'],
                    $rcaContext['ping_timeout_repeated']
                );

                /*
                |--------------------------------------------------------------------------
                | DSS
                |--------------------------------------------------------------------------
                */
                $latestDss = $dssService->recommend(
                    $latestRca
                );

            } elseif (($latestClassification['condition'] ?? null) === 'BAIK') {

                /*
                |--------------------------------------------------------------------------
                | Jika kondisi BAIK
                |--------------------------------------------------------------------------
                */
                $latestRca = [
                    'required' => false,
                    'condition' => 'BAIK',
                    'summary' =>
                        'Kualitas jaringan berada pada kondisi baik.',
                    'causes' => [],
                ];

                $latestDss = [
                    'required' => false,
                    'summary' =>
                        'Tidak diperlukan tindakan khusus karena kualitas jaringan berada pada kondisi baik.',
                    'recommendations' => [],
                ];
            } else {
                $latestRca = [
                    'required' => false,
                    'condition' => null,
                    'summary' =>
                        'Klasifikasi QoS belum dapat ditentukan karena data bandwidth belum lengkap.',
                    'causes' => [],
                ];

                $latestDss = [
                    'required' => false,
                    'summary' =>
                        'Lengkapi kapasitas bandwidth sekolah agar klasifikasi TIPHON dan rekomendasi dapat dihitung.',
                    'recommendations' => [],
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Data grafik 
         /*
        |--------------------------------------------------------------------------
        | Grafik: hanya satu pengukuran terbaru
        |--------------------------------------------------------------------------
        */
$chartMeasurements = collect();

if ($selectedSchool) {
    $chartMeasurement = QosMeasurement::where(
        'school_id',
        $selectedSchool->id
    )
        ->where('success', true)
        ->whereNotNull('throughput')
        ->whereNotNull('delay')
        ->whereNotNull('jitter')
        ->whereNotNull('packet_loss')
        ->whereNotNull('ping')
        ->latest('measured_at')
        ->latest('id')
        ->first();

    if ($chartMeasurement) {
        $chartMeasurements = collect([$chartMeasurement]);
    }
}

        /*
        |--------------------------------------------------------------------------
        | Status seluruh sekolah
        |--------------------------------------------------------------------------
        | Setiap sekolah menggunakan pengukuran terbarunya dari database.
        |--------------------------------------------------------------------------
        */
        $schoolStatuses = $schools->map(
            function ($school) use (
                $classification,
                $rcaService,
                $dssService
            ) {
                /*
                |--------------------------------------------------------------------------
                | Ambil pengukuran terbaru untuk sekolah ini
                |--------------------------------------------------------------------------
                */
                $measurement = QosMeasurement::where(
                    'school_id',
                    $school->id
                )
                    ->latest('measured_at')
                    ->latest('id')
                    ->first();

                $qosClassification = null;
                $rca = null;
                $dss = null;

                /*
                |--------------------------------------------------------------------------
                | Analisis jika tersedia pengukuran
                |--------------------------------------------------------------------------
                */
                if ($measurement) {

                    /*
                    |--------------------------------------------------------------------------
                    | TIPHON
                    |--------------------------------------------------------------------------
                    */
                    $qosClassification = $classification->analyze(
                        $measurement->throughput !== null
                            ? (float) $measurement->throughput
                            : null,
                        $school->bandwidth !== null
                            ? (float) $school->bandwidth
                            : null,
                        $measurement->delay !== null
                            ? (float) $measurement->delay
                            : null,
                        $measurement->jitter !== null
                            ? (float) $measurement->jitter
                            : null,
                        $measurement->packet_loss !== null
                            ? (float) $measurement->packet_loss
                            : null
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | RCA dan DSS untuk kondisi SEDANG atau BURUK
                    |--------------------------------------------------------------------------
                    */
                    if (
                        isset($qosClassification['condition']) &&
                        in_array(
                            $qosClassification['condition'],
                            ['SEDANG', 'BURUK']
                        )
                    ) {
                        /*
                        |--------------------------------------------------------------------------
                        | RCA
                        |--------------------------------------------------------------------------
                        |
                        | Konteks target ping (packet loss target dan timeout berulang)
                        | diambil dari pengukuran saat ini dan pengukuran sebelumnya.
                        |
                        */
                        $rcaContext = $this->buildRcaContext(
                            $measurement
                        );

                        $rca = $rcaService->analyze(
                            $qosClassification,
                            $rcaContext['target_packet_loss'],
                            $rcaContext['ping_timeout_repeated']
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | DSS
                        |--------------------------------------------------------------------------
                        */
                        $dss = $dssService->recommend($rca);

                    } elseif (($qosClassification['condition'] ?? null) === 'BAIK') {

                        /*
                        |--------------------------------------------------------------------------
                        | Jika kondisi BAIK
                        |--------------------------------------------------------------------------
                        */
                        $rca = [
                            'required' => false,
                            'condition' => 'BAIK',
                            'summary' =>
                                'Kualitas jaringan berada pada kondisi baik.',
                            'causes' => [],
                        ];

                        $dss = [
                            'required' => false,
                            'summary' =>
                                'Tidak diperlukan tindakan khusus karena kualitas jaringan berada pada kondisi baik.',
                            'recommendations' => [],
                        ];
                    } else {
                        $rca = [
                            'required' => false,
                            'condition' => null,
                            'summary' =>
                                'Klasifikasi QoS belum dapat ditentukan karena data bandwidth belum lengkap.',
                            'causes' => [],
                        ];

                        $dss = [
                            'required' => false,
                            'summary' =>
                                'Lengkapi kapasitas bandwidth sekolah agar klasifikasi TIPHON dan rekomendasi dapat dihitung.',
                            'recommendations' => [],
                        ];
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Data status sekolah
                |--------------------------------------------------------------------------
                */
                return [
                    'school' => $school,
                    'measurement' => $measurement,
                    'classification' => $qosClassification,
                    'rca' => $rca,
                    'dss' => $dss,
                ];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Interval penyegaran dashboard milik akun ini
        |--------------------------------------------------------------------------
        */
        $dashboardRefreshInterval = (int) UserSetting::getForUser(
            $user?->id,
            'dashboard_refresh_interval',
            '30'
        );

        if (!in_array($dashboardRefreshInterval, [15, 30, 60, 120], true)) {
            $dashboardRefreshInterval = 30;
        }

        /*
        |--------------------------------------------------------------------------
        | Kirim data ke dashboard
        |--------------------------------------------------------------------------
        */
        return view('dashboard', [
            'schools' => $schools,
            'selectedSchool' => $selectedSchool,
            'latestMeasurement' => $latestMeasurement,
            'latestClassification' => $latestClassification,
            'latestRca' => $latestRca,
            'latestDss' => $latestDss,
            'chartMeasurements' => $chartMeasurements,
            'schoolStatuses' => $schoolStatuses,
            'dashboardRefreshInterval' => $dashboardRefreshInterval,
        ]);
    }

    /**
     * Menyiapkan konteks RCA dari hasil pengukuran:
     * packet loss target dan status ping timeout berulang.
     *
     * @return array{target_packet_loss: ?float, ping_timeout_repeated: bool}
     */
    private function buildRcaContext(QosMeasurement $measurement): array
    {
        $targetPacketLoss = null;

        $pingTimeout = false;

        if ($measurement->raw_output) {
            $rawOutput = json_decode(
                $measurement->raw_output,
                true
            );

            if (
                is_array($rawOutput) &&
                isset($rawOutput['target_ping']) &&
                is_array($rawOutput['target_ping'])
            ) {
                $targetPing = $rawOutput['target_ping'];

                if (
                    isset($targetPing['packet_loss']) &&
                    is_numeric($targetPing['packet_loss'])
                ) {
                    $targetPacketLoss = (float) $targetPing['packet_loss'];
                }

                if (
                    isset($targetPing['success']) &&
                    $targetPing['success'] === false
                ) {
                    $pingTimeout = true;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Ping timeout tanpa data packet loss target dianggap 100%.
        |--------------------------------------------------------------------------
        */

        if ($pingTimeout && $targetPacketLoss === null) {
            $targetPacketLoss = 100.0;
        }

        /*
        |--------------------------------------------------------------------------
        | Deteksi timeout berulang dari pengukuran sebelumnya.
        |--------------------------------------------------------------------------
        */

        $previousPingTimeout = false;

        $previousMeasurement = QosMeasurement::where(
            'school_id',
            $measurement->school_id
        )
            ->where(
                'measured_at',
                '<',
                $measurement->measured_at
            )
            ->latest('measured_at')
            ->first();

        if ($previousMeasurement && $previousMeasurement->raw_output) {
            $previousRawOutput = json_decode(
                $previousMeasurement->raw_output,
                true
            );

            if (
                is_array($previousRawOutput) &&
                isset($previousRawOutput['target_ping']) &&
                is_array($previousRawOutput['target_ping'])
            ) {
                $previousPingTimeout =
                    isset($previousRawOutput['target_ping']['success']) &&
                    $previousRawOutput['target_ping']['success'] === false;
            }
        }

        return [
            'target_packet_loss' => $targetPacketLoss,
            'ping_timeout_repeated' => $pingTimeout && $previousPingTimeout,
        ];
    }
}
