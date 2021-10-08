<?php

namespace App\Http\Controllers\TelegramBot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use App\Option;
use App\TelegramState;
use App\Http\Controllers\Telegram\StudentsBotController;
use App\Http\Controllers\Telegram\TutorsBotController;
use App\Http\Controllers\Telegram\SheduleBotController;
use App\Http\Controllers\Telegram\ReportsBotController;
use App\Models\User;
use App\Models\Message;

class Host extends Controller
{
    public function host()
    {
      try {



      Log::info('ok');

      $update = Telegram::getWebhookUpdates();
      // $update = Telegram::commandsHandler(true);
      $upMessage = $update->getMessage();
      $upUser = $upMessage->getFrom();


      $user = User::firstOrNew([
        'chat_id' => $upUser->getId(),
        'first_name' => $upUser->getFirstName(),
        'last_name' => $upUser->getLastName(),
        'username' => $upUser->getUsername(),
      ]);
      $user->save();

      $type = $upMessage->detectType();
      //
      // // Log::info(gettype($type));
      // // Log::info(gettype($upMessage));
      //
      $message = Message::create([
        'user_id' => $user->id,
        'chat_id' => $upUser->getId(),
        'message_id' => $upMessage->getMessageId(),
        'type' => $type,
        // 'data' => '123',
        // 'text' => $upMessage->getText(),
        'data' => $upMessage->$type,
      ]);
      $message->save();

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


      Telegram::bot()->sendMessage([
        'chat_id' => 239268837,
        'text' => 'Hello!',
      ]);


      } catch (\Exception $e) {
        Log::info('false');
        Log::info($e);

      }

      // $this->replyWithMessage('answer!');

      // code...
    }
}
