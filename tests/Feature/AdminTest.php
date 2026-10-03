<?php

namespace Tests\Feature;

use App\Models\User;
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
}
