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
        Schema::table('tips_groups', function (Blueprint $table) {
            $table->float('commission_for_using_the_service')->default(7);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tips_groups', function (Blueprint $table) {
            $table->dropColumn('commission_for_using_the_service');
        });
    }
};
