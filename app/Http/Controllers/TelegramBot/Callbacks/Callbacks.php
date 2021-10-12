<?php

namespace App\Http\Controllers\TelegramBot\Callbacks;

use App\Http\Controllers\TelegramBot\Router;
use Telegram;
use Illuminate\Support\Facades\Log; //Log


class Callbacks extends Router
{
    public static function index($payload)
    {
      Telegram::bot()->sendMessage([
        'chat_id' => $payload->chat_id,
        'text' => 'Це Callback',
      ]);
      Log::info('is a callback (callbacks)');

    }
}
