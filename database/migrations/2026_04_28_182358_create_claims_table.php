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
        if (! Schema::hasTable('claims')) {
            Schema::create('claims', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('delivery_id');
                $table->enum('type', ['damaged', 'missing'])->default('damaged');
                $table->text('description');
                $table->string('photo_path')->nullable();
                $table->enum('status', ['open', 'under_review', 'resolved', 'rejected'])->default('open');
                $table->timestamps();

                $table->foreign('delivery_id')->references('id')->on('deliveries')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
