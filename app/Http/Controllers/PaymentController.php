<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\RabbitMQService;

class PaymentController extends Controller
{
    protected $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService)
    {
        $this->rabbitMQService = $rabbitMQService;
    }

    public function processPayment(Request $request)
    {
        $data = [
            'user_id' => $request->input('user_id'),
            'amount' => $request->input('amount'),
            'status' => 'pending',
        ];

        // Отправка сообщения в очередь
        $this->rabbitMQService->publishMessage('payments', json_encode($data));

        return response()->json(['status' => 'Message sent to queue']);
    }

    public function listenQueue()
    {
        $this->rabbitMQService->listenQueue('payments', function ($msg) {
            $data = json_decode($msg->body, true);

            // Сохранение данных в таблицу `payments`
            Payment::create($data);

            echo 'Payment processed and saved to database.';
        });
    }
}
