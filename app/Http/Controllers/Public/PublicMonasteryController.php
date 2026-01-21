<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Monastery;
use Illuminate\Http\Request;

class PublicMonasteryController extends Controller
{
    /**
     * Get all monasteries
     */
    public function index()
    {
        $monasteries = Monastery::where('type', 'topsellers')
            ->orderBy('order')
            ->paginate(10, ['id', 'name', 'type'], 'topsellers_page');

        $buildings = Monastery::where('type', 'player')
            ->orderBy('order')
            ->paginate(10, ['id', 'name', 'type'], 'players_page');

        return response()->json([
            'data' => [
                'title' => 'Tri Chat',
                'subtitle' => 'Top Sellers',
                'monasteries' => [
                    'data' => $monasteries->items(),
                    'pagination' => [
                        'current_page' => $monasteries->currentPage(),
                        'per_page' => $monasteries->perPage(),
                        'total' => $monasteries->total(),
                        'last_page' => $monasteries->lastPage(),
                        'has_more_pages' => $monasteries->hasMorePages(),
                    ],
                ],
                'buildings' => [
                    'data' => $buildings->items(),
                    'pagination' => [
                        'current_page' => $buildings->currentPage(),
                        'per_page' => $buildings->perPage(),
                        'total' => $buildings->total(),
                        'last_page' => $buildings->lastPage(),
                        'has_more_pages' => $buildings->hasMorePages(),
                    ],
                ],
            ]
        ]);
    }
}

