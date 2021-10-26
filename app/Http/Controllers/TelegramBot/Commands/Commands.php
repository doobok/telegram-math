<?php

namespace App\Http\Controllers\TelegramBot\Commands;

use App\Http\Controllers\TelegramBot\Router;
use Illuminate\Support\Facades\Log;

//Log
use Illuminate\Support\Str;
use App\Models\Sending;
use App\Jobs\TelegramOutbox;

class Commands extends Router
{
    public static function index($payload)
    {
        $command = Str::of($payload->data)->explode('_');
//      Log::debug($command[0]);

        switch ($command[0]) {
            case '/start':
                StartCommand::go($payload);
                break;
            case '/help':
                HelpCommand::go($payload);
                break;
            case '/menu':
                MenuCommand::go($payload);
                break;
            case '/test':
                TestCommand::go($payload);
                break;

            default:
                $sending = Sending::create([
                    'chat_id' => $payload->chat_id,
                    'text' => __('bot.unknown-command'),
                ]);
                TelegramOutbox::dispatch($sending);
                break;
        }
    }
}
