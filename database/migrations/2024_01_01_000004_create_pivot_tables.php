<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('centro_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('centro_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'centro_id']);
        });

        Schema::create('especialidad_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('especialidad_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'especialidad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidad_user');
        Schema::dropIfExists('centro_user');
    }
};
