<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_administrators_can_access_admin_panel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertInertia(fn ($page) => $page->component('Admin/Dashboard'));
    }

    public function test_admin_can_grant_administrator_access(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        $this->actingAs($admin)->patch(route('admin.users.toggle-admin', $user), [])->assertSessionHas('success');
        $this->assertTrue($user->refresh()->is_admin);
    }

    public function test_admin_dashboard_reports_google_registration_count(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        User::factory()->create(['registered_with_google' => true]);
        User::factory()->create(['registered_with_google' => false]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('google.google_registered_users', 1));
    }

    public function test_admin_can_enable_google_login_and_save_credentials(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.integrations.google.credentials'), [
                'client_id' => 'client-id.apps.googleusercontent.com',
                'client_secret' => 'secret-value',
                'redirect_uris' => ['https://meuhub.test/login/google/callback'],
            ])
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->patch(route('admin.integrations.google-login.toggle'), ['enabled' => true])
            ->assertSessionHas('success');

        $this->assertSame('client-id.apps.googleusercontent.com', SystemSetting::getValue('google_client_id'));
        $this->assertSame('secret-value', SystemSetting::getSecret('google_client_secret'));
        $this->assertTrue((bool) SystemSetting::getValue('google_login_enabled'));
    }

    public function test_admin_cannot_change_own_admin_access(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-admin', $admin))
            ->assertStatus(422);

        $this->assertTrue($admin->refresh()->is_admin);
    }
}
