<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\ViewService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PublicLessonController extends Controller
{
    /**
     * Get all published lessons
     */
    // public function index()
    // {
    //     try {
    //         $lessons = Lesson::with(['class', 'subject', 'teacher'])
    //             ->where('status', 'published')
    //             ->latest()
    //             ->get();
            
    //         $lessons = $lessons->map(function ($lesson) {
    //             // Handle attachment URLs
    //             if ($lesson->attachments && is_array($lesson->attachments)) {
    //                 $lesson->attachments = array_map(function ($path) {
    //                     return url('storage/' . $path);
    //                 }, $lesson->attachments);
    //             }
                
    //             // Safely get likes and comments counts
    //             $lesson->likes_count = 0;
    //             $lesson->dislikes_count = 0;
    //             $lesson->comments_count = 0;
                
    //             try {
    //                 $likes = $lesson->likes()->get();
    //                 $comments = $lesson->comments()->where('is_approved', true)->get();
    //                 $lesson->likes_count = $likes->where('type', 'like')->count();
    //                 $lesson->dislikes_count = $likes->where('type', 'dislike')->count();
    //                 $lesson->comments_count = $comments->count();
    //             } catch (\Exception $e) {
    //                 // Silently fail and keep counts at 0
    //             }
                
    //             return $lesson;
    //         });

    //         return response()->json(['data' => $lessons]);
    //     } catch (\Exception $e) {
    //         \Log::error('PublicLessonController index error: ' . $e->getMessage());
    //         return response()->json([
    //             'error' => 'Failed to fetch lessons',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function index()
{
    try {
        $lessons = Lesson::with(['class', 'subject', 'teacher'])
            ->where('status', 'published')
            ->withCount([
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
        $lessons->getCollection()->transform(function ($lesson) {

            // Handle attachment URLs
            if ($lesson->attachments && is_array($lesson->attachments)) {
                $lesson->attachments = array_map(function ($path) {
                    return url('storage/' . $path);
                }, $lesson->attachments);
            }

            return $lesson;
        });

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
            ],
        ]);

    } catch (\Exception $e) {

        \Log::error('PublicLessonController index error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Failed to fetch lessons',
        ], 500);
    }
}


    /**
     * Get single lesson
     */
    public function show(Request $request, $id)
    {
        try {
            $lesson = Lesson::with(['class', 'subject', 'teacher'])->findOrFail($id);
            
            if ($lesson->status !== 'published') {
                return response()->json(['message' => 'Lesson not found'], 404);
            }

            // Track view
            $viewService = new ViewService();
            $viewService->trackView(Lesson::class, $lesson->id, $request);

            // Handle attachment URLs
            if ($lesson->attachments && is_array($lesson->attachments)) {
                $lesson->attachments = array_map(function ($path) {
                    return url('storage/' . $path);
                }, $lesson->attachments);
            }

            // Safely get likes and comments counts
            $lesson->likes_count = 0;
            $lesson->dislikes_count = 0;
            $lesson->comments_count = 0;
            $lesson->views_count = 0;
            
            try {
                $likes = $lesson->likes()->get();
                $comments = $lesson->comments()->where('is_approved', true)->get();
                $lesson->likes_count = $likes->where('type', 'like')->count();
                $lesson->dislikes_count = $likes->where('type', 'dislike')->count();
                $lesson->comments_count = $comments->count();
                $lesson->views_count = $lesson->views()->count();
            } catch (\Exception $e) {
                // Silently fail
            }

            return response()->json(['data' => $lesson]);
        } catch (ModelNotFoundException $e) {
            // Lesson not found - return 404 instead of 500
            return response()->json([
                'error' => 'Lesson not found',
                'message' => 'The requested lesson does not exist.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('PublicLessonController show error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch lesson',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

