<?php

namespace Database\Factories;

use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estudiante>
 */
class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition()
    {
        return [
            'codigo_estudiante' => $this->faker->unique()->numerify('240###'),
            'nombre' => $this->faker->firstName,
            'apellido_paterno' => $this->faker->lastName,
            'apellido_materno' => $this->faker->lastName,
            'dni' => $this->faker->unique()->numerify('########'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'celular' => $this->faker->numerify('9########'),
            'fecha_nacimiento' => $this->faker->date(),
            'correo' => $this->faker->unique()->safeEmail,
        ];
    }
}
