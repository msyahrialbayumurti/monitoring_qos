<?php

namespace App\Http\Controllers;

use App\Models\QosMeasurement;
use App\Models\School;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $schoolId = $user?->school_id;

        $schools = $schoolId
            ? School::whereKey($schoolId)->get()
            : collect();

        $query = QosMeasurement::with('school')
            ->where('school_id', $schoolId)
            ->latest('measured_at');

        $measurements = $query
            ->paginate(10)
            ->withQueryString();

        $activeSchools = $schools->count();

        $latestMeasurement = QosMeasurement::with('school')
            ->where('school_id', $schoolId)
            ->latest('measured_at')
            ->first();

        $latestSelectedMeasurement = $latestMeasurement;

        $monitoringStatus = 'MENUNGGU DATA';

        if ($latestMeasurement) {
            $minutesSinceLastMeasurement =
                $latestMeasurement->measured_at->diffInMinutes(now());

            if ($minutesSinceLastMeasurement <= 10) {
                $monitoringStatus = 'AKTIF';
            } else {
                $monitoringStatus = 'PERLU DICEK';
            }
        }

        return view('monitoring.index', [
            'schools' => $schools,
            'measurements' => $measurements,
            'selectedSchoolId' => $schoolId,
            'activeSchools' => $activeSchools,
            'latestMeasurement' => $latestMeasurement,
            'latestSelectedMeasurement' => $latestSelectedMeasurement,
            'monitoringStatus' => $monitoringStatus,
        ]);
    }
}
