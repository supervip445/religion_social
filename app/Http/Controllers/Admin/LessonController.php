<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $lessons = Lesson::with(['class', 'subject', 'teacher'])->latest()->get()->map(function ($lesson) {
    //         // Add view count
    //         try {
    //             $lesson->views_count = $lesson->views()->count();
    //         } catch (\Exception $e) {
    //             $lesson->views_count = 0;
    //         }
    //         return $lesson;
    //     });
    //     return response()->json(['data' => $lessons]);
    // }

    public function index()
{
    $lessons = Lesson::with(['class', 'subject', 'teacher'])
        ->withCount('views')        // views_count (DB level)
        ->latest()                  // ORDER BY created_at DESC
        ->paginate(10);             // 10 per page

    return response()->json([
        'success' => true,
        'message' => 'Lessons fetched successfully',
        'data' => $lessons->items(),
        'pagination' => [
            'current_page' => $lessons->currentPage(),
            'per_page' => $lessons->perPage(),
            'total' => $lessons->total(),
            'last_page' => $lessons->lastPage(),
            'has_more_pages' => $lessons->hasMorePages(),
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
            'description' => $request->input('description') === '' ? null : $request->input('description'),
            'content' => $request->input('content') === '' ? null : $request->input('content'),
            'lesson_date' => $request->input('lesson_date') === '' ? null : $request->input('lesson_date'),
            'duration_minutes' => $request->input('duration_minutes') === '' ? null : $request->input('duration_minutes'),
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'lesson_date' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachmentPaths = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('lessons/attachments', 'public');
                $attachmentPaths[] = $path;
            }
            $validated['attachments'] = $attachmentPaths;
        }

        $lesson = Lesson::create($validated);
        $lesson->load(['class', 'subject', 'teacher']);

        // Send notification if lesson is published
        if ($lesson->status === 'published') {
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyNewLesson($lesson);
            } catch (\Exception $e) {
                \Log::error('Failed to send lesson notification: ' . $e->getMessage());
            }
        }

        return response()->json(['data' => $lesson], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $lesson = Lesson::with(['class', 'subject', 'teacher'])->findOrFail($id);
        
        // Convert attachment paths to full URLs
        if ($lesson->attachments) {
            $lesson->attachments = array_map(function ($path) {
                return asset('storage/' . $path);
            }, $lesson->attachments);
        }

        // Add view count
        try {
            $lesson->views_count = $lesson->views()->count();
        } catch (\Exception $e) {
            $lesson->views_count = 0;
        }

        return response()->json(['data' => $lesson]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        // Convert empty strings to null for nullable fields
        $request->merge([
            'description' => $request->input('description') === '' ? null : $request->input('description'),
            'content' => $request->input('content') === '' ? null : $request->input('content'),
            'lesson_date' => $request->input('lesson_date') === '' ? null : $request->input('lesson_date'),
            'duration_minutes' => $request->input('duration_minutes') === '' ? null : $request->input('duration_minutes'),
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'lesson_date' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        // Handle new file attachments
        if ($request->hasFile('attachments')) {
            // Delete old attachments if needed
            if ($lesson->attachments) {
                foreach ($lesson->attachments as $oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $attachmentPaths = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('lessons/attachments', 'public');
                $attachmentPaths[] = $path;
            }
            $validated['attachments'] = $attachmentPaths;
        }

        $oldStatus = $lesson->status;
        $newStatus = $validated['status'];

        $lesson->update($validated);
        $lesson->load(['class', 'subject', 'teacher']);

        // Send notification if lesson is being published (changed from draft to published)
        if ($newStatus === 'published' && $oldStatus !== 'published') {
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyNewLesson($lesson);
            } catch (\Exception $e) {
                \Log::error('Failed to send lesson notification: ' . $e->getMessage());
            }
        }

        return response()->json(['data' => $lesson]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        // Delete attachments
        if ($lesson->attachments) {
            foreach ($lesson->attachments as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $lesson->delete();

        return response()->json(['message' => 'Lesson deleted successfully']);
    }
}

