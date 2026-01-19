<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FCMNotification;
use Kreait\Firebase\Messaging\Topic;

class NotificationService
{
    protected $serverUrl;
    protected $serverEndpoint;
    protected $firebaseMessaging;

    public function __construct()
    {
        $this->serverUrl = config('notification.server_url', 'https://maxwin688.site');
        $this->serverEndpoint = config('notification.server_endpoint', '/');
        
        // Initialize Firebase Messaging if credentials are available
        $this->initializeFirebase();
    }

    /**
     * Initialize Firebase Messaging
     */
    protected function initializeFirebase()
    {
        try {
            $credentialsPath = storage_path('app/firebase/notification-88e7c-c50770d57b15.json');
            
            if (file_exists($credentialsPath)) {
                $factory = (new Factory)
                    ->withServiceAccount($credentialsPath);
                
                $this->firebaseMessaging = $factory->createMessaging();
                Log::info('✅ Firebase Messaging initialized');
            } else {
                Log::warning('⚠️ Firebase credentials file not found: ' . $credentialsPath);
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to initialize Firebase: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to public users via Socket.IO
     * Since we're using Socket.IO, we'll use a simple HTTP approach
     * or integrate with Socket.IO client library
     * 
     * For now, we'll use a direct Socket.IO connection via HTTP
     */
    public function sendNotification($type, $title, $body, $route = null, $data = [])
    {
        try {
            // Get static recipient IDs (public users - we'll use a special ID like 'public' or 'all')
            $staticRecipients = config('notification.static_recipient_ids', []);
            
            // For public notifications, we'll send to all online public users
            // Since public users don't have accounts, we'll use a special user_id like 'public' or broadcast to all
            // The notification server should handle broadcasting to all users
            
            // We'll need to use Socket.IO client to emit the event
            // For simplicity, we'll use HTTP to trigger the notification
            // But Socket.IO requires WebSocket connection, so we need a Socket.IO client library
            
            // Alternative: Create a simple HTTP endpoint on notification server that accepts POST requests
            // and emits the notification via Socket.IO
            
            // For now, let's use a Socket.IO client approach
            // We'll use elephantio/elephant.io if available, or create a simple HTTP trigger
            
            // For public users, we'll broadcast to all online users
            // The notification server should handle 'public' or 'all' as a special user_id
            // Or we can send to all static recipients
            $staticRecipients = config('notification.static_recipient_ids', []);
            
            // If no static recipients, use 'public' as a special broadcast ID
            $recipientId = !empty($staticRecipients) ? $staticRecipients[0] : 'public';
            
            $notificationData = [
                'to_user_id' => $recipientId, // For public, this should be handled by server to broadcast
                'title' => $title,
                'body' => $body,
                'notification_data' => array_merge([
                    'route' => $route,
                    'type' => $type,
                ], $data),
            ];

            // Send notification via Socket.IO (for real-time when app is open)
            $this->sendViaSocketIO($notificationData);
            
            // Send FCM push notification (for when app is closed or in background)
            $this->sendViaFCM($title, $body, $route, array_merge(['type' => $type], $data));
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification via Socket.IO client or HTTP endpoint
     */
    protected function sendViaSocketIO($data)
    {
        // Option 1: Use HTTP endpoint (if notification server has one)
        $endpoint = $this->serverUrl . '/api/notify';
        
        try {
            Log::info('📤 Sending notification to server', [
                'endpoint' => $endpoint,
                'data' => $data,
            ]);
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, $data);
            
            if ($response->successful()) {
                Log::info('✅ Notification sent successfully', [
                    'response' => $response->json(),
                ]);
                return true;
            } else {
                Log::error('❌ Notification server returned error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to send notification via HTTP', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Option 2: Use Socket.IO client (elephantio) if available
        if (class_exists('\ElephantIO\Client')) {
            try {
                $client = new \ElephantIO\Client(
                    \ElephantIO\Engine\SocketIO\Version4X::class,
                    $this->serverUrl,
                    [
                        'context' => [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                            ],
                        ],
                    ]
                );
                
                $client->initialize();
                $client->emit('send_noti', $data);
                $client->close();
                return true;
            } catch (\Exception $e) {
                Log::error('Socket.IO connection failed: ' . $e->getMessage());
            }
        }
        
        return false;
    }

    /**
     * Send FCM push notification to all public users (using topic)
     */
    protected function sendViaFCM($title, $body, $route = null, $data = [])
    {
        if (!$this->firebaseMessaging) {
            Log::warning('⚠️ Firebase Messaging not initialized, skipping FCM notification');
            return false;
        }

        try {
            // Use topic 'public' to send to all users subscribed to this topic
            // Flutter app should subscribe to 'public' topic on initialization
            $topic = 'public';
            
            $notification = FCMNotification::create($title, $body);
            
            // Prepare data payload
            $messageData = array_merge([
                'route' => $route,
                'type' => $data['type'] ?? 'general',
            ], $data);
            
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification)
                ->withData($messageData)
                ->withAndroidConfig([
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'default',
                    ],
                ]);
            
            $this->firebaseMessaging->send($message);
            
            Log::info('✅ FCM push notification sent successfully', [
                'topic' => $topic,
                'title' => $title,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Failed to send FCM notification: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send FCM notification to specific FCM token
     */
    public function sendFCMToToken($fcmToken, $title, $body, $route = null, $data = [])
    {
        if (!$this->firebaseMessaging) {
            Log::warning('⚠️ Firebase Messaging not initialized');
            return false;
        }

        try {
            $notification = FCMNotification::create($title, $body);
            
            $messageData = array_merge([
                'route' => $route,
                'type' => $data['type'] ?? 'general',
            ], $data);
            
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification($notification)
                ->withData($messageData)
                ->withAndroidConfig([
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'default',
                    ],
                ]);
            
            $this->firebaseMessaging->send($message);
            
            Log::info('✅ FCM notification sent to token', [
                'token' => substr($fcmToken, 0, 20) . '...',
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Failed to send FCM to token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification for new post
     */
    public function notifyNewPost($post)
    {
        return $this->sendNotification(
            'post',
            'ပို့စ်အသစ်',
            $post->title,
            '/posts/' . $post->id,
            ['post_id' => $post->id]
        );
    }

    /**
     * Send notification for new dhamma talk
     */
    public function notifyNewDhamma($dhamma)
    {
        return $this->sendNotification(
            'dhamma',
            'ဓမ္မတရားတော် အသစ်',
            $dhamma->title,
            '/dhammas/' . $dhamma->id,
            ['dhamma_id' => $dhamma->id]
        );
    }

    /**
     * Send notification for new biography
     */
    public function notifyNewBiography($biography)
    {
        return $this->sendNotification(
            'biography',
            'အတ္ထုပ္ပတ္တိ အသစ်',
            $biography->name,
            '/biographies/' . $biography->id,
            ['biography_id' => $biography->id]
        );
    }

    /**
     * Send notification for new lesson
     */
    public function notifyNewLesson($lesson)
    {
        return $this->sendNotification(
            'lesson',
            'သင်ခန်းစာ အသစ်',
            $lesson->title,
            '/lessons/' . $lesson->id,
            ['lesson_id' => $lesson->id]
        );
    }

    /**
     * Send notification for new donation
     */
    public function notifyNewDonation($donation)
    {
        return $this->sendNotification(
            'donation',
            'လှူဒါန်းမှု အသစ်',
            $donation->donor_name . ' - ' . number_format($donation->amount) . ' MMK',
            '/donations',
            ['donation_id' => $donation->id]
        );
    }

    /**
     * Send notification for new monastery building donation
     */
    public function notifyNewMonasteryBuildingDonation($donation)
    {
        return $this->sendNotification(
            'monastery_building_donation',
            'ကျောင်းတိုက် လှူဒါန်းမှု အသစ်',
            $donation->donor_name . ' - ' . number_format($donation->amount) . ' MMK',
            '/monastery-building-donations',
            ['building_donation_id' => $donation->id]
        );
    }

    // chat push noti
    public function ChatPushNoti()
    {
        
    }
}

