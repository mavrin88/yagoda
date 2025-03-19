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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->index();
            $table->string('name', 100);
            $table->string('full_name', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('contact_name', 50)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->string('inn', 12)->nullable();
            $table->string('legal_address', 100)->nullable();
            $table->string('registration_number', 100)->nullable();
            $table->integer('acquiring_fee')->nullable();
            $table->string('photo_path', 100)->nullable();
            $table->integer('tips_1')->nullable();
            $table->integer('tips_2')->nullable();
            $table->integer('tips_3')->nullable();
            $table->integer('tips_4')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
