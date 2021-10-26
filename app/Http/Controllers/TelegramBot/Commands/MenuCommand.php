<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Commands\Commands;
use App\Models\User;
use Illuminate\Support\Facades\Log; //Log
use Illuminate\Support\Str;
use App\Models\Sending;
use App\Jobs\TelegramOutbox;

class MenuCommand extends Commands
{
    public static function go($payload)
    {
      $user = User::find($payload->user_id);

      $keyboard = [
        'inline_keyboard' => [
          [
            [ 'text' => __('buttons.shedule'), 'callback_data' => 'something', ],
          ],
//          [
//            [ 'text' => __('buttons.lessons-history'), 'callback_data' => 'something', ],
//            [ 'text' => __('buttons.pass-history'), 'callback_data' => 'something', ],
//          ],
          // [
          //   [ 'text' => __('buttons.my-reviews'), 'callback_data' => 'something', ],
          // ],
           [
             [ 'text' => __('buttons.balance'), 'callback_data' => 'get-balance', ],
//             [ 'text' => __('buttons.balance-refill'), 'callback_data' => 'something', ],
           ],
        ]];

      $sending = Sending::create([
        'chat_id' => $payload->chat_id,
        'text' => 'Твоє меню',
        'keyboard' => json_encode($keyboard),

      ]);
      TelegramOutbox::dispatch($sending);
    }
}
