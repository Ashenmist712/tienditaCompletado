<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PaymentScaffoldTest extends TestCase
{
    public function test_payment_and_webhook_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('payments.start'));
        $this->assertTrue(Route::has('payments.paypal.capture'));
        $this->assertTrue(Route::has('payments.success'));
        $this->assertTrue(Route::has('payments.webhooks.paypal'));
    }

    public function test_payment_start_requires_checkout_details(): void
    {
        $this->postJson('/checkout/paypal')->assertUnprocessable();
    }

    public function test_payment_start_requires_a_country_code_and_ten_local_phone_digits(): void
    {
        $this->postJson('/checkout/paypal', [
            'nombre' => 'Juan',
            'apellidos' => 'Pérez',
            'email' => 'juan@example.com',
            'phone_code' => '+52',
            'phone_number' => '123-456-7890',
            'shipping_street' => 'Av. Principal',
            'shipping_exterior_number' => '123',
            'shipping_neighborhood' => 'Centro',
            'shipping_postal_code' => '06000',
            'shipping_municipality' => 'Cuauhtémoc',
            'shipping_state' => 'Ciudad de México',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['phone_number']);
    }

    public function test_payment_start_requires_a_five_digit_postal_code(): void
    {
        $this->postJson('/checkout/paypal', [
            'nombre' => 'Juan',
            'apellidos' => 'Pérez',
            'email' => 'juan@example.com',
            'phone_code' => '+52',
            'phone_number' => '5551234567',
            'shipping_street' => 'Av. Principal',
            'shipping_exterior_number' => '123',
            'shipping_neighborhood' => 'Centro',
            'shipping_postal_code' => '6000',
            'shipping_municipality' => 'Cuauhtémoc',
            'shipping_state' => 'Ciudad de México',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['shipping_postal_code']);
    }

    public function test_payment_start_requires_structured_shipping_fields(): void
    {
        $this->postJson('/checkout/paypal')
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'nombre',
                'apellidos',
                'email',
                'phone_code',
                'phone_number',
                'shipping_street',
                'shipping_exterior_number',
                'shipping_neighborhood',
                'shipping_postal_code',
                'shipping_municipality',
                'shipping_state',
            ]);
    }

    public function test_payment_start_route_only_accepts_supported_providers(): void
    {
        $this->post('/checkout/unsupported')->assertNotFound();
    }
}
