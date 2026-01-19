<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biography;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BiographyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $biographies = Biography::latest()->get()->map(function ($biography) {
    //         if ($biography->image) {
    //             $biography->image = asset('storage/' . $biography->image);
    //         }
    //         // Add view count
    //         try {
    //             $biography->views_count = $biography->views()->count();
    //         } catch (\Exception $e) {
    //             $biography->views_count = 0;
    //         }
    //         return $biography;
    //     });
    //     return response()->json(['data' => $biographies]);
    // }

    public function index()
{
    $biographies = Biography::withCount('views')
        ->latest()
        ->paginate(10);

    // Transform collection without breaking pagination
    $biographies->getCollection()->transform(function ($biography) {
        if ($biography->image) {
            $biography->image = asset('storage/' . $biography->image);
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
        ]
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Convert empty strings to null for nullable fields
        $request->merge([
            'birth_year' => $request->input('birth_year') === '' ? null : $request->input('birth_year'),
            'sangha_entry_year' => $request->input('sangha_entry_year') === '' ? null : $request->input('sangha_entry_year'),
            'disciples' => $request->input('disciples') === '' ? null : $request->input('disciples'),
            'teaching_monastery' => $request->input('teaching_monastery') === '' ? null : $request->input('teaching_monastery'),
            'sangha_dhamma' => $request->input('sangha_dhamma') === '' ? null : $request->input('sangha_dhamma'),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_year' => 'nullable|string',
            'sangha_entry_year' => 'nullable|string',
            'disciples' => 'nullable|string',
            'teaching_monastery' => 'nullable|string|max:255',
            'sangha_dhamma' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('biographies', 'public');
        }

        $biography = Biography::create($validated);
        
        if ($biography->image) {
            $biography->image = asset('storage/' . $biography->image);
        }

        // Send notification to public users
        try {
            $notificationService = new NotificationService();
            $notificationService->notifyNewBiography($biography);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send biography notification: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Biography created successfully',
            'data' => $biography,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $biography = Biography::findOrFail($id);
        if ($biography->image) {
            $biography->image = asset('storage/' . $biography->image);
        }
        // Add view count
        try {
            $biography->views_count = $biography->views()->count();
        } catch (\Exception $e) {
            $biography->views_count = 0;
        }
        return response()->json(['data' => $biography]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $biography = Biography::findOrFail($id);

        // Convert empty strings to null for nullable fields
        $request->merge([
            'birth_year' => $request->input('birth_year') === '' ? null : $request->input('birth_year'),
            'sangha_entry_year' => $request->input('sangha_entry_year') === '' ? null : $request->input('sangha_entry_year'),
            'disciples' => $request->input('disciples') === '' ? null : $request->input('disciples'),
            'teaching_monastery' => $request->input('teaching_monastery') === '' ? null : $request->input('teaching_monastery'),
            'sangha_dhamma' => $request->input('sangha_dhamma') === '' ? null : $request->input('sangha_dhamma'),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_year' => 'nullable|string',
            'sangha_entry_year' => 'nullable|string',
            'disciples' => 'nullable|string',
            'teaching_monastery' => 'nullable|string|max:255',
            'sangha_dhamma' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($biography->image) {
                Storage::disk('public')->delete($biography->image);
            }
            $validated['image'] = $request->file('image')->store('biographies', 'public');
        }

        $biography->update($validated);
        
        if ($biography->image) {
            $biography->image = asset('storage/' . $biography->image);
        }

        // Send notification to public users when biography is updated
        try {
            $notificationService = new NotificationService();
            $notificationService->notifyNewBiography($biography);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send biography notification: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Biography updated successfully',
            'data' => $biography,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $biography = Biography::findOrFail($id);
        
        if ($biography->image) {
            Storage::disk('public')->delete($biography->image);
        }

        $biography->delete();

        return response()->json([
            'message' => 'Biography deleted successfully',
        ]);
    }
}

