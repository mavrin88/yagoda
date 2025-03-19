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
            $table->json('webhook')->after('email_receipt')->nullable();
            $table->string('payer_name')->after('webhook')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tips_order_statistics', function (Blueprint $table) {
            $table->dropColumn(['webhook', 'payer_name']);
        });
    }
};
