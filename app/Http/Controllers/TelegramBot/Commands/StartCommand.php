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
    public static function start($payload)
    {
      $user = User::find($payload->user_id);

      if (!$user->phone_number) {

        $text = sprintf('👋 Привіт, %s! ' . PHP_EOL . 'Мене звати БОТ, я можу допомогти тобі, але для початку відправ мені свій номер телефону', $user->first_name);

            $keyboard = [
              [
                [ 'text' => '📞 Відправити номер телефону', 'request_contact' => true, ],
              ],
            ];

          Telegram::bot()->sendMessage([
            'chat_id' => $payload->chat_id,
            'text' => $text,
            'reply_markup' => Keyboard::make([
              'keyboard' => $keyboard,
              'resize_keyboard' => true,
              'one_time_keyboard' => true
            ]),
          ]);

      } else {
        $text = sprintf('👋 Привіт, %s! ' . PHP_EOL . 'Мене звати БОТ, я володію корисними навиками, я готовий допомогти тобі відповідно до привілегій твоєї ролі.' . PHP_EOL . ' Твоя поточна роль: %s ', $user->first_name, $user->role);

        Telegram::bot()->sendMessage([
          'chat_id' => $payload->chat_id,
          'text' => $text,
        ]);
      }

    }
}
