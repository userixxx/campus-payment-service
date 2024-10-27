<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RabbitMQService;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class RabbitMQListenCommand extends Command
{
    protected $signature = 'rabbitmq:listen';
    protected $description = 'Listen to RabbitMQ messages';

    protected $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService)
    {
        parent::__construct();
        $this->rabbitMQService = $rabbitMQService;
    }

    public function handle()
    {
        $this->rabbitMQService->listenQueue(env('RABBITMQ_QUEUE'), function ($msg) {
            $data = json_decode($msg->body, true);
            echo 'Received: ', $msg->body, "\n";

            // Сохраняем данные в таблицу `payments`
            Payment::create($data);
            Log::info('Payment processed and saved to database.', $data);
        });
    }
}
