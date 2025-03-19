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
            $table->text('map_link_yandex')->nullable()->after('team');
            $table->text('map_link_2gis')->nullable()->after('map_link_yandex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tips_groups', function (Blueprint $table) {
            $table->dropColumn(['map_link_yandex', 'map_link_2gis']);
        });
    }
};
