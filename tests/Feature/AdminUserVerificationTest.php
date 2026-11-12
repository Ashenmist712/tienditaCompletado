<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AdminUserVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_creation_sends_the_native_email_verification_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'form_type' => 'create',
            'name' => 'New Customer',
            'email' => 'customer@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'customer@example.test')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertSame($admin->id, auth()->id());
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_changing_a_users_email_invalidates_verification_and_sends_a_new_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create([
            'email' => 'old@example.test',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'form_type' => 'edit',
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => 'new@example.test',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $user->refresh();

        $this->assertSame('new@example.test', $user->email);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_link_can_resume_after_login_and_verifies_the_signed_in_user(): void
    {
        $user = User::factory()->create([
            'email' => 'verify@example.test',
            'password' => 'password123',
            'email_verified_at' => null,
        ]);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHour(),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );

        $this->get($verificationUrl)->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect($verificationUrl);

        $this->get($verificationUrl)->assertRedirect(route('shop.index'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
