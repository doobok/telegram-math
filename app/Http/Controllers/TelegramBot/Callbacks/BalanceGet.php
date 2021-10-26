<?php

namespace App\Http\Controllers\TelegramBot\Callbacks;

use App\Http\Controllers\TelegramBot\Callbacks\Callbacks;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TelegramBot\Authorization;
use App\Models\Sending;
use App\Jobs\TelegramOutbox;

class BalanceGet extends Callbacks
{
    public static function go($payload)
    {
        $user = User::find($payload->user_id);

        $data = Http::withHeaders([
            'Authorization' => 'Bearer ' . Authorization::getToken(),
        ])->get(config('telegram.host.cli_url') . '/api/v2/usr-balance', [
            'phone' => $user->phone_number,
            'role' => $user->role,
            'external_id' => $user->external_id,
        ]);
        Log::info($data);
        if ($data['success']) {
            $text = __('bot.you-balance', [
                'balance' => $data['balance']
            ]);
        } else {
            $text = __('bot.auth-no-access');
        }

        $sending = Sending::create([
            'chat_id' => $payload->chat_id,
            'text' => $text,
        ]);
        TelegramOutbox::dispatch($sending);
    }
}
