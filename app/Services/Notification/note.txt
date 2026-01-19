<?php

namespace App\Services\Notification;

use App\Enums\UserType;
use App\Models\DepositRequest;
use App\Models\User;
use App\Models\WithDrawRequest;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version4X;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdminSocketNotifier
{
    public function notifyDeposit(DepositRequest $deposit): void
    {
        $deposit->loadMissing('user');

        if (! $deposit->user) {
            Log::warning('Deposit notification skipped because user relation is missing', [
                'deposit_request_id' => $deposit->id,
            ]);

            return;
        }

        $this->dispatch('deposit', $deposit->user, [
            'request_id' => $deposit->id,
            'amount' => $deposit->amount,
            'reference' => $deposit->refrence_no,
            'agent_payment_type_id' => $deposit->agent_payment_type_id,
        ]);
    }

    public function notifyWithdraw(WithDrawRequest $withdraw): void
    {
        $withdraw->loadMissing('user');

        if (! $withdraw->user) {
            Log::warning('Withdraw notification skipped because user relation is missing', [
                'withdraw_request_id' => $withdraw->id,
            ]);

            return;
        }

        $this->dispatch('withdraw', $withdraw->user, [
            'request_id' => $withdraw->id,
            'amount' => $withdraw->amount,
            'payment_type_id' => $withdraw->payment_type_id,
        ]);
    }

    private function dispatch(string $type, User $player, array $meta): void
    {
        $recipients = $this->resolveRecipients($player);

        if ($recipients->isEmpty()) {
            Log::warning('No admin recipients resolved for socket notification', [
                'player_id' => $player->id,
                'type' => $type,
            ]);

            return;
        }

        foreach ($recipients as $recipientId) {
            $payload = [
                'to_user_id' => $recipientId,
                'title' => $this->title($type),
                'body' => $this->body($type, $player, $meta),
                'notification_data' => [
                    'route' => $this->route($type),
                    'type' => $type,
                    'player_id' => $player->id,
                    'player_name' => $player->user_name ?? $player->name,
                    'amount' => $meta['amount'] ?? null,
                    'meta' => $meta,
                ],
            ];

            $this->send($payload);
        }
    }

    private function send(array $payload): void
    {
        $serverUrl = (string) config('notification.server_url');

        if ($serverUrl === '') {
            Log::warning('Notification server URL missing', ['payload' => $payload]);

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
            $client->emit('send_noti', $payload);
            $client->disconnect();
        } catch (\Throwable $th) {
            Log::error('Socket emit failed', [
                'server' => $serverUrl,
                'error' => $th->getMessage(),
                'payload' => $payload,
            ]);
        }
    }

    private function resolveRecipients(User $player): Collection
    {
        return collect()
            ->merge($this->staticRecipients())
            ->when($player->agent_id, fn (Collection $ids) => $ids->push($player->agent_id))
            ->merge($this->ownerRecipientIds())
            ->unique()
            ->filter();
    }

    private function staticRecipients(): Collection
    {
        return collect(config('notification.static_recipient_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0);
    }

    private function ownerRecipientIds(): Collection
    {
        return Cache::remember('notification.owner_ids', now()->addMinutes(10), function () {
            return User::query()
                ->whereIn('type', [
                    UserType::Owner->value,
                    UserType::SystemWallet->value,
                ])
                ->pluck('id');
        });
    }

    private function title(string $type): string
    {
        return match ($type) {
            'deposit' => 'New Deposit Request',
            'withdraw' => 'New Withdraw Request',
            default => 'New Notification',
        };
    }

    private function body(string $type, User $player, array $meta): string
    {
        $amount = number_format((float) ($meta['amount'] ?? 0));
        $name = $player->user_name ?? $player->name;

        return match ($type) {
            'deposit' => "{$name} requested a deposit of {$amount} Ks.",
            'withdraw' => "{$name} requested a withdraw of {$amount} Ks.",
            default => "{$name} triggered {$type}.",
        };
    }

    private function route(string $type): string
    {
        return config("notification.routes.{$type}", '/');
    }
}

