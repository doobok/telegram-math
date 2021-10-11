<?php

namespace App\Http\Controllers\TelegramBot\Requests;

use App\Http\Controllers\TelegramBot\Router;
use Telegram;
use Illuminate\Support\Facades\Log; //Log


class Requests extends Router
{
    public static function index($payload)
    {
      Telegram::bot()->sendMessage([
        'chat_id' => $payload->chat_id,
        'text' => 'Це звичайний текст',
      ]);
      Log::info('is a request (request)');

      // code...
    }
}
