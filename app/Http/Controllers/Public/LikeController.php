<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Toggle like/dislike
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'likeable_type' => 'required|string|in:App\Models\Post,App\Models\Dhamma',
            'likeable_id' => 'required|integer',
            'type' => 'required|in:like,dislike',
        ]);

        $userIdentifier = $request->ip() . '_' . ($request->header('User-Agent') ?? 'unknown');
        $userIdentifier = substr(md5($userIdentifier), 0, 32); // Hash for privacy

        // Check if user already liked/disliked this item
        $existingLike = Like::where('likeable_type', $validated['likeable_type'])
            ->where('likeable_id', $validated['likeable_id'])
            ->where('user_identifier', $userIdentifier)
            ->first();

        if ($existingLike) {
            if ($existingLike->type === $validated['type']) {
                // Same type clicked - remove the like/dislike
                $existingLike->delete();
                return response()->json([
                    'message' => 'Removed',
                    'action' => 'removed',
                ]);
            } else {
                // Different type - update
                $existingLike->update(['type' => $validated['type']]);
                return response()->json([
                    'message' => 'Updated',
                    'action' => 'updated',
                    'type' => $validated['type'],
                ]);
            }
        } else {
            // Create new like/dislike
            Like::create([
                'likeable_type' => $validated['likeable_type'],
                'likeable_id' => $validated['likeable_id'],
                'user_identifier' => $userIdentifier,
                'type' => $validated['type'],
            ]);

            return response()->json([
                'message' => 'Added',
                'action' => 'added',
                'type' => $validated['type'],
            ]);
        }
    }

    /**
     * Get like/dislike counts
     */
    public function counts(Request $request)
    {
        $validated = $request->validate([
            'likeable_type' => 'required|string|in:App\Models\Post,App\Models\Dhamma',
            'likeable_id' => 'required|integer',
        ]);

        $likes = Like::where('likeable_type', $validated['likeable_type'])
            ->where('likeable_id', $validated['likeable_id'])
            ->where('type', 'like')
            ->count();

        $dislikes = Like::where('likeable_type', $validated['likeable_type'])
            ->where('likeable_id', $validated['likeable_id'])
            ->where('type', 'dislike')
            ->count();

        $userIdentifier = $request->ip() . '_' . ($request->header('User-Agent') ?? 'unknown');
        $userIdentifier = substr(md5($userIdentifier), 0, 32);

        $userLike = Like::where('likeable_type', $validated['likeable_type'])
            ->where('likeable_id', $validated['likeable_id'])
            ->where('user_identifier', $userIdentifier)
            ->first();

        return response()->json([
            'likes' => $likes,
            'dislikes' => $dislikes,
            'user_action' => $userLike ? $userLike->type : null,
        ]);
    }
}

