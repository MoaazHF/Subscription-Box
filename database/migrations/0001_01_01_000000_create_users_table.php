<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createInfrastructureTables();
        $this->createUserTables();
        $this->createSubscriptionTables();
        $this->createCatalogTables();
        $this->createBoxTables();
        $this->createOperationsTables();
        $this->createGrowthTables();
        $this->createAuditTables();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_posts');
        Schema::dropIfExists('flash_sales');
        Schema::dropIfExists('gift_subscriptions');
        Schema::dropIfExists('promo_code_usages');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('retention_offers');
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('claims');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('warehouse_staff');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('box_items');
        Schema::dropIfExists('box_customisations');
        Schema::dropIfExists('boxes');
        Schema::dropIfExists('item_allergens');
        Schema::dropIfExists('items');
        Schema::dropIfExists('user_allergens');
        Schema::dropIfExists('allergen_tags');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('delivery_zones');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }

    private function createInfrastructureTables(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->bigInteger('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration')->index();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedSmallInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    private function createUserTables(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 30)->unique();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedSmallInteger('role_id');
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    private function createSubscriptionTables(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 20)->unique();
            $table->decimal('price_monthly', 8, 2);
            $table->unsignedSmallInteger('max_items');
            $table->integer('max_weight_g');
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100);
            $table->string('region', 100)->nullable();
            $table->string('country', 2);
            $table->boolean('is_serviceable')->default(true);
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->unsignedSmallInteger('delivery_zone_id')->nullable()->index();
            $table->string('street');
            $table->string('city');
            $table->string('region')->nullable();
            $table->string('country', 2);
            $table->string('postal_code', 20)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('delivery_zone_id')->references('id')->on('delivery_zones')->nullOnDelete();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->unsignedSmallInteger('plan_id');
            $table->uuid('address_id')->nullable()->index();
            $table->string('status', 20)->default('active')->index();
            $table->date('start_date');
            $table->date('next_billing_date')->nullable()->index();
            $table->unsignedSmallInteger('remaining_billing_days')->default(0);
            $table->boolean('auto_renew')->default(true);
            $table->boolean('eco_shipping')->default(false);
            $table->unsignedInteger('loyalty_points')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('plan_id')->references('id')->on('subscription_plans');
            $table->foreign('address_id')->references('id')->on('addresses')->nullOnDelete();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subscription_id')->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('tax_amount', 8, 2)->default(0);
            $table->string('status', 20);
            $table->string('gateway_ref', 100)->nullable();
            $table->string('gateway_reason_code', 50)->nullable();
            $table->unsignedSmallInteger('retry_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->cascadeOnDelete();
        });
    }

    private function createCatalogTables(): void
    {
        Schema::create('allergen_tags', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 50)->unique();
        });

        Schema::create('user_allergens', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->unsignedSmallInteger('allergen_tag_id');

            $table->primary(['user_id', 'allergen_tag_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('allergen_tag_id')->references('id')->on('allergen_tags')->cascadeOnDelete();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->integer('weight_g');
            $table->string('size_category', 20)->default('medium');
            $table->decimal('unit_price', 8, 2);
            $table->integer('stock_qty')->default(0);
            $table->boolean('is_limited_edition')->default(false);
            $table->integer('limited_stock')->nullable();
            $table->string('supplier', 100)->nullable();
            $table->string('origin_country', 2)->nullable();
            $table->text('sourcing_notes')->nullable();
            $table->boolean('is_addon')->default(false);
            $table->timestamps();
        });

        Schema::create('item_allergens', function (Blueprint $table) {
            $table->uuid('item_id');
            $table->unsignedSmallInteger('allergen_tag_id');

            $table->primary(['item_id', 'allergen_tag_id']);
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('allergen_tag_id')->references('id')->on('allergen_tags')->cascadeOnDelete();
        });
    }

    private function createBoxTables(): void
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subscription_id');
            $table->unsignedSmallInteger('period_month');
            $table->unsignedSmallInteger('period_year');
            $table->string('status', 30)->default('open');
            $table->date('lock_date');
            $table->string('theme', 100)->nullable();
            $table->integer('total_weight_g')->default(0);
            $table->string('shipping_tier', 20)->default('standard');
            $table->timestamps();

            $table->unique(['subscription_id', 'period_month', 'period_year']);
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->cascadeOnDelete();
        });

        Schema::create('box_customisations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('box_id')->unique();
            $table->boolean('swap_allowed')->default(true);
            $table->string('theme_preference', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('box_id')->references('id')->on('boxes')->cascadeOnDelete();
        });

        Schema::create('box_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('box_id');
            $table->uuid('item_id');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->boolean('is_addon')->default(false);
            $table->boolean('is_swapped')->default(false);
            $table->boolean('is_surprise')->default(false);
            $table->timestamp('added_at')->nullable();
            $table->timestamps();

            $table->unique(['box_id', 'item_id']);
            $table->foreign('box_id')->references('id')->on('boxes')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    private function createOperationsTables(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('vehicle_number', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('warehouse_staff', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('warehouse_location', 100)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('box_id')->unique();
            $table->uuid('driver_id')->nullable();
            $table->uuid('address_id')->index();
            $table->string('status', 30)->default('pending')->index();
            $table->string('tracking_number', 100)->nullable();
            $table->date('estimated_delivery')->nullable();
            $table->dateTime('actual_delivery')->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->unsignedSmallInteger('stops_remaining')->nullable();
            $table->boolean('eco_dispatch')->default(false);
            $table->timestamps();

            $table->foreign('box_id')->references('id')->on('boxes')->cascadeOnDelete();
            $table->foreign('driver_id')->references('id')->on('drivers')->nullOnDelete();
            $table->foreign('address_id')->references('id')->on('addresses');
        });

        Schema::create('claims', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subscription_id')->index();
            $table->uuid('delivery_id')->index();
            $table->uuid('item_id')->nullable()->index();
            $table->uuid('resolved_by')->nullable()->index();
            $table->string('type', 20);
            $table->string('status', 20)->default('pending');
            $table->string('photo_url', 500)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->cascadeOnDelete();
            $table->foreign('delivery_id')->references('id')->on('deliveries')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->nullOnDelete();
            $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('type', 20);
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('status', 20)->default('queued')->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('item_id')->index();
            $table->uuid('user_id')->nullable()->index();
            $table->string('action', 50);
            $table->integer('quantity_change');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    private function createGrowthTables(): void
    {
        Schema::create('retention_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subscription_id')->index();
            $table->string('offer_type', 30);
            $table->string('offer_value', 100);
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('presented_at')->useCurrent();
            $table->boolean('accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->cascadeOnDelete();
        });

        Schema::create('rewards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('type', 30);
            $table->decimal('amount', 8, 2)->nullable();
            $table->integer('points')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_applied')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('applied_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('promo_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('created_by')->nullable()->index();
            $table->string('code', 30)->unique();
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 8, 2);
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('promo_code_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('promo_code_id')->index();
            $table->uuid('user_id')->index();
            $table->timestamp('used_at')->useCurrent();

            $table->unique(['promo_code_id', 'user_id']);
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('gift_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchaser_id')->index();
            $table->uuid('recipient_user_id')->nullable()->index();
            $table->unsignedSmallInteger('plan_id');
            $table->uuid('subscription_id')->nullable()->index();
            $table->string('recipient_email');
            $table->string('recipient_name', 100)->nullable();
            $table->unsignedSmallInteger('duration_months');
            $table->string('activation_code', 64)->unique();
            $table->string('status', 30)->default('pending_payment');
            $table->text('personal_message')->nullable();
            $table->timestamp('purchased_at')->useCurrent();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('scheduled_send_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->foreign('purchaser_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('recipient_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('plan_id')->references('id')->on('subscription_plans');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->nullOnDelete();
        });

        Schema::create('flash_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedSmallInteger('plan_id')->nullable();
            $table->uuid('created_by')->index();
            $table->string('name', 150);
            $table->unsignedSmallInteger('discount_percent');
            $table->integer('stock_limit')->nullable();
            $table->integer('claimed_count')->default(0);
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('plan_id')->references('id')->on('subscription_plans')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('social_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('box_id')->index();
            $table->text('caption')->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->string('visibility', 20)->default('public');
            $table->integer('loyalty_points_awarded')->default(0);
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('box_id')->references('id')->on('boxes')->cascadeOnDelete();
        });
    }

    private function createAuditTables(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('user_id')->nullable()->index();
            $table->string('action', 100);
            $table->string('entity_type', 50)->nullable();
            $table->uuid('entity_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
