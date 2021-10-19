<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Commands\Commands;
use App\Http\Controllers\TelegramBot\Authorization;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use App\Models\User;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\Sending;
use App\Jobs\TelegramOutbox;

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

      Log::info('test is Ok');

      // $data = Http::withHeaders([
      //     'Authorization' => 'Bearer ' . 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiMTU3OGQxY2QxYzk5ZWI3YTVmMDgxYmMxYTYzYWIwZjc2MWZiZGNjZjEyNTc4Mjg5YzI1N2E5YjQ0MGY2NWM4Zjk4NzRhMzE2MzA2MDNhNTAiLCJpYXQiOjE2MzQ1NTEzNzAsIm5iZiI6MTYzNDU1MTM3MCwiZXhwIjoxNjY2MDg3MzcwLCJzdWIiOiIiLCJzY29wZXMiOlsiKiJdfQ.szmB5sMBKxx1cczQBUlV-AnIn-5y0vaoGAygaRU8YnFWhbP6kXbanHBe37gOFW6idXKywWqgAMO8_eA_k4PUmBt_-sI_YbYOdob0LAxjR7OKgSQWZ6wFK7tNThtuelHqUsSc5LQDMTjkuwFTdo-7kCRaQ1FfmknzNJjNH2-Qgp2JWw6Z--kOIxdh1cBtKkYrNiL3JaqF4iDxJAoTwAADMHsUQAawZ4mOrfgGYXigfVBuVql-8KHmo32eWSpkzURdq9h44-4m2_SXFdXjnZ9TbM0bOtKMYo55ikN8lBgQ8M4QbLqb-aGwl4smbTl8R6Fxmy_76u6t0cOMfa1t06FFt-H6nMwnYFmPSvUWvZN2EKn9AhJTsIa-PA1rGwMCkp68qzDZ3q3ww3mHlWpEDilslWTwXn-_9DiFu_ZROfaY32liEFI7Ky5-yBESY-neGpziQePgMoHMs_FBDiDzpi3FSqY_rizLlcd7gGt59V1vwOjBFNo8BpA_1TyexML9q8BfOad9N5O3yMv4-kk4NtVu7xZdntUuVsp49vMlrsD8wjNorfrQkw_pJSyFOoJiXoNA8i3wuS8D7s7OcCa1P49SNuBhDbXPTgEpVoDdNOdTGC39LIj6jnEUnMcEwECNrUvNFHbYa_e5ozaEq1E_jw1L3OAP-rZ33gJA6sn7SCyOHOY',
      // ])->get(config('telegram.host.cli_url') . '/api/v2/test');
      //
      // Log::info($data);


            // $keyboard = [
            //   [
            //     [ 'text' => 'Тестова кнопка', 'callback_data' => 'something', ],
            //   ],
            // ];
            //
            // $inline_keyboard = [
            //   'inline_keyboard' => $keyboard,
            // ];

            $keyboard = [
              'inline_keyboard' => [
                [
                  [ 'text' => 'Тестова кнопка', 'callback_data' => 'something', ],
                  [ 'text' => 'Тестова кнопка 2', 'callback_data' => 'something', ],
                  [ 'text' => 'Тестова кнопка 3', 'callback_data' => 'something', ],
                ],
              ]];

        $sending = Sending::create([
          'chat_id' => $payload->chat_id,
          'text' => 'Test message',
          'keyboard' => json_encode($keyboard),
        ]);
        TelegramOutbox::dispatch($sending);



        // Telegram::sendMessage([
        //   // Telegram::bot()->sendMessage([
        //     'chat_id' => $payload->chat_id,
        //     'text' => 'Test message',
        //     // 'reply_markup' => Keyboard::make([
        //     //   'inline_keyboard' => $keyboard,
        //     // ]),
        //   ]);

          Log::debug('test finish');


    }
}
