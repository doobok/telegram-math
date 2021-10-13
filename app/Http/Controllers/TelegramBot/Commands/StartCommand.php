<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Commands\Commands;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use App\Models\User;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;

class StartCommand extends Commands
{
    public static function go($payload)
    {
      $user = User::find($payload->user_id);

      if (!$user->phone_number) {

            $keyboard = [
              [
                [ 'text' => __('bot.button-send-phone'), 'request_contact' => true, ],
              ],
            ];

          Telegram::bot()->sendMessage([
            'chat_id' => $payload->chat_id,
            'text' => __('bot.hello', [
              'username' => $user->first_name,
              'botname' => config('telegram.bots.mybot.username'),
            ]),
            'reply_markup' => Keyboard::make([
              'keyboard' => $keyboard,
              'resize_keyboard' => true,
              'one_time_keyboard' => true
            ]),
          ]);

      } else {
        Telegram::bot()->sendMessage([
          'chat_id' => $payload->chat_id,
          'text' => __('bot.hello-with-role', [
            'username' => $user->first_name,
            'role' => __('bot.role-' . $user->role),
          ]),
        ]);
      }

    }
}
