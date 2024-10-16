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
        Schema::create('matricula', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_estudiante_id', 8)->nullable();
           // $table->char('codigo_matricula',6)->unique();
            $table->char('turno', 1)->nullable();
            $table->char('condicion', 1)->nullable();
            $table->string('programa_estudio_id', 100)->nullable();
            //$table->string('docente_id', 8)->nullable();
            $table->string('numero_recibo', 10)->unique();
            //$table->dateTime('fecha_matricula')->nullable();
            $table->foreign('codigo_estudiante_id')->references('dni')->on('estudiante')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('programa_estudio_id')->references('programa_estudio')->on('especialidad');
            //$table->foreign('docente_id')->references('dni')->on('docente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matricula');
    }
};
