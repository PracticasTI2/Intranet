<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vacaciones_acumuladas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('anio');
            $table->integer('dias_totales')->default(0);
            $table->integer('dias_tomados')->default(0);
            $table->integer('dias_pendientes')->default(0);
            $table->date('fecha_corte')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices y constraints
            $table->unique(['user_id', 'anio']);
            $table->index(['user_id', 'anio']);
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vacaciones_acumuladas');
    }
};