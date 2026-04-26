<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('roles')->upsert([
            ['name' => 'subscriber'],
            ['name' => 'warehouse_staff'],
            ['name' => 'driver'],
            ['name' => 'admin'],
        ], ['name']);

        DB::table('subscription_plans')->upsert([
            [
                'name' => 'basic',
                'price_monthly' => 9.99,
                'max_items' => 3,
                'max_weight_g' => 1000,
                'features' => json_encode([
                    'swaps' => 0,
                    'addons' => false,
                    'priority_support' => false,
                ], JSON_THROW_ON_ERROR),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'standard',
                'price_monthly' => 19.99,
                'max_items' => 5,
                'max_weight_g' => 2000,
                'features' => json_encode([
                    'swaps' => 2,
                    'addons' => true,
                    'priority_support' => false,
                ], JSON_THROW_ON_ERROR),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'premium',
                'price_monthly' => 34.99,
                'max_items' => 8,
                'max_weight_g' => 3500,
                'features' => json_encode([
                    'swaps' => 5,
                    'addons' => true,
                    'priority_support' => true,
                ], JSON_THROW_ON_ERROR),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['name'], ['price_monthly', 'max_items', 'max_weight_g', 'features', 'is_active', 'updated_at']);

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'role_id' => DB::table('roles')->where('name', Role::SUBSCRIBER)->value('id') ?? 1,
                'name' => 'Test User',
                'phone' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role_id' => DB::table('roles')->where('name', Role::ADMIN)->value('id') ?? 4,
                'name' => 'Admin User',
                'phone' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
    }
}
