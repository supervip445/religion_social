<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dhamma;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DhammaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $dhammas = Dhamma::latest()->get()->map(function ($dhamma) {
    //         if ($dhamma->image) {
    //             $dhamma->image = url('storage/' . $dhamma->image);
    //         }
    //         // Add view count
    //         try {
    //             $dhamma->views_count = $dhamma->views()->count();
    //         } catch (\Exception $e) {
    //             $dhamma->views_count = 0;
    //         }
    //         return $dhamma;
    //     });
    //     return response()->json(['data' => $dhammas]);
    // }

    public function index()
{
    $dhammas = Dhamma::withCount('views')   // views_count (DB level)
        ->latest()                          // ORDER BY created_at DESC
        ->paginate(10);                     // 10 per page

    // Transform data without breaking pagination
    $dhammas->getCollection()->transform(function ($dhamma) {
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
        ]
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'speaker' => 'required|string|max:255',
            'date' => 'required|date',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        // Handle single image (for backward compatibility)
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('dhammas', 'public');
        }

        // Handle multiple images - use first image as main image
        if ($request->hasFile('images') && count($request->file('images')) > 0) {
            $images = $request->file('images');
            $firstImage = $images[0];
            $validated['image'] = $firstImage->store('dhammas', 'public');
        }

        $dhamma = Dhamma::create($validated);
        
        if ($dhamma->image) {
            $dhamma->image = url('storage/' . $dhamma->image);
        }

        // Send notification to public users
        try {
            $notificationService = new NotificationService();
            $notificationService->notifyNewDhamma($dhamma);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send dhamma notification: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Dhamma talk created successfully',
            'data' => $dhamma,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dhamma = Dhamma::findOrFail($id);
        if ($dhamma->image) {
            $dhamma->image = url('storage/' . $dhamma->image);
        }
        // Add view count
        try {
            $dhamma->views_count = $dhamma->views()->count();
        } catch (\Exception $e) {
            $dhamma->views_count = 0;
        }
        return response()->json(['data' => $dhamma]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $dhamma = Dhamma::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'speaker' => 'required|string|max:255',
            'date' => 'required|date',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        // Handle single image (for backward compatibility)
        if ($request->hasFile('image')) {
            if ($dhamma->image) {
                Storage::disk('public')->delete($dhamma->image);
            }
            $validated['image'] = $request->file('image')->store('dhammas', 'public');
        }

        // Handle multiple images - use first image as main image
        if ($request->hasFile('images') && count($request->file('images')) > 0) {
            if ($dhamma->image) {
                Storage::disk('public')->delete($dhamma->image);
            }
            $images = $request->file('images');
            $firstImage = $images[0];
            $validated['image'] = $firstImage->store('dhammas', 'public');
        }

        $dhamma->update($validated);
        
        if ($dhamma->image) {
            $dhamma->image = url('storage/' . $dhamma->image);
        }

        // Send notification to public users when dhamma talk is updated
        try {
            $notificationService = new NotificationService();
            $notificationService->notifyNewDhamma($dhamma);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send dhamma notification: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Dhamma talk updated successfully',
            'data' => $dhamma,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $dhamma = Dhamma::findOrFail($id);
        
        if ($dhamma->image) {
            Storage::disk('public')->delete($dhamma->image);
        }

        $dhamma->delete();

        return response()->json([
            'message' => 'Dhamma talk deleted successfully',
        ]);
    }
}

