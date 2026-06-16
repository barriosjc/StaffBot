<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE solicitud_destinatarios SET estado = 'no_respondio' WHERE estado = 'pendiente'");
        DB::statement("ALTER TABLE solicitud_destinatarios MODIFY COLUMN estado ENUM('no_respondio','aceptada','rechazada','expirada','cancelado') NOT NULL DEFAULT 'no_respondio'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE solicitud_destinatarios MODIFY COLUMN estado ENUM('pendiente','no_respondio','aceptada','rechazada','expirada','cancelado') NOT NULL DEFAULT 'pendiente'");
    }
};
