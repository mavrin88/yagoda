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
        Schema::create('unidentified_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->unique();
            $table->string('type')->nullable();
            $table->json('details')->nullable();
            $table->json('identification_payment')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidentified_payments');
    }
};
