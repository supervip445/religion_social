<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $subjects = Subject::with('creator')->latest()->get();
    //     return response()->json(['data' => $subjects]);
    // }

    public function index()
{
    $subjects = Subject::with('creator')
        ->latest()
        ->paginate(10);

    return response()->json([
        'success' => true,
        'data' => $subjects->items(),
        'pagination' => [
            'current_page' => $subjects->currentPage(),
            'per_page' => $subjects->perPage(),
            'total' => $subjects->total(),
            'last_page' => $subjects->lastPage(),
            'has_more_pages' => $subjects->hasMorePages(),
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
            'code' => 'required|string|max:255|unique:subjects,code',
            'description' => 'nullable|string',
            'credit_hours' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $subject = Subject::create($validated);
        $subject->load('creator');

        return response()->json(['data' => $subject], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $subject = Subject::with(['creator', 'classes', 'teachers'])->findOrFail($id);
        return response()->json(['data' => $subject]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:subjects,code,' . $id,
            'description' => 'nullable|string',
            'credit_hours' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $subject->update($validated);
        $subject->load('creator');

        return response()->json(['data' => $subject]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully']);
    }
}

