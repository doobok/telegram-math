<?php

namespace App\Http\Controllers\TelegramBot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
          // получаем токен
          $token = self::getToken();
          $external_user = self::getUser($phone);

          $user->save();
        }
        $text = 'success';
      } else {
        $text = 'error';
      }
      Log::debug($text);
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
          Log::debug($expires);
          Cache::put($key, $token, $expires);
      }
      return $token;
    }

    public static function getUser($phone)
    {
      Log::info('is phone area');

      $data = Http::withHeaders([
          'Authorization' => 'Bearer ' . 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiMTU3OGQxY2QxYzk5ZWI3YTVmMDgxYmMxYTYzYWIwZjc2MWZiZGNjZjEyNTc4Mjg5YzI1N2E5YjQ0MGY2NWM4Zjk4NzRhMzE2MzA2MDNhNTAiLCJpYXQiOjE2MzQ1NTEzNzAsIm5iZiI6MTYzNDU1MTM3MCwiZXhwIjoxNjY2MDg3MzcwLCJzdWIiOiIiLCJzY29wZXMiOlsiKiJdfQ.szmB5sMBKxx1cczQBUlV-AnIn-5y0vaoGAygaRU8YnFWhbP6kXbanHBe37gOFW6idXKywWqgAMO8_eA_k4PUmBt_-sI_YbYOdob0LAxjR7OKgSQWZ6wFK7tNThtuelHqUsSc5LQDMTjkuwFTdo-7kCRaQ1FfmknzNJjNH2-Qgp2JWw6Z--kOIxdh1cBtKkYrNiL3JaqF4iDxJAoTwAADMHsUQAawZ4mOrfgGYXigfVBuVql-8KHmo32eWSpkzURdq9h44-4m2_SXFdXjnZ9TbM0bOtKMYo55ikN8lBgQ8M4QbLqb-aGwl4smbTl8R6Fxmy_76u6t0cOMfa1t06FFt-H6nMwnYFmPSvUWvZN2EKn9AhJTsIa-PA1rGwMCkp68qzDZ3q3ww3mHlWpEDilslWTwXn-_9DiFu_ZROfaY32liEFI7Ky5-yBESY-neGpziQePgMoHMs_FBDiDzpi3FSqY_rizLlcd7gGt59V1vwOjBFNo8BpA_1TyexML9q8BfOad9N5O3yMv4-kk4NtVu7xZdntUuVsp49vMlrsD8wjNorfrQkw_pJSyFOoJiXoNA8i3wuS8D7s7OcCa1P49SNuBhDbXPTgEpVoDdNOdTGC39LIj6jnEUnMcEwECNrUvNFHbYa_e5ozaEq1E_jw1L3OAP-rZ33gJA6sn7SCyOHOY',
      ])->get(config('telegram.host.cli_url') . '/api/v2/test');

      // $user = $data->json();
      Log::info('is success user get');
      return 'ok';
      // code...
    }
}
