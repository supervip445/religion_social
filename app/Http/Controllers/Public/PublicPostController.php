<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ViewService;
use Illuminate\Http\Request;

class PublicPostController extends Controller
{
    /**
     * Get all published posts
     */
    // public function index()
    // {
    //     try {
    //         $posts = Post::with('category')
    //             ->where('status', 'published')
    //             ->latest()
    //             ->get();
            
    //         $posts = $posts->map(function ($post) {
    //             // Handle image URL
    //             if ($post->image) {
    //                 $post->image = url('storage/' . $post->image);
    //             }
                
    //             // Safely get likes and comments counts
    //             $post->likes_count = 0;
    //             $post->dislikes_count = 0;
    //             $post->comments_count = 0;
                
    //             try {
    //                 $likes = $post->likes()->get();
    //                 $comments = $post->comments()->where('is_approved', true)->get();
    //                 $post->likes_count = $likes->where('type', 'like')->count();
    //                 $post->dislikes_count = $likes->where('type', 'dislike')->count();
    //                 $post->comments_count = $comments->count();
    //             } catch (\Exception $e) {
    //                 // Silently fail and keep counts at 0
    //             }
                
    //             return $post;
    //         });

    //         return response()->json(['data' => $posts]);
    //     } catch (\Exception $e) {
    //         \Log::error('PublicPostController index error: ' . $e->getMessage());
    //         \Log::error('Stack trace: ' . $e->getTraceAsString());
    //         return response()->json([
    //             'error' => 'Failed to fetch posts',
    //             'message' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine()
    //         ], 500);
    //     }
    // }

    public function index()
{
    try {
        $posts = Post::with('category')
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
        $posts->getCollection()->transform(function ($post) {

            // Image full URL
            if ($post->image) {
                $post->image = url('storage/' . $post->image);
            }

            return $post;
        });

        return response()->json([
            'success' => true,
            'message' => 'Posts fetched successfully',
            'data' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
                'has_more_pages' => $posts->hasMorePages(),
            ],
        ]);

    } catch (\Exception $e) {

        \Log::error('PublicPostController index error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Failed to fetch posts',
        ], 500);
    }
}


    /**
     * Get single post
     */
    public function show(Request $request, $id)
    {
        try {
            $post = Post::with('category')->findOrFail($id);
            
            if ($post->status !== 'published') {
                return response()->json(['message' => 'Post not found'], 404);
            }

            // Track view
            $viewService = new ViewService();
            $viewService->trackView(Post::class, $post->id, $request);

            if ($post->image) {
                $post->image = url('storage/' . $post->image);
            }

            // Safely get likes and comments counts
            $post->likes_count = 0;
            $post->dislikes_count = 0;
            $post->comments_count = 0;
            $post->views_count = 0;
            
            try {
                $likes = $post->likes()->get();
                $comments = $post->comments()->where('is_approved', true)->get();
                $post->likes_count = $likes->where('type', 'like')->count();
                $post->dislikes_count = $likes->where('type', 'dislike')->count();
                $post->comments_count = $comments->count();
                $post->views_count = $post->views()->count();
            } catch (\Exception $e) {
                // Silently fail
            }

            return response()->json(['data' => $post]);
        } catch (\Exception $e) {
            \Log::error('PublicPostController show error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to fetch post',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
