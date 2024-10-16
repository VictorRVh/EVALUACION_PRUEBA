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
        Schema::create('especialidad', function (Blueprint $table) {
            $table->id();
            $table->char('id_unidad', 4)->unique();
            $table->string('programa_estudio', 100)->unique();
            $table->string('ciclo_formativo', 50)->nullable();
            $table->string('modalidad', 45)->nullable();
            $table->string('modulo_formativo', 200)->nullable();
            $table->text('descripcion_especialidad')->nullable();
            $table->string('docente_id', 8)->nullable();
            $table->string('periodo_academico', 10)->nullable();
            $table->integer('hora_semanal')->nullable();
            $table->string('seccion', 5)->nullable();
            $table->foreign('docente_id')->references('dni')->on('docente')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especialidad');
    }
};
