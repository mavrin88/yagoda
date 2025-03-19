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
            $table->string('email_receipt')->nullable()->after('commission_for_using_the_service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tips_order_statistics', function (Blueprint $table) {
            $table->dropColumn('email_receipt');
        });
    }
};
