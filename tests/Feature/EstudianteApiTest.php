<?php

namespace Tests\Feature;

use App\Models\Estudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EstudianteApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase; // Para reiniciar la base de datos en cada prueba

    public function testPuedeCrearEstudiante()
    {
        // Datos de ejemplo para crear un estudiante
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

        // Realizar una solicitud POST para crear un estudiante
        $response = $this->postJson('/api/students', $data);

        // Verificar que la respuesta tiene el código de estado 201 (creado)
        $response->assertStatus(201)
            ->assertJson([
                'Estudiante' => [
                    'nombre' => 'Victor',
                    'apellido_paterno' => 'Valdez',
                    'apellido_materno' => 'Huancuni',
                    'dni' => '12345678',
                    'sexo' => 'M',
                    'celular' => '987654321',
                    'correo' => 'victor@correo.com',
                    'fecha_nacimiento' => '1985-05-15',
                    // Incluye más campos según sea necesario
                ],
                'status' => 201,
            ]);
    }

    public function testPuedeListarEstudiantesCuandoHayDatos()
    {
        // Crea un estudiante en la base de datos
        Estudiante::factory()->count(5)->create();

        // Realiza la solicitud para listar estudiantes
        $response = $this->getJson('/api/students');

        // Verifica que la respuesta tenga el código de estado 200 (OK)
        $response->assertStatus(200)
            ->assertJsonStructure([
                'Estudiantes' => [
                    '*' => [
                        'codigo_estudiante',
                        'nombre',
                        'apellido_paterno',
                        'apellido_materno',
                        'dni',
                    ],
                ],
                'status',
            ]);
    }

    public function testNoPuedeListarEstudiantesSiNoHay()
    {
        // Asegúrate de que no haya estudiantes en la base de datos
        // Realiza la solicitud para listar estudiantes
        $response = $this->getJson('/api/students');

        // Verifica que la respuesta tenga el código de estado 404 (No encontrado)
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No se encontraron datos',
                'status' => 404,
            ]);
    }
    public function testPuedeEliminarEstudiante()
    {
        // Crea un estudiante en la base de datos
        $estudiante = Estudiante::factory()->create();

        // Realiza la solicitud DELETE para eliminar el estudiante
        $response = $this->deleteJson("/api/students/{$estudiante->dni}");

        // Verifica que la respuesta tenga el código de estado 200 (OK)
        $response->assertStatus(200);

        // Verifica que el estudiante fue eliminado de la base de datos
        $this->assertDatabaseMissing('estudiante', ['id' => $estudiante->id]);
    }
    public function testPuedeActualizarEstudiante()
    {
        // Crea un estudiante en la base de datos usando el factory
        $estudiante = Estudiante::factory()->create([
            'nombre' => 'Victor',
            'apellido_paterno' => 'Valdez',
            'apellido_materno' => 'Huancuni',
            'dni' => '12345678',
            'sexo' => 'M',
            'celular' => '987654321',
            'correo' => 'victor@correo.com',
            'fecha_nacimiento' => '1985-05-15',
        ]);

        // Datos para actualizar el estudiante
        $data = [
            'nombre' => 'Raul',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Sanchez',
            'dni' => '87654321',
            'sexo' => 'M',
            'celular' => '912345678',
            'correo' => 'raul@correo.com',
            'fecha_nacimiento' => '1990-01-01',
        ];

        // Realiza una solicitud PUT/PATCH para actualizar el estudiante
        $response = $this->putJson("/api/students/{$estudiante->dni}", $data);

        // Verifica que la respuesta tenga el código de estado 200 (OK)
        $response->assertStatus(200)
            ->assertJson([
                'Estudiante' => [
                    'nombre' => 'Raul',
                    'apellido_paterno' => 'Perez',
                    'apellido_materno' => 'Sanchez',
                    'dni' => '87654321',
                    'sexo' => 'M',
                    'celular' => '912345678',
                    'correo' => 'raul@correo.com',
                    'fecha_nacimiento' => '1990-01-01',
                ],
                'status' => 200,
            ]);

        // Verifica que los datos en la base de datos fueron actualizados correctamente
        $this->assertDatabaseHas('estudiante', [
            'id' => $estudiante->id,
            'nombre' => 'Raul',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Sanchez',
            'dni' => '87654321',
            'celular' => '912345678',
            'correo' => 'raul@correo.com',
        ]);
    }


}
