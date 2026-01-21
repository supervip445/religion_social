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
        $monasteries = Monastery::where('type', 'monastery')
            ->orderBy('order')
            ->get();

        $buildings = Monastery::where('type', 'building')
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => [
                'title' => 'Tri Chat',
                'subtitle' => 'Top Sellers',
                'monasteries' => $monasteries,
                'buildings' => $buildings,
            ]
        ]);
    }
}

