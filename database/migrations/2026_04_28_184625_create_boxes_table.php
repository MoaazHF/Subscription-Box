<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stub migration for the `boxes` table (owned by Team 2).
 * Contains only the columns Team 3 depends on.
 * Team 2 should replace this with their full migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('boxes')) {
            Schema::create('boxes', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('theme')->nullable();
                $table->unsignedTinyInteger('period_month')->nullable();
                $table->unsignedSmallInteger('period_year')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
