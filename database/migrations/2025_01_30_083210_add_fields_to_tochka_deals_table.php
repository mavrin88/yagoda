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
        Schema::table('tochka_deals', function (Blueprint $table) {
            $table->integer('unidentified_payment_id')->after('uuid');
            $table->json('deal_prepare_data')->nullable()->after('organization_id');
            $table->json('deal_data')->nullable()->after('deal_prepare_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tochka_deals', function (Blueprint $table) {
            $table->dropColumn('unidentified_payment_id');
            $table->dropColumn('deal_prepare_data');
            $table->dropColumn('deal_data');
        });
    }
};
