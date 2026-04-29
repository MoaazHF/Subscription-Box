<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\WarehouseStaff;
use Illuminate\Support\Facades\DB;

class WarehouseStaffAccountService
{
    /**
     * @param  array{name:string,email:string,phone?:string,password:string,password_confirmation:string,warehouse_location?:string}  $payload
     */
    public function createAccountWithProfile(array $payload): WarehouseStaff
    {
        $profile = DB::transaction(function () use ($payload): WarehouseStaff {
            $roleId = Role::query()
                ->where('name', Role::WAREHOUSE_STAFF)
                ->value('id');

            $user = User::query()->create([
                'role_id' => $roleId,
                'name' => $payload['name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'] ?? null,
                'password' => $payload['password'],
                'must_change_password' => true,
            ]);

            return WarehouseStaff::query()->create([
                'user_id' => $user->id,
                'warehouse_location' => $payload['warehouse_location'] ?? null,
            ]);
        });

        return $profile->fresh('user');
    }

    /**
     * @param  array{warehouse_location?:string}  $payload
     */
    public function updateProfile(WarehouseStaff $staff, array $payload): WarehouseStaff
    {
        $staff->update([
            'warehouse_location' => $payload['warehouse_location'] ?? null,
        ]);

        return $staff->fresh('user');
    }

    public function deleteProfile(WarehouseStaff $staff): void
    {
        DB::transaction(function () use ($staff): void {
            $user = $staff->user()->first();
            $staff->delete();

            if ($user) {
                $user->delete();
            }
        });
    }
}
