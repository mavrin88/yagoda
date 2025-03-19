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
            $table->integer('activity_type_id')->after('form_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tips_groups', function (Blueprint $table) {
            $table->dropColumn('activity_type_id');
        });
    }
};
