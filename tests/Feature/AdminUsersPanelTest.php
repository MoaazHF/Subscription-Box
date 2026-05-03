<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUsersPanelTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_update_and_delete_users_from_admin_panel(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $driverRoleId = Role::query()->where('name', Role::DRIVER)->value('id');
        $warehouseRoleId = Role::query()->where('name', Role::WAREHOUSE_STAFF)->value('id');

        $this->actingAs($admin)
            ->get(route('admin-users.index'))
            ->assertOk()
            ->assertSee('Users Control Panel');

        $this->actingAs($admin)->post(route('admin-users.store'), [
            'name' => 'Ops Managed User',
            'email' => 'ops-managed@example.com',
            'phone' => '01000000000',
            'role_id' => $driverRoleId,
            'password' => 'temp-pass-123',
            'password_confirmation' => 'temp-pass-123',
            'must_change_password' => 1,
        ])->assertSessionHas('status');

        $managedUser = User::query()->where('email', 'ops-managed@example.com')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'role_id' => $driverRoleId,
            'must_change_password' => true,
        ]);

        $this->actingAs($admin)->patch(route('admin-users.update', $managedUser), [
            'name' => 'Ops Updated User',
            'email' => 'ops-managed-updated@example.com',
            'phone' => '01111111111',
            'role_id' => $warehouseRoleId,
            'password' => 'updated-pass-123',
            'password_confirmation' => 'updated-pass-123',
        ])->assertSessionHas('status');

        $managedUser->refresh();

        $this->assertTrue(Hash::check('updated-pass-123', $managedUser->password));
        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'name' => 'Ops Updated User',
            'email' => 'ops-managed-updated@example.com',
            'role_id' => $warehouseRoleId,
            'must_change_password' => false,
        ]);

        $this->actingAs($admin)->delete(route('admin-users.destroy', $managedUser))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('users', [
            'id' => $managedUser->id,
        ]);
    }

    public function test_non_admin_user_is_forbidden_from_users_panel_routes(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $targetUser = User::factory()->create();

        $this->actingAs($subscriber)
            ->get(route('admin-users.index'))
            ->assertForbidden();

        $this->actingAs($subscriber)
            ->patch(route('admin-users.update', $targetUser), [
                'name' => 'Blocked Attempt',
                'email' => 'blocked-attempt@example.com',
                'role_id' => $targetUser->role_id,
            ])
            ->assertForbidden();
    }

    public function test_admin_cannot_delete_current_account_from_users_panel(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin-users.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email' => 'admin@example.com',
        ]);
    }
}
