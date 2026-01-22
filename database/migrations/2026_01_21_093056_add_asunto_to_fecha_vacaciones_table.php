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
        Schema::table('fecha_vacaciones', function (Blueprint $table) {
            // Agregar campo asunto después de nombre_usuario
            $table->string('asunto', 255)
                  ->nullable()
                  ->after('nombre_usuario')
                  ->comment('Texto libre para agenda (opcional)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fecha_vacaciones', function (Blueprint $table) {
            // Eliminar el campo si se revierte la migración
            $table->dropColumn('asunto');
        });
    }
};