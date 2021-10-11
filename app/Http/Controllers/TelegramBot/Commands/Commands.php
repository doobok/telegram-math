<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Router;
use Telegram;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;
use App\Http\Controllers\TelegramBot\Commands\StartCommand;

class Commands extends Router
{
    public static function index($payload)
    {
      $command = Str::of($payload->data)->explode('_');

      switch ($command[0]) {
        case '/start':
            StartCommand::start($payload);
          break;
        case '/help':
          // code...
          break;

        default:
            Telegram::bot()->sendMessage([
              'chat_id' => $payload->chat_id,
              'text' => 'Невідома команда 😕',
            ]);
          break;
      }
    }
}
