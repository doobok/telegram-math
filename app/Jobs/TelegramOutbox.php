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
      // создаем коллекцию сообщения с ID чата
      $message = collect([
        'chat_id' => $this->instance->chat_id,
      ]);
      // если есть текст то добавляем его
      if ($this->instance->text) {
        $message->put('text', $this->instance->text);
      }
      // если есть клавиатура - обрабатываем
      if ($this->instance->keyboard) {
        // проверка на команду удаления
        if ($this->instance->keyboard === 'remove') {
          $markup = Keyboard::remove();
          $message->put('reply_markup', $markup);
        // иначе пытаемся собрать клавиатуру
        } else {
          try {
            $markup = Keyboard::make(json_decode($this->instance->keyboard));
            $message->put('reply_markup', $markup);
          } catch (\Exception $e) {
            Log::info($e);
          }
        }
      }
      // отправляем сообщение
      Telegram::sendMessage(
        $message->all()
      );
      // удаляем запись в бд
      // Sending::find($this->instance->id)->delete();
    }
}
