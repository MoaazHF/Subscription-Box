<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stub migration for the `addresses` table (owned by Team 1).
 * Contains only the columns Team 3 depends on.
 * Team 1 should replace this with their full migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('addresses')) {
            Schema::create('addresses', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('street')->nullable();
                $table->string('city')->nullable();
                $table->string('region')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('country')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
