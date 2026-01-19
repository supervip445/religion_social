<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\ViewService;
use Illuminate\Http\Request;

class PublicDonationController extends Controller
{
    /**
     * Get approved donations only
     */
    // public function index()
    // {
    //     $donations = Donation::where('status', 'approved')
    //         ->latest()
    //         ->get();

    //     return response()->json(['data' => $donations]);
    // }

    public function index()
{
    $donations = Donation::where('status', 'approved')
        ->latest()
        ->paginate(10);

    return response()->json([
        'success' => true,
        'data' => $donations->items(),
        'pagination' => [
            'current_page' => $donations->currentPage(),
            'per_page' => $donations->perPage(),
            'total' => $donations->total(),
            'last_page' => $donations->lastPage(),
            'has_more_pages' => $donations->hasMorePages(),
        ],
    ]);
}


    /**
     * Get single donation
     */
    public function show(Request $request, $id)
    {
        $donation = Donation::findOrFail($id);

        if ($donation->status !== 'approved') {
            return response()->json(['message' => 'Donation not found'], 404);
        }

        // Track view
        $viewService = new ViewService();
        $viewService->trackView(Donation::class, $donation->id, $request);

        // Get view count
        try {
            $donation->views_count = $donation->views()->count();
        } catch (\Exception $e) {
            $donation->views_count = 0;
        }

        return response()->json(['data' => $donation]);
    }
}

