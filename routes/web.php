<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/webhook', 'App\Http\Controllers\WebhookController@setWebhook');

Route::post(Telegram::getAccessToken(), 'App\Http\Controllers\TelegramBot\Host@host')->name('webhook');
