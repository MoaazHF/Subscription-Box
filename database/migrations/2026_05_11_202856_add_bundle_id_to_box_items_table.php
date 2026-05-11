<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('box_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('box_items', 'bundle_id')) {
                $table->uuid('bundle_id')->nullable()->after('item_id')->index();
                $table->foreign('bundle_id')->references('id')->on('bundles')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('box_items', function (Blueprint $table): void {
            if (Schema::hasColumn('box_items', 'bundle_id')) {
                $table->dropForeign(['bundle_id']);
                $table->dropColumn('bundle_id');
            }
        });
    }
};
