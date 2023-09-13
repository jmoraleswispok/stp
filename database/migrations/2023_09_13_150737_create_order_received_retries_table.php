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
        Schema::create('order_received_retries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_received_id')->constrained()->cascadeOnUpdate();
            $table->longText('request');
            $table->longText('reason_for_rejection')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_received_retries');
    }
};
