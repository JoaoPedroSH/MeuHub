<?php

namespace Tests\Feature\Auth;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_login_redirect_requires_configuration(): void
    {
        $this->from(route('login'))
            ->get(route('login.google'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }

    public function test_google_login_redirect_stores_state_and_redirects_to_google(): void
    {
        $this->configureGoogleLogin();

        $response = $this->get(route('login.google'));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com/o/oauth2/v2/auth', $response->headers->get('Location'));
        $this->assertNotEmpty(session('google_login_state'));
    }

    public function test_google_callback_rejects_invalid_state(): void
    {
        $this->withSession(['google_login_state' => 'expected-state'])
            ->get(route('login.google.callback', ['code' => 'authorization-code', 'state' => 'wrong-state']))
            ->assertStatus(419);
    }

    public function test_google_callback_registers_and_authenticates_new_user(): void
    {
        $this->configureGoogleLogin();
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'google-access-token']),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'sub' => 'google-user-123',
                'email' => 'google@example.com',
                'email_verified' => true,
                'name' => 'Google User',
                'picture' => 'https://example.com/avatar.jpg',
            ]),
        ]);

        $response = $this->withSession(['google_login_state' => 'valid-state'])
            ->get(route('login.google.callback', ['code' => 'authorization-code', 'state' => 'valid-state']));

        $user = User::where('email', 'google@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->registered_with_google);
        $this->assertSame('google-user-123', $user->google_id);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_google_callback_links_existing_email_without_marking_registration(): void
    {
        $this->configureGoogleLogin();
        $user = User::factory()->create(['email' => 'existing@example.com']);
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'google-access-token']),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'sub' => 'existing-google-user',
                'email' => 'existing@example.com',
                'email_verified' => true,
                'name' => 'Existing User',
            ]),
        ]);

        $this->withSession(['google_login_state' => 'valid-state'])
            ->get(route('login.google.callback', ['code' => 'authorization-code', 'state' => 'valid-state']))
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->refresh()->registered_with_google);
        $this->assertSame('existing-google-user', $user->google_id);
    }

    private function configureGoogleLogin(): void
    {
        SystemSetting::setValue('google_client_id', 'client-id.apps.googleusercontent.com');
        SystemSetting::setSecret('google_client_secret', 'secret-value');
        SystemSetting::setValue('google_login_enabled', true);
    }
}
