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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('master_percentage')->default(100);
            $table->string('admin_percentage')->default(0);
            $table->string('staff_percentage')->default(0);
            $table->string('organization_percentage')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(
                [
                    'master_percentage',
                    'admin_percentage',
                    'staff_percentage',
                    'organization_percentage'
                ]
            );
        });
    }
};
