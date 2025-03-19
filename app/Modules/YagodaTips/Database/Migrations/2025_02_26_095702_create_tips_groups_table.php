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
        Schema::create('tips_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('full_name', 100)->nullable();
            $table->string('contact_name', 50)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->string('photo_path', 100)->nullable();
            $table->string('logo_path')->nullable();
            $table->integer('tips_1')->nullable();
            $table->integer('tips_2')->nullable();
            $table->integer('tips_3')->nullable();
            $table->integer('tips_4')->nullable();
            $table->string('email', 50)->nullable();
            $table->string('backup_card')->nullable();
            $table->integer('form_id')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tips_groups');
    }
};
