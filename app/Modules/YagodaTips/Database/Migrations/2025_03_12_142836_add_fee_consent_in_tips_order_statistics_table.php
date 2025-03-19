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
        Schema::table('tips_order_statistics', function (Blueprint $table) {
            $table->decimal('fee_consent', 10, 2)->after('service_acquiring_commission')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tips_order_statistics', function (Blueprint $table) {
            $table->dropColumn('fee_consent');
        });
    }
};
