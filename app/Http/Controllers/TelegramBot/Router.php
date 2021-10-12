<?php

namespace App\Http\Controllers\TelegramBot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; //Log
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use Illuminate\Support\Str;
use App\Http\Controllers\TelegramBot\Authorization;
use App\Http\Controllers\TelegramBot\Commands\Commands;
use App\Http\Controllers\TelegramBot\Requests\Requests;
use App\Http\Controllers\TelegramBot\Callbacks\Callbacks;

class Router extends Controller
{
    public static function index($payload)
    {
        switch ($payload->type) {
          case 'text':
            if (Str::startsWith($payload->data, '/' )) {
              // обрабатываем КОМАНДУ
              Commands::index($payload);
            } else {
              // обрабатываем ОБЫЧНЫЙ ВВОД (текст и прочее)
              Requests::index($payload);
            }
            break;
          case 'contact':
            // работаем с авторизацией при отправке НОМЕРА ТЕЛЕФОНА
            Authorization::auth($payload);
            break;
          case 'callback':
            // обрабатываем КОЛБЭК
            Callbacks::index($payload);
            break;

          default:
            // сообщаем об отсутствии поддержки типа данных
            Telegram::bot()->sendMessage([
              'chat_id' => $payload->chat_id,
              'text' => __('bot.unknown-message-type'),
            ]);
            break;
        }

    }
}
