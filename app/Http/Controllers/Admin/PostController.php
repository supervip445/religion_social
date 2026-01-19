<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $posts = Post::with('category')->latest()->get()->map(function ($post) {
    //         if ($post->image) {
    //             $post->image = asset('storage/' . $post->image);
    //         }
    //         // Add view count
    //         try {
    //             $post->views_count = $post->views()->count();
    //         } catch (\Exception $e) {
    //             $post->views_count = 0;
    //         }
    //         return $post;
    //     });
    //     return response()->json(['data' => $posts]);
    // }

    public function index()
{
    $posts = Post::with('category')
        ->withCount('views')              // views_count
        ->latest()                        // ORDER BY created_at DESC
        ->paginate(10);                   // 10 posts per page

    // Transform response data
    $posts->getCollection()->transform(function ($post) {

        // Image full URL
        if ($post->image) {
            $post->image = asset('storage/' . $post->image);
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
        ]
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        // Handle single image (for backward compatibility)
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // Handle multiple images - use first image as main image
        if ($request->hasFile('images') && count($request->file('images')) > 0) {
            $images = $request->file('images');
            $firstImage = $images[0];
            $validated['image'] = $firstImage->store('posts', 'public');
            
            // Store additional images in a separate directory or JSON field
            // For now, we'll just use the first image as the main image
            // You can extend this to store multiple images in a JSON field or separate table
        }

        $validated['slug'] = Str::slug($validated['title']);

        $post = Post::create($validated);
        $post->load('category');
        
        if ($post->image) {
            $post->image = url('storage/' . $post->image);
        }

        // Send notification to public users if post is published
        if ($post->status === 'published') {
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyNewPost($post);
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send post notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::with('category')->findOrFail($id);
        if ($post->image) {
            $post->image = asset('storage/' . $post->image);
        }
        // Add view count
        try {
            $post->views_count = $post->views()->count();
        } catch (\Exception $e) {
            $post->views_count = 0;
        }
        return response()->json(['data' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Convert empty strings to null for nullable fields
        $request->merge([
            'category_id' => $request->category_id === '' ? null : $request->category_id,
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        // Handle single image (for backward compatibility)
        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // Handle multiple images - use first image as main image
        if ($request->hasFile('images') && count($request->file('images')) > 0) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $images = $request->file('images');
            $firstImage = $images[0];
            $validated['image'] = $firstImage->store('posts', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);

        // Store old status to check if we need to send notification
        $oldStatus = $post->status;
        $newStatus = $validated['status'];

        $post->update($validated);
        $post->load('category');
        
        if ($post->image) {
            $post->image = asset('storage/' . $post->image);
        }

        // Send notification if post is being published (changed from draft to published)
        // Only send if it wasn't already published (to avoid duplicate notifications)
        if ($newStatus === 'published' && $oldStatus !== 'published') {
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyNewPost($post);
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send post notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}

