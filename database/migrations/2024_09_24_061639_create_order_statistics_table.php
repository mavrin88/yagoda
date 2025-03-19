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
        Schema::create('order_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('tips_percent', 10, 2);
            $table->decimal('tips_sum', 10, 2);
            $table->decimal('service_commission', 10, 2);
            $table->decimal('service_acquiring_commission', 10, 2);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statistics');
    }
};
