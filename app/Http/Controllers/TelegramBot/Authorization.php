<?php

namespace App\Http\Controllers\TelegramBot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;
use App\Models\User;


class Authorization extends Controller
{
    public static function auth($payload)
    {
      // получаем данные
      $data = json_decode($payload->data);
      // проверяем сответствие контакта ID пользователя
      if (isset($data->user_id) and $payload->chat_id === $data->user_id) {
        $phone = Str::after($data->phone_number, '+');
        // получаем пользователя и добавляем номер телефона
        $user = User::find($payload->user_id);
        if ($user->phone_number != $phone) {
          $user->phone_number = $phone;
          /*
          дополнительные действия по определению роли пользователя
          */
          $user->save();
        }
        $text = 'success';
      } else {
        $text = 'error';
      }
      // send message
      Telegram::bot()->sendMessage([
        'chat_id' => $payload->chat_id,
        'text' => __('bot.auth-' . $text),
        'reply_markup' => Keyboard::remove(),
      ]);
    }

    public static function noAccess($chat_id)
    {
      Telegram::bot()->sendMessage([
        'chat_id' => $chat_id,
        'text' => __('bot.auth-no-access'),
      ]);
    }
}
