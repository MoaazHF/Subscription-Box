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
        if (!Schema::hasTable('deliveries')) {
            Schema::create('deliveries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('box_id')->unique();
                $table->uuid('driver_id')->nullable();
                $table->uuid('address_id');
                $table->enum('status', [
                    'pending', 
                    'picking', 
                    'packed', 
                    'shipped', 
                    'out_for_delivery', 
                    'delivered', 
                    'undeliverable'
                ])->default('pending');
                $table->string('tracking_number', 100)->nullable();
                $table->date('estimated_delivery')->nullable();
                $table->dateTime('actual_delivery')->nullable();
                $table->text('delivery_instructions')->nullable();
                $table->smallInteger('stops_remaining')->nullable();
                $table->boolean('eco_dispatch')->default(false);
                $table->timestamps();

                // Foreign keys - skipped explicit constraints definition since the tables might not exist yet,
                // or we rely on existing constraints. Since this table might already exist via the big schema migration,
                // this code block might not even run.
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
