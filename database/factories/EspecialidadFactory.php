<?php

namespace Database\Factories;

use App\Models\Docente;
use App\Models\Especialidad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Especialidad>
 */
class EspecialidadFactory extends Factory
{
    protected $model = Especialidad::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'id_unidad' => $this->faker->unique()->numerify('####'), // Genera un id_unidad único
            'programa_estudio' => $this->faker->unique()->words(3, true), // Nombre del programa de estudio
            'ciclo_formativo' => $this->faker->word, // Ciclo formativo
            'modalidad' => $this->faker->randomElement(['Presencial', 'Virtual', 'Semipresencial']), // Modalidad
            'modulo_formativo' => $this->faker->sentence(6), // Módulo formativo
            'descripcion_especialidad' => $this->faker->paragraph, // Descripción de la especialidad
            'docente_id' => Docente::factory(), // Usa el DNI del docente creado
            'periodo_academico' => $this->faker->year . '-' . $this->faker->year, // Periodo académico
            'hora_semanal' => $this->faker->numberBetween(1, 40), // Horas semanales
            'seccion' => $this->faker->randomLetter . $this->faker->numberBetween(1, 5), // Sección, por ejemplo 'A1', 'B2'
        ];
    }
}
