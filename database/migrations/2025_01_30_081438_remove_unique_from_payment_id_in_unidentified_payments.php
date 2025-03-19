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
            $table->dropUnique(['payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('unidentified_payments', function (Blueprint $table) {
            $table->unique('payment_id');
        });
    }
};
