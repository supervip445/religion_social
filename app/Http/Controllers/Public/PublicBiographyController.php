<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Biography;
use App\Services\ViewService;
use Illuminate\Http\Request;

class PublicBiographyController extends Controller
{
    /**
     * Get all biographies
     */
    // public function index()
    // {
    //     $biographies = Biography::latest()
    //         ->get()
    //         ->map(function ($biography) {
    //             if ($biography->image) {
    //                 $biography->image = url('storage/' . $biography->image);
    //             }
    //             return $biography;
    //         });

    //     return response()->json(['data' => $biographies]);
    // }

    public function index()
{
    $biographies = Biography::latest()
        ->paginate(10);

    // Transform paginated items
    $biographies->getCollection()->transform(function ($biography) {
        if ($biography->image) {
            $biography->image = url('storage/' . $biography->image);
        }
        return $biography;
    });

    return response()->json([
        'success' => true,
        'data' => $biographies->items(),
        'pagination' => [
            'current_page' => $biographies->currentPage(),
            'per_page' => $biographies->perPage(),
            'total' => $biographies->total(),
            'last_page' => $biographies->lastPage(),
            'has_more_pages' => $biographies->hasMorePages(),
        ],
    ]);
}


    /**
     * Get single biography
     */
    public function show(Request $request, $id)
    {
        $biography = Biography::findOrFail($id);

        // Track view
        $viewService = new ViewService();
        $viewService->trackView(Biography::class, $biography->id, $request);

        if ($biography->image) {
            $biography->image = url('storage/' . $biography->image);
        }

        // Get view count
        try {
            $biography->views_count = $biography->views()->count();
        } catch (\Exception $e) {
            $biography->views_count = 0;
        }

        return response()->json(['data' => $biography]);
    }
}

