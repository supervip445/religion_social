<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerText;
use Illuminate\Http\Request;

class BannerTextController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bannerTexts = BannerText::with('admin')
            ->latest()
            ->get();
        
        return response()->json(['data' => $bannerTexts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['admin_id'] = $request->user()->id;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $bannerText = BannerText::create($validated);

        return response()->json([
            'message' => 'Banner text created successfully',
            'data' => $bannerText,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bannerText = BannerText::with('admin')->findOrFail($id);
        return response()->json(['data' => $bannerText]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bannerText = BannerText::findOrFail($id);

        $validated = $request->validate([
            'text' => 'required|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $bannerText->update($validated);

        return response()->json([
            'message' => 'Banner text updated successfully',
            'data' => $bannerText,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bannerText = BannerText::findOrFail($id);
        $bannerText->delete();

        return response()->json([
            'message' => 'Banner text deleted successfully',
        ]);
    }
}

