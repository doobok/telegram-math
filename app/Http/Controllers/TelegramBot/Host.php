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
      try {
        // полчаем сообщение от WEBHOOK
        $update = Telegram::getWebhookUpdates();
        // достаем само сообщение и отправителя
        $upMessage = $update->getMessage();
        $upUser = $upMessage->getFrom();
        // формируем и сохраняем пользователя, если отсутствует
        $user = User::firstOrNew([
          'chat_id' => $upUser->getId(),
          'first_name' => $upUser->getFirstName(),
          'last_name' => $upUser->getLastName(),
          'username' => $upUser->getUsername(),
        ]);
        $user->save();
        // отправляем тип сообщения
        $type = $upMessage->detectType();
        // формируем и сохраняем сообщение
        $message = Message::create([
          'user_id' => $user->id,
          'chat_id' => $upUser->getId(),
          'message_id' => $upMessage->getMessageId(),
          'type' => $type,
          'data' => $upMessage->$type,
        ]);
        $message->save();
        // добавляем сообщение в очередь на обработку
        TelegramInbox::dispatch($message);
        // отправляем событие "печатает..."
        Telegram::bot()->sendChatAction([
          'chat_id' => $upUser->getId(),
          'action' => Actions::TYPING
        ]);

      } catch (\Exception $e) {
        Log::info($e);
      }
    }
}
