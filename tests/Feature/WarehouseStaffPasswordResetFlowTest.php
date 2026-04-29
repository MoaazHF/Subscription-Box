<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class WarehouseStaffPasswordResetFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_warehouse_staff_account_and_first_login_forces_password_change(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)->post(route('warehouse-staff.accounts.store'), [
            'name' => 'Warehouse New',
            'email' => 'warehouse-new@example.com',
            'phone' => '0100000000',
            'password' => 'TempPass123!',
            'password_confirmation' => 'TempPass123!',
            'warehouse_location' => 'South Hub',
        ])->assertSessionHas('status');

        $staffUser = User::query()->where('email', 'warehouse-new@example.com')->firstOrFail();

        $this->assertTrue((bool) $staffUser->must_change_password);

        $this->post(route('logout'));

        $loginResponse = $this->post(route('login.store'), [
            'email' => 'warehouse-new@example.com',
            'password' => 'TempPass123!',
        ]);

        $loginResponse->assertRedirect(route('password.change.edit'));

        $this->patch(route('password.change.update'), [
            'password' => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ])->assertRedirect(route('dashboard'));

        $staffUser->refresh();

        $this->assertFalse((bool) $staffUser->must_change_password);

        $this->get(route('dashboard'))->assertOk();
    }
}
