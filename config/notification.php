<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Server
    |--------------------------------------------------------------------------
    |
    | URL and optional API key for the dedicated Socket.IO notification server.
    |
    */
    'server_url' => env('NOTIFICATION_SERVER_URL', 'https://maxwin688.site'),

    'server_endpoint' => env('NOTIFICATION_SERVER_ENDPOINT', '/'),

    'server_key' => env('NOTIFICATION_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Static Recipients
    |--------------------------------------------------------------------------
    |
    | Comma separated list of user IDs that should always receive notifications,
    | regardless of the player initiating the event.
    |
    */
    'static_recipient_ids' => array_values(array_filter(
        array_map(function ($id) {
            $id = (int) trim($id);

            return $id > 0 ? $id : null;
        }, explode(',', (string) env('NOTIFICATION_STATIC_RECIPIENT_IDS', '')))
    )),

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Frontend routes that should be opened when the admin clicks a notification.
    |
    */
    'routes' => [
        'deposit' => env('NOTIFICATION_DEPOSIT_ROUTE', '/admin/deposit-requests'),
        'withdraw' => env('NOTIFICATION_WITHDRAW_ROUTE', '/admin/withdraw-requests'),
    ],
];

