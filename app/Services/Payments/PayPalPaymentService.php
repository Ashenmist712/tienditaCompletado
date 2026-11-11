<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\OrderApplicationContextBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Order as PayPalOrder;
use PaypalServerSdkLib\Models\PurchaseUnit;
use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use RuntimeException;

class PayPalPaymentService
{
    public function client(): PaypalServerSdkClient
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');

        if (! is_string($clientId) || $clientId === '' || ! is_string($clientSecret) || $clientSecret === '') {
            throw new RuntimeException('PayPal credentials are not configured.');
        }

        $mode = config('services.paypal.mode');

        if (! in_array($mode, ['sandbox', 'live'], true)) {
            throw new RuntimeException('PayPal mode must be either sandbox or live.');
        }

        $environment = $mode === 'live'
            ? Environment::PRODUCTION
            : Environment::SANDBOX;

        return PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init($clientId, $clientSecret)
            )
            ->environment($environment)
            ->build();
    }

    public function createOrder(Order $order, string $successUrl): PayPalOrder
    {
        $amount = AmountWithBreakdownBuilder::init(
            config('services.payments.currency'),
            number_format((float) $order->total, 2, '.', ''),
        )->build();
        $purchaseUnit = PurchaseUnitRequestBuilder::init($amount)
            ->referenceId('order-'.$order->id)
            ->customId((string) $order->id)
            ->invoiceId('order-'.$order->id)
            ->description('Pedido '.$order->id)
            ->build();
        $body = OrderRequestBuilder::init(CheckoutPaymentIntent::CAPTURE, [$purchaseUnit])
            ->applicationContext(
                OrderApplicationContextBuilder::init()
                    ->brandName((string) config('app.name'))
                    ->returnUrl($successUrl)
                    ->cancelUrl($successUrl)
                    ->build()
            )
            ->build();

        $response = $this->client()->getOrdersController()->createOrder([
            'body' => $body,
            'prefer' => 'return=representation',
            'paypalRequestId' => 'tiendita-order-'.$order->id,
        ]);
        $paypalOrder = $response->getResult();

        if (! $response->isSuccess() || ! $paypalOrder instanceof PayPalOrder || ! $paypalOrder->getId()) {
            throw new RuntimeException('PayPal did not return a valid order.');
        }

        return $paypalOrder;
    }

    public function captureOrder(string $paypalOrderId, int $orderId): PayPalOrder
    {
        $response = $this->client()->getOrdersController()->captureOrder([
            'id' => $paypalOrderId,
            'prefer' => 'return=representation',
            'paypalRequestId' => 'tiendita-capture-'.$orderId,
        ]);
        $paypalOrder = $response->getResult();

        if (! $response->isSuccess() || ! $paypalOrder instanceof PayPalOrder) {
            throw new RuntimeException('PayPal did not return a valid capture result.');
        }

        return $paypalOrder;
    }

    public function getOrder(string $paypalOrderId): PayPalOrder
    {
        $response = $this->client()->getOrdersController()->getOrder(['id' => $paypalOrderId]);
        $paypalOrder = $response->getResult();

        if (! $response->isSuccess() || ! $paypalOrder instanceof PayPalOrder) {
            throw new RuntimeException('PayPal did not return a valid order.');
        }

        return $paypalOrder;
    }

    public function hasValidCapture(PayPalOrder $paypalOrder, Order $order): bool
    {
        if ($paypalOrder->getId() !== $order->payment_reference
            || $paypalOrder->getStatus() !== 'COMPLETED') {
            return false;
        }

        $purchaseUnits = $paypalOrder->getPurchaseUnits();

        if (! is_array($purchaseUnits) || count($purchaseUnits) !== 1) {
            return false;
        }

        $purchaseUnit = $purchaseUnits[0];

        if (! $purchaseUnit instanceof PurchaseUnit
            || $purchaseUnit->getCustomId() !== (string) $order->id) {
            return false;
        }

        $expectedAmount = number_format((float) $order->total, 2, '.', '');
        $amount = $purchaseUnit->getAmount();

        if ($amount === null
            || $amount->getCurrencyCode() !== config('services.payments.currency')
            || $amount->getValue() !== $expectedAmount) {
            return false;
        }

        $captures = $purchaseUnit->getPayments()?->getCaptures() ?? [];

        foreach ($captures as $capture) {
            $captureAmount = $capture->getAmount();

            if ($capture->getStatus() === 'COMPLETED'
                && $captureAmount !== null
                && $captureAmount->getCurrencyCode() === config('services.payments.currency')
                && $captureAmount->getValue() === $expectedAmount) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify a webhook with PayPal's authenticated verification endpoint.
     *
     * @param  array<string, mixed>  $event
     */
    public function verifyWebhookSignature(array $headers, array $event): bool
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');
        $webhookId = config('services.paypal.webhook_id');

        if (! is_string($clientId) || $clientId === ''
            || ! is_string($clientSecret) || $clientSecret === ''
            || ! is_string($webhookId) || $webhookId === '') {
            throw new RuntimeException('PayPal webhook credentials are not configured.');
        }

        foreach (['auth_algo', 'cert_url', 'transmission_id', 'transmission_sig', 'transmission_time'] as $header) {
            if (! is_string($headers[$header] ?? null) || $headers[$header] === '') {
                return false;
            }
        }

        $baseUrl = config('services.paypal.base_url');
        $tokenResponse = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->timeout(15)
            ->post($baseUrl.'/v1/oauth2/token', ['grant_type' => 'client_credentials'])
            ->throw();
        $accessToken = $tokenResponse->json('access_token');

        if (! is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('PayPal did not return an access token for webhook verification.');
        }

        $verification = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(15)
            ->post($baseUrl.'/v1/notifications/verify-webhook-signature', [
                'auth_algo' => $headers['auth_algo'] ?? null,
                'cert_url' => $headers['cert_url'] ?? null,
                'transmission_id' => $headers['transmission_id'] ?? null,
                'transmission_sig' => $headers['transmission_sig'] ?? null,
                'transmission_time' => $headers['transmission_time'] ?? null,
                'webhook_id' => $webhookId,
                'webhook_event' => $event,
            ])
            ->throw();

        return $verification->json('verification_status') === 'SUCCESS';
    }
}
