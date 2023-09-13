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
        Schema::table('order_receiveds', function (Blueprint $table) {
            $table->string('stp_id')->after('user_id');
            $table->tinyInteger('approved')->after('stp_id')->default(0);
            $table->unsignedBigInteger('retries')->after('approved')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_receiveds', function (Blueprint $table) {
            //
        });
    }
};
