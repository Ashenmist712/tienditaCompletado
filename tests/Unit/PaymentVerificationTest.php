<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\Payments\PayPalPaymentService;
use Illuminate\Support\Facades\Http;
use PaypalServerSdkLib\Models\AmountWithBreakdown;
use PaypalServerSdkLib\Models\Money;
use PaypalServerSdkLib\Models\Order as PayPalOrder;
use PaypalServerSdkLib\Models\OrdersCapture;
use PaypalServerSdkLib\Models\PaymentCollection;
use PaypalServerSdkLib\Models\PurchaseUnit;
use Tests\TestCase;

class PaymentVerificationTest extends TestCase
{
    public function test_paypal_capture_must_match_local_order_amount_currency_and_reference(): void
    {
        config(['services.payments.currency' => 'MXN']);

        $order = new Order;
        $order->forceFill([
            'id' => 42,
            'total' => '125.50',
            'payment_reference' => 'PAYPAL123',
        ]);
        $paypalOrder = new PayPalOrder;
        $paypalOrder->setId('PAYPAL123');
        $paypalOrder->setStatus('COMPLETED');

        $capture = new OrdersCapture;
        $capture->setStatus('COMPLETED');
        $capture->setAmount(new Money('MXN', '125.50'));
        $payments = new PaymentCollection;
        $payments->setCaptures([$capture]);

        $purchaseUnit = new PurchaseUnit;
        $purchaseUnit->setCustomId('42');
        $purchaseUnit->setAmount(new AmountWithBreakdown('MXN', '125.50'));
        $purchaseUnit->setPayments($payments);
        $paypalOrder->setPurchaseUnits([$purchaseUnit]);

        $service = new PayPalPaymentService;

        $this->assertTrue($service->hasValidCapture($paypalOrder, $order));

        $capture->setAmount(new Money('USD', '125.50'));
        $this->assertFalse($service->hasValidCapture($paypalOrder, $order));
    }

    public function test_paypal_webhook_signature_uses_paypals_sandbox_verification_api(): void
    {
        config([
            'services.paypal.client_id' => 'sandbox-client',
            'services.paypal.client_secret' => 'sandbox-secret',
            'services.paypal.webhook_id' => 'webhook-id',
            'services.paypal.base_url' => 'https://api-m.sandbox.paypal.com',
        ]);
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'access-token',
            ]),
            'https://api-m.sandbox.paypal.com/v1/notifications/verify-webhook-signature' => Http::response([
                'verification_status' => 'SUCCESS',
            ]),
        ]);

        $valid = (new PayPalPaymentService)->verifyWebhookSignature([
            'auth_algo' => 'SHA256withRSA',
            'cert_url' => 'https://api-m.sandbox.paypal.com/cert',
            'transmission_id' => 'transmission-id',
            'transmission_sig' => 'signature',
            'transmission_time' => now()->toIso8601String(),
        ], ['event_type' => 'PAYMENT.CAPTURE.COMPLETED']);

        $this->assertTrue($valid);
        Http::assertSent(fn ($request): bool => str_ends_with(
            $request->url(),
            '/v1/notifications/verify-webhook-signature'
        ) && $request['webhook_id'] === 'webhook-id');
    }
}
