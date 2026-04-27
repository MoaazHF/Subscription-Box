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
        if (! Schema::hasTable('boxes')) {
            Schema::create('boxes', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('subscription_id');
                $table->smallInteger('period_month');
                $table->smallInteger('period_year');
                $table->string('status');
                $table->date('lock_date');
                $table->string('theme')->nullable();
                $table->integer('total_weight_g')->default(0);
                $table->string('shipping_tier')->default('standard');
                $table->timestamps();

                $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
