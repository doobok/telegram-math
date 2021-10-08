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

class Host extends Controller
{
    public function host()
    {
      Log::info('ok');

      $update = Telegram::getWebhookUpdates();
      // $update = Telegram::commandsHandler(true);
      //
      Log::info($update);

      // $this->replyWithMessage('answer!');

      // code...
    }
}
