<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::with('admin')
            ->orderBy('order')
            ->latest()
            ->get()
            ->map(function ($banner) {
                if ($banner->image) {
                    $banner->image = url('storage/' . $banner->image);
                }
                return $banner;
            });
        
        return response()->json(['data' => $banners]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Merge request data to convert empty strings to null for nullable fields
        $requestData = $request->all();
        if (isset($requestData['order']) && $requestData['order'] === '') {
            $requestData['order'] = null;
        }
        if (isset($requestData['is_active'])) {
            // Convert string booleans to actual booleans
            if ($requestData['is_active'] === 'true' || $requestData['is_active'] === '1' || $requestData['is_active'] === 1) {
                $requestData['is_active'] = true;
            } elseif ($requestData['is_active'] === 'false' || $requestData['is_active'] === '0' || $requestData['is_active'] === 0) {
                $requestData['is_active'] = false;
            }
        }
        $request->merge($requestData);

        $validated = $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $validated['admin_id'] = $request->user()->id;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['order'] = $validated['order'] ?? 0;

        $banner = Banner::create($validated);
        
        if ($banner->image) {
            $banner->image = url('storage/' . $banner->image);
        }

        return response()->json([
            'message' => 'Banner created successfully',
            'data' => $banner,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $banner = Banner::with('admin')->findOrFail($id);
        
        if ($banner->image) {
            $banner->image = url('storage/' . $banner->image);
        }
        
        return response()->json(['data' => $banner]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        // Merge request data to convert empty strings to null for nullable fields
        $requestData = $request->all();
        if (isset($requestData['order']) && $requestData['order'] === '') {
            $requestData['order'] = null;
        }
        if (isset($requestData['is_active'])) {
            // Convert string booleans to actual booleans
            if ($requestData['is_active'] === 'true' || $requestData['is_active'] === '1' || $requestData['is_active'] === 1) {
                $requestData['is_active'] = true;
            } elseif ($requestData['is_active'] === 'false' || $requestData['is_active'] === '0' || $requestData['is_active'] === 0) {
                $requestData['is_active'] = false;
            }
        }
        $request->merge($requestData);

        $validated = $request->validate([
            'image' => 'nullable|image|max:5120',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validated);
        
        if ($banner->image) {
            $banner->image = url('storage/' . $banner->image);
        }

        return response()->json([
            'message' => 'Banner updated successfully',
            'data' => $banner,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return response()->json([
            'message' => 'Banner deleted successfully',
        ]);
    }
}

