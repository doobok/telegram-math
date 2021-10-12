<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Commands\Commands;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use App\Models\User;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;

class HelpCommand extends Commands
{
    public static function go($payload)
    {
      $user = User::find($payload->user_id);

        Telegram::bot()->sendMessage([
          'chat_id' => $payload->chat_id,
          'text' => __('bot.help-message', [
            'username' => $user->first_name,
            'botname' => config('telegram.bots.mybot.username'),
          ]),
        ]);

    }
}
