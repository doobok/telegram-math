<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Sending;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram;
use Illuminate\Support\Facades\Log; //Log

class TelegramOutbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $instance;

    public function __construct(Sending $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

      $markup = Keyboard::make(json_decode($this->instance->keyboard));

      Telegram::sendMessage([
          'chat_id' => $this->instance->chat_id,
          'text' => $this->instance->text,
          'reply_markup' => $markup,
        ]);

    }
}
