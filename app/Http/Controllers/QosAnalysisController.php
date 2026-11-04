<?php

namespace App\Http\Controllers;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Services\QosClassificationService;
use App\Services\QosRcaService;
use App\Services\QosDssService;
use Illuminate\Http\Request;

class QosAnalysisController extends Controller
{
    public function index(
        Request $request,
        QosClassificationService $classification,
        QosRcaService $rcaService,
        QosDssService $dssService
    ) {
        /*
        |--------------------------------------------------------------------------
        | Ambil seluruh sekolah
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        $schoolId = $user?->school_id;

        $schools = $schoolId
            ? School::whereKey($schoolId)->get()
            : collect();


        /*
        |--------------------------------------------------------------------------
        | Ambil data monitoring milik akun ini
        |--------------------------------------------------------------------------
        */

        $query = QosMeasurement::with('school')
            ->where('school_id', $schoolId)
            ->latest('measured_at');

        $measurements = $query
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Analisis TIPHON + RCA + DSS
        |--------------------------------------------------------------------------
        */

        $measurements->getCollection()->transform(
            function ($measurement) use (
                $classification,
                $rcaService,
                $dssService
            ) {

                /*
                |--------------------------------------------------------------------------
                | Jika sekolah tidak ditemukan
                |--------------------------------------------------------------------------
                */

                if (!$measurement->school) {
                    $measurement->qos_classification = null;
                    $measurement->rca = null;
                    $measurement->dss = null;
                    $measurement->ping_status = 'TIDAK DIKETAHUI';

                    return $measurement;
                }


                /*
                |--------------------------------------------------------------------------
                | TIPHON
                |--------------------------------------------------------------------------
                */

                $measurement->qos_classification =
                    $classification->analyze(
                        $measurement->throughput,
                        $measurement->school->bandwidth,
                        $measurement->delay,
                        $measurement->jitter,
                        $measurement->packet_loss
                    );


                /*
                |--------------------------------------------------------------------------
                | Variabel ping target
                |--------------------------------------------------------------------------
                */

                $measurement->ping_status = 'DATA';

                $targetPacketLoss = null;

                $pingTimeout = false;


                /*
                |--------------------------------------------------------------------------
                | Baca raw output
                |--------------------------------------------------------------------------
                */

                if ($measurement->raw_output) {

                    $rawOutput = $measurement->raw_output;

                    if (is_string($rawOutput)) {
                        $rawOutput = json_decode(
                            $rawOutput,
                            true
                        );
                    }


                    if (is_array($rawOutput)) {

                        /*
                        |--------------------------------------------------------------------------
                        | Target Ping
                        |--------------------------------------------------------------------------
                        */

                        if (
                            isset($rawOutput['target_ping']) &&
                            is_array($rawOutput['target_ping'])
                        ) {

                            $targetPing =
                                $rawOutput['target_ping'];


                            /*
                            |--------------------------------------------------------------------------
                            | Jika packet loss target tersedia
                            |--------------------------------------------------------------------------
                            */

                            if (
                                isset($targetPing['packet_loss']) &&
                                is_numeric($targetPing['packet_loss'])
                            ) {

                                $targetPacketLoss =
                                    (float) $targetPing['packet_loss'];
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Status Ping
                            |--------------------------------------------------------------------------
                            */

                            if (
                                isset($targetPing['success']) &&
                                $targetPing['success'] === false
                            ) {

                                $measurement->ping_status =
                                    'TIMEOUT';

                                $pingTimeout = true;

                            } elseif (
                                isset($targetPing['success']) &&
                                $targetPing['success'] === true
                            ) {

                                if (
                                    isset($targetPing['ping']) &&
                                    $targetPing['ping'] !== null
                                ) {

                                    $measurement->ping_status =
                                        'TERHUBUNG';

                                } else {

                                    $measurement->ping_status =
                                        'DATA';
                                }
                            }
                        }
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Jika ping timeout tetapi packet loss target
                | tidak tersimpan di raw_output, maka dianggap 100%.
                |--------------------------------------------------------------------------
                */

                if (
                    $pingTimeout &&
                    $targetPacketLoss === null
                ) {
                    $targetPacketLoss = 100.0;
                }


                /*
                |--------------------------------------------------------------------------
                | Cek pengukuran sebelumnya
                |--------------------------------------------------------------------------
                */

                $previousMeasurement =
                    QosMeasurement::where(
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


                /*
                |--------------------------------------------------------------------------
                | Status timeout sebelumnya
                |--------------------------------------------------------------------------
                */

                $previousPingTimeout = false;


                if (
                    $previousMeasurement &&
                    $previousMeasurement->raw_output
                ) {

                    $previousRawOutput =
                        $previousMeasurement->raw_output;


                    if (is_string($previousRawOutput)) {

                        $previousRawOutput =
                            json_decode(
                                $previousRawOutput,
                                true
                            );
                    }


                    if (
                        is_array($previousRawOutput) &&
                        isset($previousRawOutput['target_ping']) &&
                        is_array($previousRawOutput['target_ping'])
                    ) {

                        $previousTargetPing =
                            $previousRawOutput['target_ping'];


                        $previousPingTimeout =
                            isset($previousTargetPing['success']) &&
                            $previousTargetPing['success'] === false;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Timeout berulang
                |--------------------------------------------------------------------------
                */

                $pingTimeoutRepeated =
                    $pingTimeout &&
                    $previousPingTimeout;


                /*
                |--------------------------------------------------------------------------
                | Kondisi QoS
                |--------------------------------------------------------------------------
                */

                $condition =
                    $measurement->qos_classification['condition']
                    ?? null;


                /*
                |--------------------------------------------------------------------------
                | RCA + DSS
                |--------------------------------------------------------------------------
                */

                if (
                    in_array(
                        $condition,
                        ['SEDANG', 'BURUK'],
                        true
                    )
                ) {

                    $measurement->rca =
                        $rcaService->analyze(
                            $measurement->qos_classification,
                            $targetPacketLoss,
                            $pingTimeoutRepeated
                        );


                    $measurement->dss =
                        $dssService->recommend(
                            $measurement->rca
                        );

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Jika kondisi BAIK
                    |--------------------------------------------------------------------------
                    */

                    $measurement->rca = [
                        'required' => false,
                        'condition' => 'BAIK',
                        'summary' =>
                            'Tidak ditemukan indikasi gangguan pada parameter QoS.',
                        'causes' => [],
                    ];


                    $measurement->dss = [
                        'required' => false,
                        'summary' =>
                            'Tidak diperlukan tindakan khusus karena kualitas jaringan berada pada kondisi baik.',
                        'recommendations' => [],
                    ];
                }


                return $measurement;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Data terbaru
        |--------------------------------------------------------------------------
        */

        $latestMeasurement =
            $measurements->first();


        /*
        |--------------------------------------------------------------------------
        | Kirim data ke Blade
        |--------------------------------------------------------------------------
        */

        return view('analysis.index', [
            'schools' => $schools,
            'measurements' => $measurements,
            'selectedSchoolId' => $schoolId,
            'latestMeasurement' => $latestMeasurement,
        ]);
    }
}
