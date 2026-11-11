<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/paypal', [PaymentWebhookController::class, 'paypal'])
    ->name('payments.webhooks.paypal');
