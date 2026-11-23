<?php

namespace App\Http\Controllers;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Services\QosClassificationService;
use App\Services\QosRcaContextService;
use App\Services\QosRcaService;
use App\Services\QosDssService;
use Illuminate\Http\Request;

class QosAnalysisController extends Controller
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
                $rcaContextService,
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

                $rcaContext =
                    $rcaContextService->build($measurement);

                $measurement->ping_status =
                    $rcaContext['ping_status'];

                $measurement->gateway_status =
                    $rcaContext['gateway_status'];

                $measurement->gateway_ping_value =
                    $rcaContext['gateway_ping'];

                $measurement->gateway_packet_loss_value =
                    $rcaContext['gateway_packet_loss'];

                $measurement->target_status =
                    $rcaContext['ping_status'];

                $measurement->target_ping_value =
                    $rcaContext['target_ping'];

                $measurement->target_packet_loss_value =
                    $rcaContext['target_packet_loss'];

                $targetPacketLoss =
                    $rcaContext['target_packet_loss'];

                $pingTimeoutRepeated =
                    $rcaContext['ping_timeout_repeated'];


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
                            $pingTimeoutRepeated,
                            $rcaContext['speedtest_succeeded'],
                            $rcaContext['gateway_success']
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
