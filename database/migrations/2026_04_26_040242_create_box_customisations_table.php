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
        if (! Schema::hasTable('box_customisations')) {
            Schema::create('box_customisations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('box_id');
                $table->boolean('swap_allowed')->default(true);
                $table->string('theme_preference')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_customisations');
    }
};
