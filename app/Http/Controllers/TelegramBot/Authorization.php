<?php

namespace App\Http\Controllers\TelegramBot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;
use App\Models\User;


class Authorization extends Controller
{
    public static function auth($payload)
    {
      $data = json_decode($payload->data);


      if (isset($data->user_id) and $payload->chat_id === $data->user_id) {
        $phone = Str::after($data->phone_number, '+');

        $user = User::find($payload->user_id);
        if ($user->phone_number != $phone) {
          $user->phone_number = $phone;
          $user->save();
          Log::info('phone updated');
        }

        Log::info( $payload->data );
        // Log::info( gettype($payload->data) );
        // Log::info( gettype($data) );

        $text = '✅ Номер телефону ' . $phone . ' успішно додано до користувача';

      } else {
        $text = '⚠️ Помилка: ' . PHP_EOL . ' Відправте власний номер телефону!';
      }
      // send message
      Telegram::bot()->sendMessage([
        'chat_id' => $payload->chat_id,
        'text' => $text,
      ]);
    }
}
