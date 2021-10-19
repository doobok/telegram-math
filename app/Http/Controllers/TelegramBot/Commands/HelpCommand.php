<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Commands\Commands;
use App\Models\User;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;
use App\Models\Sending;
use App\Jobs\TelegramOutbox;

class HelpCommand extends Commands
{
    public static function go($payload)
    {
      $user = User::find($payload->user_id);

      $sending = Sending::create([
        'chat_id' => $payload->chat_id,
        'text' => __('bot.help-message', [
          'username' => $user->first_name,
          'botname' => config('telegram.bots.mybot.username'),
        ]),
      ]);
      TelegramOutbox::dispatch($sending);
    }
}
