<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Commands\Commands;
use App\Http\Controllers\TelegramBot\Authorization;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use App\Models\User;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;

class TestCommand extends Commands
{
    public static function go($payload)
    {
      // получаем пользователя
      $user = User::find($payload->user_id);
      // проверяем полномочия
      if ($user->cannot('isAdmin', \App\Models\Message::class)) {
        return Authorization::noAccess($payload->chat_id);
      }

            $keyboard = [
              [
                [ 'text' => 'Тестова кнопка', 'callback_data' => 'something', ],
              ],
            ];

          Telegram::bot()->sendMessage([
            'chat_id' => $payload->chat_id,
            'text' => 'Test message',
            'reply_markup' => Keyboard::make([
              'inline_keyboard' => $keyboard,
            ]),
          ]);


    }
}
