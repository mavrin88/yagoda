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
            $table->json('other_data')->nullable()->after('deal_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tochka_deals', function (Blueprint $table) {
            $table->dropColumn('other_data');
        });
    }
};
