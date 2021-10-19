<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Commands\Commands;
use App\Models\User;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;
use App\Models\Sending;
use App\Jobs\TelegramOutbox;

class StartCommand extends Commands
{
    public static function go($payload)
    {
      $user = User::find($payload->user_id);

      if (!$user->phone_number) {

            $keyboard = [
              'resize_keyboard' => true,
              'one_time_keyboard' => true,
              'keyboard' => [
                [
                  [ 'text' => __('bot.button-send-phone'), 'request_contact' => true, ],
                ],
              ]];

          $sending = Sending::create([
            'chat_id' => $payload->chat_id,
            'text' => __('bot.hello', [
              'username' => $user->first_name,
              'botname' => config('telegram.bots.mybot.username'),
            ]),
            'keyboard' => json_encode($keyboard),
          ]);
          TelegramOutbox::dispatch($sending);

      } else {
        $sending = Sending::create([
          'chat_id' => $payload->chat_id,
          'text' => __('bot.hello-with-role', [
            'username' => $user->first_name,
            'role' => __('bot.role-' . $user->role),
          ]),
        ]);
        TelegramOutbox::dispatch($sending);
      }

    }
}
