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
        Schema::create('tips_orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('full_amount', 10, 2);
            $table->string('status');
            $table->integer('tips')->default(0);
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('qr_code_id')->nullable();
            $table->boolean('is_open')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tips_orders');
    }
};
