<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\Notification\ChatSocketNotifier;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function __construct(private ChatSocketNotifier $chatNotifier)
    {
    }

    /**
     * Get all users assigned to the admin
     */
    // public function getUsers(Request $request)
    // {
    //     $admin = $request->user();

    //     // Get all users assigned to this admin (where agent_id = admin.id)
    //     $users = User::where('agent_id', $admin->id)
    //         ->where('type', 'user')
    //         ->select('id', 'name', 'user_name', 'phone', 'email', 'profile')
    //         ->get();

    //     // Get unread message counts for each user
    //     $usersWithUnreadCounts = $users->map(function ($user) use ($admin) {
    //         $unreadCount = ChatMessage::query()
    //             ->forParticipants($admin->id, $user->id)
    //             ->whereNull('read_at')
    //             ->where('receiver_id', $admin->id)
    //             ->count();

    //         // Get last message for preview
    //         $lastMessage = ChatMessage::query()
    //             ->forParticipants($admin->id, $user->id)
    //             ->latest('id')
    //             ->first();

    //         return [
    //             'id' => $user->id,
    //             'name' => $user->name,
    //             'user_name' => $user->user_name,
    //             'phone' => $user->phone,
    //             'email' => $user->email,
    //             'profile' => $user->profile,
    //             'unread_count' => $unreadCount,
    //             'last_message' => $lastMessage ? [
    //                 'message' => $lastMessage->message,
    //                 'created_at' => $lastMessage->created_at->toIso8601String(),
    //                 'sender_type' => $lastMessage->sender_type,
    //             ] : null,
    //         ];
    //     });

    //     return response()->json([
    //         'data' => $usersWithUnreadCounts,
    //     ]);
    // }

    public function getUsers(Request $request)
{
    $admin = $request->user();
    $perPage = $request->get('per_page', 20);

    $users = User::where('agent_id', $admin->id)
        ->where('type', 'user')
        ->select('id', 'name', 'user_name', 'phone', 'email', 'profile')
        ->paginate($perPage);

    $users->getCollection()->transform(function ($user) use ($admin) {

        $unreadCount = ChatMessage::query()
            ->forParticipants($admin->id, $user->id)
            ->whereNull('read_at')
            ->where('receiver_id', $admin->id)
            ->count();

        $lastMessage = ChatMessage::query()
            ->forParticipants($admin->id, $user->id)
            ->latest('id')
            ->first();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'user_name' => $user->user_name,
            'phone' => $user->phone,
            'email' => $user->email,
            'profile' => $user->profile,
            'unread_count' => $unreadCount,
            'last_message' => $lastMessage ? [
                'message' => $lastMessage->message,
                'created_at' => $lastMessage->created_at->toIso8601String(),
                'sender_type' => $lastMessage->sender_type,
            ] : null,
        ];
    });

    return response()->json([
        'data' => $users->items(),
        'pagination' => [
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'total' => $users->total(),
            'last_page' => $users->lastPage(),
            'has_more_pages' => $users->hasMorePages(),
        ],
    ]);
}


    /**
     * Get messages for a specific user conversation
     */
    // public function getMessages(Request $request, $userId)
    // {
    //     $admin = $request->user();

    //     // Verify user is assigned to this admin
    //     $user = User::where('id', $userId)
    //         ->where('agent_id', $admin->id)
    //         ->first();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'User not found or not assigned to you.',
    //         ], 404);
    //     }

    //     $perPage = (int) $request->integer('per_page', 20);

    //     $messages = ChatMessage::query()
    //         ->forParticipants($admin->id, $userId)
    //         ->latest('id')
    //         ->paginate($perPage);

    //     // Mark messages as read when admin views them
    //     ChatMessage::query()
    //         ->forParticipants($admin->id, $userId)
    //         ->whereNull('read_at')
    //         ->where('receiver_id', $admin->id)
    //         ->update(['read_at' => now()]);

    //     $messages->getCollection()->load('sender');

    //     return ChatMessageResource::collection($messages);
    // }

    public function getMessages(Request $request, $userId)
{
    $admin = $request->user();

    $user = User::where('id', $userId)
        ->where('agent_id', $admin->id)
        ->firstOrFail();

    $perPage = min(
        max((int) $request->integer('per_page', 20), 10),
        50
    );

    $messages = ChatMessage::query()
        ->forParticipants($admin->id, $userId)
        ->with('sender:id,name,profile')
        ->latest('id')
        ->paginate($perPage);

    ChatMessage::query()
        ->forParticipants($admin->id, $userId)
        ->whereNull('read_at')
        ->where('receiver_id', $admin->id)
        ->update(['read_at' => now()]);

    return ChatMessageResource::collection($messages);
}


    /**
     * Send a message to a specific user
     */
    public function sendMessage(Request $request, $userId)
    {
        $admin = $request->user();

        // Verify user is assigned to this admin
        $user = User::where('id', $userId)
            ->where('agent_id', $admin->id)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found or not assigned to you.',
            ], 404);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $messageBody = trim($validated['message']);

        if ($messageBody === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Message cannot be empty.',
            ], 422);
        }

        $message = ChatMessage::create([
            'agent_id' => $admin->id,
            'player_id' => $userId,
            'sender_id' => $admin->id,
            'receiver_id' => $userId,
            'sender_type' => ChatMessage::SENDER_AGENT,
            'message' => $messageBody,
        ]);

        $message->load('sender');

        $this->chatNotifier->notify($message);

        return (new ChatMessageResource($message))
            ->response()
            ->setStatusCode(201);
    }
}

