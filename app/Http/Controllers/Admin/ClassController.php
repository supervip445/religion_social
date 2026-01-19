<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $classes = SchoolClass::with(['academicYear', 'classTeacher', 'creator'])->latest()->get();
    //     return response()->json(['data' => $classes]);
    // }

    public function index()
{
    $classes = SchoolClass::with(['academicYear', 'classTeacher', 'creator'])
        ->latest()
        ->paginate(10);

    return response()->json([
        'success' => true,
        'data' => $classes->items(),
        'pagination' => [
            'current_page' => $classes->currentPage(),
            'per_page' => $classes->perPage(),
            'total' => $classes->total(),
            'last_page' => $classes->lastPage(),
            'has_more_pages' => $classes->hasMorePages(),
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
            'code' => 'required|string|max:255|unique:classes,code',
            'grade_level' => 'required|integer|min:1',
            'section' => 'nullable|string|max:10',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_teacher_id' => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = Auth::id();
        $class = SchoolClass::create($validated);
        $class->load(['academicYear', 'classTeacher', 'creator']);

        return response()->json(['data' => $class], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $class = SchoolClass::with(['academicYear', 'classTeacher', 'creator', 'students', 'subjects'])->findOrFail($id);
        return response()->json(['data' => $class]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:classes,code,' . $id,
            'grade_level' => 'required|integer|min:1',
            'section' => 'nullable|string|max:10',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_teacher_id' => 'nullable|exists:users,id',
        ]);

        $class->update($validated);
        $class->load(['academicYear', 'classTeacher', 'creator']);

        return response()->json(['data' => $class]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $class = SchoolClass::findOrFail($id);
        $class->delete();

        return response()->json(['message' => 'Class deleted successfully']);
    }
}

