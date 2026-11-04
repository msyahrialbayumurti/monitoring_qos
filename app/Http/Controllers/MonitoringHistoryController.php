<?php

namespace App\Http\Controllers;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Services\QosClassificationService;
use App\Services\QosMeasurementDeletionService;
use Illuminate\Http\Request;

class MonitoringHistoryController extends Controller
{
    public function index(
        Request $request,
        QosClassificationService $classification
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
            function ($measurement) use ($classification) {

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

                $measurement->ping_status = 'DATA';

                if ($measurement->raw_output) {

                    $rawOutput = json_decode(
                        $measurement->raw_output,
                        true
                    );

                    if (
                        is_array($rawOutput) &&
                        isset($rawOutput['target_ping'])
                    ) {

                        $targetPing =
                            $rawOutput['target_ping'];

                        /*
                        |--------------------------------------------------------------------------
                        | Ping berhasil
                        |--------------------------------------------------------------------------
                        */

                        if (
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

                        /*
                        |--------------------------------------------------------------------------
                        | Ping timeout
                        |--------------------------------------------------------------------------
                        */

                        } elseif (
                            isset($targetPing['success']) &&
                            $targetPing['success'] === false
                        ) {

                            $measurement->ping_status =
                                'TIMEOUT';
                        }
                    }
                }

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
