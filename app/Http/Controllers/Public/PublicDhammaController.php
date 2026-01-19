<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Dhamma;
use App\Services\ViewService;
use Illuminate\Http\Request;

class PublicDhammaController extends Controller
{
    /**
     * Get all dhamma talks
     */
    // public function index()
    // {
    //     try {
    //         $dhammas = Dhamma::latest()->get();
            
    //         $dhammas = $dhammas->map(function ($dhamma) {
    //             // Handle image URL
    //             if ($dhamma->image) {
    //                 $dhamma->image = url('storage/' . $dhamma->image);
    //             }
                
    //             // Safely get likes and comments counts
    //             $dhamma->likes_count = 0;
    //             $dhamma->dislikes_count = 0;
    //             $dhamma->comments_count = 0;
                
    //             try {
    //                 $likes = $dhamma->likes()->get();
    //                 $comments = $dhamma->comments()->where('is_approved', true)->get();
    //                 $dhamma->likes_count = $likes->where('type', 'like')->count();
    //                 $dhamma->dislikes_count = $likes->where('type', 'dislike')->count();
    //                 $dhamma->comments_count = $comments->count();
    //             } catch (\Exception $e) {
    //                 // Silently fail and keep counts at 0
    //             }
                
    //             return $dhamma;
    //         });

    //         return response()->json(['data' => $dhammas]);
    //     } catch (\Exception $e) {
    //         \Log::error('PublicDhammaController index error: ' . $e->getMessage());
    //         \Log::error('Stack trace: ' . $e->getTraceAsString());
    //         return response()->json([
    //             'error' => 'Failed to fetch dhammas',
    //             'message' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine()
    //         ], 500);
    //     }
    // }

    public function index()
{
    try {
        $dhammas = Dhamma::withCount([
                // likes_count
                'likes as likes_count' => function ($q) {
                    $q->where('type', 'like');
                },
                // dislikes_count
                'likes as dislikes_count' => function ($q) {
                    $q->where('type', 'dislike');
                },
                // comments_count (approved only)
                'comments as comments_count' => function ($q) {
                    $q->where('is_approved', true);
                },
            ])
            ->latest()
            ->paginate(10);

        // Transform data without breaking pagination
        $dhammas->getCollection()->transform(function ($dhamma) {

            // Image full URL
            if ($dhamma->image) {
                $dhamma->image = url('storage/' . $dhamma->image);
            }

            return $dhamma;
        });

        return response()->json([
            'success' => true,
            'message' => 'Dhammas fetched successfully',
            'data' => $dhammas->items(),
            'pagination' => [
                'current_page' => $dhammas->currentPage(),
                'per_page' => $dhammas->perPage(),
                'total' => $dhammas->total(),
                'last_page' => $dhammas->lastPage(),
                'has_more_pages' => $dhammas->hasMorePages(),
            ],
        ]);

    } catch (\Exception $e) {

        \Log::error('PublicDhammaController index error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Failed to fetch dhammas',
        ], 500);
    }
}


    /**
     * Get single dhamma talk
     */
    public function show(Request $request, $id)
    {
        try {
            $dhamma = Dhamma::findOrFail($id);

            // Track view
            $viewService = new ViewService();
            $viewService->trackView(Dhamma::class, $dhamma->id, $request);

            if ($dhamma->image) {
                $dhamma->image = url('storage/' . $dhamma->image);
            }

            // Safely get likes and comments counts
            try {
                $likes = $dhamma->likes()->get();
                $comments = $dhamma->comments()->where('is_approved', true)->get();
                $dhamma->likes_count = $likes->where('type', 'like')->count();
                $dhamma->dislikes_count = $likes->where('type', 'dislike')->count();
                $dhamma->comments_count = $comments->count();
                $dhamma->views_count = $dhamma->views()->count();
            } catch (\Exception $e) {
                $dhamma->likes_count = 0;
                $dhamma->dislikes_count = 0;
                $dhamma->comments_count = 0;
                $dhamma->views_count = 0;
            }

            return response()->json(['data' => $dhamma]);
        } catch (\Exception $e) {
            \Log::error('PublicDhammaController show error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to fetch dhamma', 'message' => $e->getMessage()], 500);
        }
    }
}

