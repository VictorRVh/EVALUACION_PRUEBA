<?php

namespace Tests\Feature;

use App\Models\Docente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DocenteApiTest extends TestCase
{
    use RefreshDatabase; // Esto asegura que la base de datos se reinicie después de cada prueba

    public function testPuedeCrearDocente()
    {
        $data = [
            'nombre' => 'Victor',
            'apellido_paterno' => 'Valdez',
            'apellido_materno' => 'Huancuni',
            'dni' => '12345678',
            'sexo' => 'M',
            'celular' => '987654321',
            'correo' => 'victor@correo.com',
            'fecha_nacimiento' => '1985-05-15',
        ];

        $response = $this->postJson('/api/teacher', $data);

        $response->assertStatus(201)
            ->assertJson([
                'teacher' => [
                    'nombre' => 'Victor',
                    'apellido_paterno' => 'Valdez',
                    'apellido_materno' => 'Huancuni',
                    'dni' => '12345678',
                ],
                'status' => 201,
            ]);
    }


}
