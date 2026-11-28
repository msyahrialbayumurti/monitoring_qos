<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\QosMeasurement;
use App\Services\QosClassificationService;
use App\Services\QosRcaContextService;
use App\Services\QosRcaService;
use App\Services\QosDssService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        QosClassificationService $classification,
        QosRcaContextService $rcaContextService,
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
                ->where('success', true)
                ->whereNotNull('throughput')
                ->whereNotNull('delay')
                ->whereNotNull('jitter')
                ->whereNotNull('packet_loss')
                ->whereNotNull('ping')
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

            $rcaContext = $rcaContextService->build($latestMeasurement);

            $latestMeasurement->gateway_status = $rcaContext['gateway_status'];
            $latestMeasurement->gateway_ping_value = $rcaContext['gateway_ping'];
            $latestMeasurement->gateway_packet_loss_value = $rcaContext['gateway_packet_loss'];
            $latestMeasurement->target_status = $rcaContext['ping_status'];
            $latestMeasurement->target_ping_value = $rcaContext['target_ping'];
            $latestMeasurement->target_packet_loss_value = $rcaContext['target_packet_loss'];

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
                */
                $latestRca = $rcaService->analyze(
                    $latestClassification,
                    $rcaContext['target_packet_loss'],
                    $rcaContext['ping_timeout_repeated'],
                    $rcaContext['speedtest_succeeded'],
                    $rcaContext['gateway_success']
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
                $rcaContextService,
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
                    ->where('success', true)
                    ->whereNotNull('throughput')
                    ->whereNotNull('delay')
                    ->whereNotNull('jitter')
                    ->whereNotNull('packet_loss')
                    ->whereNotNull('ping')
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

                    $rcaContext = $rcaContextService->build($measurement);

                    $measurement->gateway_status = $rcaContext['gateway_status'];
                    $measurement->gateway_ping_value = $rcaContext['gateway_ping'];
                    $measurement->gateway_packet_loss_value = $rcaContext['gateway_packet_loss'];
                    $measurement->target_status = $rcaContext['ping_status'];
                    $measurement->target_ping_value = $rcaContext['target_ping'];
                    $measurement->target_packet_loss_value = $rcaContext['target_packet_loss'];

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
                        */
                        $rca = $rcaService->analyze(
                            $qosClassification,
                            $rcaContext['target_packet_loss'],
                            $rcaContext['ping_timeout_repeated'],
                            $rcaContext['speedtest_succeeded'],
                            $rcaContext['gateway_success']
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
        ]);
    }

    public function latest(Request $request): JsonResponse
    {
        $schoolId = $request->user()?->school_id;

        if (! $schoolId) {
            return response()->json([
                'measurement_id' => null,
                'measured_at' => null,
            ]);
        }

        $measurement = QosMeasurement::query()
            ->where('school_id', $schoolId)
            ->where('success', true)
            ->whereNotNull('throughput')
            ->whereNotNull('delay')
            ->whereNotNull('jitter')
            ->whereNotNull('packet_loss')
            ->whereNotNull('ping')
            ->latest('measured_at')
            ->latest('id')
            ->first();

        return response()->json([
            'measurement_id' => $measurement?->id,
            'measured_at' => $measurement?->measured_at?->toIso8601String(),
        ]);
    }
}
