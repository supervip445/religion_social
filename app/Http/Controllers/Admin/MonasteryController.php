<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Monastery;
use Illuminate\Http\Request;

class MonasteryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $monasteries = Monastery::orderBy('order')->get();
    //     return response()->json(['data' => $monasteries]);
    // }

    public function index()
{
    $monasteries = Monastery::orderBy('order')
        ->paginate(10);

    return response()->json([
        'success' => true,
        'data' => $monasteries->items(),
        'pagination' => [
            'current_page' => $monasteries->currentPage(),
            'per_page' => $monasteries->perPage(),
            'total' => $monasteries->total(),
            'last_page' => $monasteries->lastPage(),
            'has_more_pages' => $monasteries->hasMorePages(),
        ],
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:monastery,building',
            'monastery_name' => 'nullable|string|max:255',
            'monks' => 'required|integer|min:0',
            'novices' => 'required|integer|min:0',
            'order' => 'nullable|integer',
        ]);

        // Calculate total
        $validated['total'] = $validated['monks'] + $validated['novices'];

        // Set default order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = Monastery::max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        $monastery = Monastery::create($validated);

        return response()->json([
            'message' => 'Monastery created successfully',
            'data' => $monastery,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $monastery = Monastery::findOrFail($id);
        return response()->json(['data' => $monastery]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $monastery = Monastery::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:monastery,building',
            'monastery_name' => 'nullable|string|max:255',
            'monks' => 'required|integer|min:0',
            'novices' => 'required|integer|min:0',
            'order' => 'nullable|integer',
        ]);

        // Calculate total
        $validated['total'] = $validated['monks'] + $validated['novices'];

        $monastery->update($validated);

        return response()->json([
            'message' => 'Monastery updated successfully',
            'data' => $monastery,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $monastery = Monastery::findOrFail($id);
        $monastery->delete();

        return response()->json([
            'message' => 'Monastery deleted successfully',
        ]);
    }
}

