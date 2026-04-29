<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('referrer_id')->index();
            $table->uuid('referee_id')->index();
            $table->string('referral_code', 20)->unique();
            $table->string('status', 20)->default('pending')->index();
            $table->boolean('reward_applied')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->unique(['referrer_id', 'referee_id']);
            $table->foreign('referrer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('referee_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
