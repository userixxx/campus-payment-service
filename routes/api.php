<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/payment', [PaymentController::class, 'processPayment']);
Route::get('/payment/listen', [PaymentController::class, 'listenQueue']);
