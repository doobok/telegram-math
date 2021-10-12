<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Router;
use Telegram;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;

class Commands extends Router
{
    public static function index($payload)
    {
      $command = Str::of($payload->data)->explode('_');
      Log::debug($command[0]);

      switch ($command[0]) {
        case '/start':    StartCommand::go($payload);     break;
        case '/help':     HelpCommand::go($payload);      break;
        case '/test':     TestCommand::go($payload);      break;

        default:
            Telegram::bot()->sendMessage([
              'chat_id' => $payload->chat_id,
              'text' => __('bot.unknown-command'),
            ]);
          break;
      }
    }
}
