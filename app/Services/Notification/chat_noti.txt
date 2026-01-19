<?php

namespace App\Services\Notification;

use App\Models\ChatMessage;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version4X;
use Illuminate\Support\Facades\Log;

class ChatSocketNotifier
{
    public function notify(ChatMessage $message): void
    {
        $message->loadMissing(['sender:id,name,user_name', 'player:id,user_name,name']);

        $recipientId = $message->receiver_id;

        if (! $recipientId) {
            Log::warning('Chat notification skipped because receiver_id missing', ['message_id' => $message->id]);

            return;
        }

        $payload = [
            'to_user_id' => $recipientId,
            'title' => $message->sender?->user_name ?? 'New Message',
            'body' => $message->message,
            'notification_data' => [
                'type' => 'chat',
                'agent_id' => $message->agent_id,
                'player_id' => $message->player_id,
                'player_user_name' => $message->player?->user_name,
                'sender_id' => $message->sender_id,
                'sender_type' => $message->sender_type,
                'message_id' => $message->id,
                'message' => $message->message,
                'created_at' => optional($message->created_at)->toIso8601String(),
            ],
        ];

        $this->send($payload);
    }

    private function send(array $payload): void
    {
        $serverUrl = (string) config('notification.server_url');
        $event = config('notification.events.chat', config('notification.events.notification', 'send_noti'));

        if ($serverUrl === '') {
            Log::warning('Chat notification server URL missing', ['payload' => $payload]);

            return;
        }

        try {
            $client = new Client(new Version4X($serverUrl, [
                'connect_timeout' => 5,
                'context' => [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ]));

            $client->connect();
            $client->emit($event, $payload);
            $client->disconnect();
        } catch (\Throwable $th) {
            Log::error('Chat socket emit failed', [
                'server' => $serverUrl,
                'event' => $event,
                'error' => $th->getMessage(),
                'payload' => $payload,
            ]);
        }
    }
}

