<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicYears = AcademicYear::with('creator')->latest()->get();
        return response()->json(['data' => $academicYears]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:academic_years,code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        // If setting this as active, deactivate all others
        if ($request->input('is_active', false)) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $validated['created_by'] = Auth::id();
        $academicYear = AcademicYear::create($validated);
        $academicYear->load('creator');

        return response()->json(['data' => $academicYear], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $academicYear = AcademicYear::with(['creator', 'classes'])->findOrFail($id);
        return response()->json(['data' => $academicYear]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $academicYear = AcademicYear::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:academic_years,code,' . $id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        // If setting this as active, deactivate all others
        if ($request->input('is_active', false)) {
            AcademicYear::where('is_active', true)->where('id', '!=', $id)->update(['is_active' => false]);
        }

        $academicYear->update($validated);
        $academicYear->load('creator');

        return response()->json(['data' => $academicYear]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->delete();

        return response()->json(['message' => 'Academic year deleted successfully']);
    }
}

