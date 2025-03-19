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
            $table->renameColumn('uuid', 'deal_id');
            $table->string('deal_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tochka_deals', function (Blueprint $table) {
            $table->string('deal_id')->nullable(false)->change();
            $table->renameColumn('deal_id', 'uuid');
        });
    }
};
