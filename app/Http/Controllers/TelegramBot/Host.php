<?php

namespace App\Http\Controllers\TelegramBot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use App\Models\User;
use App\Models\Message;
use App\Jobs\TelegramInbox;

class Host extends Controller
{
    public function host()
    {
      // try {



      Log::info('ok');

      $update = Telegram::getWebhookUpdates();
      // $update = Telegram::commandsHandler(true);
      $upMessage = $update->getMessage();
      $upUser = $upMessage->getFrom();

      Log::info($update);

      // Telegram::bot()->sendMessage([
      //   'chat_id' => $upUser->getId(),
      // ]);



      $user = User::firstOrNew([
        'chat_id' => $upUser->getId(),
        'first_name' => $upUser->getFirstName(),
        'last_name' => $upUser->getLastName(),
        'username' => $upUser->getUsername(),
      ]);
      $user->save();

      $type = $upMessage->detectType();
      //
      // Log::info(gettype($type));
      // Log::info(gettype($upMessage));

      $message = Message::create([
        'user_id' => $user->id,
        'chat_id' => $upUser->getId(),
        'message_id' => $upMessage->getMessageId(),
        'type' => $type,
        'data' => $upMessage->$type,
      ]);
      $message->save();

      TelegramInbox::dispatch($message);

      // Log::info($upMessage->detectType());



      //
      // Log::info($update);
      //
      // Log::info('Message');
      // $message = $update->getMessage();
      // Log::info($message);
      //
      // Log::info('Type');
      // $type = $message->detectType();
      // Log::info($type);
      //
      // Log::info('user');
      // $user = $message->getFrom();
      // Log::info($user);
      //
      // Log::info( $user->getId() );
      // Log::info( $user->getFirstName() );
      // Log::info( $user->getLastName() );
      // Log::info( $user->getUsername() );
      //
      // Log::info('user');

      // $contact = $message->getContact();
      // Log::info( $contact );
      // Log::info( $contact->getPhoneNumber() );
      // Log::info( $contact->getUserId() );


      Telegram::bot()->sendChatAction([
        'chat_id' => $upUser->getId(),
        'action' => Actions::TYPING
      ]);



          $keyboard = [
            [
              ['text'=>'📞 Відправити номер телефону','request_contact'=>true ],
            ],
          ];

        Telegram::bot()->sendMessage([
        'chat_id' => $upUser->getId(),
        'text' => 'Для продовження поділись своїм номером телефону, використай відповідну кнопку',
        'reply_markup' => Keyboard::make([
          'keyboard' => $keyboard,
          // 'inline_keyboard' => $keyboard,
          'resize_keyboard' => true,
          'one_time_keyboard' => true
        ]),
      ]);






      // } catch (\Exception $e) {
      //   Log::info('false');
      //   Log::info($e);
      //
      // }

    }
}
