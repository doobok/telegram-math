<?php

namespace App\Http\Controllers\TelegramBot\Callbacks;

use App\Http\Controllers\TelegramBot\Router;
use Illuminate\Support\Facades\Log; //Log


class Callbacks extends Router
{
    public static function index($payload)
    {

      Log::info('is a callback (callbacks)');

    }
}
