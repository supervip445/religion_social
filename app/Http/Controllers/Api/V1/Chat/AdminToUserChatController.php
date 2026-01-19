<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Models\ChatMessage;
use App\Models\User;
use App\Enums\UserType;
use App\Services\Notification\ChatSocketNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class AdminToUserChatController extends Controller
{
    public function __construct(private ChatSocketNotifier $chatNotifier)
    {
    }

    // public function index(Request $request)
    // {
    //     $player = $request->user();

    //     // Auto-assign agent if not set
    //     if (! $player->agent_id) {
    //         $admin = User::where('type', UserType::SuperAdmin->value)
    //             ->where('status', 1)
    //             ->first();
            
    //         if ($admin) {
    //             $player->agent_id = $admin->id;
    //             $player->save();
    //             $player->refresh(); // Refresh to get updated agent_id
    //         } else {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'No admin is available. Please contact support.',
    //             ], 422);
    //         }
    //     }

    //     $perPage = (int) $request->integer('per_page', 5);

    //     $messages = ChatMessage::query()
    //         ->forParticipants($player->agent_id, $player->id)
    //         ->latest('id')
    //         ->paginate($perPage);

    //     ChatMessage::query()
    //         ->forParticipants($player->agent_id, $player->id)
    //         ->whereNull('read_at')
    //         ->where('receiver_id', $player->id)
    //         ->update(['read_at' => now()]);

    //     $messages->getCollection()->load('sender');

    //     return ChatMessageResource::collection($messages);
    // }

    public function index(Request $request)
{
    $player = $request->user();

    if (! $player->agent_id) {
        $admin = User::where('type', UserType::SuperAdmin->value)
            ->where('status', 1)
            ->first();

        if (! $admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'No admin is available.',
            ], 422);
        }

        $player->agent_id = $admin->id;
        $player->save();
        $player->refresh();
    }

    $perPage = (int) $request->integer('per_page', 5);

    $messages = ChatMessage::query()
        ->forParticipants($player->agent_id, $player->id)
        ->latest('id')
        ->paginate($perPage);

    $messages->getCollection()->load('sender');

    return ChatMessageResource::collection($messages);
}



    public function store(Request $request)
{
    $player = $request->user();

    if (! $player->agent_id) {
        $admin = User::where('type', UserType::SuperAdmin->value)
            ->where('status', 1)
            ->first();

        if (! $admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'No admin available',
            ], 422);
        }

        $player->update(['agent_id' => $admin->id]);
        $player->refresh();
    }

    $validated = $request->validate([
        'message' => ['nullable', 'string', 'max:2000'],
        'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,mp4,mov,webm,avi', 'max:51200'],
    ]);

    $text = trim((string) ($validated['message'] ?? ''));
    $hasMedia = $request->hasFile('media');

    if ($text === '' && ! $hasMedia) {
        return response()->json([
            'status' => 'error',
            'message' => 'Message cannot be empty',
        ], 422);
    }

    $mediaPath = null;
    $mediaMime = null;
    $mediaType = null;
    if ($hasMedia) {
        $file = $request->file('media');
        $mediaMime = $file->getMimeType();
        $mediaType = str_starts_with($mediaMime, 'video/') ? 'video' : 'image';
        $mediaPath = $file->store('chat/media', 'public');
    }

    $message = ChatMessage::create([
        'agent_id' => $player->agent_id,
        'player_id' => $player->id,
        'sender_id' => $player->id,
        'receiver_id' => $player->agent_id,
        'sender_type' => ChatMessage::SENDER_PLAYER,
        'message' => $text,
        'media_path' => $mediaPath,
        'media_type' => $mediaType,
        'media_mime' => $mediaMime,
    ]);

    $message->load('sender');

    // 🔔 Socket + Push
    $this->chatNotifier->notify($message);

    return (new ChatMessageResource($message))
        ->response()
        ->setStatusCode(201);
}


    // public function store(Request $request)
    // {
    //     $player = $request->user();

    //     //Log::info($request->all());

    //     // Auto-assign agent if not set
    //     if (! $player->agent_id) {
    //         $admin = User::where('type', UserType::SuperAdmin->value)
    //             ->where('status', 1)
    //             ->first();
            
    //         if ($admin) {
    //             $player->agent_id = $admin->id;
    //             $player->save();
    //             $player->refresh(); // Refresh to get updated agent_id
    //         } else {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'No admin is available. Please contact support.',
    //             ], 422);
    //         }
    //     }

    //     $validated = $request->validate([
    //         'message' => ['required', 'string', 'max:2000'],
    //     ]);

    //     $messageBody = trim($validated['message']);

    //     if ($messageBody === '') {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Message cannot be empty.',
    //         ], 422);
    //     }

    //     $message = ChatMessage::create([
    //         'agent_id' => $player->agent_id,
    //         'player_id' => $player->id,
    //         'sender_id' => $player->id,
    //         'receiver_id' => $player->agent_id,
    //         'sender_type' => ChatMessage::SENDER_PLAYER,
    //         'message' => $messageBody,
    //     ]);

    //     $message->load('sender');

    //     $this->chatNotifier->notify($message);

    //     return (new ChatMessageResource($message))
    //         ->response()
    //         ->setStatusCode(201);
    // }
}

