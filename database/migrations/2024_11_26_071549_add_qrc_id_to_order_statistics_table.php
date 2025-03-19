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
            $table->string('qrcId', 56)->nullable()->after('bank_commission')->comment('Идентификатор платежа в системе QRC');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_statistics', function (Blueprint $table) {
            $table->dropColumn('qrcId');
        });
    }
};
