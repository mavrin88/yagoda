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
        Schema::table('users', function (Blueprint $table) {
            $table->text('encrypted_first_name')->nullable();
            $table->text('encrypted_email')->nullable();
            $table->text('encrypted_card_number')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('encrypted_first_name');
            $table->dropColumn('encrypted_email');
            $table->dropColumn('encrypted_card_number');
        });
    }
};
