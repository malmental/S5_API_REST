<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Incidence;
use Illuminate\Http\JsonResponse;

class MetricController extends Controller
{
    public function index(): JsonResponse
    {
        $incidences = Incidence::with(['user', 'tags'])->get();

        $byStatus = $incidences->groupBy('status');
        $byPriority = $incidences->groupBy('priority');

        return response()->json([
            'data' => [
                'by_status' => [
                    'open' => $byStatus->get('open', collect())->values(),
                    'in_progress' => $byStatus->get('in_progress', collect())->values(),
                    'resolved' => $byStatus->get('resolved', collect())->values(),
                    'closed' => $byStatus->get('closed', collect())->values(),
                ],
                'by_priority' => [
                    'low' => $byPriority->get('low', collect())->values(),
                    'medium' => $byPriority->get('medium', collect())->values(),
                    'high' => $byPriority->get('high', collect())->values(),
                    'critical' => $byPriority->get('critical', collect())->values(),
                ],
            ],
        ]);
    }
}
