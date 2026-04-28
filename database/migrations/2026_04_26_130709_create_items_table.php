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
    if (!Schema::hasTable('items')) {
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
            $table->char('origin_country', 2)->nullable();
            $table->text('sourcing_notes')->nullable();
            $table->timestamps();
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
