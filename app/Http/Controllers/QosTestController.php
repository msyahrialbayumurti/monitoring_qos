<?php

namespace App\Http\Controllers;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Services\QoSMeasurementService;
use Illuminate\Http\RedirectResponse;

class QosTestController extends Controller
{
    public function store(
        School $school,
        QoSMeasurementService $qos
    ): RedirectResponse {
        $user = auth()->user();

        if (
            ! $user ||
            (int) $user->school_id !== (int) $school->id
        ) {
            abort(403, 'Anda hanya dapat melakukan tes untuk sekolah sendiri.');
        }

        if (blank($school->ip_target)) {
            return redirect()
                ->route('dashboard', [
                    'school_id' => $school->id,
                ])
                ->with(
                    'error',
                    'IP target belum tersedia. Pasang agent sekolah agar gateway terdeteksi otomatis.'
                );
        }

        try {


$result = $qos->measure(
    $school->ip_target
);

            
if (!$result['success']) {
    return redirect()
        ->route('dashboard', [
            'school_id' => $school->id,
        ])
        ->with(
            'error',
            'Tes QoS gagal. Speedtest tidak menghasilkan data yang valid. Silakan coba lagi.'
        );
}


            /*
            |--------------------------------------------------------------------------
            | Simpan hasil pengukuran
            |--------------------------------------------------------------------------
            */
            $measurement = QosMeasurement::create([
                'school_id' => $school->id,
                'throughput' => $result['throughput'],
                'delay' => $result['delay'],
                'jitter' => $result['jitter'],
                'packet_loss' => $result['packet_loss'],
                'ping' => $result['ping'],
                'success' => $result['success'],
                'raw_output' => $result['raw_output'],
                'measured_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Tandai hasil ini sebagai hasil Tes QoS yang ditampilkan
            | pada Dashboard.
            |--------------------------------------------------------------------------
            */
            session([
                'qos_dashboard_measurement_id_' . $school->id
                    => $measurement->id,
            ]);

            return redirect()
                ->route('dashboard', [
                    'school_id' => $school->id,
                ])
                ->with(
                    'success',
                    'Tes QoS berhasil dilakukan dan hasil telah disimpan.'
                );

        } catch (\Throwable $e) {

            return redirect()
                ->route('dashboard', [
                    'school_id' => $school->id,
                ])
                ->with(
                    'error',
                    'Tes QoS gagal: ' . $e->getMessage()
                );
        }
    }
}
