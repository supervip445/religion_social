<?php

namespace App\Services;

use App\Models\View;
use Illuminate\Http\Request;

class ViewService
{
    /**
     * Track a view for a viewable model.
     */
    public function trackView(string $viewableType, int $viewableId, Request $request): void
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check if this IP has already viewed this item in the last 24 hours
        // This prevents duplicate views from the same IP
        $recentView = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $viewableId)
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if (!$recentView) {
            View::create([
                'viewable_type' => $viewableType,
                'viewable_id' => $viewableId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        }
    }

    /**
     * Get view statistics for a viewable model.
     */
    public function getViewStats(string $viewableType, int $viewableId): array
    {
        $totalViews = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $viewableId)
            ->count();

        $uniqueIPs = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $viewableId)
            ->distinct('ip_address')
            ->count('ip_address');

        $recentViews = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $viewableId)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return [
            'total_views' => $totalViews,
            'unique_ips' => $uniqueIPs,
            'recent_views_24h' => $recentViews,
        ];
    }

    /**
     * Get all views with IP addresses for a viewable model.
     */
    public function getViewsWithIPs(string $viewableType, int $viewableId, int $limit = 100)
    {
        return View::where('viewable_type', $viewableType)
            ->where('viewable_id', $viewableId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['id', 'ip_address', 'user_agent', 'created_at']);
    }
}

