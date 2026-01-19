<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Get comments for a post/dhamma
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'required|string|in:App\Models\Post,App\Models\Dhamma,App\Models\Biography,App\Models\Lesson',
            'commentable_id' => 'required|integer',
        ]);

        $comments = Comment::where('commentable_type', $validated['commentable_type'])
            ->where('commentable_id', $validated['commentable_id'])
            ->where('is_approved', true)
            ->latest()
            ->get();

        return response()->json(['data' => $comments]);
    }

    /**
     * Store a new comment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'commentable_type' => 'required|string|in:App\Models\Post,App\Models\Dhamma,App\Models\Biography,App\Models\Lesson',
            'commentable_id' => 'required|integer',
            'comment' => 'required|string|max:2000',
        ]);

        $userIdentifier = $request->ip() . '_' . ($request->header('User-Agent') ?? 'unknown');
        $userIdentifier = substr(md5($userIdentifier), 0, 32);

        $comment = Comment::create([
            'commentable_type' => $validated['commentable_type'],
            'commentable_id' => $validated['commentable_id'],
            'name' => null,
            'email' => null,
            'comment' => $validated['comment'],
            'user_identifier' => $userIdentifier,
            'is_approved' => true, // Auto-approve for now, can be changed to false for moderation
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => $comment,
        ], 201);
    }
}

