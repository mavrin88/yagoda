<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('unidentified_payments', function (Blueprint $table) {
            $table->dropColumn('deal_id');
            $table->dropColumn('deal_prepare_data');
            $table->dropColumn('deal_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('unidentified_payments', function (Blueprint $table) {
            $table->string('deal_id')->nullable()->after('unidentified_payments_prepare_data');
            $table->json('deal_prepare_data')->nullable()->after('deal_id');
            $table->json('deal_data')->nullable()->after('deal_prepare_data');
        });
    }
};
