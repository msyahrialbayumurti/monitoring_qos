<?php

namespace App\Http\Controllers;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Services\QosClassificationService;
use App\Services\QosMeasurementDeletionService;
use App\Services\QosRcaContextService;
use Illuminate\Http\Request;

class MonitoringHistoryController extends Controller
{
    public function index(
        Request $request,
        QosClassificationService $classification,
        QosRcaContextService $rcaContextService
    ) {
        /*
        |--------------------------------------------------------------------------
        | Ambil seluruh sekolah untuk filter
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        $schoolId = $user?->school_id;

        $schools = $schoolId
            ? School::whereKey($schoolId)->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Ambil data monitoring
        |--------------------------------------------------------------------------
        |
        | Data hanya dibaca dari database dan dibatasi milik akun ini.
        | Tidak ada proses Ping atau Speedtest di halaman ini.
        |
        */

        $query = QosMeasurement::with('school')
            ->where('school_id', $schoolId)
            ->latest('measured_at');

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $measurements = $query
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Hitung klasifikasi TIPHON dan status Ping
        |--------------------------------------------------------------------------
        */

        $measurements->getCollection()->transform(
            function ($measurement) use ($classification, $rcaContextService) {

                /*
                |--------------------------------------------------------------------------
                | Jika sekolah tidak ditemukan
                |--------------------------------------------------------------------------
                */

                if (!$measurement->school) {

                    $measurement->qos_classification = null;

                    $measurement->ping_status = 'TIDAK DIKETAHUI';

                    return $measurement;
                }

                /*
                |--------------------------------------------------------------------------
                | Klasifikasi TIPHON
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
                | Tentukan status Ping
                |--------------------------------------------------------------------------
                |
                | raw_output menyimpan hasil target_ping dari proses monitoring.
                |
                */

                $rcaContext = $rcaContextService->build($measurement);

                $measurement->ping_status = $rcaContext['ping_status'];
                $measurement->gateway_status = $rcaContext['gateway_status'];
                $measurement->gateway_ping_value = $rcaContext['gateway_ping'];
                $measurement->gateway_packet_loss_value = $rcaContext['gateway_packet_loss'];
                $measurement->target_status = $rcaContext['ping_status'];
                $measurement->target_ping_value = $rcaContext['target_ping'];
                $measurement->target_packet_loss_value = $rcaContext['target_packet_loss'];

                return $measurement;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Kirim data ke halaman
        |--------------------------------------------------------------------------
        */

        return view('history.index', [

            'schools' =>
                $schools,

            'measurements' =>
                $measurements,

            'selectedSchoolId' =>
                $schoolId,

        ]);
    }

    public function destroy(
        Request $request,
        QosMeasurementDeletionService $deletion
    ) {
        $deleted = $deletion->delete($request);
        $schoolId = $request->integer('school_id');

        return redirect()
            ->route(
                'monitoring.history',
                $schoolId ? ['school_id' => $schoolId] : []
            )
            ->with(
                $deleted > 0 ? 'success' : 'error',
                $deleted > 0
                    ? "{$deleted} data monitoring berhasil dihapus."
                    : 'Tidak ada data monitoring yang dihapus.'
            );
    }
}
