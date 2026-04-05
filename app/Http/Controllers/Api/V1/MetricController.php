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
     *
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
        $countsByStatus = Incidence::select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $countsByPriority = Incidence::select('priority')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        $total = Incidence::count();

        return response()->json([
            'data' => [
                'total' => $total,
                'by_status' => [
                    'open' => $countsByStatus['open'] ?? 0,
                    'in_progress' => $countsByStatus['in_progress'] ?? 0,
                    'resolved' => $countsByStatus['resolved'] ?? 0,
                    'closed' => $countsByStatus['closed'] ?? 0,
                ],
                'by_priority' => [
                    'low' => $countsByPriority['low'] ?? 0,
                    'medium' => $countsByPriority['medium'] ?? 0,
                    'high' => $countsByPriority['high'] ?? 0,
                    'critical' => $countsByPriority['critical'] ?? 0,
                ],
            ],
        ]);
    }
}
