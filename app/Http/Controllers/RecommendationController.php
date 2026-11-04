<?php

namespace App\Http\Controllers;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Services\QosClassificationService;
use App\Services\QosRcaService;
use App\Services\QosDssService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index(
        Request $request,
        QosClassificationService $classification,
        QosRcaService $rcaService,
        QosDssService $dssService
    ) {
        $user = $request->user();

        $schoolId = $user?->school_id;

        $schools = $schoolId
            ? School::whereKey($schoolId)->get()
            : collect();

        $query = QosMeasurement::with('school')
            ->where('school_id', $schoolId)
            ->latest('measured_at');

        $measurements = $query->paginate(10)->withQueryString();

        $measurements->getCollection()->transform(
            function ($measurement) use (
                $classification,
                $rcaService,
                $dssService
            ) {
                if (!$measurement->school) {
                    $measurement->dss = null;
                    return $measurement;
                }

                $measurement->qos_classification =
                    $classification->analyze(
                        $measurement->throughput,
                        $measurement->school->bandwidth,
                        $measurement->delay,
                        $measurement->jitter,
                        $measurement->packet_loss
                    );

                $condition =
                    $measurement->qos_classification['condition'] ?? null;

                if (in_array($condition, ['SEDANG', 'BURUK'], true)) {
                    $targetPacketLoss = null;
                    $pingTimeoutRepeated = false;

                    $rawOutput = $measurement->raw_output;

                    if (is_string($rawOutput)) {
                        $rawOutput = json_decode($rawOutput, true);
                    }

                    if (is_array($rawOutput)) {
                        $targetPing = $rawOutput['target_ping'] ?? [];

                        if (
                            isset($targetPing['packet_loss']) &&
                            is_numeric($targetPing['packet_loss'])
                        ) {
                            $targetPacketLoss =
                                (float) $targetPing['packet_loss'];
                        }
                    }

                    $measurement->rca = $rcaService->analyze(
                        $measurement->qos_classification,
                        $targetPacketLoss,
                        $pingTimeoutRepeated
                    );

                    $measurement->dss = $dssService->recommend(
                        $measurement->rca
                    );
                } else {
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

        return view('recommendations.index', [
            'schools' => $schools,
            'measurements' => $measurements,
            'selectedSchoolId' => $schoolId,
        ]);
    }
}
