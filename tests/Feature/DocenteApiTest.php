<?php

namespace Tests\Feature;

use App\Models\Docente; // Asegúrate de tener el modelo Docente
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

    public function testPuedeListarDocentesCuandoHayDatos()
    {
        // Crea un docente en la base de datos
        Docente::factory()->create([
            'nombre' => 'Patricia',
            'apellido_paterno' => 'Morales',
            'apellido_materno' => 'Salazar',
            'dni' => '89012345',
            'sexo' => 'F',
            'celular' => '987654328',
            'correo' => 'patricia.morales@example.com',
            'fecha_nacimiento' => '1994-12-25',
        ]);

        // Realiza la solicitud para listar docentes
        $response = $this->getJson('/api/teacher');

        // Verifica que la respuesta tenga el código de estado 200 (OK)
        $response->assertStatus(200)
            ->assertJsonStructure([
                'teachers' => [
                    '*' => [
                        'id',
                        'nombre',
                        'apellido_paterno',
                        'apellido_materno',
                        'dni',
                    ],
                ],
                'status',
            ]);
    }

    public function testNoPuedeListarDocentesSiNoHay()
    {
        // Asegúrate de que no haya docentes en la base de datos
        // Realiza la solicitud para listar docentes
        $response = $this->getJson('/api/teacher');

        // Verifica que la respuesta tenga el código de estado 404 (No encontrado)
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No se encontraron datos',
                'status' => 404,
            ]);
    }

    public function testPuedeActualizarDocente()
    {
        // Crea un docente en la base de datos
        $docente = Docente::factory()->create([
            'nombre' => 'Carlos',
            'apellido_paterno' => 'Fernandez',
            'apellido_materno' => 'Gomez',
            'dni' => '12345679',
        ]);

        $data = [
            'nombre' => 'Carlos Alberto',
            'apellido_paterno' => 'Fernandez',
            'apellido_materno' => 'Gomez',
            'dni' => '12345679',
            'sexo' => 'M',
            'celular' => '987654321',
            'correo' => 'carlos.alberto@example.com',
            'fecha_nacimiento' => '1980-05-15',
        ];

        $response = $this->putJson("/api/teacher/{$docente->dni}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'teacher' => [
                    'nombre' => 'Carlos Alberto',
                    'apellido_paterno' => 'Fernandez',
                    'apellido_materno' => 'Gomez',
                    'dni' => '12345679',
                ],
                'status' => 200,
            ]);
    }

    public function testPuedeEliminarDocente()
    {
        // Crea un docente en la base de datos
        $docente = Docente::factory()->create();

        $response = $this->deleteJson("/api/teacher/{$docente->dni}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Docente eliminado exitosamente',
                'status' => 200,
            ]);
        
        // Verifica que el docente haya sido eliminado de la base de datos
         $this->assertDatabaseMissing('estudiante', ['id' => $docente->id]);
    }
}
