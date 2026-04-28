<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inventory_logs')) {
            Schema::create('inventory_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('delivery_id');
                $table->string('event', 60);           // status_changed, claim_filed, eco_toggled
                $table->string('from_value')->nullable();
                $table->string('to_value')->nullable();
                $table->uuid('changed_by')->nullable(); // user/driver/staff UUID
                $table->string('changed_by_type')->nullable(); // 'user', 'driver', 'warehouse_staff'
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('delivery_id')->references('id')->on('deliveries')->cascadeOnDelete();
                $table->index(['delivery_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
