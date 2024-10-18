<?php

namespace Database\Factories;

use App\Models\Docente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Docente>
 */
class DocenteFactory extends Factory
{
    /**
     * El modelo asociado con esta factory.
     *
     * @var string
     */
    protected $model = Docente::class;

    /**
     * Define la estructura de datos de ejemplo para el modelo Docente.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->firstName,
            'apellido_paterno' => $this->faker->lastName,
            'apellido_materno' => $this->faker->lastName,
            'dni' => $this->faker->unique()->numerify('########'),  // 8 dígitos para DNI
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'celular' => $this->faker->numerify('9########'),  // 9 dígitos para celular
            'correo' => $this->faker->unique()->safeEmail,
            'fecha_nacimiento' => $this->faker->date(),
        ];
    }
}
