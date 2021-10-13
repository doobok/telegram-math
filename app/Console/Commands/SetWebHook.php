<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram;
use Illuminate\Support\Facades\Log; //Log

class SetWebHook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:webhook
                {--set : Установить Webhook}
                {--check : Проверить состояние Webhook}
                {--remove : Удалить Webhook}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Опрации с Telegram Webhook';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      if ($this->option('set')) {
        $params = ['query' => [
            'url' => config('app.url') . '/' . Telegram::getAccessToken()
          ]];
        $this->setupWebhook('setwebhook', $params);
      }elseif ($this->option('remove')) {
        $params = ['query' => [
            'url' => ''
          ]];
        $this->setupWebhook('setwebhook', $params);
      } elseif ($this->option('check')) {
        $this->setupWebhook('getWebhookInfo', []);
      }
    }

    protected function setupWebhook($route, $params)
    {
      $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://api.telegram.org/bot' . Telegram::getAccessToken() . '/'
      ]);

      $data = $client->request('POST', $route, $params);

        $res = $data->getBody();
        echo $res . PHP_EOL;
    }
}
