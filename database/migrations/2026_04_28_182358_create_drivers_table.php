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
        if (! Schema::hasTable('drivers')) {
            Schema::create('drivers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name', 150);
                $table->string('phone', 30)->nullable();
                $table->string('vehicle_plate', 20)->nullable();
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
