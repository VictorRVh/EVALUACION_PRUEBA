<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\Especialidad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Matricula>
 */
class MatriculaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo_estudiante_id' => '', // Referencia a un estudiante generado
            'turno' => $this->faker->randomElement(['M', 'T']), // M = Mañana, T = Tarde
            'condicion' => $this->faker->randomElement(['N', 'R']), // N = Normal, R = Reincorporación
            'programa_estudio_id' => '', // Referencia a una especialidad generada
            'numero_recibo' => $this->faker->unique()->numerify('REC#####'), // Genera un número de recibo único
        ];
    }
}
