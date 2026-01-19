<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\View;
use App\Services\ViewService;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    protected $viewService;

    public function __construct(ViewService $viewService)
    {
        $this->viewService = $viewService;
    }

    /**
     * Get views with IP addresses for a specific item
     */
    public function getViews(Request $request)
    {
        $request->validate([
            'viewable_type' => 'required|string',
            'viewable_id' => 'required|integer',
        ]);

        $views = $this->viewService->getViewsWithIPs(
            $request->viewable_type,
            $request->viewable_id,
            $request->get('limit', 100)
        );

        $stats = $this->viewService->getViewStats(
            $request->viewable_type,
            $request->viewable_id
        );

        return response()->json([
            'data' => $views,
            'stats' => $stats,
        ]);
    }

    /**
     * Get view statistics for a specific item
     */
    public function getStats(Request $request)
    {
        $request->validate([
            'viewable_type' => 'required|string',
            'viewable_id' => 'required|integer',
        ]);

        $stats = $this->viewService->getViewStats(
            $request->viewable_type,
            $request->viewable_id
        );

        return response()->json(['data' => $stats]);
    }
}

