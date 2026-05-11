<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table): void {
            if (! Schema::hasColumn('claims', 'resolution_notes')) {
                $table->text('resolution_notes')->nullable()->after('description');
            }
        });

        Schema::table('notifications', function (Blueprint $table): void {
            if (! Schema::hasColumn('notifications', 'idempotency_key')) {
                $table->string('idempotency_key', 64)->nullable()->unique()->after('status');
            }

            if (! Schema::hasColumn('notifications', 'retry_count')) {
                $table->unsignedSmallInteger('retry_count')->default(0)->after('idempotency_key');
            }

            if (! Schema::hasColumn('notifications', 'last_error')) {
                $table->text('last_error')->nullable()->after('retry_count');
            }

            if (! Schema::hasColumn('notifications', 'channel')) {
                $table->string('channel', 30)->nullable()->after('last_error');
            }

            if (! Schema::hasColumn('notifications', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table): void {
            if (Schema::hasColumn('claims', 'resolution_notes')) {
                $table->dropColumn('resolution_notes');
            }
        });

        Schema::table('notifications', function (Blueprint $table): void {
            if (Schema::hasColumn('notifications', 'processed_at')) {
                $table->dropColumn('processed_at');
            }

            if (Schema::hasColumn('notifications', 'channel')) {
                $table->dropColumn('channel');
            }

            if (Schema::hasColumn('notifications', 'last_error')) {
                $table->dropColumn('last_error');
            }

            if (Schema::hasColumn('notifications', 'retry_count')) {
                $table->dropColumn('retry_count');
            }

            if (Schema::hasColumn('notifications', 'idempotency_key')) {
                $table->dropUnique('notifications_idempotency_key_unique');
                $table->dropColumn('idempotency_key');
            }
        });
    }
};
