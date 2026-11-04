<?php

namespace App\Http\Controllers;

use App\Models\QosMeasurement;
use App\Services\QosMeasurementDeletionService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $schoolId = $user?->school_id;

        $schools = $schoolId
            ? \App\Models\School::whereKey($schoolId)->get()
            : collect();

        $query = QosMeasurement::with('school')
            ->where('school_id', $schoolId)
            ->orderByDesc('measured_at');

        $measurements = $query->paginate(15)->withQueryString();

        $notifications = $measurements->getCollection()
            ->map(function ($measurement) {
                $values = [
                    $measurement->throughput,
                    $measurement->delay,
                    $measurement->jitter,
                    $measurement->packet_loss,
                ];

                if (!$measurement->success) {
                    $level = 'danger';
                    $title = 'Pengukuran QoS gagal';
                    $message = 'Pengukuran jaringan tidak berhasil. Periksa koneksi dan konfigurasi target.';
                } elseif (in_array(null, $values, true)) {
                    $level = 'warning';
                    $title = 'Data QoS belum lengkap';
                    $message = 'Sebagian parameter QoS belum tersedia pada hasil pengukuran ini.';
                } else {
                    $level = 'info';
                    $title = 'Pengukuran QoS tersimpan';
                    $message = 'Hasil pengukuran QoS berhasil tersimpan.';
                }

                return [
                    'id' => $measurement->id,
                    'school' => $measurement->school?->nama_sekolah ?? 'Sekolah tidak diketahui',
                    'level' => $level,
                    'title' => $title,
                    'message' => $message,
                    'measured_at' => $measurement->measured_at,
                ];
            });

        return view('notification.index', compact(
            'measurements',
            'notifications',
            'schools'
        ));
    }

    public function destroy(
        Request $request,
        QosMeasurementDeletionService $deletion
    ) {
        $deleted = $deletion->delete($request);
        $schoolId = $request->integer('school_id');

        return redirect()
            ->route(
                'notifications.index',
                $schoolId ? ['school_id' => $schoolId] : []
            )
            ->with(
                $deleted > 0 ? 'success' : 'error',
                $deleted > 0
                    ? "{$deleted} data notifikasi berhasil dihapus."
                    : 'Tidak ada data notifikasi yang dihapus.'
            );
    }
}

