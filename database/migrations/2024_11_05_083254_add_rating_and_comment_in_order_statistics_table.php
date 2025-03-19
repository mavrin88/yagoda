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
            $table->string('rating')->nullable()->after('status');
            $table->string('comment')->nullable()->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_statistics', function (Blueprint $table) {
            $table->dropColumn(['rating', 'comment']);
        });
    }
};
