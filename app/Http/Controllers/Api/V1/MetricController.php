<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Incidence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Metrics
 * Endpoints for retrieving aggregated metrics and statistics about incidences, such as counts by status and priority.
 */
class MetricController extends Controller
{
    /**
     * List Metrics.
     * Retrieve aggregated metrics and statistics about incidences.
     * 
     * @authenticated
     * @response 200 scenario="Metrics retrieved" {
     *   "data": {
     *     "by_status": {
     *       "open": [
     *         {"id": 1, "title": "Server Down", "status": "open", "priority": "critical", ...}
     *       ],
     *       "in_progress": [],
     *       "resolved": [],
     *       "closed": []
     *     },
     *     "by_priority": {
     *       "low": [],
     *       "medium": [],
     *       "high": [...],
     *       "critical": [...]
     *     }
     *   }
     * }
     * @response 401 scenario="Unauthorized" {
     *   "message": "Unauthenticated."
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->per_page ?? 20, 100);
        
        $incidences = Incidence::with(['user', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
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
