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
            $table->decimal('acquiring_fee')->after('uuid')->nullable();
            $table->string('master_percentage')->default(100)->after('acquiring_fee');
            $table->string('admin_percentage')->default(0)->after('master_percentage');
            $table->string('staff_percentage')->default(0)->after('admin_percentage');
            $table->string('organization_percentage')->default(0)->after('staff_percentage');
            $table->decimal('commission_for_using_the_service')->after('organization_percentage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_statistics', function (Blueprint $table) {
            $table->dropColumn([
                'acquiring_fee', 'master_percentage', 'admin_percentage', 'staff_percentage', 'organization_percentage', 'commission_for_using_the_service'
            ]);
        });
    }
};
