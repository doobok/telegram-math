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
        // пытаемся получить callback и сообщение
        $upCallback = $update->getCallbackQuery();
        $upMessage = $update->getMessage();
        // если это колбек - задаем переменные
        if ($upCallback) {
          // пользователя
          $upUser = $upCallback->getFrom();
          // сообщения
          $message_id = -1;
          $type = 'callback';
          $data = $upCallback->getData();
          // возвращаем сообщение об успешном получении колбэка
          Telegram::bot()->answerCallbackQuery([
            'callback_query_id' => $upCallback->getId(),
          ]);
        // иначе проверяем наличие сообщения и задаем переменные из сообщения
        } else if ($upMessage) {
          // пользователь
          $upUser = $upMessage->getFrom();
          // сообщение
          $message_id = $upMessage->getMessageId();
          $type = $upMessage->detectType();
          $data = $upMessage->$type;
        // запасной вариант - прекращаем скрипт
        } else {
          return;
        }

        // формируем и сохраняем пользователя, если отсутствует
        $user = User::firstOrNew([
          'chat_id' => $upUser->getId(),
          'first_name' => $upUser->getFirstName(),
          'last_name' => $upUser->getLastName(),
          'username' => $upUser->getUsername(),
        ]);
        $user->save();
        // формируем и сохраняем сообщение
        $message = Message::create([
          'user_id' => $user->id,
          'chat_id' => $upUser->getId(),
          'message_id' => $message_id,
          'type' => $type,
          'data' => $data,
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
