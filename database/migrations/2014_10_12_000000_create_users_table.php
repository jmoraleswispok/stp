<?php

use App\Interfaces\ModelInterface;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('stp_account')->nullable();
            $table->tinyInteger('status')->default(ModelInterface::ACTIVE)->comment('0 => Inactivo, 1 => Activo');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['email', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
