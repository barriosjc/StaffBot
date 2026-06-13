<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->unsignedBigInteger('centro_id')->nullable()->after('id');
            $table->unsignedBigInteger('especialidad_id')->nullable()->after('centro_id');

            $table->foreign('centro_id')->references('id')->on('centros')->onDelete('set null');
            $table->foreign('especialidad_id')->references('id')->on('especialidades')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropForeign(['centro_id']);
            $table->dropForeign(['especialidad_id']);
            $table->dropColumn(['centro_id', 'especialidad_id']);
        });
    }
};
