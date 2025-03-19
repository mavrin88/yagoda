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
        Schema::table('order_statistics', function (Blueprint $table) {
            $table->decimal('bank_commission', 10, 2)->nullable()->after('service_acquiring_commission')->comment('Сумма комиссии банка за экваринг');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_statistics', function (Blueprint $table) {
            $table->dropColumn('bank_commission');
        });
    }
};
