<?php

namespace App\Services\Notification;

use App\Models\ChatMessage;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version4X;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FCMNotification;

class ChatSocketNotifier
{
    protected $firebase;

    public function __construct()
    {
        $this->initFirebase();
    }

    protected function initFirebase(): void
    {
        try {
            $path = storage_path('app/firebase/notification-88e7c-c50770d57b15.json');

            if (file_exists($path)) {
                $this->firebase = (new Factory)
                    ->withServiceAccount($path)
                    ->createMessaging();
            }
        } catch (\Throwable $e) {
            Log::error('FCM init failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Main entry: realtime + push
     */
    public function notify(ChatMessage $message): void
    {
        $message->loadMissing('sender:id,name,user_name');

        if (! $message->receiver_id) {
            Log::warning('Chat notify skipped: receiver_id missing', [
                'message_id' => $message->id,
            ]);
            return;
        }

        $title = $message->sender?->user_name ?? 'New Message';
        $body  = $message->message;

        $payload = [
            'to_user_id' => $message->receiver_id,
            'title' => $title,
            'body' => $body,
            'notification_data' => [
                'type' => 'chat',
                 'route' => '/chat',   
                'message_id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_type' => $message->sender_type,
                'created_at' => optional($message->created_at)->toIso8601String(),
            ],
        ];

        // 1️⃣ Realtime
        $this->sendSocket($payload);

        // 2️⃣ Push
        $this->sendPush(
            receiverId: $message->receiver_id,
            title: $title,
            body: $body,
            data: [
                'type' => 'chat',
                'route' => '/chat',
                'message_id' => (string) $message->id,
            ]
        );
    }

    /**
     * Socket.IO emit
     */
    protected function sendSocket(array $payload): void
    {
        $serverUrl  = rtrim(config('notification.server_url'), '/');
        $endpoint   = trim(config('notification.server_endpoint', '/'), '/');
        $event      = 'send_noti';

        $url = $endpoint ? "{$serverUrl}/{$endpoint}" : $serverUrl;

        if (! $url) {
            Log::warning('Socket server URL missing');
            return;
        }

        try {
            $client = new Client(
                new Version4X($url, [
                    'connect_timeout' => 5,
                    'context' => [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ],
                ])
            );

            $client->connect();
            $client->emit($event, $payload);
            $client->disconnect();

        } catch (\Throwable $e) {
            Log::error('Socket emit failed', [
                'error' => $e->getMessage(),
                'url' => $url,
                'payload' => $payload,
            ]);
        }
    }

    /**
     * Firebase Push (topic user_{id})
     */
    protected function sendPush(
        int|string $receiverId,
        string $title,
        string $body,
        array $data = []
    ): void {
        if (! $this->firebase) {
            return;
        }

        try {
            $topic = 'user_' . $receiverId;

            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(
                    FCMNotification::create($title, $body)
                )
                ->withData($data)
                ->withAndroidConfig([
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'default',
                    ],
                ]);

            $this->firebase->send($message);

        } catch (\Throwable $e) {
            Log::error('FCM send failed', [
                'receiver' => $receiverId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}


// correct start 

// namespace App\Services\Notification;

// use App\Models\ChatMessage;
// use ElephantIO\Client;
// use ElephantIO\Engine\SocketIO\Version4X;
// use Illuminate\Support\Facades\Log;
// use Kreait\Firebase\Factory;
// use Kreait\Firebase\Messaging\CloudMessage;
// use Kreait\Firebase\Messaging\Notification as FCMNotification;

// class ChatSocketNotifier
// {
//     protected $firebaseMessaging;

//     public function __construct()
//     {
//         $this->initFirebase();
//     }

//     /**
//      * Initialize Firebase Messaging
//      */
//     protected function initFirebase(): void
//     {
//         try {
//             $credentials = storage_path('app/firebase/notification-88e7c-c50770d57b15.json');

//             if (file_exists($credentials)) {
//                 $factory = (new Factory)->withServiceAccount($credentials);
//                 $this->firebaseMessaging = $factory->createMessaging();
//             }
//         } catch (\Throwable $e) {
//             Log::error('Firebase init failed', [
//                 'error' => $e->getMessage(),
//             ]);
//         }
//     }

//     /**
//      * Main notify method (Socket + Push)
//      */
//     public function notify(ChatMessage $message): void
//     {
//         $message->loadMissing([
//             'sender:id,name,user_name',
//         ]);

//         $receiverId = $message->receiver_id;

//         if (! $receiverId) {
//             Log::warning('Chat notify skipped: receiver_id missing', [
//                 'message_id' => $message->id,
//             ]);
//             return;
//         }

//         $title = $message->sender?->user_name ?? 'New Message';
//         $body  = $message->message;

//         /**
//          * 1️⃣ SOCKET.IO (Realtime)
//          */
//         $this->sendSocket([
//             'to_user_id' => $receiverId,
//             'title' => $title,
//             'body' => $body,
//             'notification_data' => [
//                 'type' => 'chat',
//                 'message_id' => $message->id,
//                 'sender_id' => $message->sender_id,
//                 'sender_type' => $message->sender_type,
//                 'created_at' => optional($message->created_at)->toIso8601String(),
//             ],
//         ]);

//         /**
//          * 2️⃣ PUSH (FCM)
//          */
//         $this->sendPush(
//             receiverId: $receiverId,
//             title: $title,
//             body: $body,
//             data: [
//                 'type' => 'chat',
//                 'message_id' => (string) $message->id,
//             ]
//         );
//     }

//     /**
//      * Emit Socket.IO event
//      */
//     protected function sendSocket(array $payload): void
//     {
//         $serverUrl = config('notification.server_url');
//         $event     = config('notification.events.chat', 'send_noti');

//         if (! $serverUrl) {
//             return;
//         }

//         try {
//             $client = new Client(
//                 new Version4X($serverUrl, [
//                     'connect_timeout' => 5,
//                     'context' => [
//                         'ssl' => [
//                             'verify_peer' => false,
//                             'verify_peer_name' => false,
//                         ],
//                     ],
//                 ])
//             );

//             $client->connect();
//             $client->emit($event, $payload);
//             $client->disconnect();

//         } catch (\Throwable $e) {
//             Log::error('Socket emit failed', [
//                 'error' => $e->getMessage(),
//                 'payload' => $payload,
//             ]);
//         }
//     }

//     /**
//      * Send Firebase Push Notification (Topic-based)
//      */
//     protected function sendPush(
//         int|string $receiverId,
//         string $title,
//         string $body,
//         array $data = []
//     ): void {
//         if (! $this->firebaseMessaging) {
//             return;
//         }

//         try {
//             $topic = 'user_' . $receiverId;

//             $message = CloudMessage::withTarget('topic', $topic)
//                 ->withNotification(
//                     FCMNotification::create($title, $body)
//                 )
//                 ->withData($data)
//                 ->withAndroidConfig([
//                     'priority' => 'high',
//                     'notification' => [
//                         'sound' => 'default',
//                         'channel_id' => 'default',
//                     ],
//                 ]);

//             $this->firebaseMessaging->send($message);

//         } catch (\Throwable $e) {
//             Log::error('FCM send failed', [
//                 'error' => $e->getMessage(),
//                 'receiver' => $receiverId,
//             ]);
//         }
//     }
// }

// correct end


// namespace App\Services\Notification;

// use App\Models\ChatMessage;
// use ElephantIO\Client;
// use ElephantIO\Engine\SocketIO\Version4X;
// use Illuminate\Support\Facades\Log;

// class ChatSocketNotifier
// {
//     public function notify(ChatMessage $message): void
//     {
//         $message->loadMissing(['sender:id,name,user_name', 'player:id,user_name,name']);

//         $recipientId = $message->receiver_id;

//         if (! $recipientId) {
//             Log::warning('Chat notification skipped because receiver_id missing', ['message_id' => $message->id]);

//             return;
//         }

//         $payload = [
//             'to_user_id' => $recipientId,
//             'title' => $message->sender?->user_name ?? 'New Message',
//             'body' => $message->message,
//             'notification_data' => [
//                 'type' => 'chat',
//                 'agent_id' => $message->agent_id,
//                 'player_id' => $message->player_id,
//                 'player_user_name' => $message->player?->user_name,
//                 'sender_id' => $message->sender_id,
//                 'sender_type' => $message->sender_type,
//                 'message_id' => $message->id,
//                 'message' => $message->message,
//                 'created_at' => optional($message->created_at)->toIso8601String(),
//             ],
//         ];

//         $this->send($payload);
//     }

//     private function send(array $payload): void
//     {
//         $serverUrl = (string) config('notification.server_url');
//         $event = config('notification.events.chat', config('notification.events.notification', 'send_noti'));

//         if ($serverUrl === '') {
//             Log::warning('Chat notification server URL missing', ['payload' => $payload]);

//             return;
//         }

//         try {
//             $client = new Client(new Version4X($serverUrl, [
//                 'connect_timeout' => 5,
//                 'context' => [
//                     'ssl' => [
//                         'verify_peer' => false,
//                         'verify_peer_name' => false,
//                     ],
//                 ],
//             ]));

//             $client->connect();
//             $client->emit($event, $payload);
//             $client->disconnect();
//         } catch (\Throwable $th) {
//             Log::error('Chat socket emit failed', [
//                 'server' => $serverUrl,
//                 'event' => $event,
//                 'error' => $th->getMessage(),
//                 'payload' => $payload,
//             ]);
//         }
//     }
// }



// namespace App\Services\Notification;

// use App\Models\ChatMessage;
// use App\Services\NotificationService;
// use ElephantIO\Client;
// use ElephantIO\Engine\SocketIO\Version4X;
// use Illuminate\Support\Facades\Log;

// class ChatSocketNotifier
// {
//     public function __construct(
//         private NotificationService $notificationService
//     ) {}

//     /**
//      * Notify chat message (Socket + Push)
//      */
//     public function notify(ChatMessage $message): void
//     {
//         $message->loadMissing([
//             'sender:id,name,user_name',
//             'player:id,user_name,name',
//         ]);

//         $receiverId = $message->receiver_id;

//         if (! $receiverId) {
//             Log::warning('Chat notify skipped: receiver_id missing', [
//                 'message_id' => $message->id,
//             ]);
//             return;
//         }

//         $title = $message->sender?->user_name ?? 'New Message';
//         $body  = $message->message;

//         /**
//          * -------------------------
//          * 1) REALTIME (Socket.IO)
//          * -------------------------
//          */
//         $socketPayload = [
//             'to_user_id' => $receiverId,
//             'title' => $title,
//             'body' => $body,
//             'notification_data' => [
//                 'type' => 'chat',
//                 'message_id' => $message->id,
//                 'agent_id' => $message->agent_id,
//                 'player_id' => $message->player_id,
//                 'sender_id' => $message->sender_id,
//                 'sender_type' => $message->sender_type,
//                 'created_at' => optional($message->created_at)->toIso8601String(),
//             ],
//         ];

//         $this->sendSocket($socketPayload);

//         /**
//          * -------------------------
//          * 2) PUSH NOTIFICATION (FCM)
//          * -------------------------
//          * Topic-based: user_{id}
//          */
//         $this->notificationService->sendViaFCM(
//             title: $title,
//             body: $body,
//             route: '/chat',
//             data: [
//                 'type' => 'chat',
//                 'message_id' => (string) $message->id,
//                 'sender_id' => (string) $message->sender_id,
//             ],
//             topic: 'user_' . $receiverId
//         );
//     }

//     /**
//      * Emit Socket.IO event
//      */
//     private function sendSocket(array $payload): void
//     {
//         $serverUrl = (string) config('notification.server_url');
//         $event     = config('notification.events.chat', 'send_noti');

//         if ($serverUrl === '') {
//             Log::warning('Socket server URL missing', $payload);
//             return;
//         }

//         try {
//             $client = new Client(
//                 new Version4X($serverUrl, [
//                     'connect_timeout' => 5,
//                     'context' => [
//                         'ssl' => [
//                             'verify_peer' => false,
//                             'verify_peer_name' => false,
//                         ],
//                     ],
//                 ])
//             );

//             $client->connect();
//             $client->emit($event, $payload);
//             $client->disconnect();

//         } catch (\Throwable $e) {
//             Log::error('Socket emit failed', [
//                 'error' => $e->getMessage(),
//                 'payload' => $payload,
//             ]);
//         }
//     }
// }
