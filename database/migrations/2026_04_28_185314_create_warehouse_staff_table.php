<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('warehouse_staff')) {
            Schema::create('warehouse_staff', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name', 150);
                $table->string('email', 150)->unique();
                $table->string('phone', 30)->nullable();
                $table->string('shift', 20)->default('morning'); // morning, afternoon, night
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_staff');
    }
};
