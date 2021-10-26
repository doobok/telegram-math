<?php

namespace App\Http\Controllers\TelegramBot\Callbacks;

use App\Http\Controllers\TelegramBot\Callbacks\BalanceGet;
use App\Http\Controllers\TelegramBot\Router;
use App\Jobs\TelegramOutbox;
use App\Models\Sending;
use Illuminate\Support\Facades\Log;


class Callbacks extends Router
{
    public static function index($payload)
    {
        switch ($payload->data) {
            case 'get-balance':
                BalanceGet::go($payload);
                break;

            default:
                $sending = Sending::create([
                    'chat_id' => $payload->chat_id,
                    'text' => __('bot.unknown-command'),
                ]);
                TelegramOutbox::dispatch($sending);
                break;
        }

    }
}
