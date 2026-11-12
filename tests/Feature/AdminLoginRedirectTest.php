<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_dashboard_even_when_an_intended_url_exists(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password123',
            'is_admin' => true,
        ]);

        $response = $this
            ->withSession(['url.intended' => route('shop.index')])
            ->post(route('login'), [
                'email' => 'admin@example.com',
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('admin.dashboard'));
    }
}
