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

        $isSchoolUser = $user && $user->isSchool();

        $schools = $isSchoolUser
            ? School::whereKey($user->school_id)->get()
            : School::orderBy('nama_sekolah')->get();

        $schoolId = $isSchoolUser
            ? $user->school_id
            : $request->get('school_id');

        $query = QosMeasurement::with('school')
            ->latest('measured_at');

        if ($isSchoolUser) {
            $query->where('school_id', $user->school_id);
        } elseif ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $measurements = $query
            ->paginate(10)
            ->withQueryString();

        $activeSchools = $schools->count();

        $latestMeasurementQuery = QosMeasurement::with('school');

        if ($isSchoolUser) {
            $latestMeasurementQuery->where('school_id', $user->school_id);
        }

        $latestMeasurement = $latestMeasurementQuery
            ->latest('measured_at')
            ->first();

        $latestSelectedMeasurement = null;

        if ($schoolId) {
            $latestSelectedMeasurement = QosMeasurement::with('school')
                ->where('school_id', $schoolId)
                ->latest('measured_at')
                ->first();
        }

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
