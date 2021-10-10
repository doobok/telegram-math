<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;


class TelegramInbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $msg;

    public function __construct( $msg)
    {
       $this->msg = $msg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Log::alert('START');

      if ($this->msg->type === 'contact') {
        Log::alert('IS Contact');

        Telegram::bot()->sendMessage([
          'chat_id' => 239268837,
          'text' => 'Дякуэмо за авторизацію',
        ]);
      } else {
        Log::alert('IS NO Contact');

        Telegram::bot()->sendMessage([
          'chat_id' => 239268837,
          'text' => 'Це не номер телефону',
        ]);
      }
      Log::alert('END..');

      // Log::alert('In INBOX msg ID:' . $this->msg->id);
      // Log::alert($this->msg->id);

    }
}
