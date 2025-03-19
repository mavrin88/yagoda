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
        Schema::create('tips_order_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->decimal('full_amount', 10, 2);
            $table->decimal('tips_sum', 10, 2);
            $table->string('type_pay')->nullable();
            $table->string('status')->default('new');
            $table->decimal('service_acquiring_commission', 10, 2);
            $table->uuid();
            $table->string('qrcId', 56)->nullable();
            $table->decimal('acquiring_fee')->nullable();
            $table->string('master_percentage')->default(100);
            $table->string('admin_percentage')->default(0);
            $table->string('staff_percentage')->default(0);
            $table->string('organization_percentage')->default(0);
            $table->decimal('commission_for_using_the_service')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tips_order_statistics');
    }
};
