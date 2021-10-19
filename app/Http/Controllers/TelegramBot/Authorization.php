<?php

namespace App\Http\Controllers\TelegramBot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Sending;
use App\Jobs\TelegramOutbox;

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
        $text = __('bot.auth-is-present');
        if (!$user->external_id) {
          $user->phone_number = $phone;
          // получаем токен
          $token = self::getToken();
          $external_user = self::getUser($phone, $payload->chat_id);

          if ($external_user === 'error' || $external_user === 'auth-no-user') {
            $sending = Sending::create([
               'chat_id' => $payload->chat_id,
               'text' => __('bot.' . $external_user),
             ]);
             TelegramOutbox::dispatch($sending);
             $user->save();
             return;
          } else {
            $user->role = $external_user['role'];
            $user->external_id = $external_user['user'];
            $text = __('bot.auth-success', ['role' => __('bot.role-' . $external_user['role']) ]);
          }
          $user->save();
        }
      } else {
        $text =  __('bot.auth-error');
      }
      $sending = Sending::create([
        'chat_id' => $payload->chat_id,
        'text' => $text,
        'keyboard' => 'remove',
      ]);
      TelegramOutbox::dispatch($sending);
    }

    public static function noAccess($chat_id)
    {
      $sending = Sending::create([
        'chat_id' => $chat_id,
        'text' => __('bot.auth-no-access'),
      ]);
      TelegramOutbox::dispatch($sending);
    }

    // Получаем глобальный токен
    public static function getToken()
    {
      // работаем с кэшем
      $key = '_global_token';
      $token = Cache::get($key);
      if($token === null) {
          $data = Http::asForm()->post(config('telegram.host.cli_url') . '/oauth/token', [
              'grant_type' => 'client_credentials',
              'client_id' => config('telegram.host.cli_id'),
              'client_secret' => config('telegram.host.cli_secret'),
          ]);
          $token = $data->json()['access_token'];
          $expires = $data->json()['expires_in'];
          Cache::put($key, $token, $expires);
      }
      return $token;
    }

    // Получаем внешнего пользователя
    public static function getUser($phone, $chat_id)
    {
      $data = Http::withHeaders([
          'Authorization' => 'Bearer ' . self::getToken(),
      ])->get(config('telegram.host.cli_url') . '/api/v2/auth-bot-user', [
          'phone' => $phone
        ]);

      $user = $data->json();
      if ($user) {
        if ($user['status'] === 'ok') {
          if ($user['role']) {
            return $user;
          } else {
            return 'auth-no-user';
          }
        } else {
          return 'error';
        }
      } else {
        return 'error';
      }
    }
}
