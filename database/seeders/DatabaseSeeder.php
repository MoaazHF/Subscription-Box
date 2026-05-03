<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $this->seedItems();

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

        User::firstOrCreate(
            ['email' => 'driver@example.com'],
            [
                'role_id' => DB::table('roles')->where('name', Role::DRIVER)->value('id') ?? 3,
                'name' => 'driver User',
                'phone' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'driver@example.com'],
            [
                'role_id' => DB::table('roles')->where('name', Role::DRIVER)->value('id') ?? 3,
                'name' => 'driver User',
                'phone' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'warehouse_staff@example.com'],
            [
                'role_id' => DB::table('roles')->where('name', Role::WAREHOUSE_STAFF)->value('id') ?? 2,
                'name' => 'warehouse_staff User',
                'phone' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'warehouse_staff@example.com'],
            [
                'role_id' => DB::table('roles')->where('name', Role::WAREHOUSE_STAFF)->value('id') ?? 2,
                'name' => 'warehouse_staff User',
                'phone' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'subscriber@example.com'],
            [
                'role_id' => DB::table('roles')->where('name', Role::SUBSCRIBER)->value('id') ?? 1,
                'name' => 'SUBSCRIBER User',
                'phone' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
    }

    private function seedItems(): void
    {
        $items = [
            [
                'name' => 'Artisan Granola',
                'description' => 'Oat granola with dried berries.',
                'weight_g' => 320,
                'size_category' => 'medium',
                'unit_price' => 6.50,
                'stock_qty' => 50,
                'supplier' => 'Nile Pantry',
                'origin_country' => 'EG',
            ],
            [
                'name' => 'Citrus Tea Blend',
                'description' => 'A light herbal tea for the monthly box.',
                'weight_g' => 180,
                'size_category' => 'small',
                'unit_price' => 4.25,
                'stock_qty' => 60,
                'supplier' => 'Delta Herbs',
                'origin_country' => 'EG',
            ],
            [
                'name' => 'Dark Chocolate Bites',
                'description' => 'Small-batch dark chocolate snack pack.',
                'weight_g' => 200,
                'size_category' => 'small',
                'unit_price' => 5.75,
                'stock_qty' => 75,
                'supplier' => 'Cairo Cocoa',
                'origin_country' => 'EG',
            ],
            [
                'name' => 'Ceramic Mug',
                'description' => 'Reusable mug for coffee or tea.',
                'weight_g' => 650,
                'size_category' => 'large',
                'unit_price' => 9.90,
                'stock_qty' => 30,
                'supplier' => 'Desert Clay',
                'origin_country' => 'EG',
            ],
            [
                'name' => 'Candle Tin',
                'description' => 'A compact soy candle with a warm scent.',
                'weight_g' => 420,
                'size_category' => 'medium',
                'unit_price' => 7.10,
                'stock_qty' => 40,
                'supplier' => 'Glow Works',
                'origin_country' => 'EG',
            ],
            [
                'name' => 'Notebook Set',
                'description' => 'Two soft-cover notebooks for everyday notes.',
                'weight_g' => 360,
                'size_category' => 'medium',
                'unit_price' => 8.20,
                'stock_qty' => 45,
                'supplier' => 'Paper Dock',
                'origin_country' => 'EG',
            ],
        ];

        foreach ($items as $item) {
            $record = Item::query()->firstOrNew(['name' => $item['name']]);
            $record->fill([
                ...$item,
                'is_limited_edition' => false,
                'limited_stock' => null,
                'sourcing_notes' => null,
                'is_addon' => false,
            ]);

            if (! $record->exists) {
                $record->id = (string) Str::uuid();
            }

            $record->save();
        }
    }
}
