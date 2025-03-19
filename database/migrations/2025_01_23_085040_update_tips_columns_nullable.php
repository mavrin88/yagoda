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
        Schema::table('order_statistics', function (Blueprint $table) {
            $table->decimal('tips_percent', 10, 2)->nullable()->change();
            $table->decimal('tips_sum', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('order_statistics', function (Blueprint $table) {
            $table->decimal('tips_percent', 10, 2)->nullable(false)->change();
            $table->decimal('tips_sum', 10, 2)->nullable(false)->change();
        });
    }
};
